let myContact = {
    init: function () {
        $('#edit-contact').find('input[type=email]').change(function () {
            let container = $('#confirm-password-container');
            if (container.length !== 0) {
                container.slideDown();
                container.find('input').prop('required', true);
            }
        });
    }
};
