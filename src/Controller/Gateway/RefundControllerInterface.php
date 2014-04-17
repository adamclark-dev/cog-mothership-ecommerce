<?php

namespace Message\Mothership\Ecommerce\Controller\Gateway;

use Message\Mothership\Commerce\Payable\PayableInterface;

/**
 * Interface for gateway refund controllers.
 *
 * @author Laurence Roberts <laurence@message.co.uk>
 */
interface RefundControllerInterface
{
	/**
	 * Refund a payment.
	 *
	 * @param  PayableInterface $refund
	 * @param  string           $reference
	 * @param  array            $stages  Routes for redirecting the customer
	 * @param  array            $options
	 * @return \Message\Cog\HTTP\Response
	 */
	public function refund(PayableInterface $refund, $reference, array $stages, array $options = null);
}