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

if (!defined('_PS_VERSION_'))
	exit;

class Novijetverzendt extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'novijetverzendt';
		$this->tab = 'shipping_logistics';
		$this->version = '1.1.5';
		$this->author = 'NoviSites.nl';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Novi jetverzendt');
		$this->description = $this->l('Novi jetverzendt connector');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		Configuration::updateValue('JETVERZENDT_CLIENT_ID', false);
		Configuration::updateValue('JETVERZENDT_GOOGLE_KEY', false);
		Configuration::updateValue('JETVERZENDT_STATUS', 0);
		Configuration::updateValue('JETVERZENDT_CLIENT_SECRET', false);
		Configuration::updateValue('JETVERZENDT_CLIENT_LABEL', false);
		Configuration::updateValue('JETVERZENDT_SHIPPER', false);
		Configuration::updateValue('JETVERZENDT_SERVICE_DPD', false);
		Configuration::updateValue('JETVERZENDT_SERVICE_DHL', false);
		Configuration::updateValue('JETVERZENDT_ORDER_STATE', 1);
		Configuration::updateValue('JETVERZENDT_PRINT_SIZE', false);
		Configuration::updateValue('JETVERZENDT_DHL_SEND', false);
		Configuration::updateValue('JETVERZENDT_DPD_SEND', false);
		Configuration::updateValue('JETVERZENDT_CARRIER_ID', false);
		Configuration::updateValue('JETVERZENDT_LM_ACTIVE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_1', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_1_TIME', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_1_PRICE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_2', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_2_TIME', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_2_PRICE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_4', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_4_TIME', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_4_PRICE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_5', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_5_TIME', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_5_PRICE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_3_1', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_3_1_PRICE', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_3_2', false);
		Configuration::updateValue('JETVERZENDT_LM_OPT_3_2_PRICE', false);

		include(dirname(__FILE__).'/sql/install.php');

		return parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('backOfficeHeader')
			&& $this->registerHook('displayAdminOrder')
			&& $this->registerHook('displayCarrierList')
			&& $this->registerHook('actionCartSave')
			&& $this->registerHook('actionCarrierUpdate');
	}

	public function uninstall()
	{
		Configuration::deleteByName('JETVERZENDT_CLIENT_ID');
		Configuration::deleteByName('JETVERZENDT_GOOGLE_KEY');
		Configuration::deleteByName('JETVERZENDT_STATUS');
		Configuration::deleteByName('JETVERZENDT_CLIENT_SECRET');
		Configuration::deleteByName('JETVERZENDT_CLIENT_LABEL');
		Configuration::deleteByName('JETVERZENDT_SHIPPER');
		Configuration::deleteByName('JETVERZENDT_SERVICE_DPD');
		Configuration::deleteByName('JETVERZENDT_SERVICE_DHL');
		Configuration::deleteByName('JETVERZENDT_ORDER_STATE');
		Configuration::deleteByName('JETVERZENDT_PRINT_SIZE');
		Configuration::deleteByName('JETVERZENDT_DHL_SEND');
		Configuration::deleteByName('JETVERZENDT_DPD_SEND');
		Configuration::deleteByName('JETVERZENDT_CARRIER_ID');
		Configuration::deleteByName('JETVERZENDT_LM_ACTIVE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_1');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_1_TIME');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_1_PRICE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_2');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_2_TIME');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_2_PRICE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_4');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_4_TIME');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_4_PRICE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_5');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_5_TIME');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_5_PRICE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_3_1');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_3_1_PRICE');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_3_2');
		Configuration::deleteByName('JETVERZENDT_LM_OPT_3_2_PRICE');

		include(dirname(__FILE__).'/sql/uninstall.php');

		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		$output = '';
		/**
		 * If values have been submitted in the form, process.
		 */
		if (((bool)Tools::isSubmit('submitNovijetverzendtModule')) == true)
			$this->postProcess();

		$this->context->smarty->assign('module_dir', $this->_path);

		// save shippment
		if (Tools::getIsset('submitShippment') && Tools::getIsset('id_order') && Tools::getIsset('shipping_option')
		&& Tools::getValue('shipping_option') > 0)
		{
			$errors = $this->addShipment(Tools::getValue('id_order'), $_POST);
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true).'&id_order='.Tools::getValue('id_order').
			'&vieworder'.((count($errors) > 0)?'&errors='.$errors[0]:''));
		}
		return $output.$this->renderForm();
	}

	public function clean($string)
	{
		$string = str_replace('-', '', str_replace(' ', '', $string)); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^0-9\-]/', '', $string); // Removes special chars.
		//$string = preg_replace("/[^a-zA-Z]/", "", $string);
		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}

	public function addShipment($id_order, $post)
	{
		$errors_arr = array();
		$api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
		//$shared_secret = Configuration::get('JETVERZENDT_CLIENT_SECRET');
		$label_type = Configuration::get('JETVERZENDT_CLIENT_LABEL');
		$label_size = Configuration::get('JETVERZENDT_PRINT_SIZE');
		$send_track_and_trace_email = Configuration::get('JETVERZENDT_DHL_SEND');
		$order_state = Configuration::get('JETVERZENDT_ORDER_STATE');
		$shipper = Configuration::get('JETVERZENDT_SHIPPER');
		$service_dpd = Configuration::get('JETVERZENDT_SERVICE_DPD');
		$service_dhl = Configuration::get('JETVERZENDT_SERVICE_DHL');
		$dpd_send = Configuration::get('JETVERZENDT_DPD_SEND');

		$order = new Order($id_order);
		$customer = new Customer($order->id_customer);
		$email = $customer->email;
		$street_line_1 = ' ';
		$number_line_1 = '1';
		$zip_code = ' ';
		$city = ' ';
		$id_country = '';
		$country = 'nl';
		$errors = '';
		$phone = '';
		$housenumber = '';
		$housenumber_ext = '';
		$address = new Address($order->id_address_delivery);
		if (Validate::isLoadedObject($address))
		{
			if ($address->company != '')
				$company_name = $address->company;
			elseif ($address->company == '' && $customer->company != '')
				$company_name = $customer->company;
			else
				$company_name = $address->firstname.' '.$address->lastname;
			if ($address->address1 != '') $street_line_1 = $address->address1;
			if ($address->address2 != '') $number_line_1 = Tools::substr($address->address2, 0, 8);
			$housenumber = $number_line_1;
			if ($number_line_1 != $this->clean($number_line_1))
			{
				$e = 0;
				$len = Tools::strlen($number_line_1);
				for ($e = 0; $e < $len; $e++)
					if (!is_numeric($number_line_1[$e]))
						break;
				if ($e != Tools::strlen($number_line_1))
				{
					$housenumber = Tools::substr($number_line_1, 0, $e);
					$housenumber_ext = Tools::substr($number_line_1, $e, Tools::strlen($number_line_1));
				}
				else
				{
					$housenumber = $number_line_1;
					$housenumber_ext = '';
				}
			}
			if ($address->postcode != '') $zip_code = str_replace(' ', '', $address->postcode);
			if ($address->city != '') $city = $address->city;
			if ($address->phone_mobile != '') $phone = $address->phone_mobile;
			if ($phone == '' && $address->phone != '') $phone = $address->phone;
			if ($address->id_country != '') $id_country = $address->id_country;
			if ($id_country > 0)
			{
				$country_info = new Country($id_country);
				$country = Tools::strtolower($country_info->iso_code);
			}
			if ($number_line_1 == '') $number_line_1 = '0';
		}
		else
			$errors_arr[] = $this->l('No address for this shipment');

		if (count($errors_arr) == 0)
		{
			$shipping_service = '';
			if (isset($post['shipping_option']))
				if ($post['shipping_option'] == 1) $shipping_service = $post['shipping_option_type_1'];
				else if ($post['shipping_option'] == 2) $shipping_service = $post['shipping_option_type_2'];
				else $shipping_service = 'DEFAULT';
			if (!isset($post['shipping_option']) || $post['shipping_option'] == '')
			{
				$post['shipping_option'] = (($shipper == 'DPD')?1:2);
				if ($post['shipping_option'] == 1) $post['shipping_option_type_1'] = $service_dpd;
				else $post['shipping_option_type_2'] = $service_dhl;
				if ($post['shipping_option'] == 1) $shipping_service = $service_dpd;
				else $shipping_service = $service_dhl;
			}
			// DPD
			if (isset($post['option_1_quantity'])) $option_1_quantity = $post['option_1_quantity'];
			else $option_1_quantity = '';
			if ($option_1_quantity < 1) $option_1_quantity = 1;
			if (isset($post['option_1_reference'])) $option_1_reference = $post['option_1_reference'];
			else $option_1_reference = '';
			if ($option_1_reference == '') $option_1_reference = $this->l('Order').' '.$id_order;
			if (isset($post['option_1_mail'])) $option_1_mail = $post['option_1_mail'];
			else $option_1_mail = $dpd_send;
			if ($option_1_mail == '') $option_1_mail = $dpd_send;
			if (isset($post['option_1_saturday_delivery'])) $option_1_saturday_delivery = $post['option_1_saturday_delivery'];
			else $option_1_saturday_delivery = '';
			if (isset($post['option_1_pickup_delivery'])) $option_1_pickup_delivery = $post['option_1_pickup_delivery'];
			else $option_1_pickup_delivery = '';
			if (isset($post['option_1_pickup_date'])) $option_1_pickup_date = $post['option_1_pickup_date'];
			else $option_1_pickup_date = date('Y-m-d', time() + 86400);
			if ($option_1_pickup_delivery == 1) $option_1_pickup_date = '';
			else $option_1_pickup_date = date('Y-m-d', strtotime($option_1_pickup_date));
			if ($option_1_pickup_date == '1970-01-01' || $option_1_pickup_date == '0000-00-00')
				$option_1_pickup_date = date('Y-m-d', time() + 86400);
			if (isset($post['option_1_amount'])) $option_1_amount = $post['option_1_amount'];
			else $option_1_amount = 0;
			if ($option_1_amount < 0) $option_1_amount = 0;
			if ($shipping_service != 'CLR') $option_1_amount = '';
			// DHL
			if (isset($post['option_2_quantity'])) $option_2_quantity = $post['option_2_quantity'];
			else $option_2_quantity = '';
			if ($option_2_quantity < 1) $option_2_quantity = 1;
			if (isset($post['option_2_reference'])) $option_2_reference = $post['option_2_reference'];
			else $option_2_reference = '';
			if ($option_2_reference == '') $option_2_reference = $this->l('Order').' '.$id_order;
			if (isset($post['option_2_weight'])) $option_2_weight = (int)$post['option_2_weight'];
			else $option_2_weight = 1;
			if (isset($post['option_2_pickup_delivery'])) $option_2_pickup_delivery = $post['option_2_pickup_delivery'];
			else $option_2_pickup_delivery = '';
			if (isset($post['option_2_pickup_date']))
			{
				$option_2_pickup_date = $post['option_2_pickup_date'];
				if (date('N', strtotime($option_2_pickup_date)) == 6)
					$option_2_pickup_date = date('Y-m-d', strtotime($option_2_pickup_date.' -1 day'));
				else
					if (date('N', strtotime($option_2_pickup_date)) == 7)
						$option_2_pickup_date = date('Y-m-d', strtotime($option_2_pickup_date.' -2 days'));
			}
			else $option_2_pickup_date = date('Y-m-d', time() + 86400);
			if ($option_2_pickup_date == '1970-01-01')
				$option_2_pickup_date = date('Y-m-d', time() + 86400);
			if ($option_2_pickup_delivery == 1) $option_2_pickup_date = '';
			else $option_2_pickup_date = date('Y-m-d', strtotime($option_2_pickup_date));

			if (isset($post['option_3_pickup_date'])) $option_3_pickup_date = $post['option_3_pickup_date'];
			else $option_3_pickup_date = date('Y-m-d', time() + 86400);
			if ($option_3_pickup_date == '1970-01-01')
				$option_3_pickup_date = date('Y-m-d', time() + 86400);

			if (isset($post['option_1_weight'])) $option_1_weight = (int)$post['option_1_weight'];
			else $option_1_weight = 1;
			if (isset($post['option_3_weight'])) $option_3_weight = (int)$post['option_3_weight'];
			else $option_3_weight = 1;

			if (isset($post['option_2_insured_value'])) $option_2_insured_value = $post['option_2_insured_value'];
			else $option_2_insured_value = '';
			if (isset($post['option_2_saturday_delivery'])) $option_2_saturday_delivery = $post['option_2_saturday_delivery'];
			else $option_2_saturday_delivery = '';
			if (isset($post['option_2_amount'])) $option_2_amount = str_replace(',', '.', $post['option_2_amount']);
			else $option_2_amount = 0;
			if ($option_2_amount < 0) $option_2_amount = 0;
			if (isset($post['option_2_signature'])) $option_2_signature = $post['option_2_signature'];
			else $option_2_signature = '';
			if (isset($post['option_2_no_neighbors'])) $option_2_no_neighbors = $post['option_2_no_neighbors'];
			else $option_2_no_neighbors = '';
			if (isset($post['option_2_evening'])) $option_2_evening = $post['option_2_evening'];
			else $option_2_evening = '';
			if (isset($post['option_2_extra_cover'])) $option_2_extra_cover = $post['option_2_extra_cover'];
			else $option_2_extra_cover = '';
			$shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$order->id_cart.'"');

			if ($shipping_service != 'DHL_FOR_YOU')
			{
				$option_2_signature = 0;
				$option_2_no_neighbors = 0;
				$option_2_evening = 0;
				$option_2_extra_cover = 0;
			}
			if (isset($post['parcelshop_id'])) $parcelshop_id = $post['parcelshop_id'];

			if (isset($post['option_3_weight'])) $option_3_weight = (int)$post['option_3_weight'];
			else $option_3_weight = 1;

			if ($post['shipping_option'] == 2) $product = 'DHL';
			else if ($post['shipping_option'] == 1) $product = 'DPD';
			else if ($post['shipping_option'] == 4) $product = 'NextDayPremium';
			else $product = 'Fadello';
			if ($post['shipping_option'] == 1) $label_type = 'PDF';
			if ($post['shipping_option'] == 2)
			{
				$order_details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'order_detail WHERE id_order="'.
							$id_order.'"');
				$weight = 0;
				foreach ($order_details as $order_detail_value)
					$weight += $order_detail_value['product_weight'];
				if (!$weight > 0) $weight = 1;
				if ($option_2_weight <= 0)  $option_2_weight = $weight;
				$label = array('type' => $label_type);
				if ($label_type == 'PDF')
					$label['size'] = $label_size;

				$info = array(
							'reference' => $option_2_reference,
							'company_name' => $company_name,
							'contact_person' => $address->firstname.' '.$address->lastname,
							'street_line_1' => $street_line_1,
							'number_line_1' => $housenumber,
							'zip_code' => $zip_code,
							'city' => $city,
							'amount' => $option_2_quantity,
							'country' => $country,
							'phone' => $phone,
							'email' => $email,
							'weight' => $option_2_weight,
							'cod' => ''.number_format((double)$option_2_amount, 2, ',', '').'',
							'product' => $product,
							'product_type' => '2',
							'service' => $shipping_service,
							'label' => $label,
							'input_source' => 'prestashop'
						);
				if ($housenumber_ext != '')
					$info['number_line_1_addition'] = $housenumber_ext;

				if ($option_2_saturday_delivery == 1 && $shipping_service == 'EUROPLUS')
					$info['saturday_delivery'] = true;
				if ($option_2_pickup_date != '' && $option_2_pickup_delivery != 1 && $option_2_pickup_date != '1970-01-01')
					$info['pickup_date'] = $option_2_pickup_date;
				if ($option_2_insured_value > 0 && ($shipping_service == 'EUROPLUS' || $shipping_service == 'EXPRESSER'
				|| $shipping_service == 'EUROPLUS_INTERNATIONAL'))
					$info['insurance'] = number_format($option_2_insured_value, 2, ',', '').'';
				if ($option_2_signature == 1)
					$info['signature_required'] = true;
				if ($option_2_no_neighbors == 1)
					$info['not_by_neighbours'] = true;
				if ($option_2_evening == 1)
					$info['evening'] = true;
				if ($option_2_extra_cover == 1)
					$info['extra_insurance'] = true;
				if ($send_track_and_trace_email == 1)
					$info['send_track_and_trace_email'] = true;
				if ($parcelshop_id != '')
				{
					$info['parcel_shop_id'] = $parcelshop_id;
					$info['predict'] = $option_1_mail;
				}
				if ($parcelshop_id != '')
					$info['service'] = 'PARCEL_SHOP';

				$order_data = Tools::jsonEncode($info);
			}
			else
			if ($post['shipping_option'] == 1)
			{
				$label = array('type' => $label_type);
				if ($label_type == 'PDF')
					$label['size'] = $label_size;
				$info = array(
							'reference' => $option_1_reference,
							'company_name' => $company_name,
							'contact_person' => $company_name,
							'street_line_1' => $street_line_1,
							'number_line_1' => $housenumber,
							'zip_code' => $zip_code,
							'city' => $city,
							'amount' => $option_1_quantity,
							'country' => $country,
							'phone' => $phone,
							'email' => $email,
							'weight' => $option_1_weight,
							'product' => $product,
							'product_type' => '2',
							'service' => $shipping_service,
							'label' => $label,
							'input_source' => 'prestashop'
						);
				if ($housenumber_ext != '')
					$info['number_line_1_addition'] = $housenumber_ext;

				if ($parcelshop_id != '')
				{
					$info['parcel_shop_id'] = $parcelshop_id;
					$info['predict'] = $option_1_mail;
				}
				if ($post['shipping_option'] == 1 && $parcelshop_id != '')
					$info['service'] = 'PARCELSHOP';

				if ($option_1_amount != '')
					$info['cod'] = number_format($option_1_amount, 2, ',', '');
				else $option_1_amount = 0;
				if ($shipping_service == 'CLR')
					$info['cod'] = number_format($option_1_amount, 2, ',', '');
				if ($option_1_saturday_delivery == 1)
					$info['saturday_delivery'] = true;
				if ($option_1_pickup_date != '' && $option_1_pickup_delivery != 1
				&& $option_1_pickup_date != '1970-01-01' && $option_1_pickup_date != '0000-00-00')
					$info['pickup_date'] = $option_1_pickup_date;
				if ($option_1_saturday_delivery != 1)
					$info['predict'] = $option_1_mail;
				//else $info['predict'] = 0;
				$order_data = Tools::jsonEncode($info);
			}
			else
			if ($post['shipping_option'] == 4)
			{
				$label = array('type' => $label_type);
				if ($label_type == 'PDF')
					$label['size'] = $label_size;
				if ($option_3_pickup_date != '' && $option_3_pickup_date != 1 && $option_3_pickup_date != '1970-01-01')
					$info['pickup_date'] = $option_3_pickup_date;

				$info = array(
							'reference' => $option_1_reference,
							'company_name' => $company_name,
							'street_line_1' => $street_line_1,
							'number_line_1' => $housenumber,
							'zip_code' => $zip_code,
							'city' => $city,
							'contact_person' => $company_name,
							'amount' => $option_1_quantity,
							'country' => $country,
							'phone' => $phone,
							'email' => $email,
							'pickup_date' => date('Y-m-d'),
							'service' => 'DEFAULT',
							'weight' => $option_3_weight,
							'product' => 'NextDayPremium',
							'label' => $label,
							'input_source' => 'prestashop'
						);
				if ($housenumber_ext != '')
					$info['number_line_1_addition'] = $housenumber_ext;

				$order_data = Tools::jsonEncode($info);
			}
			else
			{
				$label = array('type' => $label_type);
				if ($label_type == 'PDF')
					$label['size'] = $label_size;
				if ($option_3_pickup_date != '' && $option_3_pickup_date != 1 && $option_3_pickup_date != '1970-01-01')
					$info['pickup_date'] = $option_3_pickup_date;

				$info = array(
							'reference' => $option_1_reference,
							'company_name' => $company_name,
							'street_line_1' => $street_line_1,
							'number_line_1' => $housenumber,
							'zip_code' => $zip_code,
							'city' => $city,
							'contact_person' => $company_name,
							'amount' => $option_1_quantity,
							'country' => $country,
							'phone' => $phone,
							'email' => $email,
							'pickup_date' => date('Y-m-d'),
							'service' => 'DEFAULT',
							'weight' => $option_3_weight,
							'product' => 'Fadello',
							'label' => $label,
							'input_source' => 'prestashop'
						);
				if ($housenumber_ext != '')
					$info['number_line_1_addition'] = $housenumber_ext;

				$order_data = Tools::jsonEncode($info);
			}

			$testmode = Configuration::get('JETVERZENDT_STATUS');
			if ($testmode == 0) $apiurl = 'http://testportal.jetverzendt.nl';
			else $apiurl = 'https://portal.jetverzendt.nl';
			$ch = curl_init($apiurl.'/api/v2/shipment?api_token='.$api_key);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$shared_secret);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $order_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: '.Tools::strlen($order_data))
			);
			$r = curl_exec($ch);
			curl_close($ch);
			$result = (array)Tools::jsonDecode($r);

			//parceprint_r($info);
			//print_r($info);
			//print_r($result);
			$shipment_id = '';
			if (isset($result['shipment_id']))
				$shipment_id = $result['shipment_id'];
			$label = '';
			if (isset($result['label']))
				$label = $result['label'];
			$track_and_trace_code = '';
			$track_and_trace_url = '';
			if (isset($result['track_and_trace']))
			{
				$array = Tools::jsonDecode(Tools::jsonEncode($result['track_and_trace']), true);
				foreach ($array as $k => $item)
				{
					$track_and_trace_code = $k;
					$track_and_trace_url = $item;
				}
			}
			if ($shipment_id == '') $errors_arr[] = 'error';
			//$track_and_trace_code = $result['track_and_trace'];

			$shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt` WHERE id_order="'.$id_order.'"');
			if ($shipment_id != '')
			{
				if ($post['shipping_option'] == 1)
				{
					if (count($shippings) > 0)
						Db::getInstance()->execute('
								UPDATE `'._DB_PREFIX_.'novijetverzendt` SET 
								parcelshop_id="'.$post['parcelshop_id'].'", 
								shipping_type="'.$post['shipping_option'].'",
								date="'.date('Y-m-d H:i:s').'",
								shipment_id="'.$shipment_id.'",	
								label="'.$label.'",	
								track_and_trace_code="'.$track_and_trace_code.'",
								track_and_trace_url="'.$track_and_trace_url.'", 
								shipping_service="'.$shipping_service.'",
								option_1_quantity="'.$option_1_quantity.'", 
								option_1_reference="'.$option_1_reference.'",
								option_1_mail="'.$option_1_mail.'", 
								option_1_saturday_delivery="'.$option_1_saturday_delivery.'",
								option_1_pickup_delivery="'.$option_1_pickup_delivery.'", 
								option_1_pickup_date="'.(($option_1_pickup_date != '')?date('Y-m-d', strtotime($option_1_pickup_date)):'0000-00-00').'",
								option_1_amount="'.$option_1_amount.'"
								WHERE id_order="'.$id_order.'"');
					else
						Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'novijetverzendt` 
								(shipping_type, id_order, date, shipment_id, label, track_and_trace_code, track_and_trace_url, shipping_service, option_1_quantity,
								option_1_reference, option_1_mail, option_1_saturday_delivery, option_1_pickup_delivery, option_1_pickup_date,option_1_amount ) 
								VALUES 
								("'.$post['shipping_option'].'", 
								"'.$id_order.'", 
								"'.date('Y-m-d H:i:s').'", 
								"'.$shipment_id.'",
								"'.$label.'", 
								"'.$track_and_trace_code.'", 
								"'.$track_and_trace_url.'", 
								"'.$shipping_service.'", 
								"'.$option_1_quantity.'", 
								"'.$option_1_reference.'", 
								"'.$option_1_mail.'", 
								"'.$option_1_saturday_delivery.'", 
								"'.$option_1_pickup_delivery.'", 
								"'.(($option_1_pickup_date != '')?date('Y-m-d', strtotime($option_1_pickup_date)):'0000-00-00').'", 
								"'.$option_1_amount.'"
								
								)');

				}
				else
				{
					if (count($shippings) > 0)
						Db::getInstance()->execute('
								UPDATE `'._DB_PREFIX_.'novijetverzendt` SET 
								shipping_type="'.$post['shipping_option'].'", 
								date="'.date('Y-m-d H:i:s').'",
								shipment_id="'.$shipment_id.'",	
								label="'.$label.'",	
								track_and_trace_code="'.$track_and_trace_code.'",
								track_and_trace_url="'.$track_and_trace_url.'", 
								shipping_service="'.$shipping_service.'", 
								option_2_quantity="'.$option_2_quantity.'", 
								option_2_reference="'.$option_2_reference.'", 
								option_3_weight="'.$option_3_weight.'",
								option_1_weight="'.$option_1_weight.'",
								option_2_weight="'.$option_2_weight.'", 
								option_2_pickup_delivery="'.$option_2_pickup_delivery.'", 
								option_2_pickup_date="'.$option_2_pickup_date.'", 
								option_3_pickup_date="'.$option_3_pickup_date.'", 
								option_2_insured_value="'.$option_2_insured_value.'", 
								option_2_saturday_delivery="'.$option_2_saturday_delivery.'", 
								option_2_amount="'.$option_2_amount.'", 
								option_2_signature="'.$option_2_signature.'", 
								option_2_no_neighbors="'.$option_2_no_neighbors.'", 
								option_2_evening="'.$option_2_evening.'", 
								option_2_extra_cover="'.$option_2_extra_cover.'"
								WHERE id_order="'.$id_order.'"');
					else
						Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'novijetverzendt` 
								(shipping_type, id_order, date, shipment_id, label, track_and_trace_code, track_and_trace_url, shipping_service,
								option_2_quantity, option_2_reference, option_2_weight, option_2_pickup_delivery, option_2_pickup_date,
								option_2_insured_value, option_2_saturday_delivery, option_2_amount, option_2_signature, option_2_no_neighbors,
								option_2_evening, option_2_extra_cover, option_3_weight, option_1_weight) 
								VALUES 
								("'.$post['shipping_option'].'", 
								"'.$id_order.'", 
								"'.date('Y-m-d H:i:s').'", 
								"'.$shipment_id.'",
								"'.$label.'", 
								"'.$track_and_trace_code.'", 
								"'.$track_and_trace_url.'", 
								"'.$shipping_service.'", 
								"'.$option_2_quantity.'", 
								"'.$option_2_reference.'", 
								"'.$option_2_weight.'", 
								"'.$option_2_pickup_delivery.'", 
								"'.$option_2_pickup_date.'", 
								"'.$option_2_insured_value.'", 
								"'.$option_2_saturday_delivery.'", 
								"'.$option_2_amount.'", 
								"'.$option_2_signature.'", 
								"'.$option_2_no_neighbors.'", 
								"'.$option_2_evening.'", 
								"'.$option_2_extra_cover.'", 
								"'.$option_3_weight.'", 
								"'.$option_1_weight.'"
								)');
				}

				// update state of order
				$order_state = new OrderState($order_state);

				if (!Validate::isLoadedObject($order_state))
					$this->errors[] = Tools::displayError('The new order status is invalid.');
				else
				{
					$current_order_state = $order->getCurrentOrderState();
					if ($current_order_state->id != $order_state->id)
					{
						// Create new OrderHistory
						$history = new OrderHistory();
						$history->id_order = $order->id;
						$history->id_employee = (int)$this->context->employee->id;

						$use_existings_payment = false;
						if (!$order->hasInvoice())
							$use_existings_payment = true;
						$history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

						$carrier = new Carrier($order->id_carrier, $order->id_lang);
						$template_vars = array();
						if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
							$template_vars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

						// Save all changes
						if ($history->addWithemail(true, $template_vars))
						{
							// synchronizes quantities if needed..
							if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
								foreach ($order->getProducts() as $product)
									if (StockAvailable::dependsOnStock($product['product_id']))
										StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
						}
						$this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
					}
					else
						$this->errors[] = Tools::displayError('The order has already been assigned this status.');
				}
			}
			else
			{
				//print_r($result);
				if (is_array($result) && isset($result) && is_array($result))
				{
					if (is_array($result) && isset($result['errors']) && is_array($result['errors'])) {
						foreach ($result['errors'] as $item) {
							if (is_object($item)) {
								$errors[] = $item->message;
							}
							else {
								$errors[] = $item;
							}
						}
					}
					else {
						foreach ($result as $item) {
							if (is_array($item)) {
								foreach ($item as $item2) {
									$errors[] = $item2;
									break;
								}
							}
							else { 
								$errors[] = $item;
							}
						}
					}
				}
			}
		}
		//print_r($errors);
	return $errors;
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitNovijetverzendtModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm())).$helper->generateForm(array($this->getConfigFormLastMile()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		$select_values = array();
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'PDF', 'name' => $this->l('PDF')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'ZPL', 'name' => $this->l('ZPL')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'BAT', 'name' => $this->l('BAT')));
		$shipper_values = array();
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'DHL', 'name' => $this->l('DHL')));
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'DPD', 'name' => $this->l('DPD')));
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'FADELLO', 'name' => $this->l('FADELLO')));
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'NextDayPremium', 'name' => $this->l('Next Day Premium')));
		$service_dpd_values = array();
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'CL', 'name' => $this->l('Classic (Saturday delivery)')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E10', 'name' => $this->l('DPD 10:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E12', 'name' => $this->l('DPD 12:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E18', 'name' => $this->l('DPD 18:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'IE2', 'name' => $this->l('DPD Express')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'CLR', 'name' => $this->l('COD (Cash On Delivery)')));
		$service_dhl_values = array();
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' =>
		'EUROPLUS', 'name' => $this->l('EuroPlus (Saturday delivery, insurance)')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' =>
		'DHL_FOR_YOU', 'name' => $this->l('DHL For You (Not by neighbours, signature required, evening delivery, extra insurance)')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' =>
		'EUROPACK', 'name' => $this->l('Europack')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'COD', 'name' => $this->l('COD value, notation: 10,99')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EXPRESSER', 'name' => $this->l('Expresser')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EUROPLUS_INTERNATIONAL', 'name' => $this->l('Europlus international')));
		$order_states = OrderState::getOrderStates($this->context->language->id);
		$order_state = array();
		foreach ($order_states as $item)
			array_push($order_state, array('JETVERZENDT_ORDER_STATE' => $item['id_order_state'], 'name' => $item['name']));
		$print_size = array();
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'default', 'name' => $this->l('Standard')));
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'A4', 'name' => $this->l('Combine 3 labels to A4')));
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'A6', 'name' => $this->l('Combine 4 labels to A4')));

		$dhl_send = array();
		array_push($dhl_send, array('JETVERZENDT_DHL_SEND' => '0', 'name' => $this->l('No')));
		array_push($dhl_send, array('JETVERZENDT_DHL_SEND' => '1', 'name' => $this->l('Yes')));

		$dps_send = array();
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '0', 'name' => $this->l('No notification')));
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '1', 'name' => $this->l('Receive information by e-mail')));
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '2', 'name' => $this->l('Receiver information by SMS')));

		$carriers_arr = array();
		$carriers = Carrier::getCarriers($this->context->language->id, true);
		foreach ($carriers as $carrier)
			array_push($carriers_arr, array('JETVERZENDT_CARRIER_ID' => $carrier['id_carrier'], 'name' => $carrier['name']));

		$lm_active = array();
		array_push($lm_active, array('JETVERZENDT_LM_ACTIVE' => '1', 'name' => $this->l('Yes')));
		array_push($lm_active, array('JETVERZENDT_LM_ACTIVE' => '0', 'name' => $this->l('No')));

		$lm_opt_1 = array();
		array_push($lm_opt_1, array('JETVERZENDT_LM_OPT_1' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_1, array('JETVERZENDT_LM_OPT_1' => '0', 'name' => $this->l('No')));

		$lm_opt_2 = array();
		array_push($lm_opt_2, array('JETVERZENDT_LM_OPT_2' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_2, array('JETVERZENDT_LM_OPT_2' => '0', 'name' => $this->l('No')));

		$lm_opt_4 = array();
		array_push($lm_opt_4, array('JETVERZENDT_LM_OPT_4' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_4, array('JETVERZENDT_LM_OPT_4' => '0', 'name' => $this->l('No')));

		$lm_opt_5 = array();
		array_push($lm_opt_5, array('JETVERZENDT_LM_OPT_5' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_5, array('JETVERZENDT_LM_OPT_5' => '0', 'name' => $this->l('No')));

		$lm_opt_3_1 = array();
		array_push($lm_opt_3_1, array('JETVERZENDT_LM_OPT_3_1' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_3_1, array('JETVERZENDT_LM_OPT_3_1' => '0', 'name' => $this->l('No')));

		$lm_opt_3_2 = array();
		array_push($lm_opt_3_2, array('JETVERZENDT_LM_OPT_3_2' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_3_2, array('JETVERZENDT_LM_OPT_3_2' => '0', 'name' => $this->l('No')));

		$form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Settings'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-user"></i>',
						'desc' => $this->l('Enter the authentication token from the Jet Verzendt portal'),
						'name' => 'JETVERZENDT_CLIENT_ID',
						'label' => $this->l('Authentication token'),
					),
					/*array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-lock"></i>',
						'desc' => $this->l('Enter your client secret'),
						'name' => 'JETVERZENDT_CLIENT_SECRET',
						'label' => $this->l('Client secret'),
					),*/
					array(
						'type' => 'switch',
						'label' => $this->l('Live?'),
						'name' => 'JETVERZENDT_STATUS',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'JETVERZENDT_STATUS_ON',
								'value' => 1,
								'label' => $this->l('Yes')),
							array(
								'id' => 'JETVERZENDT_STATUS_OFF',
								'value' => 0,
								'label' => $this->l('No')),
						),
						'desc' => $this->l('Live or test mode?')
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Enter a google key for the used maps'),
						'name' => 'JETVERZENDT_GOOGLE_KEY',
						'label' => $this->l('Google key'),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Label type'),
						'name' => 'JETVERZENDT_CLIENT_LABEL',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $select_values,
							'id' => 'JETVERZENDT_CLIENT_LABEL',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Print size'),
						'name' => 'JETVERZENDT_PRINT_SIZE',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $print_size,
							'id' => 'JETVERZENDT_PRINT_SIZE',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Default shipper'),
						'name' => 'JETVERZENDT_SHIPPER',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $shipper_values,
							'id' => 'JETVERZENDT_SHIPPER',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Default service DHL'),
						'name' => 'JETVERZENDT_SERVICE_DHL',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $service_dhl_values,
							'id' => 'JETVERZENDT_SERVICE_DHL',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Default service DPD'),
						'name' => 'JETVERZENDT_SERVICE_DPD',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $service_dpd_values,
							'id' => 'JETVERZENDT_SERVICE_DPD',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Order state'),
						'name' => 'JETVERZENDT_ORDER_STATE',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $order_state,
							'id' => 'JETVERZENDT_ORDER_STATE',
							'name' => 'name'
						),
						'desc' => $this->l('This is the state an order should get when you create a shipment')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Send automatically'),
						'name' => 'JETVERZENDT_DHL_SEND',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $dhl_send,
							'id' => 'JETVERZENDT_DHL_SEND',
							'name' => 'name'
						),
						'desc' => $this->l('Only works for DHL. Choose "Yes" if you want the track and trace mail should be 
						automatically transmitted to register a mission at Jet Sends. Do you want to be sent by DPD track-mails, 
						choose when sending the order for the Predict option.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('DPD Predict standard'),
						'name' => 'JETVERZENDT_DPD_SEND',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $dps_send,
							'id' => 'JETVERZENDT_DPD_SEND',
							'name' => 'name'
						),
						'desc' => $this->l('DPD Predict standard option')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Carrier'),
						'name' => 'JETVERZENDT_CARRIER_ID',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $carriers_arr,
							'id' => 'JETVERZENDT_CARRIER_ID',
							'name' => 'name'
						),
						'desc' => $this->l('Carriet to which we will attach the shiiping options')
					),

				),

				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'form_settings',
				),
			),
		);
		return $form;
	}

	protected function getConfigFormLastmile()
	{
		$select_values = array();
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'PDF', 'name' => $this->l('PDF')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'ZPL', 'name' => $this->l('ZPL')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'BAT', 'name' => $this->l('BAT')));
		$shipper_values = array();
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'DHL', 'name' => $this->l('DHL')));
		array_push($shipper_values, array('JETVERZENDT_SHIPPER' => 'DPD', 'name' => $this->l('DPD')));
		$service_dpd_values = array();
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'CL', 'name' => $this->l('Classic (Saturday delivery)')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E10', 'name' => $this->l('DPD 10:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E12', 'name' => $this->l('DPD 12:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'E18', 'name' => $this->l('DPD 18:00')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'IE2', 'name' => $this->l('DPD Express')));
		array_push($service_dpd_values, array('JETVERZENDT_SERVICE_DPD' => 'CLR', 'name' => $this->l('COD (Cash On Delivery)')));
		$service_dhl_values = array();
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EUROPLUS', 'name' => $this->l('EuroPlus (Saturday delivery, insurance)')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'DHL_FOR_YOU', 'name' =>
		$this->l('DHL For You (Not by neighbours, signature required, evening delivery, extra insurance)')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EUROPACK', 'name' => $this->l('Europack')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'COD', 'name' => $this->l('COD value, notation: 10,99')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EXPRESSER', 'name' => $this->l('Expresser')));
		array_push($service_dhl_values, array('JETVERZENDT_SERVICE_DHL' => 'EUROPLUS_INTERNATIONAL', 'name' => $this->l('Europlus international')));
		$order_states = OrderState::getOrderStates($this->context->language->id);
		$order_state = array();
		foreach ($order_states as $item)
			array_push($order_state, array('JETVERZENDT_ORDER_STATE' => $item['id_order_state'], 'name' => $item['name']));
		$print_size = array();
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'default', 'name' => $this->l('Standard')));
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'A4', 'name' => $this->l('Combine 3 labels to A4')));
		array_push($print_size, array('JETVERZENDT_PRINT_SIZE' => 'A6', 'name' => $this->l('Combine 4 labels to A4')));

		$dhl_send = array();
		array_push($dhl_send, array('JETVERZENDT_DHL_SEND' => '0', 'name' => $this->l('No')));
		array_push($dhl_send, array('JETVERZENDT_DHL_SEND' => '1', 'name' => $this->l('Yes')));

		$dps_send = array();
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '0', 'name' => $this->l('No notification')));
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '1', 'name' => $this->l('Receive information by e-mail')));
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '2', 'name' => $this->l('Receiver information by SMS')));

		$carriers_arr = array();
		$carriers = Carrier::getCarriers($this->context->language->id, true);
		foreach ($carriers as $carrier)
			array_push($carriers_arr, array('JETVERZENDT_CARRIER_ID' => $carrier['id_carrier'], 'name' => $carrier['name']));

		$lm_active = array();
		array_push($lm_active, array('JETVERZENDT_LM_ACTIVE' => '1', 'name' => $this->l('Yes')));
		array_push($lm_active, array('JETVERZENDT_LM_ACTIVE' => '0', 'name' => $this->l('No')));

		$lm_opt_1 = array();
		array_push($lm_opt_1, array('JETVERZENDT_LM_OPT_1' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_1, array('JETVERZENDT_LM_OPT_1' => '0', 'name' => $this->l('No')));

		$lm_opt_2 = array();
		array_push($lm_opt_2, array('JETVERZENDT_LM_OPT_2' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_2, array('JETVERZENDT_LM_OPT_2' => '0', 'name' => $this->l('No')));

		$lm_opt_4 = array();
		array_push($lm_opt_4, array('JETVERZENDT_LM_OPT_4' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_4, array('JETVERZENDT_LM_OPT_4' => '0', 'name' => $this->l('No')));

		$lm_opt_5 = array();
		array_push($lm_opt_5, array('JETVERZENDT_LM_OPT_5' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_5, array('JETVERZENDT_LM_OPT_5' => '0', 'name' => $this->l('No')));

		$lm_opt_3_1 = array();
		array_push($lm_opt_3_1, array('JETVERZENDT_LM_OPT_3_1' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_3_1, array('JETVERZENDT_LM_OPT_3_1' => '0', 'name' => $this->l('No')));

		$lm_opt_3_2 = array();
		array_push($lm_opt_3_2, array('JETVERZENDT_LM_OPT_3_2' => '1', 'name' => $this->l('Yes')));
		array_push($lm_opt_3_2, array('JETVERZENDT_LM_OPT_3_2' => '0', 'name' => $this->l('No')));

		$form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Last mile'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Lastmile actief'),
						'name' => 'JETVERZENDT_LM_ACTIVE',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $lm_active,
							'id' => 'JETVERZENDT_LM_ACTIVE',
							'name' => 'name'
						),
						'desc' => $this->l('Met deze optie worden alle onderstaande opties ook in-/uitgeschakeld')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Tijdvaklevering DHL actief:'),
						'name' => 'JETVERZENDT_LM_OPT_1',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $lm_opt_1,
							'id' => 'JETVERZENDT_LM_OPT_1',
							'name' => 'name'
						)
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Uiterlijk tijdstip dagelijkse DHL tijdvaklevering:'),
						'name' => 'JETVERZENDT_LM_OPT_1_TIME',
						'required' => false,
						'class' => 'timepicker',
						'desc' => $this->l('Wat is dagelijks het uiterlijke tijdstip tot wanneer een tijdvaklevering de volgende nog mogelijk is? 
						Dit kan het tijdstip zijn dat uw bestellingen dagelijks door de vervoerder bij u worden opgehaald, bijvoorbeeld 16:00:00 uur.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs DHL avondlevering:'),
						'name' => 'JETVERZENDT_LM_OPT_1_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 1.50')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Zaterdaglevering DPD actief:'),
						'name' => 'JETVERZENDT_LM_OPT_2',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $lm_opt_2,
							'id' => 'JETVERZENDT_LM_OPT_2',
							'name' => 'name'
						)
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Tijdstip voor DPD zaterdaglevering:'),
						'name' => 'JETVERZENDT_LM_OPT_2_TIME',
						'required' => false,
						'class' => 'timepicker',
						'desc' => $this->l('Wat is iedere vrijdag het uiterlijke tijdstip tot wanneer een DPD zaterdaglevering nog mogelijk is?')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs zaterdaglevering DPD:'),
						'name' => 'JETVERZENDT_LM_OPT_2_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 1.50')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Same Day Delivery actief:'),
						'name' => 'JETVERZENDT_LM_OPT_4',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $lm_opt_4,
							'id' => 'JETVERZENDT_LM_OPT_4',
							'name' => 'name'
						)
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Uiterlijk tijdstip Same Day Delivery:'),
						'name' => 'JETVERZENDT_LM_OPT_4_TIME',
						'required' => false,
						'class' => 'timepicker',
						'desc' => $this->l('Wat is dagelijks het uiterlijke tijdstip tot wanneer een verzending met Same Day Delivery 
						nog mogelijk is? Dit kan het tijdstip zijn dat uw bestellingen dagelijks door de vervoerder bij u worden opgehaald, 
						bijvoorbeeld 13:00:00 uur.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs Same Day Delivery:'),
						'name' => 'JETVERZENDT_LM_OPT_4_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 9.95')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Next Day Premium actief:'),
						'name' => 'JETVERZENDT_LM_OPT_5',
						'required' => false,
						'col' => '4',
						'options' => array(
							'query' => $lm_opt_5,
							'id' => 'JETVERZENDT_LM_OPT_5',
							'name' => 'name'
						)
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Uiterlijk tijdstip Next Day Premium:'),
						'name' => 'JETVERZENDT_LM_OPT_5_TIME',
						'required' => false,
						'class' => 'timepicker',
						'desc' => $this->l('Wat is dagelijks het uiterlijke tijdstip tot wanneer een verzending met 
						Next Day Premium nog mogelijk is? Dit kan het tijdstip zijn dat uw bestellingen dagelijks door de vervoerder bij u 
						worden opgehaald, bijvoorbeeld 13:00:00 uur.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs Next Day Premium:'),
						'name' => 'JETVERZENDT_LM_OPT_5_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 9.95')
					),
					array(
						'type' => 'select',
						'label' => $this->l('DHL Parcelshop actief:'),
						'name' => 'JETVERZENDT_LM_OPT_3_1',
						'required' => false,
						'col' => '3_1',
						'options' => array(
							'query' => $lm_opt_3_1,
							'id' => 'JETVERZENDT_LM_OPT_3_1',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs DHL parcelshop:'),
						'name' => 'JETVERZENDT_LM_OPT_3_1_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 1.50')
					),
					array(
						'type' => 'select',
						'label' => $this->l('DPD Parcelshop actief:'),
						'name' => 'JETVERZENDT_LM_OPT_3_2',
						'required' => false,
						'col' => '3_1',
						'options' => array(
							'query' => $lm_opt_3_2,
							'id' => 'JETVERZENDT_LM_OPT_3_2',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Evt. meerprijs DPD parcelshop:'),
						'name' => 'JETVERZENDT_LM_OPT_3_2_PRICE',
						'required' => false,
						'desc' => $this->l('Voorbeeld: 1.50')
					),
				),

				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'form_last_mile',
				),
			),
		);
		return $form;

	}
	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		return array(
			'JETVERZENDT_CLIENT_ID' => Configuration::get('JETVERZENDT_CLIENT_ID'),
			'JETVERZENDT_GOOGLE_KEY' => Configuration::get('JETVERZENDT_GOOGLE_KEY'),
			'JETVERZENDT_STATUS' => Configuration::get('JETVERZENDT_STATUS'),
			'JETVERZENDT_CLIENT_SECRET' => Configuration::get('JETVERZENDT_CLIENT_SECRET'),
			'JETVERZENDT_CLIENT_LABEL' => Configuration::get('JETVERZENDT_CLIENT_LABEL'),
			'JETVERZENDT_SHIPPER' => Configuration::get('JETVERZENDT_SHIPPER'),
			'JETVERZENDT_SERVICE_DPD' => Configuration::get('JETVERZENDT_SERVICE_DPD'),
			'JETVERZENDT_SERVICE_DHL' => Configuration::get('JETVERZENDT_SERVICE_DHL'),
			'JETVERZENDT_ORDER_STATE' => Configuration::get('JETVERZENDT_ORDER_STATE'),
			'JETVERZENDT_PRINT_SIZE' => Configuration::get('JETVERZENDT_PRINT_SIZE'),
			'JETVERZENDT_DHL_SEND' => Configuration::get('JETVERZENDT_DHL_SEND'),
			'JETVERZENDT_DPD_SEND' => Configuration::get('JETVERZENDT_DPD_SEND'),
			'JETVERZENDT_CARRIER_ID' => Configuration::get('JETVERZENDT_CARRIER_ID'),
			'JETVERZENDT_LM_ACTIVE' => Configuration::get('JETVERZENDT_LM_ACTIVE'),
			'JETVERZENDT_LM_OPT_1' => Configuration::get('JETVERZENDT_LM_OPT_1'),
			'JETVERZENDT_LM_OPT_1_TIME' => Configuration::get('JETVERZENDT_LM_OPT_1_TIME'),
			'JETVERZENDT_LM_OPT_1_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_1_PRICE'),
			'JETVERZENDT_LM_OPT_2' => Configuration::get('JETVERZENDT_LM_OPT_2'),
			'JETVERZENDT_LM_OPT_2_TIME' => Configuration::get('JETVERZENDT_LM_OPT_2_TIME'),
			'JETVERZENDT_LM_OPT_2_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_2_PRICE'),
			'JETVERZENDT_LM_OPT_4' => Configuration::get('JETVERZENDT_LM_OPT_4'),
			'JETVERZENDT_LM_OPT_4_TIME' => Configuration::get('JETVERZENDT_LM_OPT_4_TIME'),
			'JETVERZENDT_LM_OPT_4_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_4_PRICE'),
			'JETVERZENDT_LM_OPT_5' => Configuration::get('JETVERZENDT_LM_OPT_5'),
			'JETVERZENDT_LM_OPT_5_TIME' => Configuration::get('JETVERZENDT_LM_OPT_5_TIME'),
			'JETVERZENDT_LM_OPT_5_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_5_PRICE'),
			'JETVERZENDT_LM_OPT_3_1' => Configuration::get('JETVERZENDT_LM_OPT_3_1'),
			'JETVERZENDT_LM_OPT_3_1_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE'),
			'JETVERZENDT_LM_OPT_3_2' => Configuration::get('JETVERZENDT_LM_OPT_3_2'),
			'JETVERZENDT_LM_OPT_3_2_PRICE' => Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE'),
		);
	}

	/**
	 * Save form data.
	 */
	protected function postProcess()
	{
		//$form_values = $this->getConfigFormValues();
		if (Tools::getValue('form_settings'))
		{
			Configuration::updateValue('JETVERZENDT_CLIENT_ID', Tools::getValue('JETVERZENDT_CLIENT_ID'));
			Configuration::updateValue('JETVERZENDT_GOOGLE_KEY', Tools::getValue('JETVERZENDT_GOOGLE_KEY'));
			Configuration::updateValue('JETVERZENDT_STATUS', Tools::getValue('JETVERZENDT_STATUS'));
			Configuration::updateValue('JETVERZENDT_CLIENT_SECRET', Tools::getValue('JETVERZENDT_CLIENT_SECRET'));
			Configuration::updateValue('JETVERZENDT_CLIENT_LABEL', Tools::getValue('JETVERZENDT_CLIENT_LABEL'));
			Configuration::updateValue('JETVERZENDT_SHIPPER', Tools::getValue('JETVERZENDT_SHIPPER'));
			Configuration::updateValue('JETVERZENDT_SERVICE_DPD', Tools::getValue('JETVERZENDT_SERVICE_DPD'));
			Configuration::updateValue('JETVERZENDT_SERVICE_DHL', Tools::getValue('JETVERZENDT_SERVICE_DHL'));
			Configuration::updateValue('JETVERZENDT_ORDER_STATE', Tools::getValue('JETVERZENDT_ORDER_STATE'));
			Configuration::updateValue('JETVERZENDT_PRINT_SIZE', Tools::getValue('JETVERZENDT_PRINT_SIZE'));
			Configuration::updateValue('JETVERZENDT_DHL_SEND', Tools::getValue('JETVERZENDT_DHL_SEND'));
			Configuration::updateValue('JETVERZENDT_DPD_SEND', Tools::getValue('JETVERZENDT_DPD_SEND'));
			Configuration::updateValue('JETVERZENDT_CARRIER_ID', Tools::getValue('JETVERZENDT_CARRIER_ID'));
		}
		else
		{
			Configuration::updateValue('JETVERZENDT_LM_ACTIVE', Tools::getValue('JETVERZENDT_LM_ACTIVE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_1', Tools::getValue('JETVERZENDT_LM_OPT_1'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_1_TIME', Tools::getValue('JETVERZENDT_LM_OPT_1_TIME'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_1_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_1_PRICE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_2', Tools::getValue('JETVERZENDT_LM_OPT_2'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_2_TIME', Tools::getValue('JETVERZENDT_LM_OPT_2_TIME'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_2_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_2_PRICE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_4', Tools::getValue('JETVERZENDT_LM_OPT_4'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_4_TIME', Tools::getValue('JETVERZENDT_LM_OPT_4_TIME'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_4_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_4_PRICE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_5', Tools::getValue('JETVERZENDT_LM_OPT_5'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_5_TIME', Tools::getValue('JETVERZENDT_LM_OPT_5_TIME'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_5_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_5_PRICE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_3_1', Tools::getValue('JETVERZENDT_LM_OPT_3_1'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_3_1_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_3_1_PRICE'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_3_2', Tools::getValue('JETVERZENDT_LM_OPT_3_2'));
			Configuration::updateValue('JETVERZENDT_LM_OPT_3_2_PRICE', Tools::getValue('JETVERZENDT_LM_OPT_3_2_PRICE'));
		}
	}

	/**
	* Add the CSS & JavaScript files you want to be loaded in the BO.
	*/
	public function hookBackOfficeHeader()
	{
		if (Tools::getValue('module_name') == $this->name)
		{
			$this->context->controller->addJS($this->_path.'views/js/back.js');
			$this->context->controller->addCSS($this->_path.'views/css/back.css');
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		$google_key = Configuration::get('JETVERZENDT_GOOGLE_KEY');
		if ($google_key != '')
			$this->context->controller->addJS('https://maps.googleapis.com/maps/api/js?sensor=false&key='.$google_key);
		else $this->context->controller->addJS('https://maps.googleapis.com/maps/api/js?sensor=false');
		$this->context->controller->addJS($this->_path.'/views/js/novijetverzendt.js');
		$this->context->controller->addCSS($this->_path.'/views/css/novijetverzendt.css');
	}

	public function hookDisplayAdminOrder()
	{
		return $this->getShipmentInfo(Tools::getValue('id_order'));
	}

	public function getShipmentInfo($id_order = 0)
	{
		$selected_shipping_text = '';
		if ($id_order > 0)
		{
			$order = new Order($id_order);
			$selected_shipping = '';
			$cart_shippings = Db::getInstance()->executeS('
							SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$order->id_cart.'"');
			if (count($cart_shippings) > 0)
			{
				if ($cart_shippings[0]['shipping_service'] == 'DPD')
				{
					$selected_shipping_text = '<b>'.$this->l('DPD Zaterdaglevering').'</b><br>';
					if ($cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$selected_shipping_text = '<b>'.$this->l('DPD Zaterdaglevering').'</b><br>'.$this->l('Zaterdag').' '.
						date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'].' + 1 days'));
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$selected_shipping_text = '<b>'.$this->l('Parcelshop - DPD').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
				}
				else
				if ($cart_shippings[0]['shipping_service'] == 'DHL')
				{
					$selected_shipping_text = $this->l('Bezorgmoment - DHL');
					if ($cart_shippings[0]['deliverperiod'] != '' && $cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$selected_shipping_text = '<b>'.$this->l('Bezorgmoment - DHL').'</b>
							<br>'.$this->l('Bezorgdatum').': '.date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'])).
							'<br>'.$this->l('Tijdvak').': '.$cart_shippings[0]['deliverperiod'];
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$selected_shipping_text = '<b>'.$this->l('Parcelshop - DHL').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
				}
				else if ($cart_shippings[0]['shipping_service'] == 'FADELLO')
					$selected_shipping_text = $this->l('Same Day Delivery');
				else $selected_shipping_text = $this->l('Next Day Premium');
			}
		}

		$shipper = Configuration::get('JETVERZENDT_SHIPPER');
		$service_dpd = Configuration::get('JETVERZENDT_SERVICE_DPD');
		$service_dhl = Configuration::get('JETVERZENDT_SERVICE_DHL');
		$dpd_send = Configuration::get('JETVERZENDT_DPD_SEND');

		$order = new Order($id_order);
		$selected_shipping = '';
		$shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt` WHERE id_order="'.$id_order.'"');
		$cart_shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$order->id_cart.'"');
		$selected_shipping = '';
		$shipping_service = '';
		$track_and_trace_url = '';
		$track_and_trace_code = '';
		// DPD
		$option_1_quantity = '';
		$option_1_reference = '';
		$option_1_mail = '';
		$option_1_pickup_date = '';
		$option_1_pickup_delivery = '';
		$option_1_amount = '';
		$option_1_saturday_delivery = '';
		$option_1_weight = '';

		// DHL
		$option_2_quantity = '';
		$option_2_reference = '';
		$option_2_weight = '';
		$option_2_pickup_delivery = '';
		$option_2_pickup_date = '';
		$option_2_insured_value = '';
		$option_2_signature = '';
		$option_2_no_neighbors = '';
		$option_2_evening = '';
		$option_2_extra_cover = '';
		$option_2_saturday_delivery = '';
		$option_2_amount = '';
		$parcelshop_id = '';
		$option_3_weight = '';

		if (count($shippings) > 0)
		{
			$selected_shipping = $shippings[0]['shipping_type'];
			$shipping_service = $shippings[0]['shipping_service'];
			$track_and_trace_url = $shippings[0]['track_and_trace_url'];
			$track_and_trace_code = $shippings[0]['track_and_trace_code'];
			// DPD
			$option_1_quantity = $shippings[0]['option_1_quantity'];
			$option_1_weight = $shippings[0]['option_1_weight'];
			$option_1_reference = $shippings[0]['option_1_reference'];
			$option_1_mail = $shippings[0]['option_1_mail'];
			$option_1_pickup_date = $shippings[0]['option_1_pickup_date'];
			if ($option_1_pickup_date != '0000-00-00' && $option_1_pickup_date != '1970-01-01')
			$option_1_pickup_date = date('d-m-Y', strtotime($option_1_pickup_date));
			else $option_1_pickup_date = '';
			$option_1_pickup_delivery = $shippings[0]['option_1_pickup_delivery'];
			$option_1_amount = $shippings[0]['option_1_amount'];
			$option_1_saturday_delivery = $shippings[0]['option_1_saturday_delivery'];

			$option_3_pickup_date = $shippings[0]['option_3_pickup_date'];
			if ($option_3_pickup_date != '0000-00-00') $option_3_pickup_date = date('d-m-Y', strtotime($option_3_pickup_date));
			else
				$option_3_pickup_date = '';

			// DHL
			$option_2_quantity = $shippings[0]['option_2_quantity'];
			$option_2_reference = $shippings[0]['option_2_reference'];
			$option_2_weight = $shippings[0]['option_2_weight'];
			$option_2_pickup_delivery = $shippings[0]['option_2_pickup_delivery'];
			$option_2_pickup_date = $shippings[0]['option_2_pickup_date'];
			if ($option_2_pickup_date != '0000-00-00') $option_2_pickup_date = date('d-m-Y', strtotime($option_2_pickup_date));
			else $option_2_pickup_date = '';
			$option_2_insured_value = $shippings[0]['option_2_insured_value'];
			$option_2_signature = $shippings[0]['option_2_signature'];
			$option_2_no_neighbors = $shippings[0]['option_2_no_neighbors'];
			$option_2_evening = $shippings[0]['option_2_evening'];
			$option_2_extra_cover = $shippings[0]['option_2_extra_cover'];
			$option_2_saturday_delivery = $shippings[0]['option_2_saturday_delivery'];
			$option_2_amount = $shippings[0]['option_2_amount'];

			$parcelshop_id = $shippings[0]['parcelshop_id'];
			$option_3_weight = $shippings[0]['option_3_weight'];
		}
		else
		{
			if (count($cart_shippings) > 0)
			{
				if ($cart_shippings[0]['shipping_service'] == 'DPD') $selected_shipping = 1;
				else if ($cart_shippings[0]['shipping_service'] == 'DHL') $selected_shipping = 2;
				else if ($cart_shippings[0]['shipping_service'] == 'FADELLO') $selected_shipping = 3;
				else $selected_shipping = 4;
				//$selected_shipping = (($cart_shippings[0]['shipping_service'] == 'DPD')?1:($cart_shippings[0]['shipping_service'] == 'DHL')?2:3);
				if ($selected_shipping == 2 && $cart_shippings[0]['deliverperiod'] != '')
				{
					/*
					if ($cart_shippings[0]['deliverperiod'] == '09:00-13:00' || $cart_shippings[0]['deliverperiod'] == '11:00-15:00')
						$shipping_service = 'E10';
					if ($cart_shippings[0]['deliverperiod'] == '14:00-18:00')
						$shipping_service = 'E12';
					if ($cart_shippings[0]['deliverperiod'] == '18:00-21:00')
						$shipping_service = 'E18';
					*/
					$shipping_service = 'DHL_FOR_YOU';
				}
				if ($selected_shipping == 1 && $cart_shippings[0]['deliverdate'] != '0000-00-00')
					$shipping_service = 'CL';
				//$shipping_service = $shippings[0]['shipping_service'];
				// DPD
				$option_1_pickup_date = $cart_shippings[0]['deliverdate'];
				if ($option_1_pickup_date != '0000-00-00' && $option_1_pickup_date != '1970-01-01')
				$option_1_pickup_date = date('d-m-Y', strtotime($option_1_pickup_date));
				else $option_1_pickup_date = '';
				if ($selected_shipping == 1 && $cart_shippings[0]['deliverdate'] != '0000-00-00')
					$option_1_saturday_delivery = 1;
				$parcelshop_id = $cart_shippings[0]['parcelshop_id'];
				if ($parcelshop_id != '')
				{
					if ($selected_shipping == 1) $shipping_service = 'PARCEL_SHOP';
					else if ($selected_shipping == 2) $shipping_service = 'PARCELSHOP';
				}
				//echo $selected_shipping."_".$shipping_service;
				//print_r($cart_shippings);
				//echo $cart_shippings[0]['shipping_service']."_".$selected_shipping."_".$shipping_service."_";
				// DHL

				if ($cart_shippings[0]['deliverdate'] != '0000-00-00' && $cart_shippings[0]['deliverdate'] != '1970-01-01')
				{
					$option_3_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
					$option_2_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
					$option_1_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
				}
				else
				{
					$option_2_pickup_delivery = 1;
					$option_1_pickup_delivery = 1;
				}

			}
		}
		if ($option_3_weight <= 0) $option_3_weight = $this->getOrderWeight($id_order);
		if ($option_2_weight <= 0) $option_2_weight = $this->getOrderWeight($id_order);
		if ($option_1_weight <= 0) $option_1_weight = $this->getOrderWeight($id_order);

		$option_1_weight = ceil($option_1_weight);
		$option_2_weight = ceil($option_2_weight);
		$option_3_weight = ceil($option_3_weight);

		$this->html = '
			<br>
			'.(($id_order != 0)?'<form method="post" action="'.$this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.
				'&tab_module='.$this->tab.'&module_name='.$this->name.'&saveShippment=1">':'').'
				<fieldset>
					<div class="row">
						<div class="col-md-12">
							<div class="panel form-horizontal">
								'.(($id_order != 0)?'<input type="hidden" name="id_order" value="'.$id_order.'">':'').'
								<div class="panel-heading"><i class="icon-truck "></i> Shipment</div>
								'.((Tools::getValue('errors') != '')?
								'<div class="alert alert-warning">
									<button data-dismiss="alert" class="close" type="button">×</button>
									<h4>'.Tools::getValue('errors').'</h4>
								</div>'
								:'').'
								'.$selected_shipping_text.'
								<div class="form-group">
									<label class="control-label col-lg-3">'.$this->l('Carrier').'</label>
									<div class="col-lg-9">
										<select name="shipping_option" class="shipping_option">
											<option value=""></option>
											<option value="1" '.(($selected_shipping == 1 || ($selected_shipping == '' && $shipper == 'DPD'))?'selected':'').
											'>'.$this->l('DPD').'</option>
											<option value="2" '.(($selected_shipping == 2 || ($selected_shipping == '' && $shipper == 'DHL'))?'selected':'').
											'>'.$this->l('DHL').'</option>
											<option value="3" '.(($selected_shipping == 3 || ($selected_shipping == '' && $shipper == 'FADELLO'))?'selected':'').
											'>'.$this->l('Same Day Delivery').'</option>
											<option value="4" '.(($selected_shipping == 4 || ($selected_shipping == ''
											&& $shipper == 'NextDayPremium'))?'selected':'').'>'.$this->l('Next Day Premium').'</option>
										</select>
									</div>
								</div>
								<div id="shipping_option_type_1" class="shipping_option_type" style="display:'.(($selected_shipping == 1
								|| ($selected_shipping == '' && $shipper == 'DPD'))?'block':'none').'">
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_1_quantity.'" name="option_1_quantity" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_1_reference.'" name="option_1_reference" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Service').'</label>
										<div class="col-lg-9">
											<select name="shipping_option_type_1" class="shipping_option_type_1">
												<option value=""></option>
												<option value="CL" '.(($shipping_service == 'CL'
												|| ($shipping_service == '' && $service_dpd == 'CL'))?'selected':'').'>'.
												$this->l('Classic (Saturday delivery)').'</option>
												<option value="E10" '.(($shipping_service == 'E10'
												|| ($shipping_service == '' && $service_dpd == 'E10'))?'selected':'').'>'.$this->l('DPD 10:00').'</option>
												<option value="E12" '.(($shipping_service == 'E12'
												|| ($shipping_service == '' && $service_dpd == 'E12'))?'selected':'').'>'.$this->l('DPD 12:00').'</option>
												<option value="E18" '.(($shipping_service == 'E18'
												|| ($shipping_service == '' && $service_dpd == 'E18'))?'selected':'').'>'.$this->l('DPD 18:00').'</option>
												<option value="IE2" '.(($shipping_service == 'IE2'
												|| ($shipping_service == '' && $service_dpd == 'IE2'))?'selected':'').'>'.$this->l('DPD Express').'</option>
												<option value="CLR" '.(($shipping_service == 'CLR'
												|| ($shipping_service == '' && $service_dpd == 'CLR'))?'selected':'').'>'.
												$this->l('COD (Cash On Delivery)').'</option>
												<option value="PARCEL_SHOP" '.(($shipping_service == 'PARCEL_SHOP'
												|| ($shipping_service == '' && $service_dpd == 'PARCEL_SHOP'))?'selected':'').'>'.
												$this->l('Parcelshop delivery').'</option>
											</select>
											<select name="option_1_mail">
												<option value="0">'.$this->l('No notification').'</option>
												<option value="1" '.(($option_1_mail == 1 || ($option_1_mail == ''
												&& $dpd_send == 1))?'selected':'').'>'.$this->l('Receive information by e-mail').'</option>
												<option value="2" '.(($option_1_mail == 2 || ($option_1_mail == ''
												&& $dpd_send == 2))?'selected':'').'>'.$this->l('Receiver information by SMS').'</option>
											</select>
											<label><input type="checkbox" name="option_1_saturday_delivery" value="1" id="option_1_saturday_delivery" '.
											(($option_1_saturday_delivery == 1)?'checked':'').'> '.$this->l('Saturday Delivery').'</label>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
										<div class="col-lg-9">
											<label><input type="checkbox" name="option_1_pickup_delivery" id="option_1_pickup_delivery" value="1" '.
											(($option_1_pickup_delivery == 1)?'checked':'').'> '.$this->l('I have daily pickup delivery').'</label><br>
											<input type="text" value="'.$option_1_pickup_date.
											'" name="option_1_pickup_date" class="datepicker_special form-control" id="option_1_pickup_date" '.
											(($option_1_pickup_delivery == 1)?'style="display:none"':'').'>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_1_weight.'" name="option_1_weight" id="option_1_weight" class="form-control">
										</div>
									</div>
									<div class="form-group option_1_amount" style="display:'.((($shipping_service == 'CLR'
									|| ($shipping_service == '' && $service_dpd == 'CLR')))?'block':'none').'">
										<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_1_amount.'" name="option_1_amount" class="form-control">
										</div>
									</div>
								</div>
								<div id="shipping_option_type_2" class="shipping_option_type" style="display:'.(($selected_shipping == 2
								|| ($selected_shipping == '' && $shipper == 'DHL'))?'block':'none').'">
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_2_quantity.'" name="option_2_quantity" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_2_reference.'" name="option_2_reference" class="form-control" placeholder="'.
											(($id_order != 0)?$this->l('Order').' '.$id_order:'').'">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_2_weight.'" name="option_2_weight" id="option_2_weight" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Service').'</label>
										<div class="col-lg-9">
											<select name="shipping_option_type_2" class="shipping_option_type_2">
												<option value=""></option>
												<option value="EUROPLUS" '.(($shipping_service == 'EUROPLUS' || ($shipping_service == ''
												&& $service_dhl == 'EUROPLUS'))?'selected':'').'>'.$this->l('EuroPlus (Saturday delivery, insurance)').'</option>
												<option value="DHL_FOR_YOU" '.(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
												&& $service_dhl == 'DHL_FOR_YOU'))?'selected':'').'>'.
												$this->l('DHL For You (Not by neighbours, signature required, evening delivery, extra insurance)').'</option>
												<option value="EUROPACK" '.(($shipping_service == 'EUROPACK' || ($shipping_service == ''
												&& $service_dhl == 'EUROPACK'))?'selected':'').'>'.$this->l('Europack').'</option>
												<option value="COD" '.(($shipping_service == 'COD' || ($shipping_service == ''
												&& $service_dhl == 'COD'))?'selected':'').'>'.$this->l('COD value, notation: 10,99').'</option>
												<option value="EXPRESSER" '.(($shipping_service == 'EXPRESSER' || ($shipping_service == ''
												&& $service_dhl == 'EXPRESSER'))?'selected':'').'>'.$this->l('Expresser').'</option>
												<option value="EUROPLUS_INTERNATIONAL" '.(($shipping_service == 'EUROPLUS_INTERNATIONAL'
												|| ($shipping_service == '' && $service_dhl == 'EUROPLUS_INTERNATIONAL'))?'selected':'').'>'.$this->l('Europlus international').'</option>
												<option value="PARCELSHOP" '.(($shipping_service == 'PARCELSHOP' || ($shipping_service == ''
												&& $service_dhl == 'PARCELSHOP'))?'selected':'').'>'.$this->l('Parcelshop delivery').'</option>
											</select>
											<div style="display:none;">
												<label id="option_2_saturday_delivery" style="display:'.
												(($shipping_service == 'EUROPLUS' || ($shipping_service == '' && $service_dhl == 'EUROPLUS'))?'block':'none').
												'">
												<input type="checkbox" name="option_2_saturday_delivery" value="1" '.
												(($option_2_saturday_delivery == 1)?'checked':'').'> 
												'.
												$this->l('Saturday Delivery').'<br></label>
											</div>
											<label id="option_2_signature"  style="display:'.
											(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
											'">
											<input type="checkbox" name="option_2_signature" value="1" '.(($option_2_signature == 1)?'checked':'').'> '.
											$this->l('Signature required').'<br></label>
											<label id="option_2_no_neighbors" style="display:'.
											(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
											'">
											<input type="checkbox" name="option_2_no_neighbors" value="1" '.(($option_2_no_neighbors == 1)?'checked':'').'> '.
											$this->l('Do not deliver with neighbors').'<br></label>
											<label id="option_2_evening" style="display:'.
											(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
											'">
											<input type="checkbox" name="option_2_evening" value="1" '.(($option_2_evening == 1)?'checked':'').'> '.
											$this->l('Evening delivery').'<br></label>
											<label id="option_2_extra_cover" style="display:'.
											(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
											'">
											<input type="checkbox" name="option_2_extra_cover" value="1" '.(($option_2_extra_cover == 1)?'checked':'').'> '.
											$this->l('Extra cover').'<br></label>
										</div>
									</div>
									<div class="form-group option_2_insured_value" style="display:'.(((($shipping_service == 'EUROPLUS'
									|| $shipping_service == 'EXPRESSER' || $shipping_service == 'EUROPLUS_INTERNATIONAL')
									|| ($shipping_service == '' && ($shipping_service == 'EUROPLUS' || $shipping_service == 'EXPRESSER'
									|| $shipping_service == 'EUROPLUS_INTERNATIONAL'))))?'block':'none').'">
										<label class="control-label col-lg-3">'.$this->l('Insured value').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_2_insured_value.'" name="option_2_insured_value" class="form-control">
										</div>
									</div>
									<div class="form-group" style="display:none;">
										<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
										<div class="col-lg-9">
											<label><input type="checkbox" name="option_2_pickup_delivery" id="option_2_pickup_delivery" value="1" '.
											(($option_2_pickup_delivery == 1)?'checked':'').'> '.$this->l('I have daily pickup delivery').'</label><br>
											<input type="text" value="'.$option_2_pickup_date.
											'" name="option_2_pickup_date" class="datepicker_special form-control" id="option_2_pickup_date" '.
											(($option_2_pickup_delivery == 1)?'style="display:none"':'').'>
										</div>
									</div>
									<div class="form-group option_2_amount" style="display:'.((($shipping_service == 'COD' || ($shipping_service == ''
									&& $service_dhl == 'COD')))?'block':'none').'">
										<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_2_amount.'" name="option_2_amount" class="form-control">
										</div>
									</div>
								</div>
								<div class="form-group parcelshop_id" style="display:'.((($parcelshop_id != ''))?'block':'none').'">
									<label class="control-label col-lg-3">'.$this->l('Parcelshop id').'</label>
									<div class="col-lg-9">
										<input type="text" value="'.$parcelshop_id.'" name="parcelshop_id" class="form-control">
									</div>
								</div>
								<div id="shipping_option_type_3" class="shipping_option_type" style="display:'.(($selected_shipping == 3
								|| $selected_shipping == 4 || ($selected_shipping == '' && ($shipper == 'Fadello'
								|| $shipper == 'NextDayPremium')))?'block':'none').'">
								
									<div class="form-group">
										<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
										<div class="col-lg-9">
											<input type="text" value="'.$option_3_weight.'" name="option_3_weight" id="option_3_weight" class="form-control">
										</div>
									</div>
								</div>
								<script>
								$( document ).ready(function() {
									$(".shipping_option").change(function(){
										$(".shipping_option_type").hide();
										$("#shipping_option_type_"+$(this).val()).show();
										if ($(this).val() == 4)
											$("#shipping_option_type_3").show();
									});
									$(".shipping_option_type_1").change(function(){
										if ($(this).val() == "CLR") $(".option_1_amount").show();
										else $(".option_1_amount").hide();
									});
									$(".shipping_option_type_2").change(function(){
										if ($(this).val() == "COD") $(".option_2_amount").show();
										else $(".option_2_amount").hide();
										if ($(this).val() == "EUROPLUS") $("#option_2_saturday_delivery").css("display", "block");
										else $("#option_2_saturday_delivery").hide();
										if ($(this).val() == "DHL_FOR_YOU") $("#option_2_signature").css("display", "block");
										else $("#option_2_signature").hide();
										if ($(this).val() == "DHL_FOR_YOU") $("#option_2_no_neighbors").css("display", "block");
										else $("#option_2_no_neighbors").hide();
										if ($(this).val() == "DHL_FOR_YOU") $("#option_2_evening").css("display", "block");
										else $("#option_2_evening").hide();
										if ($(this).val() == "DHL_FOR_YOU") $("#option_2_extra_cover").css("display", "block");
										else $("#option_2_extra_cover").hide();
										if ($(this).val() == "EUROPLUS" || $(this).val() == "EXPRESSER" || $(this).val() == "EUROPLUS_INTERNATIONAL") 
											$(".option_2_insured_value").show();
										else $(".option_2_insured_value").hide();
										
										
									});
									$("#option_1_pickup_delivery").change(function(){
										if (document.getElementById("option_1_pickup_delivery").checked == true)
											$("#option_1_pickup_date").hide();
										else $("#option_1_pickup_date").show();
									});
									$("#option_2_pickup_delivery").change(function(){
										if (document.getElementById("option_2_pickup_delivery").checked == true)
											$("#option_2_pickup_date").hide();
										else $("#option_2_pickup_date").show();
									});
									$(".datepicker_special").datepicker({
										minDate: 1,
										beforeShowDay: $.datepicker.noWeekends
									});
									$("#option_3_weight").keyup(function(){
										$(".weight_error").remove();
										if ($(this).val() > 30){
											$(this).val(30);
											$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 30KG').'</span>");
										}
									});
									$("#option_1_weight").keyup(function(){
										$(".weight_error").remove();
										if ($(".shipping_option").val() == 1){
											// DPD
											if ($(".shipping_option_type_1").val() == "PARCEL_SHOP"){
												if ($(this).val() > 15){
													$(this).val(15);
													$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
												}
											}
											if (document.getElementById("option_1_saturday_delivery").checked == true){
												if ($(this).val() > 31){
													$(this).val(31);
													$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 31KG').'</span>");
												}
											}
										}
									});
									$("#option_2_weight").keyup(function(){
										$(".weight_error").remove();
										if ($(".shipping_option").val() == 2){
											// DPD
											if ($(".shipping_option_type_2").val() == "PARCELSHOP"){
												if ($(this).val() > 15){
													$(this).val(15);
													$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
												}
											}
											if ($(".shipping_option_type_2").val() == "DHL_FOR_YOU"){
												if ($(this).val() > 20){
													$(this).val(20);
													$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 20KG').'</span>");
												}
											}
										}
									});
									$(".shipping_option").change(function(){
										$("#option_1_weight").trigger("keyup");
										$("#option_2_weight").trigger("keyup");
										$("#option_3_weight").trigger("keyup");
									});
									$(".shipping_option_type_2").change(function(){
										$("#option_1_weight").trigger("keyup");
										$("#option_2_weight").trigger("keyup");
										$("#option_3_weight").trigger("keyup");
									});
									$(".shipping_option_type_1").change(function(){
										$("#option_1_weight").trigger("keyup");
										$("#option_2_weight").trigger("keyup");
										$("#option_3_weight").trigger("keyup");
									});
									$("#option_1_saturday_delivery").click(function(){
										$("#option_1_weight").trigger("keyup");
										$("#option_2_weight").trigger("keyup");
										$("#option_3_weight").trigger("keyup");
									});
								});
								</script>
								<br>
								<button name="submitShippmentLabel" class="btn btn-primary pull-left hidden" id="submitShippmentLabel" type="submit">
										'.$this->l('Get label').'
								</button>
								'.(($track_and_trace_code != '')?'
								<a href="/index.php?fc=module&module=novijetverzendt&controller=print?id_order='.$id_order.
								'" target="_blank" class="btn btn-primary pull-left" id="submitShippmentLabel" type="submit">
										'.$this->l('Get label').'
								</a>
								':'').'
								'.(($track_and_trace_code != '')?'
								<a href="'.$track_and_trace_url.
								'" target="_blank" class="btn btn-primary pull-left" id="submitShippmentLabel" type="submit" style="margin-left:10px;">
										'.$track_and_trace_code.'
								</a>
								':'').'
								'.(($id_order != 0)?'<button name="submitShippment" class="btn btn-primary pull-right" id="submitShippment" type="submit">
										'.$this->l('Add shippment').'
								</button>':'').'
								<div class="clearfix"></div>
							</div>
						</div>  
					</div>
				</fieldset>
			'.(($id_order != 0)?'</form>':'').'
			';
		return $this->html;
	}

	public function getShipmentInfoList($id_order = 0)
	{
		//$this->html = '<form method="post" action="'.$this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.
		//			'&tab_module='.$this->tab.'&module_name='.$this->name.'&saveShippment=1'.'">';
		$this->html = '';
		foreach (Tools::getValue('orderBox') as $id_order)
		{
			$selected_shipping_text = '';
			$order = new Order($id_order);
			$selected_shipping = '';
			$cart_shippings = array();
			$cart_shippings = Db::getInstance()->executeS('
							SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$order->id_cart.'"');
			if (count($cart_shippings) > 0)
			{
				if ($cart_shippings[0]['shipping_service'] == 'DPD')
				{
					$selected_shipping_text = '<b>'.$this->l('DPD Zaterdaglevering').'</b><br>';
					if ($cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$selected_shipping_text = '<b>'.$this->l('DPD Zaterdaglevering').'</b><br>'.$this->l('Zaterdag').' '.
						date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'].' + 1 days'));
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$selected_shipping_text = '<b>'.$this->l('Parcelshop - DPD').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
				}
				else
				if ($cart_shippings[0]['shipping_service'] == 'DHL')
				{
					$selected_shipping_text = $this->l('Bezorgmoment - DHL');
					if ($cart_shippings[0]['deliverperiod'] != '' && $cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$selected_shipping_text = '<b>'.$this->l('Bezorgmoment - DHL').'</b>
							<br>'.$this->l('Bezorgdatum').': '.date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'])).
							'<br>'.$this->l('Tijdvak').': '.$cart_shippings[0]['deliverperiod'];
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$selected_shipping_text = '<b>'.$this->l('Parcelshop - DHL').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
				}
				else if ($cart_shippings[0]['shipping_service'] == 'FADELLO')
					$selected_shipping_text = $this->l('Same Day Delivery');
				else $selected_shipping_text = $this->l('Next Day Premium');
			}

			$shipper = Configuration::get('JETVERZENDT_SHIPPER');
			$service_dpd = Configuration::get('JETVERZENDT_SERVICE_DPD');
			$service_dhl = Configuration::get('JETVERZENDT_SERVICE_DHL');
			$dpd_send = Configuration::get('JETVERZENDT_DPD_SEND');

			$order = new Order($id_order);
			$selected_shipping = '';
			$shippings = array();
			$shippings = Db::getInstance()->executeS('
							SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt` WHERE id_order="'.$id_order.'"');
			$cart_shippings = array();
			$cart_shippings = Db::getInstance()->executeS('
							SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$order->id_cart.'"');
			$selected_shipping = '';
			$shipping_service = '';
			//$track_and_trace_url = '';
			//$track_and_trace_code = '';
			// DPD
			$option_1_quantity = '';
			$option_1_reference = '';
			$option_1_mail = '';
			$option_1_pickup_date = '';
			$option_1_pickup_delivery = '';
			$option_1_amount = '';
			$option_1_saturday_delivery = '';
			$option_1_weight = '';

			// DHL
			$option_2_quantity = '';
			$option_2_reference = '';
			$option_2_weight = '';
			$option_2_pickup_delivery = '';
			$option_2_pickup_date = '';
			$option_2_insured_value = '';
			$option_2_signature = '';
			$option_2_no_neighbors = '';
			$option_2_evening = '';
			$option_2_extra_cover = '';
			$option_2_saturday_delivery = '';
			$option_2_amount = '';
			$parcelshop_id = '';
			$option_3_weight = '';

			if (count($shippings) > 0)
			{
				$selected_shipping = $shippings[0]['shipping_type'];
				$shipping_service = $shippings[0]['shipping_service'];
				//$track_and_trace_url = $shippings[0]['track_and_trace_url'];
				//$track_and_trace_code = $shippings[0]['track_and_trace_code'];
				// DPD
				$option_1_quantity = $shippings[0]['option_1_quantity'];
				$option_1_weight = $shippings[0]['option_1_weight'];
				$option_1_reference = $shippings[0]['option_1_reference'];
				$option_1_mail = $shippings[0]['option_1_mail'];
				$option_1_pickup_date = $shippings[0]['option_1_pickup_date'];
				if ($option_1_pickup_date != '0000-00-00' && $option_1_pickup_date != '1970-01-01')
				$option_1_pickup_date = date('d-m-Y', strtotime($option_1_pickup_date));
				else $option_1_pickup_date = '';
				$option_1_pickup_delivery = $shippings[0]['option_1_pickup_delivery'];
				$option_1_amount = $shippings[0]['option_1_amount'];
				$option_1_saturday_delivery = $shippings[0]['option_1_saturday_delivery'];

				$option_3_pickup_date = $shippings[0]['option_3_pickup_date'];
				if ($option_3_pickup_date != '0000-00-00') $option_3_pickup_date = date('d-m-Y', strtotime($option_3_pickup_date));
				else $option_3_pickup_date = '';

				// DHL
				$option_2_quantity = $shippings[0]['option_2_quantity'];
				$option_2_reference = $shippings[0]['option_2_reference'];
				$option_2_weight = $shippings[0]['option_2_weight'];
				$option_2_pickup_delivery = $shippings[0]['option_2_pickup_delivery'];
				$option_2_pickup_date = $shippings[0]['option_2_pickup_date'];
				if ($option_2_pickup_date != '0000-00-00') $option_2_pickup_date = date('d-m-Y', strtotime($option_2_pickup_date));
				else $option_2_pickup_date = '';
				$option_2_insured_value = $shippings[0]['option_2_insured_value'];
				$option_2_signature = $shippings[0]['option_2_signature'];
				$option_2_no_neighbors = $shippings[0]['option_2_no_neighbors'];
				$option_2_evening = $shippings[0]['option_2_evening'];
				$option_2_extra_cover = $shippings[0]['option_2_extra_cover'];
				$option_2_saturday_delivery = $shippings[0]['option_2_saturday_delivery'];
				$option_2_amount = $shippings[0]['option_2_amount'];

				$parcelshop_id = $shippings[0]['parcelshop_id'];
				$option_3_weight = $shippings[0]['option_3_weight'];
			}
			else
				if (count($cart_shippings) > 0)
				{
					if ($cart_shippings[0]['shipping_service'] == 'DPD') $selected_shipping = 1;
					else if ($cart_shippings[0]['shipping_service'] == 'DHL') $selected_shipping = 2;
					else if ($cart_shippings[0]['shipping_service'] == 'FADELLO') $selected_shipping = 3;
					else $selected_shipping = 4;
					//$selected_shipping = (($cart_shippings[0]['shipping_service'] == 'DPD')?1:($cart_shippings[0]['shipping_service'] == 'DHL')?2:3);
					if ($selected_shipping == 2 && $cart_shippings[0]['deliverperiod'] != '')
					{
						/*
						if ($cart_shippings[0]['deliverperiod'] == '09:00-13:00' || $cart_shippings[0]['deliverperiod'] == '11:00-15:00')
							$shipping_service = 'E10';
						if ($cart_shippings[0]['deliverperiod'] == '14:00-18:00')
							$shipping_service = 'E12';
						if ($cart_shippings[0]['deliverperiod'] == '18:00-21:00')
							$shipping_service = 'E18';
						*/
						$shipping_service = 'DHL_FOR_YOU';
					}
					if ($selected_shipping == 1 && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$shipping_service = 'CL';
					//$shipping_service = $shippings[0]['shipping_service'];
					// DPD
					$option_1_pickup_date = $cart_shippings[0]['deliverdate'];
					if ($option_1_pickup_date != '0000-00-00' && $option_1_pickup_date != '1970-01-01')
					$option_1_pickup_date = date('d-m-Y', strtotime($option_1_pickup_date));
					else $option_1_pickup_date = '';
					if ($selected_shipping == 1 && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$option_1_saturday_delivery = 1;
					$parcelshop_id = $cart_shippings[0]['parcelshop_id'];
					if ($parcelshop_id != '')
						if ($selected_shipping == 1) $shipping_service = 'PARCEL_SHOP';
						else if ($selected_shipping == 2) $shipping_service = 'PARCELSHOP';
					//print_r($cart_shippings);
					//echo $cart_shippings[0]['shipping_service']."_".$selected_shipping."_".$shipping_service."_";
					// DHL

					if ($cart_shippings[0]['deliverdate'] != '0000-00-00' && $cart_shippings[0]['deliverdate'] != '1970-01-01')
					{
						$option_3_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
						$option_2_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
						$option_1_pickup_date = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
					}
					else
					{
						$option_2_pickup_delivery = 1;
						$option_1_pickup_delivery = 1;
					}

				}

			if ($option_3_weight <= 0) $option_3_weight = $this->getOrderWeight($id_order);
			if ($option_2_weight <= 0) $option_2_weight = $this->getOrderWeight($id_order);
			if ($option_1_weight <= 0) $option_1_weight = $this->getOrderWeight($id_order);
			$option_1_weight = ceil($option_1_weight);
			$option_2_weight = ceil($option_2_weight);
			$option_3_weight = ceil($option_3_weight);
			$this->html .= '
					<fieldset>
						<div class="row">
							<div class="col-md-12">
								<div class="panel form-horizontal" style="margin:0;">
									'.(($id_order != 0)?'<input type="hidden" name="id_order" value="'.$id_order.'">':'').'
									<div class="panel-heading"><i class="icon-truck "></i> Shipment voor order '.$id_order.
									' <a href="" class="open_close_shipment">('.$this->l('WIJZIGEN').')</a></div>
									'.((Tools::getValue('errors') != '')?
									'<div class="alert alert-warning">
										<button data-dismiss="alert" class="close" type="button">×</button>
										<h4>'.Tools::getValue('errors').'</h4>
									</div>'
									:'').'
									'.$selected_shipping_text.'
									<div class="open_close_shipment_wrapper" style="display:none;">
										<div class="form-group">
											<label class="control-label col-lg-3">'.$this->l('Carrier').'</label>
											<div class="col-lg-9">
												<select name="shipping_option['.$id_order.']" class="shipping_option">
													<option value=""></option>
													<option value="1" '.(($selected_shipping == 1 || ($selected_shipping == ''
													&& $shipper == 'DPD'))?'selected':'').'>'.$this->l('DPD').'</option>
													<option value="2" '.(($selected_shipping == 2 || ($selected_shipping == ''
													&& $shipper == 'DHL'))?'selected':'').'>'.$this->l('DHL').'</option>
													<option value="3" '.(($selected_shipping == 3 || ($selected_shipping == ''
													&& $shipper == 'FADELLO'))?'selected':'').'>'.$this->l('Same Day Delivery').'</option>
													<option value="4" '.(($selected_shipping == 4 || ($selected_shipping == ''
													&& $shipper == 'NextDayPremium'))?'selected':'').'>'.$this->l('Next Day Premium').'</option>
												</select>
											</div>
										</div>
										<div id="shipping_option_type_1" class="shipping_option_type" style="display:'.
										(($selected_shipping == 1 || ($selected_shipping == '' && $shipper == 'DPD'))?'block':'none').'">
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_quantity.'" name="option_1_quantity['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_reference.'" name="option_1_reference['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Service').'</label>
												<div class="col-lg-9">
													<select name="shipping_option_type_1['.$id_order.']" class="shipping_option_type_1">
														<option value=""></option>
														<option value="CL" '.(($shipping_service == 'CL' || ($shipping_service == ''
														&& $service_dpd == 'CL'))?'selected':'').'>'.$this->l('Classic (Saturday delivery)').'</option>
														<option value="E10" '.(($shipping_service == 'E10' || ($shipping_service == ''
														&& $service_dpd == 'E10'))?'selected':'').'>'.$this->l('DPD 10:00').'</option>
														<option value="E12" '.(($shipping_service == 'E12' || ($shipping_service == ''
														&& $service_dpd == 'E12'))?'selected':'').'>'.$this->l('DPD 12:00').'</option>
														<option value="E18" '.(($shipping_service == 'E18' || ($shipping_service == ''
														&& $service_dpd == 'E18'))?'selected':'').'>'.$this->l('DPD 18:00').'</option>
														<option value="IE2" '.(($shipping_service == 'IE2' || ($shipping_service == ''
														&& $service_dpd == 'IE2'))?'selected':'').'>'.$this->l('DPD Express').'</option>
														<option value="CLR" '.(($shipping_service == 'CLR' || ($shipping_service == ''
														&& $service_dpd == 'CLR'))?'selected':'').'>'.$this->l('COD (Cash On Delivery)').'</option>
														<option value="PARCEL_SHOP" '.(($shipping_service == 'PARCEL_SHOP'
														|| ($shipping_service == '' && $service_dpd == 'PARCEL_SHOP'))?'selected':'').'>'.
														$this->l('Parcelshop delivery').'</option>
													</select>
													<select name="option_1_mail['.$id_order.']">
														<option value="0">'.$this->l('No notification').'</option>
														<option value="1" '.(($option_1_mail == 1 || ($option_1_mail == ''
														&& $dpd_send == 1))?'selected':'').'>'.$this->l('Receive information by e-mail').'</option>
														<option value="2" '.(($option_1_mail == 2 || ($option_1_mail == ''
														&& $dpd_send == 2))?'selected':'').'>'.$this->l('Receiver information by SMS').'</option>
													</select>
													<label><input type="checkbox" name="option_1_saturday_delivery['.$id_order.
													']" value="1" id="option_1_saturday_delivery" '.(($option_1_saturday_delivery == 1)?'checked':'').
													'> '.$this->l('Saturday Delivery').'</label>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
												<div class="col-lg-9">
													<label><input type="checkbox" name="option_1_pickup_delivery['.$id_order.
													']" id="option_1_pickup_delivery'.$id_order.'" class="option_1_pickup_delivery" value="1" '.
													(($option_1_pickup_delivery == 1)?'checked':'').'> '.$this->l('I have daily pickup delivery').'</label><br>
													<input type="text" value="'.$option_1_pickup_date.'" name="option_1_pickup_date['.$id_order.
													']" class="datepicker_special form-control" id="option_1_pickup_date'.$id_order.'" '.
													(($option_1_pickup_delivery == 1)?'style="display:none"':'').'>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_weight.'" name="option_1_weight['.$id_order.
													']" id="option_1_weight" class="option_1_weight form-control">
												</div>
											</div>
											<div class="form-group option_1_amount" style="display:'.((($shipping_service == 'CLR'
											|| ($shipping_service == '' && $service_dpd == 'CLR')))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_amount.'" name="option_1_amount['.$id_order.
													']" class="form-control">
												</div>
											</div>
										</div>
										<div id="shipping_option_type_2" class="shipping_option_type" style="display:'.(($selected_shipping == 2
										|| ($selected_shipping == '' && $shipper == 'DHL'))?'block':'none').'">
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_quantity.'" name="option_2_quantity['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_reference.'" name="option_2_reference['.$id_order.
													']" class="form-control" placeholder="'.(($id_order != 0)?$this->l('Order').' '.$id_order:'').'">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_weight.'" name="option_2_weight['.$id_order.
													']" id="option_2_weight" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Service').'</label>
												<div class="col-lg-9">
													<select name="shipping_option_type_2['.$id_order.']" class="shipping_option_type_2">
														<option value=""></option>
														<option value="EUROPLUS" '.(($shipping_service == 'EUROPLUS' || ($shipping_service == ''
														&& $service_dhl == 'EUROPLUS'))?'selected':'').'>'.$this->l('EuroPlus (Saturday delivery, insurance)').
														'</option>
														<option value="DHL_FOR_YOU" '.(($shipping_service == 'DHL_FOR_YOU'
														|| ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'selected':'').'>'.
														$this->l('DHL For You (Not by neighbours, signature required, evening delivery, extra insurance)').
														'</option>
														<option value="EUROPACK" '.(($shipping_service == 'EUROPACK'
														|| ($shipping_service == '' && $service_dhl == 'EUROPACK'))?'selected':'').'>'.
														$this->l('Europack').'</option>
														<option value="COD" '.(($shipping_service == 'COD' || ($shipping_service == ''
														&& $service_dhl == 'COD'))?'selected':'').'>'.$this->l('COD value, notation: 10,99').'</option>
														<option value="EXPRESSER" '.(($shipping_service == 'EXPRESSER' || ($shipping_service == ''
														&& $service_dhl == 'EXPRESSER'))?'selected':'').'>'.$this->l('Expresser').'</option>
														<option value="EUROPLUS_INTERNATIONAL" '.(($shipping_service == 'EUROPLUS_INTERNATIONAL'
														|| ($shipping_service == '' && $service_dhl == 'EUROPLUS_INTERNATIONAL'))?'selected':'').'>'.
														$this->l('Europlus international').'</option>
														<option value="PARCELSHOP" '.(($shipping_service == 'PARCELSHOP' || ($shipping_service == ''
														&& $service_dhl == 'PARCELSHOP'))?'selected':'').'>'.$this->l('Parcelshop delivery').'</option>
													</select>
													<div style="display:none;">
														<label id="option_2_saturday_delivery" style="display:'.
														(($shipping_service == 'EUROPLUS' || ($shipping_service == ''
														&& $service_dhl == 'EUROPLUS'))?'block':'none').
														'">
														<input type="checkbox" name="option_2_saturday_delivery['.$id_order.']" value="1" '.
														(($option_2_saturday_delivery == 1)?'checked':'').'> '.
														$this->l('Saturday Delivery').'<br></label>
													</div>
													<label id="option_2_signature"  style="display:'.
													(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
													&& $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_signature['.$id_order.']" value="1" '.
													(($option_2_signature == 1)?'checked':'').'> '.
													$this->l('Signature required').'<br></label>
													<label id="option_2_no_neighbors" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
													&& $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_no_neighbors['.$id_order.']" value="1" '.
													(($option_2_no_neighbors == 1)?'checked':'').'> '.
													$this->l('Do not deliver with neighbors').'<br></label>
													<label id="option_2_evening" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
													&& $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_evening['.$id_order.']" value="1" '.
													(($option_2_evening == 1)?'checked':'').'> '.
													$this->l('Evening delivery').'<br></label>
													<label id="option_2_extra_cover" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU'
													|| ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_extra_cover['.$id_order.']" value="1" '.
													(($option_2_extra_cover == 1)?'checked':'').'> '.
													$this->l('Extra cover').'<br></label>
												</div>
											</div>
											<div class="form-group option_2_insured_value" style="display:'.(((($shipping_service == 'EUROPLUS'
											|| $shipping_service == 'EXPRESSER' || $shipping_service == 'EUROPLUS_INTERNATIONAL')
											|| ($shipping_service == '' && ($shipping_service == 'EUROPLUS' || $shipping_service == 'EXPRESSER'
												|| $shipping_service == 'EUROPLUS_INTERNATIONAL'))))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Insured value').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_insured_value.
													'" name="option_2_insured_value['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group" style="display:none;">
												<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
												<div class="col-lg-9">
													<label><input type="checkbox" name="option_2_pickup_delivery['.
													$id_order.']" id="option_2_pickup_delivery" value="1" '.(($option_2_pickup_delivery == 1)?'checked':'').
													'> '.$this->l('I have daily pickup delivery').'</label><br>
													<input type="text" value="'.$option_2_pickup_date.'" name="option_2_pickup_date['.
													$id_order.']" class="datepicker_special form-control" id="option_2_pickup_date" '.
													(($option_2_pickup_delivery == 1)?'style="display:none"':'').'>
												</div>
											</div>
											<div class="form-group option_2_amount" style="display:'.((($shipping_service == 'COD'
											|| ($shipping_service == '' && $service_dhl == 'COD')))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_amount.'" name="option_2_amount['.$id_order.']" class="form-control">
												</div>
											</div>
										</div>
										<div class="form-group parcelshop_id" style="display:'.((($parcelshop_id != ''))?'block':'none').'">
											<label class="control-label col-lg-3">'.$this->l('Parcelshop id').'</label>
											<div class="col-lg-9">
												<input type="text" value="'.$parcelshop_id.'" name="parcelshop_id['.$id_order.']" class="form-control">
											</div>
										</div>
										<div id="shipping_option_type_3" class="shipping_option_type" style="display:'.(($selected_shipping == 3
										|| $selected_shipping == 4 || ($selected_shipping == '' && ($shipper == 'Fadello'
										|| $shipper == 'NextDayPremium')))?'block':'none').'">
										
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_3_weight.'" name="option_3_weight['.$id_order.
													']" id="option_3_weight" class="form-control">
												</div>
											</div>
										</div>
									</div>
									<script>
									$( document ).ready(function() {
										$(document).on("click", ".open_close_shipment", function(e){
											e.preventDefault();
											$(this).parent().parent().find(".open_close_shipment_wrapper").show();
											$(this).attr("class", "close_shipment");
										});
										$(document).on("click", ".close_shipment", function(e){
											e.preventDefault();
											$(this).parent().parent().find(".open_close_shipment_wrapper").hide();
											$(this).attr("class", "open_close_shipment");
										});
										$(".shipping_option").change(function(){
											$(this).closest(".form-horizontal").find(".shipping_option_type").hide();
											$(this).closest(".form-horizontal").find("#shipping_option_type_"+$(this).val()).show();
											if ($(this).val() == 4)
												$(this).closest(".form-horizontal").find("#shipping_option_type_3").show();
										});
										$(".shipping_option_type_1").change(function(){
											if ($(this).val() == "CLR") $(this).closest(".form-horizontal").find(".option_1_amount").show();
											else $(this).closest(".form-horizontal").find(".option_1_amount").hide();
										});
										$(".shipping_option_type_2").change(function(){
											if ($(this).val() == "COD") $(this).closest(".form-horizontal").find(".option_2_amount").show();
											else $(this).closest(".form-horizontal").find(".option_2_amount").hide();
											if ($(this).val() == "EUROPLUS") $(this).closest(".form-horizontal").find("#option_2_saturday_delivery").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_saturday_delivery").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_signature").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_signature").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_no_neighbors").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_no_neighbors").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_evening").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_evening").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_extra_cover").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_extra_cover").hide();
											if ($(this).val() == "EUROPLUS" || $(this).val() == "EXPRESSER" || $(this).val() == "EUROPLUS_INTERNATIONAL") 
												$(this).closest(".form-horizontal").find(".option_2_insured_value").show();
											else $(this).closest(".form-horizontal").find(".option_2_insured_value").hide();
											
											
										});
										$(".option_1_pickup_delivery").change(function(){
											if (document.getElementById($(this).attr("id")).checked == true)
												$(this).parent().next().next().hide();
											else $(this).parent().next().next().show();
										});
										$("#option_2_pickup_delivery").change(function(){
											if (document.getElementById("option_2_pickup_delivery").checked == true)
												$(this).closest(".form-horizontal").find("#option_2_pickup_date").hide();
											else $(this).closest(".form-horizontal").find("#option_2_pickup_date").show();
										});
										$(".datepicker_special").datepicker({
											minDate: 1,
											beforeShowDay: $.datepicker.noWeekends
										});
										$(".option_3_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).val() > 30){
												$(this).val(30);
												$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 30KG').'</span>");
											}
										});
										$(".option_1_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).closest(".form-horizontal").find(".shipping_option").val() == 1){
												// DPD
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_1").val() == "PARCEL_SHOP"){
													if ($(this).val() > 15){
														$(this).val(15);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
													}
												}
												if ($(this).closest(".form-horizontal").find("option_1_saturday_delivery").attr("checked") == true){
													if ($(this).val() > 31){
														$(this).val(31);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 31KG').'</span>");
													}
												}
											}
										});
										$(".option_2_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).closest(".form-horizontal").find(".shipping_option").val() == 2){
												// DPD
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_2").val() == "PARCELSHOP"){
													if ($(this).val() > 15){
														$(this).val(15);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
													}
												}
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_2").val() == "DHL_FOR_YOU"){
													if ($(this).val() > 20){
														$(this).val(20);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 20KG').'</span>");
													}
												}
											}
										});
										$(".shipping_option").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$(".shipping_option_type_2").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$(".shipping_option_type_1").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$("#option_1_saturday_delivery").click(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
									});
									</script>
									<div class="clearfix"></div>
								</div>
							</div>  
						</div>
					</fieldset>
				';
		}
		//'<button name="submitShippment" class="btn btn-primary pull-right" id="submitShippment" type="submit">'.$this->l('Add shippment').'</button>'.
		//$this->html .= '</form>';
		return $this->html;
	}

	public function getShipmentInfoListDefault($id_order = 0)
	{
		//$this->html = '<form method="post" action="'.$this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.
		//			'&tab_module='.$this->tab.'&module_name='.$this->name.'&saveShippment=1'.'">';
		$this->html = '';
			$selected_shipping_text = '';
			//$order = new Order($id_order);
			$selected_shipping = '';

			$shipper = Configuration::get('JETVERZENDT_SHIPPER');
			$service_dpd = Configuration::get('JETVERZENDT_SERVICE_DPD');
			$service_dhl = Configuration::get('JETVERZENDT_SERVICE_DHL');
			$dpd_send = Configuration::get('JETVERZENDT_DPD_SEND');

			$$selected_shipping = '';
			$shipping_service = '';
			//$track_and_trace_url = '';
			//$track_and_trace_code = '';
			// DPD
			$option_1_quantity = '';
			$option_1_reference = '';
			$option_1_mail = '';
			$option_1_pickup_date = '';
			$option_1_pickup_delivery = '';
			$option_1_amount = '';
			$option_1_saturday_delivery = '';
			$option_1_weight = '';

			// DHL
			$option_2_quantity = '';
			$option_2_reference = '';
			$option_2_weight = '';
			$option_2_pickup_delivery = '';
			$option_2_pickup_date = '';
			$option_2_insured_value = '';
			$option_2_signature = '';
			$option_2_no_neighbors = '';
			$option_2_evening = '';
			$option_2_extra_cover = '';
			$option_2_saturday_delivery = '';
			$option_2_amount = '';
			$parcelshop_id = '';
			$option_3_weight = '';

			if ($option_3_weight <= 0) $option_3_weight = $this->getOrderWeight($id_order);
			if ($option_2_weight <= 0) $option_2_weight = $this->getOrderWeight($id_order);
			if ($option_1_weight <= 0) $option_1_weight = $this->getOrderWeight($id_order);
			$option_1_weight = ceil($option_1_weight);
			$option_2_weight = ceil($option_2_weight);
			$option_3_weight = ceil($option_3_weight);
			$this->html .= '
					<fieldset>
						<div class="row">
							<div class="col-md-12">
								<div class="panel form-horizontal" style="margin:0;">
									'.(($id_order != 0)?'<input type="hidden" name="id_order" value="'.$id_order.'">':'').'
									<div class="panel-heading"><i class="icon-truck "></i> Shipment</div>
									'.((Tools::getValue('errors') != '')?
									'<div class="alert alert-warning">
										<button data-dismiss="alert" class="close" type="button">×</button>
										<h4>'.Tools::getValue('errors').'</h4>
									</div>'
									:'').'
									'.$selected_shipping_text.'
									<div class="open_close_shipment_wrapper">
										<div class="form-group">
											<label class="control-label col-lg-3">'.$this->l('Carrier').'</label>
											<div class="col-lg-9">
												<select name="shipping_option['.$id_order.']" class="shipping_option">
													<option value=""></option>
													<option value="1" '.(($selected_shipping == 1 || ($selected_shipping == ''
													&& $shipper == 'DPD'))?'selected':'').'>'.$this->l('DPD').'</option>
													<option value="2" '.(($selected_shipping == 2 || ($selected_shipping == ''
													&& $shipper == 'DHL'))?'selected':'').'>'.$this->l('DHL').'</option>
													<option value="3" '.(($selected_shipping == 3 || ($selected_shipping == ''
													&& $shipper == 'FADELLO'))?'selected':'').'>'.$this->l('Same Day Delivery').'</option>
													<option value="3" '.(($selected_shipping == 3 || ($selected_shipping == ''
													&& $shipper == 'NextDayPremium'))?'selected':'').'>'.$this->l('Next Day Premium').'</option>
												</select>
											</div>
										</div>
										<div id="shipping_option_type_1" class="shipping_option_type" style="display:'.(($selected_shipping == 1
										|| ($selected_shipping == '' && $shipper == 'DPD'))?'block':'none').'">
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_quantity.'" name="option_1_quantity['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_reference.'" name="option_1_reference['.$id_order.']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Service').'</label>
												<div class="col-lg-9">
													<select name="shipping_option_type_1['.$id_order.']" class="shipping_option_type_1">
														<option value=""></option>
														<option value="CL" '.(($shipping_service == 'CL' || ($shipping_service == ''
														&& $service_dpd == 'CL'))?'selected':'').'>'.$this->l('Classic (Saturday delivery)').'</option>
														<option value="E10" '.(($shipping_service == 'E10' || ($shipping_service == ''
														&& $service_dpd == 'E10'))?'selected':'').'>'.$this->l('DPD 10:00').'</option>
														<option value="E12" '.(($shipping_service == 'E12' || ($shipping_service == ''
														&& $service_dpd == 'E12'))?'selected':'').'>'.$this->l('DPD 12:00').'</option>
														<option value="E18" '.(($shipping_service == 'E18' || ($shipping_service == ''
														&& $service_dpd == 'E18'))?'selected':'').'>'.$this->l('DPD 18:00').'</option>
														<option value="IE2" '.(($shipping_service == 'IE2' || ($shipping_service == ''
														&& $service_dpd == 'IE2'))?'selected':'').'>'.$this->l('DPD Express').'</option>
														<option value="CLR" '.(($shipping_service == 'CLR' || ($shipping_service == ''
														&& $service_dpd == 'CLR'))?'selected':'').'>'.$this->l('COD (Cash On Delivery)').'</option>
														<option value="PARCEL_SHOP" '.(($shipping_service == 'PARCEL_SHOP' || ($shipping_service == ''
														&& $service_dpd == 'PARCEL_SHOP'))?'selected':'').'>'.$this->l('Parcelshop delivery').'</option>
													</select>
													<select name="option_1_mail['.$id_order.']">
														<option value="0">'.$this->l('No notification').'</option>
														<option value="1" '.(($option_1_mail == 1 || ($option_1_mail == ''
														&& $dpd_send == 1))?'selected':'').'>'.$this->l('Receive information by e-mail').'</option>
														<option value="2" '.(($option_1_mail == 2 || ($option_1_mail == ''
														&& $dpd_send == 2))?'selected':'').'>'.$this->l('Receiver information by SMS').'</option>
													</select>
													<label><input type="checkbox" name="option_1_saturday_delivery['.$id_order.
													']" value="1" id="option_1_saturday_delivery" '.(($option_1_saturday_delivery == 1)?'checked':'').
													'> '.$this->l('Saturday Delivery').'</label>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
												<div class="col-lg-9">
													<label><input type="checkbox" name="option_1_pickup_delivery['.$id_order.
													']" id="option_1_pickup_delivery" value="1" '.(($option_1_pickup_delivery == 1)?'checked':'').'> '.
													$this->l('I have daily pickup delivery').'</label><br>
													<input type="text" value="'.$option_1_pickup_date.'" name="option_1_pickup_date['.
													$id_order.']" class="datepicker_special form-control" id="option_1_pickup_date" '.
													(($option_1_pickup_delivery == 1)?'style="display:none"':'').'>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_weight.'" name="option_1_weight['.$id_order.
													']" id="option_1_weight" class="form-control">
												</div>
											</div>
											<div class="form-group option_1_amount" style="display:'.((($shipping_service == 'CLR'
											|| ($shipping_service == '' && $service_dpd == 'CLR')))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_1_amount.'" name="option_1_amount['.$id_order.']" class="form-control">
												</div>
											</div>
										</div>
										<div id="shipping_option_type_2" class="shipping_option_type" style="display:'.
										(($selected_shipping == 2 || ($selected_shipping == '' && $shipper == 'DHL'))?'block':'none').'">
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Number of packages').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_quantity.'" name="option_2_quantity['.$id_order.
													']" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Reference').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_reference.'" name="option_2_reference['.$id_order.
													']" class="form-control" placeholder="'.(($id_order != 0)?$this->l('Order').' '.$id_order:'').'">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_weight.'" name="option_2_weight['.$id_order.
													']" id="option_2_weight" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Service').'</label>
												<div class="col-lg-9">
													<select name="shipping_option_type_2['.$id_order.']" class="shipping_option_type_2">
														<option value=""></option>
														<option value="EUROPLUS" '.(($shipping_service == 'EUROPLUS' || ($shipping_service == ''
														&& $service_dhl == 'EUROPLUS'))?'selected':'').'>'.$this->l('EuroPlus (Saturday delivery, insurance)').
														'</option>
														<option value="DHL_FOR_YOU" '.(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
														&& $service_dhl == 'DHL_FOR_YOU'))?'selected':'').'>'.
														$this->l('DHL For You (Not by neighbours, signature required, evening delivery, extra insurance)').'</option>
														<option value="EUROPACK" '.(($shipping_service == 'EUROPACK' || ($shipping_service == ''
														&& $service_dhl == 'EUROPACK'))?'selected':'').'>'.$this->l('Europack').'</option>
														<option value="COD" '.(($shipping_service == 'COD' || ($shipping_service == ''
														&& $service_dhl == 'COD'))?'selected':'').'>'.$this->l('COD value, notation: 10,99').'</option>
														<option value="EXPRESSER" '.(($shipping_service == 'EXPRESSER' || ($shipping_service == ''
														&& $service_dhl == 'EXPRESSER'))?'selected':'').'>'.$this->l('Expresser').'</option>
														<option value="EUROPLUS_INTERNATIONAL" '.(($shipping_service == 'EUROPLUS_INTERNATIONAL'
														|| ($shipping_service == '' && $service_dhl == 'EUROPLUS_INTERNATIONAL'))?'selected':'').'>'.
														$this->l('Europlus international').'</option>
														<option value="PARCELSHOP" '.(($shipping_service == 'PARCELSHOP' || ($shipping_service == ''
														&& $service_dhl == 'PARCELSHOP'))?'selected':'').'>'.$this->l('Parcelshop delivery').'</option>
													</select>
													<div style="display:none;">
														<label id="option_2_saturday_delivery" style="display:'.
														(($shipping_service == 'EUROPLUS' || ($shipping_service == ''
														&& $service_dhl == 'EUROPLUS'))?'block':'none').
														'">
														<input type="checkbox" name="option_2_saturday_delivery['.$id_order.']" value="1" '.
														(($option_2_saturday_delivery == 1)?'checked':'').'> '.
														$this->l('Saturday Delivery').'<br></label>
													</div>
													<label id="option_2_signature"  style="display:'.
													(($shipping_service == 'DHL_FOR_YOU' || ($shipping_service == ''
													&& $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_signature['.$id_order.']" value="1" '.
													(($option_2_signature == 1)?'checked':'').'> '.
													$this->l('Signature required').'<br></label>
													<label id="option_2_no_neighbors" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU'
													|| ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_no_neighbors['.$id_order.']" value="1" '.
													(($option_2_no_neighbors == 1)?'checked':'').'> '.
													$this->l('Do not deliver with neighbors').'<br></label>
													<label id="option_2_evening" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU'
													|| ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_evening['.$id_order.']" value="1" '.
													(($option_2_evening == 1)?'checked':'').'> '.
													$this->l('Evening delivery').'<br></label>
													<label id="option_2_extra_cover" style="display:'.
													(($shipping_service == 'DHL_FOR_YOU'
													|| ($shipping_service == '' && $service_dhl == 'DHL_FOR_YOU'))?'block':'none').
													'">
													<input type="checkbox" name="option_2_extra_cover['.$id_order.']" value="1" '.
													(($option_2_extra_cover == 1)?'checked':'').'> '.
													$this->l('Extra cover').'<br></label>
												</div>
											</div>
											<div class="form-group option_2_insured_value" style="display:'.(((($shipping_service == 'EUROPLUS'
											|| $shipping_service == 'EXPRESSER' || $shipping_service == 'EUROPLUS_INTERNATIONAL')
											|| ($shipping_service == '' && ($shipping_service == 'EUROPLUS' || $shipping_service == 'EXPRESSER'
											|| $shipping_service == 'EUROPLUS_INTERNATIONAL'))))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Insured value').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_insured_value.'" name="option_2_insured_value['.$id_order.
													']" class="form-control">
												</div>
											</div>
											<div class="form-group" style="display:none;">
												<label class="control-label col-lg-3">'.$this->l('Jet Sends pickup date').'</label>
												<div class="col-lg-9">
													<label><input type="checkbox" name="option_2_pickup_delivery['.$id_order.
													']" id="option_2_pickup_delivery" value="1" '.(($option_2_pickup_delivery == 1)?'checked':'').'> '.
													$this->l('I have daily pickup delivery').'</label><br>
													<input type="text" value="'.$option_2_pickup_date.'" name="option_2_pickup_date['.
													$id_order.']" class="datepicker_special form-control" id="option_2_pickup_date" '.
													(($option_2_pickup_delivery == 1)?'style="display:none"':'').'>
												</div>
											</div>
											<div class="form-group option_2_amount" style="display:'.((($shipping_service == 'COD'
											|| ($shipping_service == '' && $service_dhl == 'COD')))?'block':'none').'">
												<label class="control-label col-lg-3">'.$this->l('Rembours amount').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_2_amount.'" name="option_2_amount['.$id_order.']" class="form-control">
												</div>
											</div>
										</div>
										<div class="form-group parcelshop_id" style="display:'.((($parcelshop_id != ''))?'block':'none').'">
											<label class="control-label col-lg-3">'.$this->l('Parcelshop id').'</label>
											<div class="col-lg-9">
												<input type="text" value="'.$parcelshop_id.'" name="parcelshop_id['.$id_order.']" class="form-control">
											</div>
										</div>
										<div id="shipping_option_type_3" class="shipping_option_type" style="display:'.
										(($selected_shipping == 3 || ($selected_shipping == '' && ($shipper == 'Fadello'
										|| $shipper == 'NextDayPremium')))?'block':'none').'">
										
											<div class="form-group">
												<label class="control-label col-lg-3">'.$this->l('Weight (KG)').'</label>
												<div class="col-lg-9">
													<input type="text" value="'.$option_3_weight.'" name="option_3_weight['.$id_order.
													']" id="option_3_weight" class="form-control">
												</div>
											</div>
										</div>
									</div>
									<script>
									$( document ).ready(function() {
										$(document).on("click", ".open_close_shipment", function(e){
											e.preventDefault();
											$(this).parent().parent().find(".open_close_shipment_wrapper").show();
											$(this).attr("class", "close_shipment");
										});
										$(document).on("click", ".close_shipment", function(e){
											e.preventDefault();
											$(this).parent().parent().find(".open_close_shipment_wrapper").hide();
											$(this).attr("class", "open_close_shipment");
										});
										$(".shipping_option").change(function(){
											$(this).closest(".form-horizontal").find(".shipping_option_type").hide();
											$(this).closest(".form-horizontal").find("#shipping_option_type_"+$(this).val()).show();
										});
										$(".shipping_option_type_1").change(function(){
											if ($(this).val() == "CLR") $(this).closest(".form-horizontal").find(".option_1_amount").show();
											else $(this).closest(".form-horizontal").find(".option_1_amount").hide();
										});
										$(".shipping_option_type_2").change(function(){
											if ($(this).val() == "COD") $(this).closest(".form-horizontal").find(".option_2_amount").show();
											else $(this).closest(".form-horizontal").find(".option_2_amount").hide();
											if ($(this).val() == "EUROPLUS") $(this).closest(".form-horizontal").find("#option_2_saturday_delivery").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_saturday_delivery").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_signature").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_signature").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_no_neighbors").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_no_neighbors").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_evening").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_evening").hide();
											if ($(this).val() == "DHL_FOR_YOU") $(this).closest(".form-horizontal").find("#option_2_extra_cover").css("display", "block");
											else $(this).closest(".form-horizontal").find("#option_2_extra_cover").hide();
											if ($(this).val() == "EUROPLUS" || $(this).val() == "EXPRESSER" || $(this).val() == "EUROPLUS_INTERNATIONAL") 
												$(this).closest(".form-horizontal").find(".option_2_insured_value").show();
											else $(this).closest(".form-horizontal").find(".option_2_insured_value").hide();
											
											
										});
										$("#option_1_pickup_delivery").change(function(){
											if (document.getElementById("option_1_pickup_delivery").checked == true)
												$(this).closest(".form-horizontal").find("#option_1_pickup_date").hide();
											else $(this).closest(".form-horizontal").find("#option_1_pickup_date").show();
										});
										$("#option_2_pickup_delivery").change(function(){
											if (document.getElementById("option_2_pickup_delivery").checked == true)
												$(this).closest(".form-horizontal").find("#option_2_pickup_date").hide();
											else $(this).closest(".form-horizontal").find("#option_2_pickup_date").show();
										});
										$(".datepicker_special").datepicker({
											minDate: 1,
											beforeShowDay: $.datepicker.noWeekends
										});
										$("#option_3_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).val() > 30){
												$(this).val(30);
												$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 30KG').'</span>");
											}
										});
										$("#option_1_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).closest(".form-horizontal").find(".shipping_option").val() == 1){
												// DPD
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_1").val() == "PARCEL_SHOP"){
													if ($(this).val() > 15){
														$(this).val(15);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
													}
												}
												if ($(this).closest(".form-horizontal").find("option_1_saturday_delivery").attr("checked") == true){
													if ($(this).val() > 31){
														$(this).val(31);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 31KG').'</span>");
													}
												}
											}
										});
										$("#option_2_weight").keyup(function(){
											$(this).closest(".form-horizontal").find(".weight_error").remove();
											if ($(this).closest(".form-horizontal").find(".shipping_option").val() == 2){
												// DPD
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_2").val() == "PARCELSHOP"){
													if ($(this).val() > 15){
														$(this).val(15);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 15KG').'</span>");
													}
												}
												if ($(this).closest(".form-horizontal").find(".shipping_option_type_2").val() == "DHL_FOR_YOU"){
													if ($(this).val() > 20){
														$(this).val(20);
														$(this).after("<span class=\"weight_error\">'.$this->l('Max weight is 20KG').'</span>");
													}
												}
											}
										});
										$(".shipping_option").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$(".shipping_option_type_2").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$(".shipping_option_type_1").change(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
										$("#option_1_saturday_delivery").click(function(){
											$(this).closest(".form-horizontal").find("#option_1_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_2_weight").trigger("keyup");
											$(this).closest(".form-horizontal").find("#option_3_weight").trigger("keyup");
										});
									});
									</script>
									<div class="clearfix"></div>
								</div>
							</div>  
						</div>
					</fieldset>
				';

		//'<button name="submitShippment" class="btn btn-primary pull-right" id="submitShippment" type="submit">'.$this->l('Add shippment').'</button>'.
		//$this->html .= '</form>';
		return $this->html;
	}

	public function hookdisplayCarrierList()
	{
		$cart = new Cart($this->context->cookie->id_cart);
		$lm_active = Configuration::get('JETVERZENDT_LM_ACTIVE');
		$lm_opt_1 = Configuration::get('JETVERZENDT_LM_OPT_1');
		$lm_opt_2 = Configuration::get('JETVERZENDT_LM_OPT_2');
		$lm_opt_4 = Configuration::get('JETVERZENDT_LM_OPT_4');
		$lm_opt_5 = Configuration::get('JETVERZENDT_LM_OPT_5');
		$lm_opt_3_1 = Configuration::get('JETVERZENDT_LM_OPT_3_1');
		$lm_opt_3_2 = Configuration::get('JETVERZENDT_LM_OPT_3_2');
		$lm_opt_4_time = Configuration::get('JETVERZENDT_LM_OPT_4_TIME');
		$lm_opt_4_price = Configuration::get('JETVERZENDT_LM_OPT_4_PRICE');
		$lm_opt_5_time = Configuration::get('JETVERZENDT_LM_OPT_5_TIME');
		$lm_opt_5_price = Configuration::get('JETVERZENDT_LM_OPT_5_PRICE');
		$lm_opt_2_price = Configuration::get('JETVERZENDT_LM_OPT_2_PRICE');
		$lm_opt_3_1_price = Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE');
		$lm_opt_3_2_price = Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE');
		if (date('H:i') > $lm_opt_4_time) $lm_opt_4 = 0;
		if ((float)$cart->getTotalWeight() > 30)  $lm_opt_4 = 0;
		if (date('H:i') > $lm_opt_5_time) $lm_opt_5 = 0;
		if ((float)$cart->getTotalWeight() > 30)  $lm_opt_5 = 0;
		$jet_carrier = Configuration::get('JETVERZENDT_CARRIER_ID');
		$address = new Address((int)$cart->id_address_delivery);
		$country = new Country((int)$address->id_country, $this->context->language->id);
		$this->smarty->assign('jet_address', $address->address1.' '.$address->address2.' '.str_replace(' ', '', $address->postcode).' '.
		$address->city, ' '.$country->name);
		$this->smarty->assign('jet_country', $country->iso_code);
		$this->smarty->assign('jet_postcode', str_replace(' ', '', $address->postcode));
		$this->smarty->assign('jet_carrier', $jet_carrier);
		$this->smarty->assign('lm_active', $lm_active);
		$this->smarty->assign('lm_opt_1', $lm_opt_1);
		$this->smarty->assign('lm_opt_2', $lm_opt_2);
		$this->smarty->assign('lm_opt_4', $lm_opt_4);
		$this->smarty->assign('lm_opt_4_price', $lm_opt_4_price);
		$this->smarty->assign('lm_opt_4_price_display', number_format($lm_opt_4_price, 2, ',', ''));
		$this->smarty->assign('lm_opt_5', $lm_opt_5);
		$this->smarty->assign('lm_opt_5_price', $lm_opt_5_price);
		$this->smarty->assign('lm_opt_5_price_display', number_format($lm_opt_5_price, 2, ',', ''));
		$this->smarty->assign('lm_opt_2_price_display', number_format($lm_opt_2_price, 2, ',', ''));
		$this->smarty->assign('lm_opt_3_1_price_display', number_format($lm_opt_3_1_price, 2, ',', ''));
		$this->smarty->assign('lm_opt_3_2_price_display', number_format($lm_opt_3_2_price, 2, ',', ''));
		$this->smarty->assign('lm_opt_3_1', $lm_opt_3_1);
		$this->smarty->assign('lm_opt_3_2', $lm_opt_3_2);

		$cart_shippings = array();
		$cart_shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.(int)$this->context->cart->id.'"');
		$jet_extra_costs = 0;
		$jet_name_lastmile = '';
		if (count($cart_shippings) > 0)
		{
			$jet_extra_costs += $cart_shippings[0]['extra_shipping'];
			if ($cart_shippings[0]['shipping_service'] == 'NextDayPremium') $jet_name_lastmile = $this->l('Next Day Premium');
			else
				if ($cart_shippings[0]['shipping_service'] == 'FADELLO') $jet_name_lastmile = $this->l('Same Day Delivery');
				else
					if ($cart_shippings[0]['shipping_service'] == 'DHL' && $cart_shippings[0]['parcelshop_id'] == '' && $cart_shippings[0]['deliverperiod'] != '')
					$jet_name_lastmile = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'])).$this->l(' | tijdstip: ').$cart_shippings[0]['deliverperiod'];
				else
					if ($cart_shippings[0]['shipping_service'] == 'DPD' && $cart_shippings[0]['parcelshop_id'] == '')
						$jet_name_lastmile = $this->l('Zaterdaglevering: ').date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
				else
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$jet_name_lastmile = $cart_shippings[0]['parcelshop_description'];
		}
		$this->smarty->assign('jet_extra_costs', $jet_extra_costs);

		$this->smarty->assign('jet_name_lastmile', $jet_name_lastmile);
		return $this->display(__FILE__, 'carrierlist.tpl');
	}

	public function hookactionCartSave()
	{
		/*
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'novijetverzendt_cart` WHERE id_cart="'.$this->context->cookie->id_cart.'"');
		$shipping_type = 1;
		if (Tools::getValue('lastmile_type') == 'DHL') $shipping_type = 2;
		if (Tools::getValue('lastmile_type') == 'fadello') $shipping_type = 3;
		$shipping_service = Tools::getValue('lastmile_service');
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'novijetverzendt_cart`
									(extra_shipping, id_cart, shipping_type, shipping_service, parcelshop_id, deliverdate, deliverperiod,
									deliverevening, last_mile_choice, parcelshop_description
									)
									VALUES
									(
									"'.Tools::getValue('extra_costs_shipping').'",
									"'.$this->context->cookie->id_cart.'",
									"'.$shipping_type.'",
									"'.$shipping_service.'",
									"'.Tools::getValue('lastmile_parcelshop_id').'",
									"'.Tools::getValue('lastmile_deliverdate').'",
									"'.Tools::getValue('lastmile_deliverperiod').'",
									"'.Tools::getValue('lastmile_deliverevening').'",
									"'.Tools::getValue('jet_last_mile_choice').'",
									"'.addslashes(Tools::getValue('lastmile_parcelshop_description')).'"
									)');
		*/
	}
	public function hookactionCarrierUpdate($params)
	{
		$jet_carrier = Configuration::get('JETVERZENDT_CARRIER_ID');
		if ($jet_carrier == $params['id_carrier'])
			Configuration::updateValue('JETVERZENDT_CARRIER_ID', $params['carrier']->id);
	}
	public function getOrderWeight($id_order)
	{
		$weight = 0;
		$products = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'order_detail` WHERE id_order="'.(int)$id_order.'"');
		foreach ($products as $product)
			$weight += $product['product_weight'] * $product['product_quantity'];
		return $weight;
	}
}
