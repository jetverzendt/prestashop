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

class NoviJetverzendtPrintModuleFrontController extends ModuleFrontController
{
	public $handle;
	public $exact_api;
	public $division;

	public function initContent()
	{
		parent::initContent();

		$api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
		//$shared_secret = Configuration::get('JETVERZENDT_CLIENT_SECRET');
		$label_type = Configuration::get('JETVERZENDT_CLIENT_LABEL');

		if (Tools::getIsset('id_order') && Tools::getValue('id_order') > 0)
		{
			$shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt` WHERE id_order="'.Tools::getValue('id_order').'"');
			if (count($shippings) > 0)
			{
				$selected_shipping = $shippings[0]['shipping_type'];
				$id = $shippings[0]['shipment_id'];
				if ($selected_shipping == 1) $label_type = 'PDF';
				$label_data = Tools::jsonEncode(
					[
						'shipments' => [
							$id
						],
						'type' => $label_type
					]
				);
				$testmode = Configuration::get('JETVERZENDT_STATUS');
				if ($testmode == 0) $apiurl = 'http://testportal.jetverzendt.nl';
				else $apiurl = 'https://portal.jetverzendt.nl';
				$ch = curl_init($apiurl.'/api/v2/label?api_token='.$api_key);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$shared_secret);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $label_data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Accept: application/json',
						'Content-Length: '.Tools::strlen($label_data)
					)
				);
				$result = Tools::jsonDecode(curl_exec($ch));
				chmod(_PS_ROOT_DIR_.'/modules/novijetverzendt/labels.'.Tools::strtolower($label_type), 0777);
				file_put_contents(_PS_ROOT_DIR_.'/modules/novijetverzendt/labels.'.Tools::strtolower($label_type), base64_decode($result->labels));
				header('Content-disposition: attachment; filename=label.'.Tools::strtolower($label_type));
				header('Content-type: application/'.Tools::strtolower($label_type));
				readfile(_PS_ROOT_DIR_.'/modules/novijetverzendt/labels.'.Tools::strtolower($label_type));
				unlink(_PS_ROOT_DIR_.'/modules/novijetverzendt/labels.'.Tools::strtolower($label_type), base64_decode($result->labels));
			}
		}
	}
}