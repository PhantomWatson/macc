let TagManager = {
  tags: [],
  selected_tags: [],

  /**
   * @param data An array of tag objects
   * @param container $('#container_id')
   * @returns
   */
  createTagList: function(data, container) {
    let list = $('<ul></ul>');
    for (let i = 0; i < data.length; i++) {
      let tag_id = data[i].id;
      let tag_name = data[i].name;
      let children = data[i].children;
      let has_children = (children.length > 0);
      let is_selectable = data[i].selectable;
      let list_item = $('<li id="available_tag_li_' + tag_id + '"></li>');
      let row = $('<div></div>').addClass('single_row');
      list_item.append(row);
      list.append(list_item);

      if (is_selectable) {
        let tag_link = $('<a href="#"></a>')
          .addClass('available_tag')
          .attr('id', 'available_tag_' + tag_id)
          .attr('title', 'Click to select')
          .append(tag_name);
        (function(tag_id) {
          tag_link.click(function(event) {
            event.preventDefault();
            let link = $(this);
            let tag_name = link.html();
            let list_item = link.parents('li').first();
            TagManager.selectTag(tag_id, tag_name, list_item);
          });
        })(tag_id);
        tag_name = tag_link;
      }

      // Bullet point
      if (has_children) {
        let collapsed_icon = $('<a href="#"></a>')
          .attr('title', 'Click to expand/collapse');
        let glyphicon = $('<span />')
          .attr('title', 'Click to expand/collapse')
          .addClass('glyphicon glyphicon-triangle-right expand_collapse');
        collapsed_icon.append(glyphicon);
        (function(children) {
          collapsed_icon.click(function(event) {
            event.preventDefault();
            let icon = $(this);
            let icon_container = icon.parent('div');
            let children_container = icon_container.next('.children');

            // Populate list if it is empty
            if (children_container.is(':empty')) {
              TagManager.createTagList(children, children_container);
            }

            // Open/close
            let toggle = function() {
              let icon = icon.children('span.expand_collapse');
              if (children_container.is(':visible')) {
                icon.removeClass('glyphicon-triangle-right');
                icon.addClass('glyphicon-triangle-bottom');
              } else {
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
              }
            };
            children_container.slideToggle(200, function() {
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
        let children_container = $('<div></div>')
          .addClass('children')
          .hide();
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
    let selected_tags = $('#selected_tags').find('a');
    for (let i = 0; i < selected_tags.length; i++) {
      let tag = $(selected_tags[i]);
      if (tag.data('tagId') === tag_id) {
        return true;
      }
    }
    return false;
  },

  preselectTags: function(selected_tags) {
    if (selected_tags.length === 0) {
      return;
    }
    $('#selected_tags_container').show();
    for (let i = 0; i < selected_tags.length; i++) {
      TagManager.selectTag(selected_tags[i].id, selected_tags[i].name);
    }
  },

  unselectTag: function(tag_id, unselect_link) {
    let available_tag_list_item = $('#available_tag_li_' + tag_id);

    // Mark form as dirty
    if (typeof $.fn.dirty !== 'undefined') {
      unselect_link.closest('form').dirty('setAsDirty');
    }

    // If available tag has not yet been loaded, then simply remove the selected tag
    if (available_tag_list_item.length === 0) {
      unselect_link.remove();
      if ($('#selected_tags').children().length === 0) {
        $('#selected_tags_container').slideUp(200);
      }
      return;
    }

    // Remove 'selected' class from available tag
    let available_link = $('#available_tag_' + tag_id);
    available_link.removeClass('selected');

    let remove_link = function() {
      unselect_link.fadeOut(200, function() {
        unselect_link.remove();
        const noTagsSelected = $('#selected_tags').children().length === 0;
        if (noTagsSelected) {
          $('#selected_tags_container').slideUp(200);
        }
      });
    };

    available_tag_list_item.slideDown(200);

    // If available tag is not visible, then no transfer effect
    if (available_link.is(':visible')) {
      let options = {
        to: '#available_tag_' + tag_id,
        className: 'ui-effects-transfer',
      };
      unselect_link.effect('transfer', options, 200, remove_link);
    } else {
      remove_link();
    }
  },

  selectTag: function(tag_id, tag_name) {
    let selected_container = $('#selected_tags_container');
    if (! selected_container.is(':visible')) {
      selected_container.slideDown(200);
    }

    // Do not add tag if it is already selected
    if (this.tagIsSelected(tag_id)) {
      return;
    }

    // Add tag
    let list_item = $('<a href="#"></a>')
      .attr('id', 'selected_tag_' + tag_id)
      .attr('title', 'Click to remove')
      .attr('data-tag-id', tag_id);
    list_item.append(tag_name);
    list_item.append('<input />')
      .attr('type', 'hidden')
      .attr('name', 'tags[_ids][]')
      .attr('value', tag_id);
    list_item.click(function(event) {
      event.preventDefault();
      let unselect_link = $(this);
      let tag_id = unselect_link.data('tagId');
      TagManager.unselectTag(tag_id, unselect_link);
    });
    list_item.hide();
    $('#selected_tags').append(list_item);
    list_item.fadeIn(200);

    // If available tag has not yet been loaded, then return
    let available_tag_list_item = $('#available_tag_li_' + tag_id);
    if (available_tag_list_item.length === 0) {
      return;
    }

    // Hide/update link to add tag
    let link = $('#available_tag_' + tag_id);
    let options = {
      to: '#selected_tag_' + tag_id,
      className: 'ui-effects-transfer',
    };
    let callback = function() {
      link.addClass('selected');
      const children = available_tag_list_item.children('div.children');
      let has_children = children.length !== 0;
      if (! has_children) {
        available_tag_list_item.slideUp(200);
      }
    };
    link.effect('transfer', options, 200, callback);

    // Mark form as dirty
    if (typeof $.fn.dirty !== 'undefined') {
      link.closest('form').dirty('setAsDirty');
    }
  },

  setupAutosuggest: function(selector) {
    $(selector).bind('keydown', function(event) {
      if (event.keyCode === $.ui.keyCode.TAB &&
        $(this).data('autocomplete').menu.active) {
        event.preventDefault();
      }
    }).autocomplete({
      source: function(request, response) {
        $.getJSON('/tags/auto_complete', {
          term: extractLast(request.term),
        }, response);
      },
      delay: 0,
      search: function() {
        let term = extractLast(this.value);
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
        let tag_name = ui.item.label;
        let terms = split(this.value);
        terms.pop();
        terms.push(tag_name);
        // Add placeholder to get the comma-and-space at the end
        terms.push('');
        this.value = terms.join(', ');
        return false;
      },
    });
  },

  setupCustomTagInput: function(selector) {
    if (! selector) {
      selector = '#custom_tag_input';
    }
    $(selector).bind('keydown', function(event) {
      // don't navigate away from the field on tab when selecting an item
      if (event.keyCode === $.ui.keyCode.TAB &&
        $(this).data('autocomplete').menu.active) {
        event.preventDefault();
      }
    }).autocomplete({
      source: function(request, response) {
        $.getJSON('/tags/auto_complete', {
          term: extractLast(request.term),
        }, response);
      },
      delay: 0,
      search: function() {
        // custom minLength
        let term = extractLast(this.value);
        if (term.length < 2) {
          //  return false;
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
        let tag_name = ui.item.label;
        let tag_id = ui.item.value;
        TagManager.selectTag(tag_id, tag_name);

        let terms = split(this.value);
        // Remove the term being typed from the input field
        terms.pop();
        if (terms.length > 0) {
          // Add placeholder to get the comma-and-space at the end
          terms.push('');
        }
        this.value = terms.join(', ');

        return false;
      },
    });
  },
};
