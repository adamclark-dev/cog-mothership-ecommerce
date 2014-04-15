<?php

namespace Message\Mothership\Ecommerce\Controller\Gateway;

use Symfony\Component\HttpFoundation\JsonResponse;
use Message\Mothership\Commerce\Payable\PayableInterface;
use Message\Mothership\Commerce\Order\Entity\Payment\MethodInterface;

/**
 * Interface for complete controllers.
 *
 * @author Laurence Roberts <laurence@message.co.uk>
 */
interface CompleteControllerInterface
{
	/**
	 * Complete a payable.
	 *
	 * @param  PayableInterface $payable
	 * @param  MethodInterface  $method
	 * @return JsonResponse
	 */
	public function complete(PayableInterface $payable, array $stages, MethodInterface $method);
}