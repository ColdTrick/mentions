<?php
/**
 * Wrapper for autocomplete results
 */

echo elgg_view_module('popup', '', elgg_view('graphics/ajax_loader', ['hidden' => false]), [
	'class' => 'hidden',
	'id' => 'mentions-popup',
]);
