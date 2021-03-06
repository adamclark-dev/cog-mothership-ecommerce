<?php

namespace Message\Mothership\Ecommerce\Gateway;

use Message\Mothership\Commerce\Address\Address;
use Omnipay\Common\CreditCard as OmnipayCreditCard;

/**
 * Provides helper methods for setting the addresses on a credit card for
 * use with payment gateways.
 *
 * @author Laurence Roberts <laurence@message.co.uk>
 */
class CreditCard extends OmnipayCreditCard
{
	public function setDeliveryAddress(Address $address)
	{
		$this->setShippingFirstName($address->forename);
		$this->setShippingLastName($address->surname);

		$this->setShippingAddress1($address->lines[1]);
		$this->setShippingAddress2($address->lines[2]);

		$this->setShippingCity($address->town);
		$this->setShippingPostcode($address->postcode);
		$this->setShippingState($address->stateID);
		$this->setShippingCountry($address->countryID);

		return $this;
	}

	public function setBillingAddress(Address $address)
	{
		$this->setFirstName($address->forename);
		$this->setLastName($address->surname);

		$this->setAddress1($address->lines[1]);
		$this->setAddress2($address->lines[2]);

		$this->setCity($address->town);
		$this->setPostcode($address->postcode);
		$this->setState($address->stateID);
		$this->setCountry($address->countryID);

		return $this;
	}
}