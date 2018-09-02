let myContact = {
    init: function () {
        $('#edit-contact').find('input[type=email]').change(function () {
            let container = $('#confirm-password-container');
            container.slideDown();
            container.find('input').prop('required', true);
        });
    }
};
