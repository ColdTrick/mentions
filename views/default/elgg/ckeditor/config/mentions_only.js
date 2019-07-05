define(function(require) {
	require('elgg/init');
	var elgg = require('elgg');
	var $ = require('jquery');

	return elgg.trigger_hook('config', 'ckeditor', {'editor': 'mentions_only'}, {
		toolbar: [],
		height: 100,
		allowedContent: 'img',
		baseHref: elgg.get_site_url(),
		removePlugins: 'toolbar,liststyle,contextmenu,tabletools,elementspath,tableselection',
		resize_enabled: false,
		extraPlugins: 'blockimagepaste',
		autoParagraph: false,
		enterMode: CKEDITOR.ENTER_BR, 
		shiftEnterMode: CKEDITOR.ENTER_BR,
		forceEnterMode: true,
//		basicEntities: false,
//		entities: false,
		defaultLanguage: 'en',
		language: elgg.get_language(),
		skin: 'moono-lisa',
		contentsCss: elgg.get_simplecache_url('elgg/wysiwyg.css'),
		disableNativeSpellChecker: false,
		disableNativeTableHandles: false,
		customConfig: false, //no additional config.js
		stylesSet: false, //no additional styles.js
	});
});

