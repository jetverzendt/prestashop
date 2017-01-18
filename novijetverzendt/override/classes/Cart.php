<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Cart extends CartCore
{
	public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
	{
		if (isset(Context::getContext()->cookie->id_country))
			$default_country = new Country(Context::getContext()->cookie->id_country);
		if (is_null($delivery_option))
			$delivery_option = $this->getDeliveryOption($default_country, false, false);

		$total_shipping = 0;
		$cart_shippings = array();
		$cart_shippings = Db::getInstance()->executeS('
			SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.(int)$this->id.'"');
		$jet_carrier = Configuration::get('JETVERZENDT_CARRIER_ID');
		$id_carrier = (int)$this->id_carrier;

		if ($id_carrier == $jet_carrier && count($cart_shippings) > 0)
		{
			$carrier = null;
			$carrier = new Carrier($id_carrier, (int)$this->id_lang);

			$vat = $carrier->getTaxesRate(new Address((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
			if ($use_tax)
				$total_shipping += $cart_shippings[0]['extra_shipping'];
			else $total_shipping += number_format((100 * $cart_shippings[0]['extra_shipping'] / (100 + $vat)), 6, '', '');
		}

		$delivery_option_list = $this->getDeliveryOptionList($default_country);
		foreach ($delivery_option as $id_address => $key)
		{
			if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key]))
				continue;
			if ($use_tax)
				$total_shipping += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
			else
				$total_shipping += $delivery_option_list[$id_address][$key]['total_price_without_tax'];
		}

		return $total_shipping;
	}
}
