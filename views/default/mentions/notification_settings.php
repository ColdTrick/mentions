<?php
/**
 * User setting for mentions
 */

$user = elgg_extract('user', $vars);
if (!$user instanceof ElggUser || !$user->canEdit()) {
	return;
}

$methods = elgg_get_notification_methods();
if (empty($methods)) {
	return;
}

$value = [];
$method_options = [];
foreach ($methods as $method) {
	$label = elgg_echo("notification:method:$method");
	$method_options[$label] = $method;
	
	if (elgg_get_plugin_user_setting("notify_{$method}", $user->guid, 'mentions', 1)) {
		$value[] = $method;
	}
}

$content = elgg_format_element('div', ['class' => ['elgg-subscription-description']], elgg_echo('mentions:settings:send_notification'));
$content .= elgg_view_field([
	'#type' => 'checkboxes',
	'#class' => 'elgg-subscription-methods',
	'name' => 'mentions_notify',
	'options' => $method_options,
	'default' => false,
	'value' => $value,
	'align' => 'horizontal',
]);

echo elgg_format_element('div', ['class' => ['elgg-subscription-record']], $content);
