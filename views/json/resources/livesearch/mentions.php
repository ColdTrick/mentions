<?php

elgg_gatekeeper();

// needed for correct output, but not used when handling results
$vars['name'] = 'dummy_input';

// don't mention banned users
$vars['include_banned'] = false;

// check for group, and plugin settings
$target_guid = (int) elgg_extract('target_guid', $vars);
$target = get_entity($target_guid);
if ($target instanceof ElggGroup && elgg_get_plugin_setting('restrict_group_search', 'mentions')) {
	// search only for group members
	$vars['group_guid'] = $target_guid;
	
	echo elgg_view_resource('livesearch/group_members', $vars);
	return;
} elseif (elgg_get_plugin_setting('friends_only_search', 'mentions')) {
	echo elgg_view_resource('livesearch/friends', $vars);
	return;
}

echo elgg_view_resource('livesearch/users', $vars);
