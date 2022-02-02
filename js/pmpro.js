jQuery(document).ready(function($) {

    $("#pmpro_payment_method input[name=gateway]").change(function (e) {
        const pmproStripeNotice = $("#pmpro-stripe-notice");

        if (e.target.value === 'stripe') {
            pmpropbc.hide_billing_address_fields = 0;
            pmproStripeNotice.show();
        } else if (e.target.value === 'check') {
            pmpropbc.hide_billing_address_fields = 1;
            pmproStripeNotice.hide();
        }
    });

});