<?php

namespace Elgg\Mentions;

class Notifications {
	
	/**
	 * Save the mention notification settings
	 *
	 * @param \Elgg\Hook $hook 'action:validate', 'notifications/settings'
	 *
	 * @return void
	 */
	public static function saveNotificationSettings(\Elgg\Hook $hook) {
		
		$user_guid = (int) get_input('guid');
		$user = get_user($user_guid);
		if (!$user instanceof \ElggUser || !$user->canEdit()) {
			return;
		}
		
		$mentions = (array) get_input('mentions_notify');
		$methods = elgg_get_notification_methods();
		foreach ($methods as $method) {
			elgg_set_plugin_user_setting("notify_{$method}", (int) in_array($method, $mentions), $user->guid, 'mentions');
		}
	}
	
	/**
	 * Send out notification to @mentioned users
	 *
	 * @param \Elgg\Event $event 'create|publish', 'object'
	 *
	 * @return void
	 */
	public static function sendNotifications(\Elgg\Event $event) {
		
		$entity = $event->getObject();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		$owner = $entity->getOwnerEntity();
		if (!$owner instanceof \ElggUser) {
			// some entities aren't owned by a user
			$owner = elgg_get_logged_in_user_entity();
		}
		
		if (!$owner instanceof \ElggUser) {
			// no user found who mentioned other users
			return;
		}
		
		// special case for blogs, only handle 'publish' event not 'create'
		if ($entity instanceof \ElggBlog && $event->getName() === 'create') {
			return;
		}
		
		$type_key = "mentions:notification_types:{$entity->getType()}:{$entity->getSubtype()}";
		if (!elgg_language_key_exists($type_key)) {
			// not supported by any plugin
			return;
		}
		
		// allow plugins to change the fields to search for @mentions
		$fields = ['title', 'description'];
		$fields = elgg_trigger_plugin_hook('get_fields', 'mentions', ['entity' => $entity], $fields);
		if (empty($fields) || !is_array($fields)) {
			return;
		}
		
		$usernames = [];
		foreach ($fields as $field) {
			$value = $entity->$field;
			if (empty($value)) {
				continue;
			} elseif (is_array($value)) {
				$value = implode(' ', $value);
			}
			
			$usernames = array_merge($usernames, Regex::findUsernames($value));
		}
		
		if (empty($usernames)) {
			return;
		}
		
		$usernames = array_unique($usernames);
		$notified_users = [
			$owner->guid, // don't notify owner of created content, probably current user
			elgg_get_logged_in_user_guid(), // don't notify logged in user
		];
		
		foreach ($usernames as $username) {
			$user = get_user_by_username($username);
			// check for trailing punctuation caught by the regex
			if (!$user instanceof \ElggUser && substr($username, -1) === '.') {
				$user = get_user_by_username(rtrim($username, '.'));
			}
			
			if (!$user instanceof \ElggUser) {
				continue;
			}
			
			if (in_array($user->guid, $notified_users)) {
				// already notified, could be because of punctuation
				continue;
			}
			
			$notified_users[] = $user->guid;
			
			if (!has_access_to_entity($entity, $user)) {
				// no access
				continue;
			}
			
			$notification_settings = self::getUserNotificationSettings($user);
			if (empty($notification_settings)) {
				// user doesn't wish notifications
				continue;
			}
			system_message('mentions: ' . $user->getDisplayName());
			$language = $user->getLanguage();
			
			$localized_type_str = elgg_echo($type_key, [], $language);
			
			$subject = elgg_echo('mentions:notification:subject', [
				$owner->getDisplayName(),
				$localized_type_str,
			], $language);
			
			$message = elgg_echo('mentions:notification:body', [
				$owner->getDisplayName(),
				$localized_type_str,
				$entity->getURL(),
			], $language);
			
			$params = [
				'object' => $entity,
				'action' => 'mention',
			];
			
			notify_user($user->guid, $owner->guid, $subject, $message, $params, $notification_settings);
		}
	}
	
	/**
	 * Get the notification settings for @mentions for the given user
	 *
	 * @param \ElggUser $user the user to check
	 *
	 * @return string[] allowed notification methods
	 */
	protected static function getUserNotificationSettings(\ElggUser $user) {
		$result = [];
		
		$methods = elgg_get_notification_methods();
		foreach ($methods as $method) {
			if (!elgg_get_plugin_user_setting("notify_{$method}", $user->guid, 'mentions', 1)) {
				continue;
			}
			
			$result[] = $method;
		}
		
		return $result;
	}
}
