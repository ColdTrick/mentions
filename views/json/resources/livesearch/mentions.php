<?php

elgg_gatekeeper();

$target_guid = (int) get_input('target_guid');
$target = get_entity($target_guid);

// needed for correct output, but not used when handling results
set_input('name', 'dummy_input');

if ($target instanceof ElggGroup && elgg_get_plugin_setting('restrict_group_search', 'mentions')) {
	// search only for group members
	set_input('group_guid', $target_guid);
	
	echo elgg_view_resource('livesearch/group_members');
	return;
} elseif (elgg_get_plugin_setting('friends_only_search', 'mentions')) {
	echo elgg_view_resource('livesearch/friends');
	return;
}

echo elgg_view_resource('livesearch/users');
