require(['jquery', 'elgg'], function($, elgg) {
	
	elgg.register_hook_handler('config', 'ckeditor', function(hook, type, params, returnValue) {
		
		returnValue.extraPlugins += ',mentions';
		returnValue.mentions = [
			{
				minChars: 1,
				feed: elgg.normalize_url('livesearch/mentions?q={encodedQuery}'),

				itemTemplate: '<li data-id="{id}">' +
					'<div class="elgg-image-block mentions-autocomplete-item">' +
						'<div class="elgg-image">' +
							'<div class="elgg-avatar elgg-avatar-tiny"><img class="photo" src="{mentions_icon_url}" /></div>' +
						'</div>' +	
				        '<div class="elgg-body">{name}</div>' +
			        '</div>' +	
	            '</li>',
	            outputTemplate: '@{username}&nbsp;'
			}
		];
		
		return returnValue;
	});
});
