var defaultMagnificConfig = {
    type: 'image',
    image: {
        titleSrc: function () {
            return null;
        }
    }
};

var membershipPurchase = {
    init: function (params) {
        paymentProcessor.postUrl = params.postUrl;
        paymentProcessor.redirectUrl = params.redirectUrl;
        paymentProcessor.email = params.email;
        paymentProcessor.key = params.key;
        paymentProcessor.buttonSelector = '#payment-button';
        paymentProcessor.costDollars = params.costDollars;
        paymentProcessor.description = params.membershipLevelName+' ($'+params.costDollars+')';
        paymentProcessor.postData = {
            membershipLevelId: params.membershipLevelId,
            userId: params.userId,
            autoRenew: params.autoRenew ? 1 : 0
        };
        paymentProcessor.beforePurchase = function () {
            // Only check auto-renewal form field if it is found on the page
            var autoRenew = params.autoRenew;
            var renewalField = $('input[name=renewal]:checked');
            if (renewalField.length) {
              autoRenew = renewalField.val() === 'automatic';
              paymentProcessor.postData.autoRenew = autoRenew ? 1 : 0;
            }

            paymentProcessor.confirmationMessage = 'Confirm payment of $' + params.costDollars + ' to purchase one year of membership?';
            if (autoRenew) {
                paymentProcessor.confirmationMessage += ' You will be automatically charged to renew your membership every year and can cancel automatic renewal at any time.';
            }
            return true;
        };
        paymentProcessor.setupPurchaseButton();
    }
};

var commonmarkPreviewer = {
    init: function (previewButton, input, output) {
        $('#'+previewButton).click(function (event) {
            var reader = new commonmark.Parser();
            var writer = new commonmark.HtmlRenderer();
            var profileCommonmark = $('#'+input).val();

            // Escape HTML in input
            profileCommonmark = profileCommonmark.replace(/</g, '&lt;');
            profileCommonmark = profileCommonmark.replace(/>/g, '&gt;');

            var parsed = reader.parse(profileCommonmark);
            var htmlResult = writer.render(parsed);

            // Strip out any tags created by parser but not approved
            var sanitizer = new Sanitize({
                elements: [
                    'p', 'br',
                    'i', 'em',
                    'b', 'strong',
                    'a',
                    'ul', 'ol', 'li',
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                    'blockquote'
                ],
                attributes: {
                    a: ['href']
                }
            });
            var dummyInputNode = document.createElement('div');
            dummyInputNode.innerHTML = htmlResult;
            htmlResult = sanitizer.clean_node(dummyInputNode);

            $('#'+output).html(htmlResult);
        });
    }
};

