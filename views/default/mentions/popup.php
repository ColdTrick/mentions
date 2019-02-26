<?php
/**
 * Wrapper for autocomplete results
 */

$defaults = [
	'id' => 'elgg-input-' . base_convert(mt_rand(), 10, 36),
];
$vars = array_merge($defaults, $vars);

// rebuilding module because need more control
$content = elgg_view('graphics/ajax_loader', [
	'hidden' => false,
]);
$body = elgg_format_element('div', ['class' => 'elgg-body'], $content);

echo elgg_format_element('div', [
	'class' => ['elgg-module', 'elgg-module-popup', 'hidden', 'mentions-popup'],
	'id' => 'mentions-popup-' . elgg_extract('id', $vars),
	'data-input-id' => elgg_extract('id', $vars),
], $body);
