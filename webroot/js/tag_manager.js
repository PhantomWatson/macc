var TagManager = {
	tags: [],
	selected_tags: [],

	/**
	 * @param data An array of tag objects
	 * @param container $('#container_id')
	 * @returns
	 */
	createTagList: function(data, container) {
		var list = $('<ul></ul>');
		var available_tags_container = $('#available_tags');
		for (var i = 0; i < data.length; i++) {
			var tag_id = data[i].id;
			var tag_name = data[i].name;
			var children = data[i].children;
			var has_children = (children.length > 0);
			var is_selectable = data[i].selectable;
			var list_item = $('<li id="available_tag_li_'+tag_id+'"></li>');
			var row = $('<div class="single_row"></div>');
			list_item.append(row);
			list.append(list_item);
			
			if (is_selectable) {
				var tag_link = $('<a href="#" class="available_tag" title="Click to select" id="available_tag_'+tag_id+'"></a>');
				tag_link.append(tag_name);
				(function(tag_id) {
					tag_link.click(function (event) {
						event.preventDefault();
						var link = $(this);
						var tag_name = link.html();
						var list_item = link.parents('li').first();
						TagManager.selectTag(tag_id, tag_name, list_item);
					});
				})(tag_id);
				tag_name = tag_link;
			}
			
			// Bullet point
			if (has_children) {
				var collapsed_icon = $('<a href="#" title="Click to expand/collapse"></a>');
				collapsed_icon.append('<span class="glyphicon glyphicon-triangle-right expand_collapse" />');
				(function(children) {
					collapsed_icon.click(function(event) {
						event.preventDefault();
						var icon = $(this);
						var icon_container = icon.parent('div');
						var children_container = icon_container.next('.children');
						var row = icon_container.parent('li');
						
						// Populate list if it is empty
						if (children_container.is(':empty')) {
							TagManager.createTagList(children, children_container);
						}
						
						// Open/close
						var toggle = function(icon) {
                            var icon = icon.children('span.expand_collapse');
                            if (children_container.is(':visible')) {
                                icon.removeClass('glyphicon-triangle-right');
                                icon.addClass('glyphicon-triangle-bottom');
                            } else {
                                icon.removeClass('glyphicon-triangle-bottom');
                                icon.addClass('glyphicon-triangle-right');
                            }
						};
						children_container.slideToggle(200, function () {
						    toggle(icon);
					    });
					});
				})(children);
				
				row.append(collapsed_icon);
			} else {
				row.append('<span class="glyphicon glyphicon-tag"></span>');
			}
			
			row.append(tag_name);
			
			// Tag and submenu
			if (has_children) {
				var children_container = $('<div style="display: none;" class="children"></div>');
				row.after(children_container);
			}
			
			// If tag has been selected
			if (is_selectable && this.tagIsSelected(tag_id)) {
				tag_name.addClass('selected');
				if (! has_children) {
					list_item.hide();
				}
			}
		}
		container.append(list);
	},

	tagIsSelected: function(tag_id) {
		var selected_tags = $('#selected_tags a');
		for (var i = 0; i < selected_tags.length; i++) {
			var tag = $(selected_tags[i]);
			if (tag.data('tagId') == tag_id) {
				return true;
			}
		}
		return false;
	},

	preselectTags: function(selected_tags) {
		if (selected_tags.length == 0) {
			return;
		}
		$('#selected_tags_container').show();
		for (var i = 0; i < selected_tags.length; i++) {
			TagManager.selectTag(selected_tags[i].id, selected_tags[i].name);
		}
	},

	unselectTag: function(tag_id, unselect_link) {
		var available_tag_list_item = $('#available_tag_li_'+tag_id);
		
		// If available tag has not yet been loaded, then simply remove the selected tag
		if (available_tag_list_item.length == 0) {
			unselect_link.remove();
			if ($('#selected_tags').children().length == 0) {
				$('#selected_tags_container').slideUp(200);
			}
			return;
		}

		// Remove 'selected' class from available tag
		var available_link = $('#available_tag_'+tag_id);
		if (available_link.hasClass('selected')) {
			available_link.removeClass('selected');
		}
		
		var remove_link = function() {
			unselect_link.fadeOut(200, function() {
				unselect_link.remove();
				if ($('#selected_tags').children().length == 0) {
					$('#selected_tags_container').slideUp(200);
				}
			});
		};
		
		available_tag_list_item.slideDown(200);
		
		// If available tag is not visible, then no transfer effect
		if (available_link.is(':visible')) {
			var options = {
				to: '#available_tag_'+tag_id,
				className: 'ui-effects-transfer'
			};
			unselect_link.effect('transfer', options, 200, remove_link);
		} else {
			remove_link();
		}
	},

	selectTag: function(tag_id, tag_name, available_tag_list_item) {
		var selected_container = $('#selected_tags_container');
		if (! selected_container.is(':visible')) {
			selected_container.slideDown(200);
		}
		
		// Do not add tag if it is already selected
		if (this.tagIsSelected(tag_id)) {
			return;
		}
		
		// Add tag
		var list_item = $('<a href="#" title="Click to remove" data-tag-id="'+tag_id+'" id="selected_tag_'+tag_id+'"></a>');
		list_item.append(tag_name);
		list_item.append('<input type="hidden" name="data[Tag][]" value="'+tag_id+'" />');
		list_item.click(function (event) {
			event.preventDefault();
			var unselect_link = $(this);
			var tag_id = unselect_link.data('tagId');
			TagManager.unselectTag(tag_id, unselect_link);
		});
		list_item.hide();
		$('#selected_tags').append(list_item);
		list_item.fadeIn(200);
		
		// If available tag has not yet been loaded, then return
		var available_tag_list_item = $('#available_tag_li_'+tag_id);
		if (available_tag_list_item.length == 0) {
			return;
		}
		
		// Hide/update link to add tag
		var link = $('#available_tag_'+tag_id);
		var options = {
			to: '#selected_tag_'+tag_id,
			className: 'ui-effects-transfer'
		};
		var callback = function() {
			link.addClass('selected');
			var has_children = (available_tag_list_item.children('div.children').length != 0);
			if (! has_children) {
				available_tag_list_item.slideUp(200);
			}
		};
		link.effect('transfer', options, 200, callback);
	},

	setupAutosuggest: function(selector) {
		$(selector).bind('keydown', function (event) {
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data('autocomplete').menu.active) {
				event.preventDefault();
			}
		}).autocomplete({
			source: function(request, response) {
				$.getJSON('/tags/auto_complete', {
					term: extractLast(request.term)
				}, response);
			},
			delay: 0,
			search: function() {
				var term = extractLast(this.value);
				if (term.length < 2) {
					return false;
				}
				$(selector).siblings('img.loading').show();
			},
			response: function() {
				$(selector).siblings('img.loading').hide();
			},
			focus: function() {
				return false;
			},
			select: function(event, ui) {
				var tag_name = ui.item.label;
				var terms = split(this.value);
				terms.pop();
				terms.push(tag_name);
				// Add placeholder to get the comma-and-space at the end
				terms.push('');
				this.value = terms.join(', ');
				return false;
			}
		});
	},
	
	setupCustomTagInput: function(selector) {
		if (! selector) {
			selector = '#custom_tag_input';
		}
		$(selector).bind('keydown', function (event) {
			// don't navigate away from the field on tab when selecting an item
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data('autocomplete').menu.active) {
				event.preventDefault();
			}
		}).autocomplete({
			source: function(request, response) {
				$.getJSON('/tags/auto_complete', {
					term: extractLast(request.term)
				}, response);
			},
			delay: 0,
			search: function() {
				// custom minLength
				var term = extractLast(this.value);
				if (term.length < 2) {
				//	return false;
				}
				$('#tag_autosuggest_loading').show();
			},
			response: function() {
				$('#tag_autosuggest_loading').hide();
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function(event, ui) {
				// Add the selected term to 'selected tags'
				var tag_name = ui.item.label;
				var tag_id = ui.item.value;
				TagManager.selectTag(tag_id, tag_name);
				
				var terms = split(this.value);
				// Remove the term being typed from the input field
				terms.pop();
				if (terms.length > 0) {
					// Add placeholder to get the comma-and-space at the end
					terms.push('');
				}
				this.value = terms.join(', ');
				
				return false;
			}
		});
	}
};