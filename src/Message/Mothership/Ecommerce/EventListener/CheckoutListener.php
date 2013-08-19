<?php

namespace Message\Mothership\Ecommerce\EventListener;

use Message\User\Event as UserEvents;
use Symfony\Component\HttpKernel\HttpKernel;
use Message\Cog\Event\SubscriberInterface;
use Message\Cog\Event\EventListener as BaseListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Message\Cog\HTTP\RedirectResponse;

/**
 * Checkout event listener for deciding where where to route the user
 *
 * @author Danny Hannah <danny@message.co.uk>
 */
class CheckoutListener extends BaseListener implements SubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(KernelEvents::REQUEST => array(
			array('routeUser')
		));
	}

	public function routeUser(GetResponseEvent $event)
	{
		$route = $event->getRequest()->attributes->get('_route');
		$collections = $event->getRequest()->attributes->get('_route_collections');
		$numItems = count($this->get('basket')->getOrder()->items);
		$url = $this->get('routing.generator');
		$user = $this->get('user.current');

		$allowedRoutes = array(
			'ms.ecom.checkout',
			'ms.ecom.checkout.payment.successful',
			'ms.ecom.checkout.payment.unsuccessful',
			'ms.ecom.checkout.payment.response',
		);
		// Throw users to the first stage of checkout if they don't have any items
		// in their basket unless they are at the first stage OR on the confirmation
		// page as their basket will get emptied
		if ($collections[0] == 'ms.ecom.checkout' && $numItems == 0 && !in_array($route, $allowedRoutes)) {
			return $event->setResponse(new RedirectResponse($url->generate('ms.ecom.checkout')));
		}

		$allowedRoutes = array(
			'ms.ecom.checkout',
			'ms.ecom.checkout.details',
			'ms.ecom.checkout.account',
			'ms.ecom.checkout.payment.response'
		);

		if (!$user instanceof \Message\User\User && $collections[0] == 'ms.ecom.checkout' && !in_array($route, $allowedRoutes)) {
			return $event->setResponse(new RedirectResponse($url->generate('ms.ecom.checkout')));
		}

		// Handles where to throw the user after the first stage of checkout
		if ($route == 'ms.ecom.checkout.details') {

			// Is the user logged in?
			if ($user instanceof \Message\User\AnonymousUser) {
				// Sign up / Register
				$route = $url->generate('ms.ecom.checkout.account');

				return $event->setResponse(new RedirectResponse($route));
			}

			$addresses = $this->get('commerce.user.address.loader')->getByUser($user);

			if ($user instanceof \Message\User\User && $addresses) {
				// Route to the delivery stage
				$route = $url->generate('ms.ecom.checkout.delivery');
			}

			if ($user instanceof \Message\User\User && !$addresses) {
				// Route to the update addresses page
				$route = $url->generate('ms.ecom.checkout.details.addresses');
			}

		 	return $event->setResponse(new RedirectResponse($route));
		}

		if ($route == 'ms.ecom.checkout.delivery') {
			$order = $this->get('basket')->getOrder();
			if (count($order->addresses) < 2) {
				$route = $url->generate('ms.ecom.checkout.details.addresses');

			 	return $event->setResponse(new RedirectResponse($route));
			}
		}
	}
}