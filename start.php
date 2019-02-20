<?php

use Elgg\Mentions\Regex;

/**
 * Provides links and notifications for using @username mentions
 */
elgg_register_event_handler('init', 'system', 'mentions_init');

/**
 * Initialize
 * @return void
 */
function mentions_init() {
	// can't use notification hooks here because of many reasons
	elgg_register_event_handler('create', 'object', 'mentions_notification_handler');
	elgg_register_event_handler('create', 'annotation', 'mentions_notification_handler');

	// @todo This will result in multiple notifications for an edited entity so we don't do this
	//register_elgg_event_handler('update', 'all', 'mentions_notification_handler');
}

/**
 * Catch all create events and scan for @username tags to notify user.
 *
 * @param string                    $event      "create"
 * @param string                    $event_type "object"|"annotation"
 * @param ElggObject|ElggAnnotation $object     Created object or annotation
 * @return void
 */
function mentions_notification_handler($event, $event_type, $object) {

        if ($object === false) {
                return;
        }
        
	$type = $object->getType();
	$subtype = $object->getSubtype();
	$owner = $object->getOwnerEntity();

	$type_key = "mentions:notification_types:$type:$subtype";
	if (!elgg_language_key_exists($type_key)) {
		// plugins can add to the list of mention objects by defining
		// the language string 'mentions:notification_types:<type>:<subtype>'
		return;
	}
	$type_str = elgg_echo($type_key);

	if ($object instanceof ElggAnnotation) {
		$fields = ['value'];
		$entity = $object->getEntity();
	} else {
		$fields = ['title', 'description'];
		$fields = elgg_trigger_plugin_hook('get_fields', 'mentions', ['entity' => $object], $fields);
		$entity = $object;
	}

	if (empty($fields)) {
		return;
	}

	$usernames = [];

	foreach ($fields as $field) {
		$content = $object->$field;
		if (is_array($content)) {
			$content = implode(' ', $content);
		}

		// it's ok in this case if 0 matches == FALSE
		if (preg_match_all(Regex::getRegex(), $content, $matches)) {
			// match against the 2nd index since the first is everything
			foreach ($matches[3] as $username) {
				if (empty($username)) {
					continue;
				}
				$usernames[] = $username;
			}
		}
	}

	$notified_guids = [];

	foreach ($usernames as $username) {
		$user = get_user_by_username($username);

		// check for trailing punctuation caught by the regex
		if (!$user && substr($username, -1) == '.') {
			$user = get_user_by_username(rtrim($username, '.'));
		}

		if (!$user) {
			continue;
		}

		if (in_array($user->guid, $notified_guids)) {
			continue;
		}

		$notified_guids[] = $user->guid;

		// if they haven't set the notification status default to sending.
		// Private settings are stored as strings so we check against "0"
		$notification_setting = elgg_get_plugin_user_setting('notify', $user->guid, 'mentions');
		if ($notification_setting === "0") {
			continue;
		}

		// user must have access to view object/annotation
		if (!has_access_to_entity($entity, $user)) {
			continue;
		}

		if ($user->language) {
			$language = $user->language;
		} else {
			$language = elgg_get_config('language');
		}

		$link = $object->getURL();

		$localized_type_str = elgg_echo($type_key, [], $language);
		$subject = elgg_echo('mentions:notification:subject', array($owner->name, $localized_type_str), $language);

		$body = elgg_echo('mentions:notification:body', array(
			$owner->name,
			$localized_type_str,
			$link,
				), $language);

		$params = array(
			'object' => $object,
			'action' => 'mention',
		);

		notify_user($user->guid, $owner->guid, $subject, $body, $params);
	}
}
