$(document).ready(function () {
    $("#submit-quick-contact").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-quick-contact").validationEngine('validate'));
        if (valid) {
            $('#form-quick-contact').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-newsletter--").validationEngine('validate'));
        if (valid) {
            $('#form-newsletter--').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });


});