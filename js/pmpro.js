jQuery(document).ready(function($) {
    const pmproCheckoutFormTitle = $("#compila-il-form-per-diventare-un-associato-di-inag");
    const pmproUserFields = $("#pmpro_user_fields");
    const pmproStripeNotice = $("#pmpro-stripe-notice");
    const pmproTosFields = $("#pmpro_tos_fields");
    const pmproFiscalCodeField = $("#fiscal_code_div");
    const pmproSubmit = $(".pmpro-checkout .pmpro_submit");

    // TODO: Disable PMPro checkout form submission when payment method is "check", and enable it when payment method is "stripe"

    $("#pmpro_payment_method input[name=gateway]").change(function (e) {
        if (e.target.value === 'stripe') {
            pmpropbc.hide_billing_address_fields = 0;
            pmproCheckoutFormTitle.show();
            pmproUserFields.show();
            pmproStripeNotice.show();
            pmproTosFields.show();
            pmproFiscalCodeField.show();
            pmproSubmit.show();
        } else if (e.target.value === 'check') {
            pmpropbc.hide_billing_address_fields = 1;
            pmproCheckoutFormTitle.hide();
            pmproUserFields.hide();
            pmproStripeNotice.hide();
            pmproTosFields.hide();
            pmproFiscalCodeField.hide();
            pmproSubmit.hide();
        }
    });

});