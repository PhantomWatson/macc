let donation = {
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
