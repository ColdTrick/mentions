<?php
/**
 * Plugin settings for mentions
 */

/* @var $entity \ElggPlugin */
$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('mentions:named_links'),
	'name' => 'params[named_links]',
	'value' => 1,
	'checked' => (bool) $entity->named_links,
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('mentions:fancy_links'),
	'name' => 'params[fancy_links]',
	'value' => 1,
	'checked' => (bool) $entity->fancy_links,
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('mentions:restrict_group_search'),
	'name' => 'params[restrict_group_search]',
	'value' => 1,
	'checked' => (bool) $entity->restrict_group_search,
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('mentions:friends_only_search'),
	'name' => 'params[friends_only_search]',
	'value' => 1,
	'checked' => (bool) $entity->friends_only_search,
	'switch' => true,
]);
