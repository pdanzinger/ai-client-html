<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$order = $this->extOrderItem;

/// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s)
$msg = $this->translate( 'client', 'Your order %1$s from %2$s has been dispatched.' );

$key = 'stat:' . $order->getDeliveryStatus();
$status = $this->translate( 'client/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

$string = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );


?>
<?php $this->block()->start( 'email/delivery/html/intro' ); ?>
<p class="email-common-intro content-block">
	<?= $enc->html( nl2br( $string ), $enc::TRUST ); ?>
</p>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/delivery/html/intro' ); ?>
