jQuery(document).ready(function($) {

    $("#pmpro_payment_method input[name=gateway]").bind('click change keyup', function (e) {
        let pmproStripeNotice = $("#pmpro-stripe-notice");

        e.target.value === 'stripe' ? pmproStripeNotice.show() : pmproStripeNotice.hide();
    });

});