<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Conferma di associazione a INAG</title>

        <style>
            table, th, td {
                border: none;
            }

            table {
                width: 700px;
            }

            td {
                vertical-align: top;
            }
        </style>
    </head>

    <body>
        <table>
            <tr>
                <td style="width:75%; padding-right: 20px;">
                    <p>INAG<br>Via Guido D'Arezzo 2<br>00198<br>Roma<br>CF 97593430586</p>
                </td>

                <td>
                    <img src="<?= site_url('/wp-content/uploads/logo-inag-lg.png') ?>" width="126" height="125" alt="INAG logo">
                </td>
            </tr>
        </table>

        <h4>
            <?php printf(__('Ricevuta #%s del %s', 'generatepresschild' ), $code, $today) ?>
        </h4>

        <p>
            <strong><?php _e('Destinatario', 'generatepresschild') ?></strong><br>
            <?= $name ? $name . "<br>" : '' ?>
            <?= $street ? $street . "<br>" : '' ?>
            <?= $state ? $state . "<br>" : '' ?>
            <?= ($zip ? $zip . " " : '') . $country ?><br>
            CF <?= get_user_meta(get_current_user_id(), 'fiscal_code', true) ?>
        </p>

        <p>
            <?php printf(__('Si attesta di aver ricevuto la somma di euro %s con', 'generatepresschild' ), $pmpro_invoice->total) ?><br>
            <strong><?php _e('Metodo di Pagamento', 'generatepresschild') ?></strong>: <?= $payment_method ?>
        </p>

        <p>
            <?php _e('Per la seguente causale:', 'generatepresschild') ?><br>
            <?php printf(__('- Euro %s quota associativa anno %s/%s', 'generatepresschild' ), $pmpro_invoice->total, $payment_year, (int)$payment_year + 1) ?>
        </p>

        <table>
            <tr>
                <td style="width:70%; padding-right: 20px;">
                    <p><?php _e('Le ricevute relative all’incasso delle quote associative non sono assoggettate all’imposta di bollo.', 'generatepresschild') ?></p>
                </td>

                <td>
                    <img src="<?= site_url('/wp-content/uploads/digital-signature.jpeg') ?>" alt='Firma del presidente' width='200' height='128'>
                </td>
            </tr>
        </table>
    </body>
</html>