<?php
/**
 * Provides links and notifications for using @username mentions
 *
 */

elgg_register_event_handler('init', 'system', 'mentions_init');

function mentions_init() {
	elgg_extend_view('css/elgg', 'css/mentions');

	elgg_register_event_handler('pagesetup', 'system', 'mentions_get_views');

	// can't use notification hooks here because of many reasons
	// only check against annotations:generic_comment and entity:object
	elgg_register_event_handler('create', 'object', 'mentions_notification_handler');
	elgg_register_event_handler('create', 'annotation', 'mentions_notification_handler');

	// @todo This will result in multiple notifications for an edited entity so we don't do this
	//register_elgg_event_handler('update', 'all', 'mentions_notification_handler');
}

function mentions_get_regex() {
	// @todo this won't work for usernames that must be html encoded.
	// get all chars with unicode 'letter' or 'mark' properties or a number _ or .,
	// preceeded by @, and possibly surrounded by word boundaries.
	return '/[\b]?@([\p{L}\p{M}_\.0-9]+)[\b]?/iu';
}

function mentions_get_views() {
	// allow plugins to add additional views to be processed for usernames
	$views = array('output/longtext');
	$views = elgg_trigger_plugin_hook('get_views', 'mentions', null, $views);
	foreach ($views as $view) {
		elgg_register_plugin_hook_handler('view', $view, 'mentions_rewrite');
	}
}

/**
 * Rewrites a view for @username mentions.
 *
 * @param string $hook    The name of the hook
 * @param string $type    The type of the hook
 * @param string $content The content of the page
 * @return string
 */
function mentions_rewrite($hook, $entity_type, $returnvalue, $params) {

	$regexp = mentions_get_regex();
	$returnvalue =  preg_replace_callback($regexp, 'mentions_preg_callback', $returnvalue);
	
	return $returnvalue;
}

/**
 * Used as a callback fro the preg_replace in mentions_rewrite()
 *
 * @param type $matches
 * @return type str
 */
function mentions_preg_callback($matches) {
	$user = get_user_by_username($matches[1]);
	$period = '';

	// Catch the trailing period when used as punctuation and not a username.
	if (!$user && substr($matches[1], -1) == '.') {
		$user = get_user_by_username(rtrim($matches[1], '.'));
		$period = '.';
	}

	if ($user) {
		if (elgg_get_plugin_setting('fancy_links', 'mentions')) {
			$icon = "<img class='pas mentions-user-icon' src='" . $user->getIcon('topbar') ."' />";
			return "<a class='mentions-user-link' href=\"{$user->getURL()}\">$icon{$user->name}</a>$period";
		} else {
			return "<a href=\"{$user->getURL()}\">{$matches[0]}</a>";
		}
	} else {
		return $matches[0];
	}
}

/**
 * Catch all create events and scan for @username tags to notify user.
 *
 * @param string   $event      The event name
 * @param string   $event_type The event type
 * @param ElggData $object     The object that was created
 * @return void
 */
function mentions_notification_handler($event, $event_type, $object) {
	// excludes messages - otherwise an endless loop of notifications occur!
	if (elgg_instanceof($object, 'object', 'messages')) {
		return;
	}

	$type = $object->getType();
	$subtype = $object->getSubtype();
	$owner = $object->getOwnerEntity();

	$fields = array(
		'title', 'description', 'value'
	);

	// store the guids of notified users so they only get one notification per creation event
	$notified_guids = array();

	foreach ($fields as $field) {
		$content = $object->$field;
		// it's ok in this case if 0 matches == FALSE
		if (preg_match_all(mentions_get_regex(), $content, $matches)) {
			// match against the 2nd index since the first is everything
			foreach ($matches[1] as $username) {

				$user = get_user_by_username($username);

				// check for trailing punctuation caught by the regex
				if (!$user && substr($username, -1) == '.') {
					$user = get_user_by_username(rtrim($username, '.'));
				}

				if (!$user) {
					continue;
				}

				// user must have access to view object/annotation
				if ($type == 'annotation') {
					$annotated_entity = $object->getEntity();
					if (!$annotated_entity || !has_access_to_entity($annotated_entity, $user)) {
						continue;
					}
				} else {
					if (!has_access_to_entity($object, $user)) {
						continue;
					}
				}

				if (!in_array($user->getGUID(), $notified_guids)) {
					$notified_guids[] = $user->getGUID();

					// if they haven't set the notification status default to sending.
					// Private settings are stored as strings so we check against "0"
					$notification_setting = elgg_get_plugin_user_setting('notify', $user->getGUID(), 'mentions');
					if ($notification_setting === "0") {
						continue;
					}

					$link = $object->getURL();
					$type_key = "mentions:notification_types:$type:$subtype";
					$type_str = elgg_echo($type_key);
					if ($type_str == $type_key) {
						// plugins can add to the list of mention objects by defining
						// the language string 'mentions:notification_types:<type>:<subtype>'
						continue;
					}
					$subject = elgg_echo('mentions:notification:subject', array($owner->name, $type_str));

					$body = elgg_echo('mentions:notification:body', array(
						$owner->name,
						$type_str,
						$link,
					));

					notify_user($user->getGUID(), $owner->getGUID(), $subject, $body);
				}
			}
		}
	}
}