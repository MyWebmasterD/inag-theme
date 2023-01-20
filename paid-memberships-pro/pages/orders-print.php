<!-- Template for Print Invoices -->

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<style>
        .header, .main {
			display: block;
		}

        .header {
            margin-bottom: 3em;
        }

		.right {
			display: inline-block;
			float: right;
		}

		@media screen {
			body {
				max-width: 50%;
				margin: 0 auto;
			}
		}
	</style>
</head>

<body>
	<header class="header">
        <img src="<?= site_url('/wp-content/uploads/logo-inag-lg.png') ?>" class="right" width="126" height="125" alt="INAG logo">
        <p>INAG<br>Via Tirso 26<br>00198<br>Roma<br>CF 97593430586</p>
	</header>

	<main class="main">
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
                <?php printf(__('Si attesta di aver ricevuto la somma di euro %s con', 'generatepresschild' ), $order->total) ?><br>
                <strong><?php _e('Metodo di Pagamento', 'generatepresschild') ?></strong>: <?= $payment_method ?>
            </p>

            <p>
                <?php _e('Per la seguente causale:', 'generatepresschild') ?><br>
                <?php printf(__('- Euro %s quota associativa anno %s/%s', 'generatepresschild' ), $order->total, $payment_year, (int)$payment_year + 1) ?>
            </p>

            <p><?php _e('Le ricevute relative all’incasso delle quote associative non sono assoggettate all’imposta di bollo.', 'generatepresschild') ?></p>

            <img src="<?= site_url('/wp-content/uploads/digital-signature.jpeg') ?>" class="right" width="200" height="128" alt="Firma del presidente">
	</main>
</body>
</html>
