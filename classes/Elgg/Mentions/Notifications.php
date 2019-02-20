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
}
