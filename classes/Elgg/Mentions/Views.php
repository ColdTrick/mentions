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
	
	/**
	 * Adds id to objects export in livesearch
	 *
	 * @param \Elgg\Hook $hook 'to:object', 'entity'
	 *
	 * @return void|array
	 */
	public static function addMentionDataToLivesearch(\Elgg\Hook $hook) {
		
		$object = $hook->getValue();
		
		$entity = $hook->getEntityParam();
		if (!$entity) {
			return;
		}
		
		if (!elgg_in_context('livesearch')) {
			return;
		}
		
		$object->id = $entity->guid;
		$object->mentions_icon_url = $entity->getIconURL('tiny');
		
		return $object;
	}
}
