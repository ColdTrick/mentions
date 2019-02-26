define(function(require) {
	/**
	 * @todo check if everything still works because of refactoring.
	 * See mckeditor file for best example
	 */
	var MentionsAutocomplete = require('mentions/autocomplete');

	// Give some time for the TinyMCE to load
	setTimeout(function () {
		for (var i = 0; i < tinymce.editors.length; i++) {
			var editor = tinymce.editors[i];

			var $elem = $(editor.getElement());
			var selector;
			if ($elem.attr('id').length) {
				selector = '#mentions-popup-' + $elem.attr('id'); 
			} else {
				selector = $elem.siblings('.mentions-popup').eq(0);
			}
			
			var mentions = new MentionsAutocomplete(selector);
			
			editor.on('keyup', function (e) {
				// Skip keycodes that cannot be used for entering a username
				if (!mentions.isValidKey(e.keyCode)) {
					return;
				}
				
				position = editor.selection.getRng(1).startOffset;
				content = tinyMCE.activeEditor.getContent({format : 'text'});
				
				mentions.autocomplete(content, position, function(content) {
					tinyMCE.activeEditor.setContent(content);
				});
			});
		}
	}, 500);
});
