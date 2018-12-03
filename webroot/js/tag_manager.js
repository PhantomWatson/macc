let TagManager = {
  tags: [],
  selected_tags: [],

  /**
   * @param data An array of tag objects
   * @param container $('#container_id')
   * @returns
   */
  createTagList: function (data, container) {
    let list = $('<ul></ul>');
    for (let i = 0; i < data.length; i++) {
      let tagId = data[i].id;
      let tagName = data[i].name;
      let children = data[i].children;
      let hasChildren = (children.length > 0);
      let isSelectable = data[i].selectable;
      let listItem = $('<li id="available_tag_li_' + tagId + '"></li>');
      let row = $('<div></div>').addClass('single_row');
      listItem.append(row);
      list.append(listItem);

      if (isSelectable) {
        let tagLink = $('<a href="#"></a>')
          .addClass('available_tag')
          .attr('id', 'available_tag_' + tagId)
          .attr('title', 'Click to select')
          .append(tagName);
        (function (tagId) {
          tagLink.click(function (event) {
            event.preventDefault();
            let link = $(this);
            let tagName = link.html();
            let listItem = link.parents('li').first();
            TagManager.selectTag(tagId, tagName, listItem);
          });
        })(tagId);
        tagName = tagLink;
      }

      // Bullet point
      if (hasChildren) {
        let iconLink = $('<a href="#"></a>')
          .attr('title', 'Click to expand/collapse');
        let glyphicon = $('<span />')
          .attr('title', 'Click to expand/collapse')
          .addClass('glyphicon glyphicon-triangle-right expand_collapse');
        iconLink.append(glyphicon);
        (function (children) {
          iconLink.click(function (event) {
            event.preventDefault();
            let iconLink = $(this);
            let iconContainer = iconLink.parent('div');
            let childrenContainer = iconContainer.next('.children');

            // Populate list if it is empty
            if (childrenContainer.is(':empty')) {
              TagManager.createTagList(children, childrenContainer);
            }

            // Open/close
            let toggle = function (iconLink) {
              let icon = iconLink.children('span.expand_collapse');
              if (childrenContainer.is(':visible')) {
                icon.removeClass('glyphicon-triangle-right');
                icon.addClass('glyphicon-triangle-bottom');
              } else {
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
              }
            };
            childrenContainer.slideToggle(200, function () {
              toggle(iconLink);
            });
          });
        })(children);

        row.append(iconLink);
      } else {
        row.append('<span class="glyphicon glyphicon-tag"></span>');
      }

      row.append(tagName);

      // Tag and submenu
      if (hasChildren) {
        let childrenContainer = $('<div></div>')
          .addClass('children')
          .hide();
        row.after(childrenContainer);
      }

      // If tag has been selected
      if (isSelectable && this.tagIsSelected(tagId)) {
        tagName.addClass('selected');
        if (! hasChildren) {
          listItem.hide();
        }
      }
    }
    container.append(list);
  },

  tagIsSelected: function (tagId) {
    let selectedTags = $('#selected_tags').find('a');
    for (let i = 0; i < selectedTags.length; i++) {
      let tag = $(selectedTags[i]);
      if (tag.data('tagId') === tagId) {
        return true;
      }
    }
    return false;
  },

  preselectTags: function (selectedTags) {
    if (selectedTags.length === 0) {
      return;
    }
    $('#selected_tags_container').show();
    for (let i = 0; i < selectedTags.length; i++) {
      TagManager.selectTag(selectedTags[i].id, selectedTags[i].name);
    }
  },

  unselectTag: function (tagId, unselectLink) {
    let availableTagListItem = $('#available_tag_li_' + tagId);

    // Mark form as dirty
    if (typeof $.fn.dirty !== 'undefined') {
      unselectLink.closest('form').dirty('setAsDirty');
    }

    // If available tag has not yet been loaded, then simply remove the selected tag
    if (availableTagListItem.length === 0) {
      unselectLink.remove();
      if ($('#selected_tags').children().length === 0) {
        $('#selected_tags_container').slideUp(200);
      }
      return;
    }

    // Remove 'selected' class from available tag
    let availableLink = $('#available_tag_' + tagId);
    availableLink.removeClass('selected');

    let removeLink = function () {
      unselectLink.fadeOut(200, function () {
        unselectLink.remove();
        const noTagsSelected = $('#selected_tags').children().length === 0;
        if (noTagsSelected) {
          $('#selected_tags_container').slideUp(200);
        }
      });
    };

    availableTagListItem.slideDown(200);

    // If available tag is not visible, then no transfer effect
    if (availableLink.is(':visible')) {
      let options = {
        to: '#available_tag_' + tagId,
        className: 'ui-effects-transfer',
      };
      unselectLink.effect('transfer', options, 200, removeLink);
    } else {
      removeLink();
    }
  },

  selectTag: function (tagId, tagName) {
    let selectedContainer = $('#selected_tags_container');
    if (! selectedContainer.is(':visible')) {
      selectedContainer.slideDown(200);
    }

    // Do not add tag if it is already selected
    if (this.tagIsSelected(tagId)) {
      return;
    }

    // Add tag
    let listItem = $('<a href="#"></a>')
      .attr('id', 'selected_tag_' + tagId)
      .attr('title', 'Click to remove')
      .attr('data-tag-id', tagId)
      .html(tagName);
    let hiddenInput = $('<input />')
      .attr('type', 'hidden')
      .attr('name', 'tags[_ids][]')
      .attr('value', tagId);
    listItem.append(hiddenInput);
    listItem.click(function (event) {
      event.preventDefault();
      let unselectLink = $(this);
      let tagId = unselectLink.data('tagId');
      TagManager.unselectTag(tagId, unselectLink);
    });
    listItem.hide();
    $('#selected_tags').append(listItem);
    listItem.fadeIn(200);

    // If available tag has not yet been loaded, then return
    let availableTagListItem = $('#available_tag_li_' + tagId);
    if (availableTagListItem.length === 0) {
      return;
    }

    // Hide/update link to add tag
    let link = $('#available_tag_' + tagId);
    let options = {
      to: '#selected_tag_' + tagId,
      className: 'ui-effects-transfer',
    };
    let callback = function () {
      link.addClass('selected');
      const children = availableTagListItem.children('div.children');
      let hasChildren = children.length !== 0;
      if (! hasChildren) {
        availableTagListItem.slideUp(200);
      }
    };
    link.effect('transfer', options, 200, callback);

    // Mark form as dirty
    if (typeof $.fn.dirty !== 'undefined') {
      link.closest('form').dirty('setAsDirty');
    }
  },

  setupAutosuggest: function (selector) {
    $(selector).bind('keydown', function (event) {
      if (event.keyCode === $.ui.keyCode.TAB &&
        $(this).data('autocomplete').menu.active) {
        event.preventDefault();
      }
    }).autocomplete({
      source: function (request, response) {
        $.getJSON('/tags/auto_complete', {
          term: extractLast(request.term),
        }, response);
      },
      delay: 0,
      search: function () {
        let term = extractLast(this.value);
        if (term.length < 2) {
          return false;
        }
        $(selector).siblings('img.loading').show();
      },
      response: function () {
        $(selector).siblings('img.loading').hide();
      },
      focus: function () {
        return false;
      },
      select: function (event, ui) {
        let tagName = ui.item.label;
        let terms = split(this.value);
        terms.pop();
        terms.push(tagName);
        // Add placeholder to get the comma-and-space at the end
        terms.push('');
        this.value = terms.join(', ');
        return false;
      },
    });
  },

  setupCustomTagInput: function (selector) {
    if (! selector) {
      selector = '#custom_tag_input';
    }
    $(selector).bind('keydown', function (event) {
      // don't navigate away from the field on tab when selecting an item
      if (event.keyCode === $.ui.keyCode.TAB &&
        $(this).data('autocomplete').menu.active) {
        event.preventDefault();
      }
    }).autocomplete({
      source: function (request, response) {
        $.getJSON('/tags/auto_complete', {
          term: extractLast(request.term),
        }, response);
      },
      delay: 0,
      search: function () {
        // custom minLength
        let term = extractLast(this.value);
        if (term.length < 2) {
          //  return false;
        }
        $('#tag_autosuggest_loading').show();
      },
      response: function () {
        $('#tag_autosuggest_loading').hide();
      },
      focus: function () {
        // prevent value inserted on focus
        return false;
      },
      select: function (event, ui) {
        // Add the selected term to 'selected tags'
        let tagName = ui.item.label;
        let tagId = ui.item.value;
        TagManager.selectTag(tagId, tagName);

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
