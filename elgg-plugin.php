<?php

use Elgg\Mentions\Bootstrap;

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
];
