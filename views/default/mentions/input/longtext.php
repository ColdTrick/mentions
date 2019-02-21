<?php
/**
 * Extension on input/longtext to support mentions in different WYSIWYG editors
 */

if (!elgg_extract('allow_mentions', $vars, true)) {
	return;
}

$editor = 'plaintext';
if (elgg_extract('editor', $vars, true)) {
	// in the format:
	// plugin_id => editor_file to require
	$plugins = [
		'ckeditor' => 'mckeditor',
		'tinymce' => 'timymce',
		'extended_tinymce' => 'tinymce',
	];
	
	foreach ($plugins as $plugin_id => $editor_file) {
		if (elgg_is_active_plugin($plugin_id)) {
			$editor = $editor_file;
			break;
		}
	}
}

elgg_require_js("mentions/editors/$editor");

echo elgg_view('mentions/popup');
