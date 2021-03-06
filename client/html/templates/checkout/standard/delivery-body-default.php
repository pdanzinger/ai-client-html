<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$services = $this->get( 'deliveryServices', [] );
$servicePrices = $this->get( 'deliveryServicePrices', [] );
$serviceAttributes = $this->get( 'deliveryServiceAttributes', [] );

try
{
	$orderService = $this->standardBasket->getService( 'delivery' );
	$orderServiceId = $orderService->getServiceId();
}
catch( Exception $e )
{
	$orderService = null;
	$orderServiceId = null;

	if( ( $service = reset( $services ) ) !== false ) {
		$orderServiceId = $service->getId();
	}
}

$serviceOption = $this->param( 'c_deliveryoption', $orderServiceId );

$deliveryCss = [];
foreach( $this->get( 'deliveryError', [] ) as $name => $msg ) {
	$deliveryCss[$name][] = 'error';
}

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'checkout/standard/delivery' ); ?>
<section class="checkout-standard-delivery">

	<h1><?= $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h1>
	<p class="note"><?= $enc->html( $this->translate( 'client', 'Please choose your delivery method' ), $enc::TRUST ); ?></p>


	<?php foreach( $services as $id => $service ) : ?>

		<div id="c_delivery-<?= $enc->attr( $id ); ?>" class="item item-service">
			<label class="description" for="c_deliveryoption-<?= $enc->attr( $id ); ?>">

				<input class="option" type="radio"
					id="c_deliveryoption-<?= $enc->attr( $id ); ?>"
					name="<?= $enc->attr( $this->formparam( array( 'c_deliveryoption' ) ) ); ?>"
					value="<?= $enc->attr( $id ); ?>"
					<?= ( $id == $serviceOption ? 'checked="checked"' : '' ); ?>
				/>


				<?php if( isset( $servicePrices[$id] ) ) : ?>
					<?php $currency = $this->translate( 'client/currency', $servicePrices[$id]->getCurrencyId() ); ?>

					<?php if( $servicePrices[$id]->getValue() > 0 ) : ?>
						<span class="price-value">
							<?= $enc->html( sprintf( /// Service fee value (%1$s) and shipping cost value (%2$s) with currency (%3$s)
								$this->translate( 'client', '%1$s%3$s + %2$s%3$s' ),
								$this->number( $servicePrices[$id]->getValue() ),
								$this->number( $servicePrices[$id]->getCosts() ),
								$currency )
							); ?>
						</span>
					<?php else : ?>
						<span class="price-value">
							<?= $enc->html( sprintf(
								$priceFormat,
								$this->number( $servicePrices[$id]->getCosts() ),
								$currency )
							); ?>
						</span>
					<?php endif; ?>

				<?php endif; ?>


				<div class="icons">
					<?php foreach( $service->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
						<?= $this->partial(
							$this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ),
							array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'icon' ) )
						); ?>
					<?php endforeach; ?>
				</div>

				<h2><?= $enc->html( $service->getName() ); ?></h2>

				<?php foreach( $service->getRefItems( 'text', null, 'default' ) as $textItem ) : ?>
					<?php if( ( $type = $textItem->getType() ) !== 'name' ) : ?>
						<p class="<?= $enc->attr( $type ); ?>"><?= $enc->html( $textItem->getContent(), $enc::TRUST ); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>

			</label><!--


			--><?php if( isset( $serviceAttributes[$id] ) && !empty( $serviceAttributes[$id] ) ) : ?><!--

				--><ul class="form-list">

					<?php foreach( $serviceAttributes[$id] as $key => $attribute ) : ?>
						<?php $value = ( isset( $orderService ) && ( $value = $orderService->getAttribute( $key ) ) !== null ? $value : $attribute->getDefault() ); ?>
						<?php $css = ( isset( $deliveryCss[$key] ) ? ' ' . join( ' ', $deliveryCss[$key] ) : '' ) . ( $attribute->isRequired() ? ' mandatory' : '' ); ?>

						<li class="form-item <?= $enc->attr( $key ) . $css; ?>">
							<label for="delivery-<?= $enc->attr( $key ); ?>"><?= $enc->html( $this->translate( 'client/code', $key ) ); ?></label><!--

							--><?php switch( $attribute->getType() ) : case 'select': ?><!--

									--><select id="delivery-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>">

										<?php foreach( (array) $attribute->getDefault() as $option ) : ?>
											<option value="<?= $enc->attr( $option ); ?>">
												<?php $code = $key . ':' . $option; echo $enc->html( $this->translate( 'client/code', $code ) ); ?>
											</option>
										<?php endforeach; ?>

									</select><!--

								--><?php break; case 'boolean': ?><!--
									--><input type="checkbox" id="delivery-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; case 'integer': case 'number': ?><!--
									--><input type="number" id="delivery-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; case 'date': case 'datetime': case 'time': ?><!--
									--><input type="<?= $attribute->getType(); ?>" id="delivery-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; default: ?><!--
									--><input type="text" id="delivery-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>"
									/><!--
							--><?php endswitch; ?>

						</li>

					<?php endforeach; ?>

				</ul>

			<?php endif; ?>

		</div>

	<?php endforeach; ?>


	<div class="button-group">
		<a class="standardbutton btn-back" href="<?= $enc->attr( $this->get( 'standardUrlBack' ) ); ?>">
			<?= $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ); ?>
		</a>
		<button class="standardbutton btn-action">
			<?= $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ); ?>
		</button>
	</div>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/delivery' ); ?>
