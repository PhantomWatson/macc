var paymentProcessor = {    
    // params must contain key, post_data, and post_url
    getStripeHandler: function (params) {
        var configuration = {
            key: params.key,
            //image: '', (logo)
            panelLabel: 'Continue (Total: {{amount}})',
            token: function(token) {
                var modal = paymentProcessor.getConfirmationModal(params.confirmation_message);
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
                    var data = params.post_data;
                    data.token = token.id;
                    $.ajax({
                        type: 'POST',
                        url: params.post_url,
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
                                if (params.hasOwnProperty('redirect_url')) {
                                    window.location.href = params.redirect_url;
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
            }
        };
        
        if (params.hasOwnProperty('email') && params.email != '') {
            configuration.email = params.email;
        }
        
        return StripeCheckout.configure(configuration);
    },
    
    setupPurchaseButton: function (params) {
        var handler = this.getStripeHandler(params);
        
        $(params.button_selector).on('click', function(event) {
            event.preventDefault();
            handler.open({
                name: 'Muncie Arts and Culture Council',
                description: params.description,
                amount: params.cost_dollars * 100
            });
        });

        $(window).on('popstate', function() {
            handler.close();
        });
    },
    
    getConfirmationModal: function (confirmation_message) {
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
                                confirmation_message+
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
