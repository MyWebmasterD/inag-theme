<!-- Template: Confirmation -->

<div class="<?= pmpro_get_element_class('pmpro_confirmation_wrap') ?>">
    <div class="printable-invoice">
        <div class="header">
            <img src="<?= site_url('/wp-content/uploads/logo-inag-lg.png') ?>" class="float-right" width="126" height="125" alt="INAG logo">
            <p>INAG<br>Via Tirso 26<br>00198<br>Roma<br>CF 97593430586</p>
        </div>

        <?php
            global $current_user, $pmpro_invoice;

            if (!empty($pmpro_invoice) && !empty($pmpro_invoice->id)) {
                $pmpro_invoice->getUser();
                $pmpro_invoice->getMembershipLevel();
                $formatted_total = number_format((float)$pmpro_invoice->total, 2, ',', ' ');
                $payment_year = date('Y', $pmpro_invoice->timestamp);

                if (empty($pmpro_invoice->gateway)) {
                    $payment_method = __('Test', 'generatepresschild');
                } elseif ($pmpro_invoice->gateway == 'check') {
                    $payment_method = __('Bonifico', 'generatepresschild');
                } elseif ($pmpro_invoice->gateway == 'stripe') {
                    $payment_method = __('Carta di credito', 'generatepresschild');
                } else {
                    $payment_method = __('Non specificato', 'generatepresschild');
                }
        ?>
                <h4>
                    <?php printf(__('Ricevuta #%s del %s', 'generatepresschild' ), $pmpro_invoice->code, date_i18n(get_option('date_format'), $pmpro_invoice->getTimestamp())) ?>
                </h4>

                <p>
                    <strong><?php _e('Destinatario', 'generatepresschild') ?></strong><br>
                    <?= $pmpro_invoice->billing->name ? $pmpro_invoice->billing->name . "<br>" : '' ?>
                    <?= $pmpro_invoice->billing->street ? $pmpro_invoice->billing->street . "<br>" : '' ?>
                    <?= $pmpro_invoice->billing->state ? $pmpro_invoice->billing->state . "<br>" : '' ?>
                    <?= ($pmpro_invoice->billing->zip ? $pmpro_invoice->billing->zip . " " : '') . $pmpro_invoice->billing->country ?>
                </p>

                <p>
                    <?php printf(__('Si attesta di aver ricevuto la somma di euro %s con', 'generatepresschild' ), $formatted_total) ?><br>
                    <strong><?php _e('Metodo di Pagamento', 'generatepresschild') ?></strong>: <?= $payment_method ?>
                </p>

                <p>
                    <?php _e('Per la seguente causale:', 'generatepresschild') ?><br>
                    <?php printf(__('- Euro %s quota associativa anno %s/%s', 'generatepresschild' ), $formatted_total, $payment_year, (int)$payment_year + 1) ?>
                </p>

                <p><?php _e('Le ricevute relative all’incasso delle quote associative non sono assoggettate all’imposta di bollo.', 'generatepresschild') ?></p>

                <img src="<?= site_url('/wp-content/uploads/digital-signature.jpeg') ?>" class="float-right" width="200" height="128" alt="Firma del presidente">

                <div class="clear-float"></div>
            <?php } else { ?>
                <p>Nessuna ricevuta trovata. Controlla la tua e-mail o contatta INAG.</p>
        <?php } ?>
    </div>

    <hr>

    <p class="<?= pmpro_get_element_class('pmpro_actions_nav') ?>">
        <?php if (!empty($current_user->membership_level)): ?>
            <a href="<?= pmpro_url('account') ?>"><?php _e('&larr; Vai al tuo account', 'generatepresschild') ?></a>
        <?php endif; ?>

        <?php if (!empty($pmpro_invoice) && !empty($pmpro_invoice->id)): ?>
            <a class="<?= pmpro_get_element_class('pmpro_a-print') ?>" href="javascript:window.print()"><?php _e('Stampa', 'generatepresschild') ?></a>
        <?php endif; ?>

        <?php if (!empty($current_user->membership_level)): ?>
            <a href="/area-riservata"><?php _e('Accedi al area riservata &rarr;', 'generatepresschild') ?></a>
        <?php else: ?>
            <p><?php _e('Se il tuo account non viene attivato entro pochi minuti, contatta INAG.', 'generatepresschild') ?></p>
        <?php endif; ?>
    </p>
</div>