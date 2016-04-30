var paymentProcessor = {
    buttonSelector: null,
    confirmationMessage: null,
    costDollars: 0,
    description: null,
    email: null,
    key: null,
    postData: {},
    postUrl: null,
    redirectUrl: null,
    beforePurchase: null,
    
    getStripeHandler: function () {
        var configuration = this.getStripeConfig();
        return StripeCheckout.configure(configuration);
    },
    
    getStripeConfig: function () {
        return {
            email: this.email,
            key: this.key,
            image: 'http://members.munciearts.org/img/macc-logo-200px.jpg',
            panelLabel: 'Continue (Total: {{amount}})',
            token: function (token) {
                paymentProcessor.getToken(token);
            }
        };
    },
    
    getToken: function (token) {
        var modal = this.getConfirmationModal();
        $('body').append(modal);
        modal.on('shown.bs.modal', function() {
            var dialog = modal.find('.modal-dialog');
            var initModalHeight = dialog.outerHeight();
            dialog.css('margin-top', (window.screenY / 2) + initModalHeight);
        });
        modal.modal();
        var status = modal.find('.status');
        modal.find('.btn-primary').click(function (event) {
            event.preventDefault();
            var data = paymentProcessor.postData;
            data.stripeToken = token.id;
            data.email = token.email;
            $.ajax({
                type: 'POST',
                url: paymentProcessor.postUrl,
                data: data,
                dataType: 'json',
                beforeSend: function (jqXHR, settings) {
                    modal.find('.btn').addClass('disabled');
                    status.html('Please wait... <img src="/img/loading_small.gif" />');
                    status.slideDown();
                },
                success: function (data, textStatus, jqXHR) {
                    data = data.retval;
                    if (data.success) {
                        $('#confirmation-modal-label').html('Done!');
                        modal.find('.modal-body p:first-child').slideUp();
                        
                        // Redirect if redirect_url is provided, refresh otherwise
                        if (paymentProcessor.redirectUrl !== null) {
                            window.location.href = paymentProcessor.redirectUrl;
                        } else {
                            location.reload(true);
                        }
                    } else {
                        modal.find('.btn-primary').remove();
                        modal.find('.btn').removeClass('disabled');
                        modal.find('.modal-title').html('Error');
                        modal.find('.modal-body').html(data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    modal.find('.btn').removeClass('disabled');
                    status.slideUp(500, function() {
                        var msg = '<span class="text-danger">There was an error submitting your payment';
                        if (errorThrown) {
                            msg += ' ('+errorThrown+')';
                        }
                        msg += '. Please try again.</span>';
                        status.html(msg);
                        status.slideDown();
                    });
                }
            });
        });
    },
    
    setupPurchaseButton: function () {
        var handler = this.getStripeHandler();
        
        $(this.buttonSelector).on('click', function(event) {
            event.preventDefault();
            if (paymentProcessor.beforePurchase !== null) {
                if (! paymentProcessor.beforePurchase()) {
                    return;
                }
            }
            handler.open({
                name: 'Muncie Arts and Culture Council',
                description: paymentProcessor.description,
                amount: paymentProcessor.costDollars * 100
            });
        });

        $(window).on('popstate', function() {
            handler.close();
        });
    },
    
    getConfirmationModal: function () {
        return $(
            '<div class="modal fade" id="confirmation_modal" tabindex="-1" role="dialog" aria-labelledby="confirmation-modal-label" aria-hidden="true">'+
                '<div class="modal-dialog">'+
                    '<div class="modal-content">'+
                        '<div class="modal-header">'+
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                            '<h4 class="modal-title" id="confirmation-modal-label">Almost done!</h4>'+
                        '</div>'+
                        '<div class="modal-body">'+
                            '<p>'+
                                this.confirmationMessage+
                                ' A receipt will be emailed to you once your payment is complete.'+
                            '</p>'+
                            '<p class="status" style="display: none;"></p>'+
                        '</div>'+
                        '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>'+
                            '<button type="button" class="btn btn-primary">Complete Purchase</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'
        );
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
            userId: params.userId
        };
        paymentProcessor.beforePurchase = function () {
            var renewal = $('input[name=renewal]:checked').val();
            paymentProcessor.postData.autoRenew = (renewal == 'automatic') ? 1 : 0;
            
            paymentProcessor.confirmationMessage = 'Confirm payment of $'+params.costDollars+' to purchase one year of membership?';
            if (renewal == 'automatic') {
                paymentProcessor.confirmationMessage += ' You will be automatically charged to renew your membership every year and can cancel automatic renewal at any time.';
            }
            return true;
        };
        paymentProcessor.setupPurchaseButton();
    }
};

var donation = {
    init: function (params) {
        paymentProcessor.buttonSelector = '#donation-button';
        paymentProcessor.key = params.key;
        paymentProcessor.postData = {userId: params.userId};
        paymentProcessor.postUrl = params.postUrl;
        paymentProcessor.redirectUrl = params.redirectUrl;
        paymentProcessor.beforePurchase = function () {
            var amount = $('#donation-amount').val();
            amount = parseInt(amount);
            if (isNaN(amount) || amount < 1) {
                alert('Sorry, your donation amount must be at least one dollar.');
                return false;
            }
            paymentProcessor.postData.amount = amount;
            paymentProcessor.costDollars = amount;
            paymentProcessor.confirmationMessage = 'Confirm donation of $'+amount+'?';
            paymentProcessor.description = 'Donation of $'+amount+' to MACC';
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

var pictureUploader = {
    init: function (params) {
        $('#picture-upload').uploadifive({
            'uploadScript': '/pictures/add.json',
            'checkScript': false,
            'onCheck': false,
            'fileSizeLimit': params.filesizeLimit,
            'buttonText': 'Click to select an image to upload',
            'width': 300,
            'fileType': '.jpg,.jpeg,.png,.gif',
            'formData': {
                'timestamp': params.timestamp,
                'token': params.token,
                'user_id': params.user_id
            },
            'onUploadComplete': function(file, data) {
                data = JSON.parse(data);
                var filename = data.picture;
                var fullPath = '/img/members/'+params.user_id+'/'+filename;
                var thumbnailFilename = pictureUploader.getThumbnailFilename(filename);
                var thumbPath = '/img/members/'+params.user_id+'/'+thumbnailFilename;
                var img = $('<img src="'+thumbPath+'" />');
                var link = $('<a href="'+fullPath+'" title="Click for full-size"></a>').append(img);
                link.magnificPopup({type: 'image'});
                var pictureCell = $('<td></td>').append(link);
                var removeButton = $('<button class="btn btn-link remove" title="Remove" data-picture-id="'+data.pictureId+'"></button>');
                removeButton.html('<span class="glyphicon glyphicon-remove text-danger"></span>');
                removeButton.click(function (event) {
                    event.preventDefault();
                    profileEditor.deletePicture($(this));
                });
                var actionsCell = $('<td></td>').append(removeButton);
                var row = $('<tr></tr>').append(actionsCell).append(pictureCell);
                $('#pictures tbody').append(row);
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
    },
    
    getThumbnailFilename: function (fullsizeFilename) {
        var filenameParts = fullsizeFilename.split('.');
        var extension = filenameParts.pop();
        return filenameParts.join('.') + '.thumb.' + extension;
    }
};

var profileEditor = {
    init: function () {
        $('#pictures a').magnificPopup({type: 'image'});
        $('#pictures button.remove').click(function (event) {
            event.preventDefault();
            profileEditor.deletePicture($(this));
        });
    },
    
    deletePicture: function (button) {
        if (! confirm('Are you sure you want to remove this picture?')) {
            return;
        }
        var pictureId = button.data('picture-id');
        $.ajax({
            type: 'DELETE',
            url: '/pictures/delete/'+pictureId,
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
    }
};
