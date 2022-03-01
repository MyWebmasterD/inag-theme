jQuery(document).ready(function($) {

    const pmproStripeNotice = $("#pmpro-stripe-notice");

    $(".pmpro_checkout-field-baddress1 label").text("Indirizzo");

    $("#pmpro_payment_method input[name=gateway]").change(function (e) {
        if (e.target.value === 'stripe') {
            pmproStripeNotice.show();
        } else if (e.target.value === 'check') {
            pmproStripeNotice.hide();
        }
    });

    $("#pmpro_form").submit(function () {
        if ($("#pmpro_payment_method input[name='gateway']:checked").val() === 'check') {
            if (
                ($("#baddress1").val().trim() === "") || ($("#bcity").val().trim() === "") || ($("#bstate").val().trim() === "") ||
                ($("#bzipcode").val().trim() === "") || ($("#bcountry").val().trim() === "") || ($("#bphone").val().trim() === "")
            ) {
                pmpro_require_billing = true;
                setTimeout(function () {$("#pmpro_message").text("Devi compilare tutti i campi richiesti.")}, 200);
            } else {
                pmpro_require_billing = false;
            }
        }
    });

});