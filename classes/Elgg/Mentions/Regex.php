<?php

namespace Elgg\Mentions;

class Regex {

	/**
	 * Returns regex pattern for matching a @mention
	 * @return string
	 */
	public static function getRegex() {
		
		// match anchor tag with all attributes and wrapped html
		// we want to exclude matches that have already been wrapped in an anchor
		$match_anchor = "<a[^>]*?>.*?<\/a>";

		// match tag name and attributes
		// we want to exclude matches that found within tag attributes
		$match_attr = "<.*?>";

		// match username followed by @
		$match_username = "(@([\p{L}\p{Nd}._-]+))";

		// match at least one space or punctuation char before a match
		$match_preceding_char = "(^|\s|\!|\.|\?|>|\G)+";

		return "/{$match_anchor}|{$match_attr}|{$match_preceding_char}{$match_username}/i";
	}

	/**
	 * Replace @mention in text
	 *
	 * @param string $text text to search/replace in
	 *
	 * @return string
	 */
	public static function replaceMentions(string $text) {
		return preg_replace_callback(self::getRegex(), self::class . '::callback', $text);
	}
	
	/**
	 * Used as a callback for the preg_replace
	 *
	 * @param array $matches preg_match result
	 *
	 * @return string
	 */
	public static function callback(array $matches) {
		$source = elgg_extract(0, $matches);
		$preceding_char = elgg_extract(1, $matches);
		$mention = elgg_extract(2, $matches);
		$username = elgg_extract(3, $matches);
		
		$plugin = elgg_get_plugin_from_id('mentions');
		
		if (empty($username)) {
			return $source;
		}
		
		$user = get_user_by_username($username);
		
		// Catch the trailing period when used as punctuation and not a username.
		$period = '';
		if (!$user && substr($username, -1) == '.') {
			$user = get_user_by_username(rtrim($username, '.'));
			$period = '.';
		}
		
		if (!$user) {
			return $source;
		}
		
		$label = $mention;
		if ($plugin->getSetting('named_links')) {
			$label = $user->getDisplayName();
		}
		
		$replacement = elgg_view('output/url', [
			'text' => $label,
			'href' => $user->getURL(),
		]);
	
		return $preceding_char . $replacement . $period;
	}
	
	/**
	 * Find usernames in text
	 *
	 * @param string $text the text to search
	 *
	 * @return string[]
	 */
	public static function findUsernames(string $text) {
		$matches = [];
		
		if (!preg_match_all(self::getRegex(), $text, $matches)) {
			return [];
		}
		
		$usernames = elgg_extract(3, $matches, []);
		return array_filter($usernames);
	}
}
