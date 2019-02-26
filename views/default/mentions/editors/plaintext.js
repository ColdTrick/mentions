define(function(require) {
	var MentionsAutocomplete = require('mentions/autocomplete');
	var $ = require('jquery');

	var getCursorPosition = function(el) {
		var pos = 0;

		if ('selectionStart' in el) {
			pos = el.selectionStart;
		} else if ('selection' in document) {
			el.focus();
			var Sel = document.selection.createRange();
			var SelLength = document.selection.createRange().text.length;
			Sel.moveStart('character', - el.value.length);
			pos = Sel.text.length - SelLength;
		}

		return pos;
	};

	$('textarea').each(function(index, elem) {
		var $elem = $(elem);
		var selector;
		if ($elem.attr('id').length) {
			selector = '#mentions-popup-' + $elem.attr('id'); 
		} else {
			selector = $elem.siblings('.mentions-popup').eq(0);
		}
		
		var mentions = new MentionsAutocomplete(selector);
		
		$elem.on('keyup', function(e) {
			// Skip keycodes that cannot be used for entering a username
		 	if (!mentions.isValidKey(e.which)) {
		 		return;
		 	}

			textarea = $(this);
			content = $(this).val();
			position = getCursorPosition(this);

			mentions.autocomplete(content, position, function(content) {
				textarea.val(content);
			});
		});
	});
});
