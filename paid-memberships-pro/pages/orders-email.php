<!-- Template for Email Invoices -->

<?php
    $payment_year = date('Y', $order->getTimestamp());

    if (empty($order->gateway)) {
        $payment_method = __('Test', 'generatepresschild');
    } elseif ($order->gateway == 'check') {
        $payment_method = __('Bonifico', 'generatepresschild');
    } elseif ($order->gateway == 'stripe') {
        $payment_method = __('Carta di credito', 'generatepresschild');
    } else {
        $payment_method = __('Non specificato', 'generatepresschild');
    }
?>

<table style="width: 700px; border: none;">
    <tr>
        <td style="width:75%; border: none; vertical-align: top; padding-right: 20px;">
            <p>INAG<br>Via Guido D'Arezzo 2<br>00198<br>Roma<br>CF 97593430586</p>
        </td>

        <td style="border: none; vertical-align: top;">
            <img src="<?= site_url('/wp-content/uploads/logo-inag-lg.png') ?>" width="126" height="125" alt="INAG logo">
        </td>
    </tr>
</table>

<h4>
    <?php printf(__('Ricevuta #%s del %s', 'generatepresschild' ), $order->code, date_i18n(get_option('date_format'), $order->getTimestamp())) ?>
</h4>

<p>
    <strong><?php _e('Destinatario', 'generatepresschild') ?></strong><br>
    <?= $order->billing->name ? $order->billing->name . "<br>" : '' ?>
    <?= $order->billing->street ? $order->billing->street . "<br>" : '' ?>
    <?= $order->billing->street ? $order->billing->city . "<br>" : '' ?>
    <?= $order->billing->state ? $order->billing->state . "<br>" : '' ?>
    <?= ($order->billing->zip ? $order->billing->zip . " " : '') . $order->billing->country ?><br>
    CF <?= get_user_meta($order->getUser()->ID, 'fiscal_code', true) ?>
</p>

<p>
    <?php printf(__('Si attesta di aver ricevuto la somma di euro %s con', 'generatepresschild' ), $pmpro_invoice->total) ?><br>
    <strong><?php _e('Metodo di Pagamento', 'generatepresschild') ?></strong>: <?= $payment_method ?>
</p>

<p>
    <?php _e('Per la seguente causale:', 'generatepresschild') ?><br>
    <?php printf(__('- Euro %s quota associativa anno %s', 'generatepresschild' ), $pmpro_invoice->total, $payment_year) ?>
</p>

<table style="width: 700px; border: none;">
    <tr>
        <td style="width:70%; border: none; vertical-align: top; padding-right: 20px;">
            <p><?php _e('Le ricevute relative all’incasso delle quote associative non sono assoggettate all’imposta di bollo.', 'generatepresschild') ?></p>
        </td>

        <td style="border: none; vertical-align: top;">
            <img src="<?= site_url('/wp-content/uploads/digital-signature.jpeg') ?>" alt='Firma del presidente' width='200' height='128'>
        </td>
    </tr>
</table>