var userPictureEditor = {
    isAdmin: false,
    limit: null,
    mainPictureId: null,
    userId: null,

    init: function (params) {
        this.isAdmin = params.hasOwnProperty('admin') && params.admin === true;
        this.limit = params.limit;
        this.mainPictureId = params.mainPictureId;
        this.userId = params.userId;

        this.checkLimitReached(false);

        $('#pictures a').magnificPopup(defaultMagnificConfig);

        $('#pictures button.remove').click(function (event) {
            event.preventDefault();
            userPictureEditor.deletePicture($(this));
        });
        const url = (this.isAdmin ? '/admin' : '') + '/pictures/add.json';
        let formData = {
          'timestamp': params.timestamp,
          'token': params.token
        };
        if (this.isAdmin) {
          formData.user_id = this.userId;
        }

        $('#picture-upload').uploadifive({
            'uploadScript': url,
            'checkScript': false,
            'onCheck': false,
            'fileSizeLimit': params.filesizeLimit,
            'buttonText': 'Click to select an image to upload',
            'width': 300,
            'fileType': '.jpg,.jpeg,.png,.gif',
            'formData': formData,
            'onUploadComplete': function(file, data) {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    var msg = 'There was an error uploading that image.';
                    msg += ' Please try again with a smaller image, or contact an administrator if you need assistance.';
                    $('#upload-status')
                        .attr('class', 'alert alert-danger')
                        .html(msg)
                        .show();
                    return;
                }
                var filename = data.picture;
                var fullPath = '/img/members/'+params.userId+'/'+filename;
                var thumbnailFilename = userPictureEditor.getThumbnailFilename(filename);
                var thumbPath = '/img/members/'+params.userId+'/'+thumbnailFilename;
                var img = $('<img src="'+thumbPath+'" />');
                var link = $('<a href="'+fullPath+'" title="Click for full-size"></a>').append(img);
                link.magnificPopup(defaultMagnificConfig);
                var pictureCell = $('<td></td>').append(link);

                var isMainIndicator = $('<span class="glyphicon glyphicon-star is-main" title="Main picture"></span>').hide();
                var makeMainButtonContainer = $('<div class="make-main-container"></div>').show();
                var makeMainButton = $('<button class="btn btn-link make-main" title="Make main picture"></button>');
                makeMainButton.click(function (event) {
                    event.preventDefault();
                    var button = $(this);
                    userPictureEditor.makeMain(button);
                });
                makeMainButton.append('<span class="glyphicon glyphicon-star-empty"></span>');
                makeMainButtonContainer.append(makeMainButton);

                var removeButton = $('<button class="btn btn-link remove" title="Remove"></button>');
                removeButton.html('<span class="glyphicon glyphicon-remove text-danger"></span>');
                removeButton.click(function (event) {
                    event.preventDefault();
                    userPictureEditor.deletePicture($(this));
                });

                var actionsCell = $('<td></td>');
                actionsCell.append(isMainIndicator);
                actionsCell.append(makeMainButtonContainer);
                actionsCell.append(removeButton);
                var row = $('<tr data-picture-id="'+data.pictureId+'"></tr>').append(actionsCell).append(pictureCell);
                $('#pictures tbody').append(row);

                $('#upload-status')
                    .attr('class', 'alert alert-success')
                    .html('Picture added')
                    .show();

                /* If this is the only picture, it's automatically set (in the back end)
                 * as the user's main picture. Update page to reflect that. */
                var pictures = $('#pictures tbody tr').not('.deleting');
                if (pictures.length == 1) {
                    userPictureEditor.mainPictureId = pictures.first().data('picture-id');
                    userPictureEditor.toggleMainPicButtons();
                }

                userPictureEditor.checkLimitReached(true);
            },
            'onError': function(errorType, files) {
                var response = JSON.parse(file.xhr.responseText);
                $('#upload-status')
                    .attr('class', 'alert alert-danger')
                    .html(response.message)
                    .show();
            },
            'onQueueComplete': function() {
                this.uploadifive('clearQueue');
            }
        });

        this.toggleMainPicButtons();

        $('#pictures .make-main').click(function (event) {
            event.preventDefault();
            var button = $(this);
            userPictureEditor.makeMain(button);
        });
    },

    makeMain: function (button) {
        var pictureId = button.closest('tr').data('picture-id');
        const url = this.isAdmin
          ? '/admin/pictures/make-main/' + this.userId + '/' + pictureId + '.json'
          : '/pictures/make-main/' + pictureId + '.json';
        const isAdmin = this.isAdmin;

        $.ajax({
            url: url,
            beforeSend: function () {
                button.find('.glyphicon').hide();
                button.append('<img src="/img/loading_small.gif" alt="..." title="Loading..." />');
            },
            success: function () {
                var row = button.closest('tr');
                button.find('img').remove();
                button.find('.glyphicon').show();
                userPictureEditor.mainPictureId = pictureId;
                userPictureEditor.toggleMainPicButtons();
                row.parent().prepend(row);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                button.find('.glyphicon').show();
                button.find('img').remove();
                try {
                    var response = JSON.parse(jqXHR.responseText);
                    message = response.message;
                } catch(error) {
                    message = 'There was an error making that ' + (isAdmin ? 'the user\'s' : 'your') + ' main picture.';
                }
                alert(message);
            }
        });
    },

    toggleMainPicButtons: function () {
        $('#pictures tr').each(function () {
            var row = $(this);
            var pictureId = row.data('picture-id');
            if (pictureId == userPictureEditor.mainPictureId) {
                row.find('.is-main').show();
                row.find('.make-main-container').hide();
            } else {
                row.find('.is-main').hide();
                row.find('.make-main-container').show();
            }
        });
    },

    deletePicture: function (button) {
        if (! confirm('Are you sure you want to remove this picture?')) {
            return;
        }
        var pictureId = button.closest('tr').data('picture-id');
        const url = (this.isAdmin ? '/admin' : '') + '/pictures/delete/' + pictureId;
        $.ajax({
            type: 'DELETE',
            url: url,
            dataType: 'json',
            beforeSend: function (jqXHR, settings) {
                button.find('.glyphicon').hide();
                button.append('<img src="/img/loading_small.gif" class="loading" />');
                button.closest('tr').addClass('deleting');
            },
            success: function (data, textStatus, jqXHR) {
                var row = button.closest('tr');
                row.removeClass('deleting').addClass('deleted');
                var msg = $('<span class="text-success">Picture deleted</span>');
                var cell = row.find('td:nth-child(2)');
                cell.prepend(msg);
                cell.find('img').slideUp();
                setTimeout(function () {
                    row.fadeOut(500, function () {
                        $(this).remove();
                    });
                }, 3000);

                userPictureEditor.checkLimitReached(true);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var message;
                button.find('.glyphicon').show();
                button.find('img.loading').remove();
                button.closest('tr').removeClass('deleting');
                try {
                    var response = JSON.parse(jqXHR.responseText);
                    message = response.message;
                } catch(error) {
                    message = 'There was an error deleting that picture.';
                }
                alert(message);
            }
        });
    },

    getThumbnailFilename: function (fullsizeFilename) {
        var filenameParts = fullsizeFilename.split('.');
        var extension = filenameParts.pop();
        return filenameParts.join('.') + '.thumb.' + extension;
    },

    checkLimitReached: function (animate) {
        var picCount = $('#pictures tbody tr').not('.deleted').length;
        var alert = $('#limit-reached');
        var input = $('#picture-upload-container');
        var slideDuration = animate ? 300 : 0;
        if (picCount < this.limit) {
            if (alert.is(':visible')) {
                alert.slideUp(slideDuration);
            }
            if (! input.is(':visible')) {
                input.slideDown(slideDuration);
            }
        } else {
            if (! alert.is(':visible')) {
                alert.slideDown(slideDuration);
            }
            if (input.is(':visible')) {
                input.slideUp(slideDuration);
            }
        }
    }
};

var memberProfile = {
    init: function () {
        $('a.popup-img').magnificPopup(defaultMagnificConfig);
    }
};

var membershipsList = {
    init: function () {
        $('#auto-renew').click(function (event) {
            event.preventDefault();
            var button = $(this);
            $.ajax({
                url: $(this).attr('href'),
                beforeSend: function () {
                    var loading = $(' <img src="/img/loading_small.gif" alt="Loading..." />');
                    button.append(loading);
                },
                success: function (data, textStatus, jqXHR) {
                    membershipsList.showRenewalResults(data, false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    membershipsList.showRenewalResults(jqXHR.responseText, true);
                },
                complete: function () {
                    button.find('img').remove();
                }
            });
        });
    },

    showRenewalResults: function (msg, error) {
        var container = $('#auto-renew-results');
        if (error) {
            container.removeClass('alert-success');
            container.addClass('alert-error');
        } else {
            container.removeClass('alert-error');
            container.addClass('alert-success');
        }
        container.removeClass('alert-error');
        if (container.is(':visible')) {
            container.slideUp(300, function () {
                container.html(msg);
                container.slideDown();
            });
        } else {
            container.html(msg);
            container.slideDown(300);
        }
    }
};
