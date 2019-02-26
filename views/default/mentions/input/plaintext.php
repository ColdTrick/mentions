<?php
/**
 * Extension on input/plaintext to support mentions
 */

if (!elgg_extract('allow_mentions', $vars, true)) {
	return;
}

elgg_require_js('mentions/editors/plaintext');

echo elgg_view('mentions/popup', $vars);
