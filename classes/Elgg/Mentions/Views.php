<?php

namespace Elgg\Mentions;

class Views {
	
	/**
	 * Replace @mentions with user information in views
	 *
	 * @param \Elgg\Hook $hook 'view', '<view name>'
	 *
	 * @return string
	 */
	public static function replaceViewMentions(\Elgg\Hook $hook) {
		
		$content = $hook->getValue();
		
		return Regex::replaceMentions($content);
	}
	
	/**
	 * Replace @mentions with user information in river listing
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'river/elements/body'
	 *
	 * @return void|array
	 */
	public static function replaceRiverMentions(\Elgg\Hook $hook) {
		
		$vars = $hook->getValue();
		
		$message = elgg_extract('message', $vars);
		if (elgg_is_empty($message)) {
			return;
		}
		
		$vars['message'] = Regex::replaceMentions($message);
		
		return $vars;
	}
}
