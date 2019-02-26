/**
 * Autocomplete @mentions
 *
 * Fetch and display a list of matching users when writing a @mention and
 * autocomplete the selected user.
 */
define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');
	var Ajax = require('elgg/Ajax');
	
	function MentionsAutocomplete(popup_selector) {
		this.$popup = $(popup_selector);
		
		this.callback;
		this.beforeMention;
		this.afterMention;
		this.position;
		this.current;
		this.debounceTimeout;
	};
	
	MentionsAutocomplete.prototype = {
		/**
		 * Display AJAX response and provide new content for the editor
		 */
		handleResponse : function (json) {
			var userOptions = '';
			$(json).each(function(key, user) {
				userOptions += '<li data-username="' + user.value + '">' + user.label + "</li>";
			});
			
			if (!userOptions) {
				this.hide();
				return;
			}
			
			var instance = this;
			
			this.$popup.find('> .elgg-body').html('<ul class="mentions-autocomplete">' + userOptions + "</ul>");
			this.$popup.removeClass('hidden');
	
			this.$popup.find('.mentions-autocomplete > li').bind('click', function(e) {
				e.preventDefault();
	
				var username = $(this).data('username');
	
				// Remove the partial @username string from the first part
				var newBeforeMention = instance.beforeMention.substring(0, instance.position - instance.current.length);
	
				// Add the complete @username string and the rest of the original
				// content after the first part
				var newContent = newBeforeMention + username + instance.afterMention;
	
				instance.callback(newContent);
	
				// Hide the autocomplete popup
				instance.hide();
			});
		},
		
		autocomplete : function (content, position, editorCallback) {
			this.callback = editorCallback;
	
			this.position = position;
			this.beforeMention = content.substring(0, position);
			this.afterMention = content.substring(position);
			
			var parts = this.beforeMention.split(' ');
			this.current = parts[parts.length - 1];
			
			var precurrent = false;
			if (parts.length > 1) {
				precurrent = parts[parts.length - 1];
	
				if (!this.current.match(/@/)) {
					if (precurrent.match(/@/)) {
						this.current = precurrent + ' ' + this.current;
					}
				}
			}
			
			if (this.current.match(/@/) && this.current.length > 2) {
				this.current = this.current.replace('@', '');
				
				this.debounce(this.getAutocompleteData, 200)(this.current);
			}
		},
		
		getAutocompleteData : function (term) {
			this.show();
			
			var instance = this;
			var target_guid = elgg.get_page_owner_guid();
			var ajax = new Ajax(false);
			ajax.path('livesearch/mentions', {
				data: {
					term: term,
					target_guid: target_guid,
					view: 'json'
				},
				success: function(data) {
					instance.handleResponse(data);
				},
				error: function () {
					instance.hide();
				}
			});
		},
	
		/**
		 * Check if entered key represents a valid character for a username
		 *
		 * 8  = backspace
		 * 13 = enter
		 * 32 = space
		 *
		 * @param {String} keyCode
		 * @return {Boolean}
		 */
		isValidKey : function(keyCode) {
			var keyCodes = [8, 13, 32];
	
			if (keyCodes.indexOf(keyCode) == -1) {
				return true;
			} else {
				this.hide();
				return;
			}
		},
	
		/**
		 * Hide the autocomplete results
		 */
		hide : function() {
			this.$popup.find('> .elgg-body').html('<div class="elgg-ajax-loader"></div>');
			this.$popup.addClass('hidden');
		},
		
		show : function() {
			this.$popup.removeClass('hidden');
		},
		
		/**
		 * Delayed function call, prevent overloading browser with requests
		 */
		debounce : function (func, wait, immediate) {
			var instance = this;
			
			return function() {
				var context = instance, args = arguments;
				var later = function() {
					instance.debounceTimeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(instance.debounceTimeout);
				instance.debounceTimeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}
	};

	return MentionsAutocomplete;
});
