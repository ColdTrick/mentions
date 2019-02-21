<?php

namespace Elgg\Mentions\Upgrades;

use Elgg\Upgrade\AsynchronousUpgrade;
use Elgg\Upgrade\Result;

class MigrateNotificationSettings implements AsynchronousUpgrade {
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\Upgrade\Batch::getVersion()
	 */
	public function getVersion() {
		return 2019022100;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\Upgrade\Batch::needsIncrementOffset()
	 */
	public function needsIncrementOffset() {
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\Upgrade\Batch::shouldBeSkipped()
	 */
	public function shouldBeSkipped() {
		return empty($this->countItems());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\Upgrade\Batch::countItems()
	 */
	public function countItems() {
		return elgg_get_entities_from_plugin_user_settings($this->getOptions(['count' => true]));
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\Upgrade\Batch::run()
	 */
	public function run(Result $result, $offset) {
		
		$methods = elgg_get_notification_methods();
		
		$batch = elgg_get_entities_from_plugin_user_settings($this->getOptions(['offset' => $offset]));
		/* @var $user \ElggUser */
		foreach ($batch as $user) {
			
			if (elgg_get_plugin_user_setting('notify', $user->guid, 'mentions', 1)) {
				// user has notifications enabled (or not set)
				if (elgg_unset_plugin_user_setting('notify', $user->guid, 'mentions')) {
					$result->addSuccesses();
				} else {
					$result->addFailures();
				}
				continue;
			}
			
			// notifications should be disabled
			foreach ($methods as $method) {
				elgg_set_plugin_user_setting("notify_{$method}", 0, $user->guid, 'mentions');
			}
			
			// remove old setting
			if (elgg_unset_plugin_user_setting('notify', $user->guid, 'mentions')) {
				$result->addSuccesses();
			} else {
				$result->addFailures();
			}
		}
		
		return $result;
	}
	
	/**
	 * Get options for elgg_get_entities_from_plugin_user_settings
	 *
	 * @param array $options additional options
	 *
	 * @return array
	 */
	protected function getOptions(array $options) {
		$defaults = [
			'type' => 'user',
			'limit' => 25,
			'batch' => true,
			'plugin_id' => 'mentions',
			'plugin_user_setting_name' => 'notify',
		];
		
		return array_merge($defaults, $options);
	}
}
