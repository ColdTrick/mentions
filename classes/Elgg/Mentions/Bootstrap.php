<?php

namespace Elgg\Mentions;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\DefaultPluginBootstrap::init()
	 */
	public function init() {
		$this->extendViews();
		$this->registerEvents();
		$this->registerHooks();
		
		elgg_define_js('mentions/editors/ckeditor2', [
			'src' => elgg_get_simplecache_url('mentions/editors/ckeditor'),
		]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\DefaultPluginBootstrap::ready()
	 */
	public function ready() {
		$this->registerSupportedOutputViews();
	}
	
	/**
	 * Extend views
	 *
	 * @return void
	 */
	protected function extendViews() {
		elgg_extend_view('elgg.css', 'mentions/mentions.css');
		elgg_extend_view('notifications/settings/other', 'mentions/notification_settings');
		
		if (elgg_is_logged_in()) {
			// mentions only supported for logged in users
			elgg_extend_view('input/longtext', 'mentions/input/longtext');
			elgg_extend_view('input/plaintext', 'mentions/input/plaintext');
		}
	}
	
	/**
	 * Register event listeners
	 *
	 * @return void
	 */
	protected function registerEvents() {
		$events = $this->elgg()->events;
		
		$events->registerHandler('create', 'object', __NAMESPACE__ . '\Notifications::sendNotifications');
		$events->registerHandler('publish', 'object', __NAMESPACE__ . '\Notifications::sendNotifications');
	}
	
	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('action:validate', 'notifications/settings', __NAMESPACE__ . '\Notifications::saveNotificationSettings');
	}
	
	/**
	 * Configure output views for support of mentions
	 *
	 * @return void
	 */
	protected function registerSupportedOutputViews() {
		$hooks = $this->elgg()->hooks;
		
		$views = [
			'output/longtext',
			'object/elements/summary/content',
			'object/elements/full/body',
		];
		$views = $hooks->trigger('get_views', 'mentions', null, $views);
		foreach ($views as $view) {
			$hooks->registerHandler('view', $view, __NAMESPACE__ . '\Views::replaceViewMentions');
		}
		
		$hooks->registerHandler('view_vars', 'river/elements/body', __NAMESPACE__ . '\Views::replaceRiverMentions');
	}
}
