<?php
/**
 * Extension on input/longtext to support mentions in different WYSIWYG editors
 */

$plugins = [
	'ckeditor',
	'tinymce',
	'extended_tinymce',
];

$editor = 'plaintext';
foreach ($plugins as $plugin) {
	if (elgg_is_active_plugin($plugin)) {
		$editor = $plugin;
	}
}

if ($editor == 'extended_tinymce') {
	$editor = 'tinymce';
}

elgg_require_js("mentions/editors/$editor");
