jQuery(document).ready(function($) {

    $("#pmpro_form").submit(function (e) {
        if ($("#pmpro_payment_method input[name=gateway]").val() === 'check') {
            alert('check');
        } else {
            alert('stripe');
        }
    });

});