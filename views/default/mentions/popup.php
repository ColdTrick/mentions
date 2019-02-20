<?php

echo elgg_view_module('popup', '', elgg_view('graphics/ajax_loader', ['hidden' => false]), [
	'class' => 'mentions-popup hidden',
	'id' => 'mentions-popup',
]);
