<?php

use Elgg\Mentions\Bootstrap;
use Elgg\Mentions\Upgrades\MigrateNotificationSettings;

$composer_dir = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_dir = __DIR__ . '/';
}

return [
	'bootstrap' => Bootstrap::class,
	'routes' => [
		'default:mentions:search' => [
			'path' => '/mentions/search/{target_guid}',
			'requirements' => [
				'target_guid' => '\d+',
			],
			'resource' => 'mentions/search',
		],
	],
	'settings' => [
		'named_links' => 1,
	],
	'upgrades' => [
		MigrateNotificationSettings::class,
	],
	'views' => [
		'default' => [
			'ckeditor/' => $composer_dir . 'vendor/ckeditor/ckeditor/',
			'jquery.ckeditor.js' => $composer_dir . 'vendor/ckeditor/ckeditor/adapters/jquery.js',
		],
	],
];
