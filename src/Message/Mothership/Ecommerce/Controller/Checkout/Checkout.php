<?php

namespace Message\Mothership\Ecommerce\Controller\Checkout;

use Message\Cog\Controller\Controller;

/**
 * Class Checkout
 * @package Message\Mothership\Ecommerce\Controller\Fulfillment
 *
 * Controller for processing orders in Fulfillment
 */
class Checkout extends Controller
{
	public function index()
	{
		return $this->render('Message:Mothership:Ecommerce::Checkout:checkout', array(
			'basket'   => $this->getGroupedBasket(),
			'order'    => $this->get('basket')->getOrder(),
			'form'     => $this->checkoutForm(),
			'discount' => $this->discountForm(),
		));
	}

	public function process()
	{
		$basket = $this->get('basket');
		$form = $this->checkoutForm();
		if ($form->isValid() && $data = $form->getFilteredData()) {
			foreach($data['items'] as $unitID => $quantity) {
				$product = $this->get('product.loader')->getByUnitID($unitID);
				$unit = $product->units->get($unitID);
				$basket->updateQuantity($unit, $quantity);
			}

			$this->addFlash('success','Basket updated');
		}

		return $this->redirectToReferer();
	}

	public function removeUnit($unitID)
	{

		$basket = $this->get('basket');
		$product = $this->get('product.loader')->getByUnitID($unitID);
		$unit = $product->units->get($unitID);
		$basket->updateQuantity($unit, 0);

		$this->addFlash('success','Item removed');

		return $this->redirectToReferer();
	}

	public function checkoutForm()
	{
		$form = $this->get('form');
		$form->setName('confirm_basket')
			->setAction($this->generateUrl('ms.ecom.checkout.action'))
			->setMethod('post');

		$basketDisplay = $this->getGroupedBasket();

		$defaults = array();
		foreach ($basketDisplay as $item) {
			$defaults[$item['item']->unitID] = $item['quantity'];
		}

		$itemsForm = $this->get('form')
			->setName('items')
			->setDefaultValues($defaults)
			->addOptions(array(
				'auto_initialize' => false,
			)
		);

		foreach ($basketDisplay as $item) {
			$itemsForm->add((string) $item['item']->unitID, 'number', $item['item']->options)
			->val()->digit();
		}

		$form->add($itemsForm->getForm(), 'form');

		return $form;
	}

	public function discountProcess()
	{
		$form = $this->discountForm();
		if ($form->isValid() && $data = $form->getFilteredData()) {
			de($data);
		}
	}

	public function discountForm()
	{
		$form = $this->get('form');
		$form->setName('discount_form')
			->setAction($this->generateUrl('ms.ecom.checkout.discount'))
			->setMethod('post');
		$form->add('discount', 'text', 'I have a discount token / camapign code')
			->val()->optional();
		return $form;
	}

	public function getGroupedBasket()
	{
		$basketDisplay = array();
		foreach ($this->get('basket')->getOrder()->items as $item) {

			if (!isset($basketDisplay[$item->unitID]['quantity'])) {
				$basketDisplay[$item->unitID]['quantity'] = 0;
			}

			if (!isset($basketDisplay[$item->unitID]['subTotal'])) {
				$basketDisplay[$item->unitID]['subTotal'] = 0;
			}

			$basketDisplay[$item->unitID]['item'] = $item;
			$basketDisplay[$item->unitID]['quantity'] += 1;
			$basketDisplay[$item->unitID]['subTotal'] += $item->gross;
		}

		return $basketDisplay;
	}
}