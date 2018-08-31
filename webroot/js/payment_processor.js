let paymentProcessor = {
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
    autoRenew: false,

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
