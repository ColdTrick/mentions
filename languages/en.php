<?php
/**
 * English language translation.
 */

return [
	
	// upgrades
	'mentions:upgrade:2019022100:title' => "Migrate mention notification settings",
	'mentions:upgrade:2019022100:description' => "Notification settings are now stored per notifiction method,
migrate all old settings to the new settings.",
	
	// notification
	'mentions:notification:subject' => '%s mentioned you in %s',
	'mentions:notification:body' => '%s mentioned you in %s.

To see the full post, click on the link below:
%s',
	
	// supported entities
	'mentions:notification_types:object:blog' => 'a blog post',
	'mentions:notification_types:object:bookmarks' => 'a bookmark',
	'mentions:notification_types:object:discussion' => 'a discussion post',
	'mentions:notification_types:object:thewire' => 'a wire post',
	'mentions:notification_types:object:comment' => 'a comment',
	
	// user notification settings
	'mentions:settings:send_notification' => 'Send a notification when someone @mentions you in a post?',

	// plugin settings
	'mentions:named_links' => 'Replace @username with a user\'s display name',
	'mentions:fancy_links' => 'Add a small picture of the user to next to user\'s name',
	'mentions:restrict_group_search' => 'In group context, restrict autocomplete suggestions to group members only',
	'mentions:friends_only_search' => 'Restrict autocomplete suggestions to friends only',
];
