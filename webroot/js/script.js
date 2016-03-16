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
            //image: '', (logo)
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
                paymentProcessor.beforePurchase();
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
            paymentProcessor.costDollars = amount;
            // validate amount
            paymentProcessor.confirmationMessage = 'Confirm donation of $'+amount+'?';
            paymentProcessor.description = 'Donation of $'+amount+' to MACC';
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

            // Strip HTML out of input
            var pattern = /(<([^>]+)>)/ig;
            profileCommonmark = profileCommonmark.replace(pattern, '');

            var parsed = reader.parse(profileCommonmark);
            var htmlResult = writer.render(parsed);
            $('#'+output).html(htmlResult);
        });
    }
};
