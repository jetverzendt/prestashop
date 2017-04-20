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
//white screen or errors during install, try saving this file as Encoding UTF-8
if (!defined('_PS_VERSION_'))
	exit;

class Keendelivery extends Module
{
    //TODO: vertalingen, database/tabellen controle & database update bij addshipment functie
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'keendelivery';
		$this->tab = 'shipping_logistics';
		$this->version = '1.2.0';
		$this->author = 'NoviSites.nl';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		$this->displayName = $this->l('KeenDelivery');
		$this->description = $this->l('KeenDelivery connector');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        parent::__construct();
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
        Configuration::updateValue('JETVERZENDT_SERVICE', false);
		Configuration::updateValue('JETVERZENDT_ORDER_STATE', 1);
		Configuration::updateValue('JETVERZENDT_PRINT_SIZE', false);
		Configuration::updateValue('JETVERZENDT_DHL_SEND', false);
		Configuration::updateValue('JETVERZENDT_DPD_SEND', 2);
		Configuration::updateValue('JETVERZENDT_CARRIER_ID', false);
        Configuration::updateValue('KEENDELIVERY_AMOUNT', 1);
        Configuration::updateValue('KEENDELIVERY_WEIGHT', 1);
        Configuration::updateValue('KEENDELIVERY_BCC_EMAIL', false);
        Configuration::updateValue('KEENDELIVERY_PACKAGETYPE_DPD', 2);
        Configuration::updateValue('KEENDELIVERY_AUTOPROCESSENABLE', false);
        Configuration::updateValue('KEENDELIVERY_SHIPPINGMETHODS', 0);

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

        $override_admin_dir = _PS_ROOT_DIR_."/override/controllers/admin";

        if (!is_writable($override_admin_dir)) {
            $this->_errors[] = Tools::displayError('Unable to install the module (Folder: /override/controllers/admin is not writeable).');
            return false;
        }

        $dest_dir = $override_admin_dir . '/templates/orders/helpers/list';
        if (!is_dir($dest_dir)) {
            @mkdir($dest_dir, 0777, true);
        }
        $src_dir = dirname(__FILE__) . '/override/controllers/admin/templates/orders/helpers/list';

        copy($src_dir . '/list_header.tpl', $dest_dir . '/list_header.tpl');

        $classIndexCache = _PS_CACHE_DIR_.'class_index.php';
        if (is_file($classIndexCache)) {
            @ unlink($classIndexCache);
        }

		return parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('backOfficeHeader')
			&& $this->registerHook('displayAdminOrder')
			&& $this->registerHook('displayCarrierList')
			//&& $this->registerHook('actionCartSave')
			&& $this->registerHook('actionCarrierUpdate')
            && $this->registerHook('actionValidateOrder');
	}

	public function uninstall()
	{
		Configuration::deleteByName('JETVERZENDT_CLIENT_ID');
		Configuration::deleteByName('JETVERZENDT_GOOGLE_KEY');
		Configuration::deleteByName('JETVERZENDT_STATUS');
		Configuration::deleteByName('JETVERZENDT_CLIENT_SECRET');
		Configuration::deleteByName('JETVERZENDT_CLIENT_LABEL');
		Configuration::deleteByName('JETVERZENDT_SHIPPER');
        Configuration::deleteByName('JETVERZENDT_SERVICE');
        Configuration::deleteByName('KEENDELIVERY_AMOUNT');
        Configuration::deleteByName('KEENDELIVERY_WEIGHT');
        Configuration::deleteByName('KEENDELIVERY_BCC_EMAIL');
        Configuration::deleteByName('KEENDELIVERY_PACKAGETYPE_DPD');

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

        $override_admin_dir = _PS_ROOT_DIR_."/override/controllers/admin";
        $src_dir = $override_admin_dir . '/templates/orders/helpers/list';
        if (is_file($src_dir . '/list_header.tpl')) {
            unlink($src_dir . '/list_header.tpl');
        }

        $classIndexCache = _PS_CACHE_DIR_.'class_index.php';
        if (is_file($classIndexCache)) {
            @ unlink($classIndexCache);
        }

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
		if (((bool)Tools::isSubmit('submitKeendeliveryModule')) == true)
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
		$label_type = Configuration::get('JETVERZENDT_CLIENT_LABEL');
		$label_size = Configuration::get('JETVERZENDT_PRINT_SIZE');
		$order_state = Configuration::get('JETVERZENDT_ORDER_STATE');
		$shipper = Configuration::get('JETVERZENDT_SHIPPER');

		$order = new Order($id_order);
		$customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);

        //set the recieved $postvalues/shippingdata to $info
        //$info = $post;
        $info = array();
        foreach($post as $key => $value) {
            if (isset($value)){
                $info[$key] = $value;
            }
        }

		if (Validate::isLoadedObject($address))
		{
            if ($address->company != '') {
                $info['company_name'] = $address->company;
            }
            elseif ($address->company == '' && $customer->company != '') {
                $info['company_name'] = $customer->company;
            }
            else {
                $info['company_name'] = $address->firstname . ' ' . $address->lastname;
            }
            $info['contact_person'] = $address->firstname . ' ' . $address->lastname;
            $info['street_line_1'] = $address->address1;
            $info['number_line_1'] = $address->address2;//$this->clean($address->address2);

            // format streetline data
            if (empty($address->address2)) { // if no/empty street 1, try to split street 0 field.
                $streetArr = explode(' ', $address->address1);
                if (count($streetArr) >= 2) {
                    $info['number_line_1'] = end($streetArr);
                    $info['street_line_1'] = implode(' ',
                        array_slice($streetArr, 0, -1));
                }
            }

            $info['zip_code'] = str_replace(' ', '', $address->postcode);
            $info['city'] = $address->city;
            if($address->id_country != '' && $address->id_country > 0){
                $country_info = new Country($address->id_country);
                $info['country'] = Tools::strtolower($country_info->iso_code);
            }
            $info['country'] = strtoupper($info['country']);
            if($address->phone_mobile != ''){
                $info['phone'] =$address->phone_mobile;
            }else if($address->phone != ''){
                $info['phone'] = $address->phone;
            }
            $info['email'] = $customer->email;
            $info['input_source'] = 'prestashop';

            $label = array('type' => $label_type);
            if ($label_type == 'PDF')
                $label['size'] = $label_size;
            $info['label'] = $label;
            if(empty($info['reference'])){
                $info['reference'] = 'Order: ' . $id_order;
            }
            $errors = '';
		}
		else
			$errors_arr[] = $this->l('No address for this shipment');
		if (count($errors_arr) == 0)
		{
            $order_data = Tools::jsonEncode($info);
			$testmode = Configuration::get('JETVERZENDT_STATUS');
			if ($testmode == 0) $apiurl = 'http://testportal.keendelivery.com';
			else $apiurl = 'https://portal.keendelivery.com';
			$ch = curl_init($apiurl.'/api/v2/shipment?api_token='.$api_key);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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


			$shipment_id = '';
            $label = '';
            $track_and_trace_code = '';
            $track_and_trace_url = '';
			if (isset($result['shipment_id'])) $shipment_id = $result['shipment_id'];
			if (isset($result['label'])) $label = $result['label'];
			if (isset($result['track_and_trace'])){
				$array = Tools::jsonDecode(Tools::jsonEncode($result['track_and_trace']), true);
				foreach ($array as $k => $item){
					$track_and_trace_code = $k;
					$track_and_trace_url = $item;
				}
			}
			if ($shipment_id == '') $errors_arr[] = 'error';

			$shippings = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'keendelivery` WHERE id_order="'.$id_order.'"');
			if ($shipment_id != '')//has succesfully send a shipment set informatie i.e. track&trace into the database
			{
				if ($info['product'] == 'DPD')
				{
					if (count($shippings) > 0)
						Db::getInstance()->execute('
								UPDATE `'._DB_PREFIX_.'keendelivery` SET 
								parcelshop_id="0", 
								shipping_type="1",                                                      
								date="'.date('Y-m-d H:i:s').'",
								shipment_id="'.$shipment_id.'",	
								label="'.$label.'",	
								track_and_trace_code="'.$track_and_trace_code.'",
								track_and_trace_url="'.$track_and_trace_url.'", 
								shipping_service="'. $info['service'] . '",
								option_1_quantity="'. $info['amount'] . '", 
								option_1_reference="'. $info['reference'] . '",
								option_1_mail="'. $info['predict'] . '", 
								option_1_saturday_delivery="'. $info['saturday_delivery'] . '",
								option_1_pickup_delivery="0",                          
								option_1_pickup_date="'. $info['pickup_date'] . '",
								option_1_amount="'. (empty($info['cod']) ? $info['cod']: '') .'"                                     
								WHERE id_order="'.$id_order.'"');
					else
						Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'keendelivery` 
								(shipping_type, id_order, date, shipment_id, label, track_and_trace_code, track_and_trace_url, shipping_service, option_1_quantity,
								option_1_reference, option_1_mail, option_1_saturday_delivery, option_1_pickup_delivery, option_1_pickup_date,option_1_amount ) 
								VALUES 
								("1", 
								"'.$id_order.'", 
								"'.date('Y-m-d H:i:s').'", 
								"'.$shipment_id.'",
								"'.$label.'", 
								"'.$track_and_trace_code.'", 
								"'.$track_and_trace_url.'", 
								"", "", "", "", "", "", "", ""
								)');
				}
				else
				{
                    $product_id = 5;
				    if($info['product'] == 'Fadello' || $info['product'] == 'NextDayPremium') {
                        $product_id = 4;
                    }
				    if($info['product'] == 'DHL') {
                        $product_id = 2;
                    }
					if (count($shippings) > 0)
						Db::getInstance()->execute('
								UPDATE `'._DB_PREFIX_.'keendelivery` SET 
								shipping_type="'.$product_id.'", 
								date="'.date('Y-m-d H:i:s').'",
								shipment_id="'. $shipment_id .'",	
								label="'.$label.'",	
								track_and_trace_code="'.$track_and_trace_code.'",
								track_and_trace_url="'.$track_and_trace_url.'", 
								shipping_service="'. $info['service'] . '", 
								option_2_quantity="'. $info['amount'] . '", 
								option_2_reference="'. $info['reference'] . '", 
								option_3_weight="'. $info['weight'] . '",
								option_1_weight="'. $info['weight'] . '",
								option_2_weight="'. $info['weight'] . '", 
								option_2_pickup_delivery="0", 
								option_2_pickup_date="'. $info['pickup_date'] . '", 
								option_3_pickup_date="'. $info['pickup_date'] . '", 
								option_2_insured_value="'. (empty($info['insurance']) ? $info['insurance']: '') .'", 
								option_2_saturday_delivery="'. (empty($info['saturday']) ? $info['saturday']: '') .'", 
								option_2_amount="'. (empty($info['cod']) ? $info['cod']: '') .'", 
								option_2_signature="'. (empty($info['signature_required']) ? $info['signature_required']: '') .'", 
								option_2_no_neighbors="'. (empty($info['not_by_neighbours']) ? $info['not_by_neighbours']: '') .'", 
								option_2_evening="'. (empty($info['evening_delivery']) ? $info['evening_delivery']: '') .'", 
								option_2_extra_cover="'. (empty($info['extra_insurance']) ? $info['extra_insurance']: '') .'"
								WHERE id_order="'.$id_order.'"');
					else
						Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'keendelivery` 
								(shipping_type, id_order, date, shipment_id, label, track_and_trace_code, track_and_trace_url, shipping_service,
								option_2_quantity, option_2_reference, option_2_weight, option_2_pickup_delivery, option_2_pickup_date,
								option_2_insured_value, option_2_saturday_delivery, option_2_amount, option_2_signature, option_2_no_neighbors,
								option_2_evening, option_2_extra_cover, option_3_weight, option_1_weight) 
								VALUES 
								("'.$product_id.'", 
								"'.$id_order.'", 
								"'.date('Y-m-d H:i:s').'", 
								"'.$shipment_id.'",
								"'.$label.'", 
								"'.$track_and_trace_code.'", 
								"'.$track_and_trace_url.'", 
								"", "", "", "", "", "", "", "", "", "", "", "", "", "", ""
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
			else//return errors
			{
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
		$helper->submit_action = 'submitKeendeliveryModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm())).$helper->generateForm(array($this->getConfigFormAutoprocess())).$helper->generateForm(array($this->getConfigFormLastMile()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
        $default_service = Configuration::get('JETVERZENDT_SERVICE');
		$select_values = array();
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'PDF', 'name' => $this->l('PDF')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'ZPL', 'name' => $this->l('ZPL')));
		array_push($select_values, array('JETVERZENDT_CLIENT_LABEL' => 'BAT', 'name' => $this->l('BAT')));
        $shipping_methods = json_decode($this->get_shipping_methods(true));
        $shipper_values = array();
        if($shipping_methods) {
            foreach ($shipping_methods as $method) {
                array_push($shipper_values, array('JETVERZENDT_SHIPPER' => $method->value, 'name' => $method->text));
            }
        }
        $service_values = array();
        if($shipping_methods) {
            foreach ($shipping_methods as $method) {
                foreach ($method->services as $service) {
                    if ($service->value === $default_service) {
                        array_push($service_values, array('JETVERZENDT_SERVICE' => $service->value . '" class="keendelivery_fields ' . $method->value . '" selected="selected', 'name' => $service->text));
                    } else {
                        array_push($service_values, array('JETVERZENDT_SERVICE' => $service->value . '" class="keendelivery_fields ' . $method->value, 'name' => $service->text));
                    }
                }
            }
        }

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
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '2', 'name' => $this->l('Receive information by e-mail')));
		array_push($dps_send, array('JETVERZENDT_DPD_SEND' => '1', 'name' => $this->l('Receiver information by SMS')));

        $dpd_packagetype = array();
        array_push($dpd_packagetype, array('KEENDELIVERY_PACKAGETYPE_DPD' => '1', 'name' => $this->l('Klein pakket')));
        array_push($dpd_packagetype, array('KEENDELIVERY_PACKAGETYPE_DPD' => '2', 'name' => $this->l('Normaal pakket')));

		$carriers_arr = array();
		$carriers = Carrier::getCarriers($this->context->language->id, true);
		foreach ($carriers as $carrier)
			array_push($carriers_arr, array('JETVERZENDT_CARRIER_ID' => $carrier['id_carrier'], 'name' => $carrier['name']));

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
						'desc' => $this->l('Enter the authentication token from the KeenDelivery portal'),
						'name' => 'JETVERZENDT_CLIENT_ID',
						'label' => $this->l('Authentication token'),
					),
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
                        'onchange' => 'set_keen_shipment_and_order_fields();',
						'options' => array(
							'query' => $shipper_values,
							'id' => 'JETVERZENDT_SHIPPER',
							'name' => 'name'
						)
					),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Default service'),
                        'name' => 'JETVERZENDT_SERVICE',
                        'required' => false,
                        'col' => '4',
                        'options' => array(
                            'query' => $service_values,
                            'id' => 'JETVERZENDT_SERVICE',
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
						)
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
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'KEENDELIVERY_AMOUNT',
                        'label' => $this->l('Aantal'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'KEENDELIVERY_WEIGHT',
                        'label' => $this->l('Gewicht'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'KEENDELIVERY_BCC_EMAIL',
                        'label' => $this->l('Standaard BCC e-mailadres'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('packet formaat (DPD)'),
                        'name' => 'KEENDELIVERY_PACKAGETYPE_DPD',
                        'required' => false,
                        'col' => 4,
                        'options' => array(
                            'query' => $dpd_packagetype,
                            'id' => 'KEENDELIVERY_PACKAGETYPE_DPD',
                            'name' => 'name'
                        )
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
						'desc' => $this->l('Carrier to which we will attach the shipping options')
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

	protected function getConfigFormAutoprocess(){
	    $autoprocess_enabled = Configuration::get('KEENDELIVERY_AUTOPROCESSENABLE');
        $autoprocess_active = array();
        if($autoprocess_enabled){
            array_push($autoprocess_active, array('KEENDELIVERY_AUTOPROCESSENABLE' => '1', 'name' => $this->l('Yes')));
        }
        array_push($autoprocess_active, array('KEENDELIVERY_AUTOPROCESSENABLE' => '0', 'name' => $this->l('No')));

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Auto process. Warning, this part is still in test phase!'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'KEENDELIVERY_AUTOPROCESSVALUE',
                        'disabled' => 'false',
                        'label' => $this->l('Auto process settings'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Autoprocess actief'),
                        'name' => 'KEENDELIVERY_AUTOPROCESSENABLE',
                        'required' => false,
                        'col' => '4',
                        'options' => array(
                            'query' => $autoprocess_active,
                            'id' => 'KEENDELIVERY_AUTOPROCESSENABLE',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('To enable this function, go to orders, bulk action, create shipment and select the preferred shipping settings')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'form_autoprocess',
                ),
            ),
        );

        return $form;
    }

	protected function getConfigFormLastmile()
	{
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
            'JETVERZENDT_SERVICE' => Configuration::get('JETVERZENDT_SERVICE'),
			'JETVERZENDT_ORDER_STATE' => Configuration::get('JETVERZENDT_ORDER_STATE'),
			'JETVERZENDT_PRINT_SIZE' => Configuration::get('JETVERZENDT_PRINT_SIZE'),
			'JETVERZENDT_DHL_SEND' => Configuration::get('JETVERZENDT_DHL_SEND'),
			'JETVERZENDT_DPD_SEND' => Configuration::get('JETVERZENDT_DPD_SEND'),
			'JETVERZENDT_CARRIER_ID' => Configuration::get('JETVERZENDT_CARRIER_ID'),
            'KEENDELIVERY_AMOUNT' => Configuration::get('KEENDELIVERY_AMOUNT'),
            'KEENDELIVERY_WEIGHT' =>  Configuration::get('KEENDELIVERY_WEIGHT'),
            'KEENDELIVERY_BCC_EMAIL' => Configuration::get('KEENDELIVERY_BCC_EMAIL'),
            'KEENDELIVERY_PACKAGETYPE_DPD' => Configuration::get('KEENDELIVERY_PACKAGETYPE_DPD'),
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
            'KEENDELIVERY_AUTOPROCESSVALUE' => Configuration::get('KEENDELIVERY_AUTOPROCESSVALUE'),
            'KEENDELIVERY_AUTOPROCESSENABLE' => Configuration::get('KEENDELIVERY_AUTOPROCESSENABLE')
		);
	}

	/**
	 * Save form data.
	 */
	protected function postProcess()
	{
		if (Tools::getValue('form_settings'))
		{
			Configuration::updateValue('JETVERZENDT_CLIENT_ID', Tools::getValue('JETVERZENDT_CLIENT_ID'));
			Configuration::updateValue('JETVERZENDT_GOOGLE_KEY', Tools::getValue('JETVERZENDT_GOOGLE_KEY'));
			Configuration::updateValue('JETVERZENDT_STATUS', Tools::getValue('JETVERZENDT_STATUS'));
			Configuration::updateValue('JETVERZENDT_CLIENT_SECRET', Tools::getValue('JETVERZENDT_CLIENT_SECRET'));
			Configuration::updateValue('JETVERZENDT_CLIENT_LABEL', Tools::getValue('JETVERZENDT_CLIENT_LABEL'));
			Configuration::updateValue('JETVERZENDT_SHIPPER', Tools::getValue('JETVERZENDT_SHIPPER'));
            Configuration::updateValue('JETVERZENDT_SERVICE', Tools::getValue('JETVERZENDT_SERVICE'));
			Configuration::updateValue('JETVERZENDT_ORDER_STATE', Tools::getValue('JETVERZENDT_ORDER_STATE'));
			Configuration::updateValue('JETVERZENDT_PRINT_SIZE', Tools::getValue('JETVERZENDT_PRINT_SIZE'));
			Configuration::updateValue('JETVERZENDT_DHL_SEND', Tools::getValue('JETVERZENDT_DHL_SEND'));
			Configuration::updateValue('JETVERZENDT_DPD_SEND', Tools::getValue('JETVERZENDT_DPD_SEND'));
			Configuration::updateValue('JETVERZENDT_CARRIER_ID', Tools::getValue('JETVERZENDT_CARRIER_ID'));
            Configuration::updateValue('KEENDELIVERY_AMOUNT', Tools::getValue('KEENDELIVERY_AMOUNT'));
            Configuration::updateValue('KEENDELIVERY_WEIGHT', Tools::getValue('KEENDELIVERY_WEIGHT'));
            Configuration::updateValue('KEENDELIVERY_BCC_EMAIL', Tools::getValue('KEENDELIVERY_BCC_EMAIL'));
            Configuration::updateValue('KEENDELIVERY_PACKAGETYPE_DPD', Tools::getValue('KEENDELIVERY_PACKAGETYPE_DPD'));
		}elseif (Tools::getValue('form_autoprocess')){
            Configuration::updateValue('KEENDELIVERY_AUTOPROCESSENABLE', Tools::getValue('KEENDELIVERY_AUTOPROCESSENABLE'));
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
		if (!empty($google_key))
			$this->context->controller->addJS('https://maps.googleapis.com/maps/api/js?sensor=false&key='.$google_key);
		else $this->context->controller->addJS('https://maps.googleapis.com/maps/api/js?sensor=false');
		$this->context->controller->addJS($this->_path.'/views/js/novijetverzendt.js');
		$this->context->controller->addCSS($this->_path.'/views/css/novijetverzendt.css');
	}

	//normal shipping function/screen
	public function getShipmentInfoList()
	{
		$this->html = '';
        $orders = array();

        $shipment_method =  Configuration::get('JETVERZENDT_SHIPPER');
        $shipment_service = Configuration::get('JETVERZENDT_SERVICE');
		$DHL_send = Configuration::get('JETVERZENDT_DHL_SEND');
        $DPD_send = Configuration::get('JETVERZENDT_DPD_SEND');
        $amount = Configuration::get('KEENDELIVERY_AMOUNT');
        $weight = Configuration::get('KEENDELIVERY_WEIGHT');
        $bcc_email = Configuration::get('KEENDELIVERY_BCC_EMAIL');
        $package_type_dpd = Configuration::get('KEENDELIVERY_PACKAGETYPE_DPD');

        $shipping_methods = $this->get_shipping_methods();
        $this->html .= '
					<fieldset>
						<div class="row">
                            <div class="col-md-12">
							<div class="panel  form-horizontal">
							<div class="panel-heading"><i class="icon-truck "></i> Shipment</div>
							
                                <div id="shipment_form"></div>
                                <script>
                                var shipping_methods = '.$shipping_methods.';
                                var default_method = "'.$shipment_method.'";
                                var default_service = "'.$shipment_service.'";
                                var amount_packs = "'.$amount.'";
                                var DHL_send = "'.$DHL_send.'";
                                var default_predict = "'.$DPD_send.'";
                                var default_weight = "'.$weight.'";
                                var default_email = "'.$bcc_email.'";
                                var package_type_dpd = "'.$package_type_dpd.'";
                                
                                $( document ).ready(function() {
                                    function generate_shipment_form() {
                                        var form = \'\';
                                
                                        form += generate_product_dropdown();
                                
                                        form += generate_service_field();
                                
                                        form += generate_amount_field();
                                
                                        form += generate_reference_field();
                               
                                        form += generate_service_options();
                                
                                        $(\'#shipment_form\').html(form);
                                
                                        set_keen_services();
                                    }
                                    
                                    generate_shipment_form();
                                
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
                                });
                                
                                function generate_product_dropdown() {
                                    result = \'<div class="form-group">\';
                                    result += \'<label class="control-label col-lg-3">Vervoerder</label>\';
                                    result += \'<div class="col-lg-9">\';
                                    result += \'<select id="keen_product" name="product" onchange="set_keen_services()">\';
                            
                                    if (Object.keys(shipping_methods).length > 0) {
                                        for (var k in shipping_methods) {
                                            if (typeof shipping_methods[k] !== \'function\') {
                                                var selected = (default_method == shipping_methods[k][\'value\'] ? \'selected\' : \'\');
                                                result += \'<option value="\' + shipping_methods[k][\'value\'] + \'"\'
                                                + selected
                                                + \'>\' + shipping_methods[k][\'text\'] + \'</option>\';
                                            }
                                        }
                                    }
                                    result += \'</select>\';
                                    result += \'</div></div>\';
                            
                                    return result;
                                }
                                
                                function generate_service_field() {
                                    result = \'<div class="form-group">\';
                                    result += \'<label class="control-label col-lg-3">Service</label>\';
                                    result += \'<div class="col-lg-9">\';
                                    result += \'<select id="keen_service" name="service" onchange="set_keen_service_options()">\';
                                    result += \'</select>\';
                                    result += \'</div></div>\';
                            
                                    return result;
                                }
                            
                                function generate_amount_field() {
                                    result = \'<div class="form-group">\';
                                    result += \'<label class="control-label col-lg-3">Aantal pakketten</label>\';
                                    result += \'<div class="col-lg-9">\';
                                    result += \'<input type="text" name="amount" id="keen_amount" value="\' + amount_packs + \'"/>\';
                                    result += \'</div></div>\';
                            
                                    return result;
                                }
                            
                                function generate_reference_field() {
                                    result = \'<div class="form-group">\';
                                    result += \'<label class="control-label col-lg-3">Referentie</label>\';
                                    result += \'<div class="col-lg-9">\';
                                    result += \'<input type="text" name="reference" id="keen_reference" placeholder="Order: order_nr"/>\';
                                    result += \'</div></div>\';
                            
                                    return result;
                                }
                            
                                function generate_service_options() {
                                    result = \'<div id="keen_service_options"></div>\';
                                    return result;
                                }
                            
                                function set_keen_services() {
                                    var current_product = $(\'#keen_product\').val();
                                    result = \'\';
                            
                                    if (Object.keys(shipping_methods).length > 0) {
                                        for (var k in shipping_methods) {
                                            if (typeof shipping_methods[k] !== \'function\') {
                                                if (shipping_methods[k].value == current_product) {
                            
                                                    if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                        for (var i in shipping_methods[k][\'services\']) {
                                                            if (typeof shipping_methods[k][\'services\'][i] !== \'function\') {
                                                                var selected_services = (default_service == shipping_methods[k][\'services\'][i][\'value\'] ? \' selected\' : \'\');
                                                                result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'value\'] + \'"\'
                                                                + selected_services
                                                                + \'>\' + shipping_methods[k][\'services\'][i][\'text\'] + \'</option>\';
                                                            }
                                                        }
                            
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $(\'#keen_service\').html(\'\');
                                    $(\'#keen_service\').html(result);
                            
                                    set_keen_service_options();
                                }
                                
                                function set_keen_service_options() {
                                    var current_product = $(\'#keen_product\').val();
                                    var keen_service = $(\'#keen_service\').val();
                                    result = \'\';
                            
                                    if (Object.keys(shipping_methods).length > 0) {
                                        for (var k in shipping_methods) {
                                            if (typeof shipping_methods[k] !== \'function\') {
                                                if (shipping_methods[k].value == current_product) {
                            
                                                    if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                        for (var i in shipping_methods[k][\'services\']) {
                                                            if (shipping_methods[k][\'services\'][i].value == keen_service) {
                            
                                                                if (Object.keys(shipping_methods[k][\'services\'][i][\'options\']).length > 0) {
                                                                    for (var j in shipping_methods[k][\'services\'][i][\'options\']) {
                            
                                                                        if (typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] != \'hidden\') {
                                                                            result += \'<div class="form-group">\';
                                                                            result += \'<label class="control-label col-lg-3">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'text\'] + \'</label>\';
                                                                            result += \'<div class="col-lg-9">\';
                                                                            type = shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'];
                            
                                                                            if (type == \'selectbox\') {
                                                                                result += \'<select \';
                                                                                if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                    result += \' required \';
                                                                                }
                                                                                result += \' name="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'">\';
                            
                                                                                if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 0) {
                                                                                    result += \'<option value="">Kies evt. een optie</option>\';
                                                                                }
                            
                                                                                if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                    for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                        if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                            result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] + \'</option>\';
                                                                                        }
                                                                                    }
                                                                                }
                                                                                result += \'</select>\';
                                                                            }
                            
                            
                                                                            if (type == \'radio\') {
                                                        
                                                                                
                                                                                if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                    for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                        if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                            result += \'<label style="padding-left: 0">\';
                                                                                            result += \'<input type="radio" style="margin-left: 0"\';
                                                                                            result += \' name="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" \';
                                                                                            if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                                result += \' required \';
                                                                                            }
                                                                                            result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\'  + \'</input>\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] +\'&nbsp;&nbsp;&nbsp;</label>\';
                                                                                        }
                                                                                    }
                                                                                }
                            
                                                                            }
                            
                            
                                                                            if (type == \'checkbox\') {
                            
                                                                                result += \'<input type="checkbox" \';
                                                                                if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                    result += \' required \';
                                                                                }
                            
                                                                                result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                            
                                                                            }
                            
                            
                                                                            if (type == \'textbox\' || type == \'date\' || type == \'email\') {
                            
                                                                                result += \'<input type="text" \';
                            
                                                                                if (type == \'textbox\') {
                                                                                    result += \' type="text" \';
                                                                                } else if (type == \'email\') {
                                                                                    result += \' type="email" \';
                                                                                } else if (type == \'date\') {
                                                                                    result += \'class="datepicker_special" type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" \';
                                                                                }
                            
                            
                                                                                if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                    result += \' required \';
                                                                                }
                            
                                                                                result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                            
                                                                            }
                            
                                                                            result += \'</div></div>\';
                                                                        }
                                                                        if(typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] == \'hidden\'){
                                                                            result += \'<div class="form-group">\';
                                                                            result += \'<input type="hidden"\';
                                                                            result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][\'value\'] +\'"\';
                                                                            result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                                                                            result += \'</div>\';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                            
                                    $(\'#keen_service_options\').html(\'\');
                                    $(\'#keen_service_options\').html(result);
                                    
                                    set_keen_default_options();
                                }
                                
                                function set_keen_default_options(){
                                    jQuery(\'.datepicker_special\').datepicker({dateFormat: \'dd-mm-yy\'});
                                    var elements = document.getElementsByTagName("input");
                                    $(\'#shipment_form\').get
                                    for (var i = 0; i < elements.length; i++) {
                                        if(elements[i].name == "weight") {
                                            elements[i].value = ( default_weight != \'\' ? default_weight : \'\');
                                        }
                                        if(elements[i].name == "bcc_email") {
                                            elements[i].value = ( default_email != \'\' ? default_email : \'\');
                                        }
                                        if(elements[i].name == "product_type" && elements[i].value == package_type_dpd) {
                                            elements[i].checked = true;
                                        }
                                        if(elements[i].name == "send_track_and_trace_email"){
                                            if(DHL_send == true){
                                                elements[i].checked = true;
                                            }
                                        }
                                    }
                                    var select_elements = document.getElementsByTagName("select");
                                    for (var e = 0; e < select_elements.length; e++) {
                                        if(select_elements[e].name == "predict"){
                                            for(var b = 0; b < select_elements[e].length; b++){
                                                if(select_elements[e].options[b].value == default_predict){
                                                    select_elements[e].options[b].selected = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            </script>
                            <div class="clearfix"></div>
                        </div>  
                    </div>
                    </div>
                </fieldset>
            ';

		return $this->html;
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

    public function get_shipping_methods($update = false)
    {
        $shipping_methods = '';
        $api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
        $status = Configuration::get('JETVERZENDT_STATUS');
        //session_start();
//        if (isset($_SESSION['shipping_methods']) && is_array($_SESSION['shipping_methods'])) {
//            $shipping_methods = $_SESSION['shipping_methods'];
        $config_shipping_methods = Configuration::get('KEENDELIVERY_SHIPPINGMETHODS');
        if(!empty($config_shipping_methods && $update == false)){
            $shipping_methods = json_decode(Configuration::get('KEENDELIVERY_SHIPPINGMETHODS'));
        } else {
            if($status == 0){
                $url = 'http://testportal.keendelivery.com/api/v2/shipping_methods?api_token=' . $api_key . '&source=prestashop';
            }else {
                $url = 'https://portal.keendelivery.com/api/v2/shipping_methods?api_token=' . $api_key . '&source=prestashop';
            }
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            );
            $shipping_methods             = json_decode(curl_exec($ch));
            $shipping_methods             = (isset($shipping_methods->shipping_methods)) ? (array)$shipping_methods->shipping_methods : false;
            //$_SESSION['shipping_methods'] = $shipping_methods;
            Configuration::updateValue('KEENDELIVERY_SHIPPINGMETHODS', json_encode($shipping_methods));
        }
        return json_encode($shipping_methods);
    }

    //functionname has still previous version name, this is actual the lastmile part.
    public function getShipmentInfoListDefault($id_order = 0)
    {
        $shipping_methods = $this->get_shipping_methods();
        $shipment_method = array();
        $shipment_service = array();
        $DHL_send = array();
        $DPD_send = array();
        $amount = array();
        $weight = array();
        $bcc_email = array();
        $package_type_dpd = array();
        $is_lastmile = array();
        $lastmile = array();
        foreach (Tools::getValue('orderBox') as $id_order) {
            $shipment_method[$id_order] =  Configuration::get('JETVERZENDT_SHIPPER');
            $shipment_service[$id_order] = Configuration::get('JETVERZENDT_SERVICE');
            $DHL_send[$id_order] = Configuration::get('JETVERZENDT_DHL_SEND');          //Track&trace
            $DPD_send[$id_order] = Configuration::get('JETVERZENDT_DPD_SEND');          //Predict
            $weight[$id_order] = (ceil($this->getOrderWeight($id_order)) > 0? ceil($this->getOrderWeight($id_order)) : Configuration::get('KEENDELIVERY_WEIGHT'));
            $amount[$id_order] = Configuration::get('KEENDELIVERY_AMOUNT');
            $bcc_email[$id_order] = Configuration::get('KEENDELIVERY_BCC_EMAIL');
            $package_type_dpd[$id_order] = Configuration::get('KEENDELIVERY_PACKAGETYPE_DPD');

            //Lastmile values
            $order = new Order($id_order);
            $cart_shippings = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$order->id_cart.'"');
            if (count($cart_shippings) > 0) {
                $is_lastmile[$id_order] = 1;
                $shipment_method[$id_order] = $cart_shippings[0]['shipping_service']; //set shipping_method DPD,DHL,Fadello,NextDayPremium
                if(!empty($cart_shippings[0]['parcelshop_id'])){
                    if ($cart_shippings[0]['shipping_service'] === 'DPD') {
                        $shipment_service[$id_order] = 'PARCELSHOP';
                    }
                    elseif ($cart_shippings[0]['shipping_service'] === 'DHL') {
                        $shipment_service[$id_order] = 'PARCEL_SHOP';
                    }
                    $lastmile[$id_order]['parcel_shop_id'] = $cart_shippings[0]['parcelshop_id'];
                }else {
                    if ($cart_shippings[0]['shipping_service'] == 'DHL' && $cart_shippings[0]['deliverperiod'] != '') {
//                        if ($cart_shippings[0]['deliverperiod'] == '09:00-13:00' || $cart_shippings[0]['deliverperiod'] == '11:00-15:00') {
//                            $shipment_service[$id_order] = 'E10';
//                        }
//                        if ($cart_shippings[0]['deliverperiod'] == '14:00-18:00') {
//                            $shipment_service[$id_order] = 'E12';
//                        }
                        if ($cart_shippings[0]['deliverperiod'] == '18:00-21:00') {
                            $lastmile[$id_order]['evening'] = 1;
//                            $shipment_service[$id_order] = 'E18';
                        }
//                        else {
                            $shipment_service[$id_order] = 'DHL_FOR_YOU';
//                        }
                    }
                    if ($cart_shippings[0]['shipping_service'] == 'DPD' && $cart_shippings[0]['deliverdate'] != '0000-00-00') {
                        $shipment_service[$id_order] = 'CL';
                        $lastmile[$id_order]['saturday_delivery'] = 1;
                    }
                    $lastmile[$id_order]['pickup_date'] = $cart_shippings[0]['deliverdate'];
                    if ($lastmile[$id_order]['pickup_date'] != '0000-00-00' && $lastmile[$id_order]['pickup_date'] != '1970-01-01') {
                        $lastmile[$id_order]['pickup_date'] = date('d-m-Y', strtotime($lastmile[$id_order]['pickup_date']));
                    }
                    else $lastmile[$id_order]['pickup_date'] = '';
                }
            }else{
                $is_lastmile[$id_order] = 0;
            }
        }
        $this->html = '';
        //set extra text at each shipping if it is a lastmile
        foreach (Tools::getValue('orderBox') as $id_order)
        {
            $selected_shipping_text = '';
            $order = new Order($id_order);
            $cart_shippings = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$order->id_cart.'"');
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
                    }//Capitals where used in previous version, still in here to prevent issues in case of overwriting
                    else if ($cart_shippings[0]['shipping_service'] == 'FADELLO' || $cart_shippings[0]['shipping_service'] == 'Fadello')
                        $selected_shipping_text = $this->l('Same Day Delivery');
                    else $selected_shipping_text = $this->l('Next Day Premium');
            }

            $this->html .= '
                <fieldset>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="keen-form panel form-horizontal" style="margin:0;">
                                '.(($id_order != 0)?'<input type="hidden" name="id_order" value="'.$id_order.'">':'').'
                                <input type="hidden" name="is_lastmile" value="'. $is_lastmile[$id_order] .'">
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
                                        <div id="shipment_form"></div>
                                        <button type="button" class="btn btn-default" onclick="set_default_to_all(this)">'.$this->l('Set for all, except lastmile').'</button>
                                </div>
                                
                                <div class="clearfix"></div>
                            </div>
                        </div> 
                    </div>
                </fieldset>
            ';
        }

        $this->html .= '<script>
                            var default_method = '.json_encode($shipment_method).';
                            var default_service = '.json_encode($shipment_service).';
                            var amount_packs = '.json_encode($amount).';
                            var DHL_send = '.json_encode($DHL_send).';
                            var default_predict = '.json_encode($DPD_send).';
                            var default_weight = '.json_encode($weight).';
                            var default_email = '.json_encode($bcc_email).';
                            var package_type_dpd = '.json_encode($package_type_dpd).';
                            var last_mile = '.json_encode($is_lastmile).';
                            var last_mile_data = '.json_encode($lastmile).';
                            var shipping_methods = '.$shipping_methods.';
                            $( document ).ready(function() {
                                $("input[name=\'id_order\']").each(function(e){
                                    generate_shipment_form($(this).closest(".form-horizontal"));
                                });
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
                                function generate_shipment_form(closest_form) {
                                    var form = \'\';
                                    form += generate_product_dropdown(closest_form);
                            
                                    form += generate_service_field(closest_form);
                            
                                    form += generate_amount_field(closest_form);
                            
                                    form += generate_reference_field(closest_form);
                           
                                    form += generate_service_options(closest_form);
                                    closest_form.find("#shipment_form").html(form);
                                    set_keen_services(closest_form.find(\'.keen_product\'));
                                }
                            });
                            
                            function generate_product_dropdown(closest_form) {
                            var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'<div class="form-group">\';
                                result += \'<label class="control-label col-lg-3">Vervoerder</label>\';
                                result += \'<div class="col-lg-9">\';
                                result += \'<select class="keen_product" name="shipment[\' + current_order_id + \'][product]" onchange="set_keen_services(this)">\';
                                if (Object.keys(shipping_methods).length > 0) {
                                    for (var k in shipping_methods) {
                                        if (typeof shipping_methods[k] !== \'function\') {
                                            var selected = (default_method[current_order_id] == shipping_methods[k][\'value\'] ? \'selected\' : \'\');
                                            result += \'<option value="\' + shipping_methods[k][\'value\'] + \'"\'
                                            + selected
                                            + \'>\' + shipping_methods[k][\'text\'] + \'</option>\';
                                        }
                                    }
                                }
                                result += \'</select>\';
                                result += \'</div></div>\';
                        
                                return result;
                            }
                            
                            function generate_service_field(closest_form) {
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'<div class="form-group">\';
                                result += \'<label class="control-label col-lg-3">Service</label>\';
                                result += \'<div class="col-lg-9">\';
                                result += \'<select class="keen_service" name="shipment[\' + current_order_id + \'][service]" onchange="set_keen_service_options(this)">\';
                                result += \'</select>\';
                                result += \'</div></div>\';
                        
                                return result;
                            }
                        
                            function generate_amount_field(closest_form) {
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'<div class="form-group">\';
                                result += \'<label class="control-label col-lg-3">Aantal pakketten</label>\';
                                result += \'<div class="col-lg-9">\';
                                result += \'<input type="text" name="shipment[\' + current_order_id + \'][amount]" id="keen_amount" value="\' + amount_packs[current_order_id] + \'"/>\';
                                result += \'</div></div>\';
                        
                                return result;
                            }
                        
                            function generate_reference_field(closest_form) {
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'<div class="form-group">\';
                                result += \'<label class="control-label col-lg-3">Referentie</label>\';
                                result += \'<div class="col-lg-9">\';
                                result += \'<input type="text" name="shipment[\' + current_order_id + \'][reference]" id="keen_reference" placeholder="Order: \' + current_order_id + \'"/>\';
                                result += \'</div></div>\';
                        
                                return result;
                            }
                        
                            function generate_service_options(closest_form) {
                                result = \'<div class="keen_service_options"></div>\';
                                return result;
                            }
                        
                            function set_keen_services(product_list) {
                                var closest_form = $(product_list).closest(".form-horizontal");
                                var current_product = closest_form.find(\'.keen_product\').val();
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'\';
                        
                                if (Object.keys(shipping_methods).length > 0) {
                                    for (var k in shipping_methods) {
                                        if (typeof shipping_methods[k] !== \'function\') {
                                            if (shipping_methods[k].value == current_product) {
                        
                                                if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                    for (var i in shipping_methods[k][\'services\']) {
                                                        if (typeof shipping_methods[k][\'services\'][i] !== \'function\') {
                                                            var selected_services = (default_service[current_order_id] == shipping_methods[k][\'services\'][i][\'value\'] ? \' selected\' : \'\');
                                                            result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'value\'] + \'"\'
                                                            + selected_services
                                                            + \'>\' + shipping_methods[k][\'services\'][i][\'text\'] + \'</option>\';
                                                        }
                                                    }
                        
                                                }
                                            }
                                        }
                                    }
                                }
                                closest_form.find(\'.keen_service\').html(\'\');
                                closest_form.find(\'.keen_service\').html(result);
                        
                                set_keen_service_options(closest_form);
                            }
                            
                            function set_keen_service_options(service_list) {
                                var closest_form =  $(service_list).closest(".form-horizontal");
                                var current_product = closest_form.find(\'.keen_product\').val();
                                var keen_service = closest_form.find(\'.keen_service\').val();
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                result = \'\';
                        
                                if (Object.keys(shipping_methods).length > 0) {
                                    for (var k in shipping_methods) {
                                        if (typeof shipping_methods[k] !== \'function\') {
                                            if (shipping_methods[k].value == current_product) {
                        
                                                if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                    for (var i in shipping_methods[k][\'services\']) {
                                                        if (shipping_methods[k][\'services\'][i].value == keen_service) {
                        
                                                            if (Object.keys(shipping_methods[k][\'services\'][i][\'options\']).length > 0) {
                                                                for (var j in shipping_methods[k][\'services\'][i][\'options\']) {
                        
                                                                    if (typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] != \'hidden\') {
                                                                        result += \'<div class="form-group">\';
                                                                        result += \'<label class="control-label col-lg-3">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'text\'] + \'</label>\';
                                                                        result += \'<div class="col-lg-9">\';
                                                                        type = shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'];
                        
                                                                        if (type == \'selectbox\') {
                                                                            result += \'<select \';
                                                                            if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                result += \' required \';
                                                                            }
                                                                            result += \' name="shipment[\' + current_order_id + \'][\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \']" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'_\' + current_order_id +\'">\';
                        
                                                                            if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 0) {
                                                                                result += \'<option value="">Kies evt. een optie</option>\';
                                                                            }
                        
                                                                            if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                    if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                        result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] + \'</option>\';
                                                                                    }
                                                                                }
                                                                            }
                                                                            result += \'</select>\';
                                                                        }
                        
                        
                                                                        if (type == \'radio\') {
                                                    
                                                                            
                                                                            if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                    if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                        result += \'<label style="padding-left: 0">\';
                                                                                        result += \'<input type="radio" style="margin-left: 0"\';
                                                                                        result += \' name="shipment[\' + current_order_id + \'][\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \']" \';
                                                                                        if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                            result += \' required \';
                                                                                        }
                                                                                        result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\'  + \'</input>\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] +\'&nbsp;&nbsp;&nbsp;</label>\';
                                                                                    }
                                                                                }
                                                                            }
                        
                                                                        }
                        
                        
                                                                        if (type == \'checkbox\') {
                        
                                                                            result += \'<input type="checkbox" \';
                                                                            if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                result += \' required \';
                                                                            }
                        
                                                                            result += \' name="shipment[\' + current_order_id + \'][\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \']" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'_\' + current_order_id +\'" />\';
                        
                                                                        }
                        
                        
                                                                        if (type == \'textbox\' || type == \'date\' || type == \'email\') {
                        
                                                                            result += \'<input type="text" \';
                        
                                                                            if (type == \'textbox\') {
                                                                                result += \' type="text" \';
                                                                            } else if (type == \'email\') {
                                                                                result += \' type="email" \';
                                                                            } else if (type == \'date\') {
                                                                                result += \'class="datepicker_special" type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" \';
                                                                            }
                        
                        
                                                                            if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                result += \' required \';
                                                                            }
                        
                                                                            result += \' name="shipment[\' + current_order_id + \'][\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \']" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'_\' + current_order_id +\'" />\';
                        
                                                                        }
                        
                                                                        result += \'</div></div>\';
                                                                    }
                                                                    if(typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] == \'hidden\'){
                                                                        result += \'<div class="form-group">\';
                                                                        result += \'<input type="hidden"\';
                                                                        result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][\'value\'] +\'"\';
                                                                        result += \' name="shipment[\' + current_order_id + \'][\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \']" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'_\' + current_order_id +\'" />\';
                                                                        result += \'</div>\';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                        
                                $(closest_form).find(\'.keen_service_options\').html(\'\');
                                $(closest_form).find(\'.keen_service_options\').html(result);
                                
                                set_keen_default_options($(closest_form));
                            }
                            
                            function set_keen_default_options(closest_form){
                                var current_order_id = closest_form.find("input[name=\'id_order\']").val();
                                $(\'.datepicker_special\').each(function(){
                                    $(this).datepicker({dateFormat: \'dd-mm-yy\'});
                                });
                               
                                var elements = closest_form.find(":input");
                                for (var i = 0; i < elements.length; i++) {
                                    if(elements[i].name.indexOf("weight") !== -1) {
                                        elements[i].value = ( default_weight[current_order_id] != \'\' ? default_weight[current_order_id] : \'\');
                                    }
                                    if(elements[i].name.indexOf("bcc_email") !== -1) {
                                        elements[i].value = ( default_email[current_order_id] != \'\' ? default_email[current_order_id] : \'\');
                                    }
                                    if(elements[i].name.indexOf("product_type") !== -1 && elements[i].value == package_type_dpd[current_order_id]) {
                                        elements[i].checked = true;
                                    }
                                    if(elements[i].name.indexOf("send_track_and_trace_email") !== -1){
                                        if(DHL_send[current_order_id] == true){
                                            elements[i].checked = true;
                                        }
                                    }
                                    //Lastmile settings
                                    if(current_order_id in last_mile_data){
                                        if(elements[i].name.indexOf("saturday_delivery") !== -1 && last_mile_data[current_order_id]["saturday_delivery"]){
                                            if(last_mile_data[current_order_id]["saturday_delivery"] == true){
                                                elements[i].checked = true;
                                            }
                                        }
                                        if(elements[i].name.indexOf("evening") !== -1 && last_mile_data[current_order_id]["evening"]){
                                            if(last_mile_data[current_order_id]["evening"] == true){
                                                elements[i].checked = true;
                                            }
                                        }
                                        if(elements[i].name.indexOf("pickup_date") !== -1 && (typeof last_mile_data[current_order_id]["pickup_date"] !== "undefined")){
                                            elements[i].value = ( last_mile_data[current_order_id]["pickup_date"] != \'\' ? last_mile_data[current_order_id]["pickup_date"] : \'\');
                                        }
                                        if(elements[i].name.indexOf("parcel_shop_id") !== -1 && (typeof last_mile_data[current_order_id]["parcel_shop_id"] !== "undefined")){
                                            elements[i].value = ( last_mile_data[current_order_id]["parcel_shop_id"] != \'\' ? last_mile_data[current_order_id]["parcel_shop_id"] : \'\');
                                        }
                                    }
                                }
                                var select_elements = closest_form.find("select");
                                for (var e = 0; e < select_elements.length; e++) {
                                    if(select_elements[e].name.indexOf("predict") !== -1){
                                        for(var b = 0; b < select_elements[e].length; b++){
                                            if(select_elements[e].options[b].value == default_predict[current_order_id]){
                                                select_elements[e].options[b].selected = true;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            function set_default_to_all(form_button){
                                var default_form = $(form_button).closest(".form-horizontal");
                                var current_id = default_form.find("input[name=\'id_order\']").val();
                                var input_names = [];
                                //Get the data of the form used as default
                                default_form.find(":input").each(function(){
                                    if(this.name != \'id_order\' && this.name != \'is_lastmile\' && this.name != \'shipment[\' + current_id +\'][reference]\'
                                    && this.name != \'shipment[\' + current_id +\'][amount]\' && this.name != \'shipment[\' + current_id +\'][weight]\'){
                                        
                                        if(this.type === "checkbox"){
                                            input_names[this.name.split(\'shipment[\' + current_id + \']\')[1]] = this.checked;
                                        }
                                        else if(this.type === "radio" &&  this.checked == true){
                                            input_names[this.name.split(\'shipment[\' + current_id + \']\')[1]] = this.value;
                                        }
                                        else if(this.type != "radio" && this.type != "button"){
                                            input_names[this.name.split(\'shipment[\' + current_id + \']\')[1]] = this.value;
                                        }
                                    }
                                });
                                var shipment_forms = $(".keen-form");
                                //Fill products and services of non LastMile first else it requires more loop
                                shipment_forms.each(function(){
                                    if($(this).find("input[name=\'is_lastmile\']").val() == 0  && $(this).find("input[name=\'id_order\']").val() != current_id){
                                        $(this).find(".keen_product").val(input_names["[product]"]);
                                        set_keen_services($(this));
                                        $(this).find(".keen_service").val(input_names["[service]"]);
                                        set_keen_service_options($(this));
                                    }
                                });
                                //Fill options of non LastMile
                                shipment_forms.each(function(){
                                    if($(this).find("input[name=\'is_lastmile\']").val() == 0  && $(this).find("input[name=\'id_order\']").val() != current_id){
                                        var loop_id = $(this).find("input[name=\'id_order\']").val();
                                        $(this).find(":input").each(function(){
                                            if(input_names[this.name.split(\'shipment[\' + loop_id + \']\')[1]]){
                                                if(this.type === "checkbox"){
                                                    this.checked = input_names[this.name.split(\'shipment[\' + loop_id + \']\')[1]];
                                                }
                                                else if(this.type === "radio" && this.value == input_names[this.name.split(\'shipment[\' + loop_id + \']\')[1]]){
                                                    this.checked = true;
                                                }
                                                else if(this.type !== "radio" && this.type != "button"){
                                                    this.value = input_names[this.name.split(\'shipment[\' + loop_id + \']\')[1]];
                                                }
                                            }
                                        });
                                    }
                                });
                            }
                        </script>';
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
        $lm_opt_4_price = (Configuration::get('JETVERZENDT_LM_OPT_4_PRICE') > 0? Configuration::get('JETVERZENDT_LM_OPT_4_PRICE'): 0);
        $lm_opt_5_time = Configuration::get('JETVERZENDT_LM_OPT_5_TIME');
        $lm_opt_5_price = (Configuration::get('JETVERZENDT_LM_OPT_5_PRICE') > 0? Configuration::get('JETVERZENDT_LM_OPT_5_PRICE'): 0);
        $lm_opt_2_price = Configuration::get('JETVERZENDT_LM_OPT_2_PRICE') > 0? Configuration::get('JETVERZENDT_LM_OPT_2_PRICE'): 0;
        $lm_opt_3_1_price = Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE') > 0? Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE') : 0;
        $lm_opt_3_2_price = Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE')> 0? Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE'): 0;
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
						SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.(int)$this->context->cart->id.'"');
        $jet_extra_costs = 0;
        $jet_name_lastmile = '';
        if (count($cart_shippings) > 0)
        {
            $jet_extra_costs += $cart_shippings[0]['extra_shipping'];
            if ($cart_shippings[0]['shipping_service'] == 'NextDayPremium') $jet_name_lastmile = $this->l('Next Day Premium');
            //Capitals where used in previous version, still in here to prevent issues in case of overwriting
            else if ($cart_shippings[0]['shipping_service'] == 'FADELLO' || $cart_shippings[0]['shipping_service'] == 'Fadello') $jet_name_lastmile = $this->l('Same Day Delivery');
            else if ($cart_shippings[0]['shipping_service'] == 'DHL' && $cart_shippings[0]['parcelshop_id'] == '' && $cart_shippings[0]['deliverperiod'] != '')
                $jet_name_lastmile = date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'])).$this->l(' | tijdstip: ').$cart_shippings[0]['deliverperiod'];
            else if ($cart_shippings[0]['shipping_service'] == 'DPD' && $cart_shippings[0]['parcelshop_id'] == '')
                $jet_name_lastmile = $this->l('Zaterdaglevering: ').date('d-m-Y', strtotime($cart_shippings[0]['deliverdate']));
            else if ($cart_shippings[0]['parcelshop_id'] != '')
                $jet_name_lastmile = $cart_shippings[0]['parcelshop_description'];
        }
        $this->smarty->assign('jet_extra_costs', $jet_extra_costs);

        $this->smarty->assign('jet_name_lastmile', $jet_name_lastmile);
        return $this->display(__FILE__, 'carrierlist.tpl');
    }

	public function hookactionCarrierUpdate($params)
	{
		$jet_carrier = Configuration::get('JETVERZENDT_CARRIER_ID');
		if ($jet_carrier == $params['id_carrier'])
			Configuration::updateValue('JETVERZENDT_CARRIER_ID', $params['carrier']->id);
	}

    public function hookDisplayAdminOrder()
    {
        return $this->getShipmentInfo(Tools::getValue('id_order'));
    }

    public function hookactionValidateOrder($params){
	    $order = $params['order'];
        if(Configuration::get('KEENDELIVERY_AUTOPROCESSENABLE') == 1 && json_encode($order->id_carrier) == Configuration::get('JETVERZENDT_CARRIER_ID')) {
            $cart_shippings = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$order->id_cart.'"');
            if (count($cart_shippings) > 0) {
                $data = array();
                $data['amount'] = Configuration::get('KEENDELIVERY_AMOUNT');
                $data['reference'] = '';
                $data['weight'] = (ceil($this->getOrderWeight($order->id)) > 0? ceil($this->getOrderWeight($order->id)) : Configuration::get('KEENDELIVERY_WEIGHT'));
                $data['send_track_and_trace_email'] = '';
                $data['bcc_email'] = Configuration::get('KEENDELIVERY_BCC_EMAIL');
                $data['product'] = $cart_shippings[0]['shipping_service']; //set shipping_method DPD,DHL,Fadello,NextDayPremium
                if($cart_shippings[0]['parcelshop_id'] != '' && $cart_shippings[0]['parcelshop_id'] != 0){
                    if ($cart_shippings[0]['shipping_service'] == 'DPD') $data['service'] = 'PARCELSHOP';
                    else if ($cart_shippings[0]['shipping_service'] == 'DHL') $data['service'] = 'PARCEL_SHOP';
                }else {
                    if ($cart_shippings[0]['shipping_service'] == 'DHL' && $cart_shippings[0]['deliverperiod'] != '') {
                        if ($cart_shippings[0]['deliverperiod'] == '18:00-21:00') {
                            $data['evening'] = 1;
                        }
                        $data['service'] = 'DHL_FOR_YOU';
                    }
                    if ($cart_shippings[0]['shipping_service'] == 'DPD' && $cart_shippings[0]['deliverdate'] != '0000-00-00') {
                        $data['service'] = 'CL';
                        $data['saturday_delivery'] = 1;
                    }
                    $data['pickup_date'] = $cart_shippings[0]['deliverdate'];
                    if ($data['pickup_date'] != '0000-00-00' && $data['pickup_date'] != '1970-01-01') {
                        $data['pickup_date'] = date('d-m-Y', strtotime($data['pickup_date']));
                    } else $data['pickup_date'] = '';
                }
                $this->addShipment($order->id, $data);
            }else {
                $data = json_decode(Configuration::get('KEENDELIVERY_AUTOPROCESSVALUE'), true);
                //print_r(json_encode($data) . ' ' . $order->id);
                //$data['weight'] = (ceil($this->getOrderWeight($order->id)) > 0? ceil($this->getOrderWeight($order->id)) : Configuration::get('KEENDELIVERY_WEIGHT'));
                $this->addShipment($order->id, $data);
            }
        }
    }

    //No used detected
    public function getShipmentInfo($id_order = 0)
    {
        $selected_shipping_text = '';
        if ($id_order > 0)
        {
            $order = new Order($id_order);
            $selected_shipping = '';
            $cart_shippings = Db::getInstance()->executeS('
							SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$order->id_cart.'"');
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
                    //Capitals where used in previous version, still in here to prevent issues in case of overwriting
                    else if ($cart_shippings[0]['shipping_service'] == 'FADELLO' || $cart_shippings[0]['shipping_service'] == 'Fadello')
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
						SELECT * FROM  `'._DB_PREFIX_.'keendelivery` WHERE id_order="'.$id_order.'"');
        $cart_shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$order->id_cart.'"');
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
                //Capitals where used in previous version, still in here to prevent issues in case of overwriting
                else if ($cart_shippings[0]['shipping_service'] == 'FADELLO') $selected_shipping = 3;
                else if ($cart_shippings[0]['shipping_service'] == 'Fadello') $selected_shipping = 3;
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
							
								<div id="shipment_form"></div>
								
								<script>
								$( document ).ready(function() {
								    var shipping_methods = jQuery.parseJSON(\'<?php echo get_json_shipping_methods() ?>\');
								    
                                    function generate_shipment_form() {
                                        var form = \'\';
                                
                                        form += generate_product_dropdown();
                                
                                        form += generate_amount_field();
                                
                                        form += generate_reference_field();
                                
                                        form += generate_service_field();
                                
                                        form += generate_service_options();
                                
                                        $(\'#shipment_form\').html(form);
                                
                                        set_keen_services();
                                
                                    }
                                    
                                    function generate_product_dropdown() {
                                        result = \'<div class="form-group">\';
                                
                                        result += \'<div class="label">Vervoerder</div>\';
                                
                                        result += \'<select id="keen_product" name="product" onchange="set_keen_services()">\';
                                
                                        if (Object.keys(shipping_methods).length > 0) {
                                            for (var k in shipping_methods) {
                                                if (typeof shipping_methods[k] !== \'function\') {
                                                    result += \'<option value="\' + shipping_methods[k][\'value\'] + \'">\' + shipping_methods[k][\'text\'] + \'</option>\';
                                                }
                                            }
                                        }
                                
                                        result += \'</select>\';
                                        result += \'</div>\';
                                
                                        return result;
                                
                                    }
                                
                                    function generate_amount_field() {
                                        result = \'<div class="form-group">\';
                                        result += \'<div class="label">Aantal pakketten</div>\';
                                        result += \'<input type="text" name="amount" id="keen_amount" />\';
                                        result += \'</div>\';
                                
                                        return result;
                                    }
								
                                    function generate_reference_field() {
                                        result = \'<div class="form-group">\';
                                        result += \'<div class="label">Referentie</div>\';
                                        result += \'<input type="text" name="reference" id="keen_reference" />\';
                                        result += \'</div>\';
                                
                                        return result;
                                    }
                                
                                    function generate_service_field() {
                                        result = \'<div class="form-group">\';
                                        result += \'<div class="label">Service</div>\';
                                        result += \'<select id="keen_service" name="service" onchange="set_keen_service_options()">\';
                                        result += \'</select>\';
                                        result += \'</div>\';
                                
                                        return result;
                                    }
								
								    function generate_service_options() {
                                        result = \'<div id="keen_service_options"></div>\';
                                        return result;
                                    }
                                
                                    function set_keen_services() {
                                        var current_product = $(\'#keen_product\').val();
                                        result = \'\';
                                
                                        if (Object.keys(shipping_methods).length > 0) {
                                            for (var k in shipping_methods) {
                                                if (typeof shipping_methods[k] !== \'function\') {
                                                    if (k == current_product) {
                                
                                                        if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                            for (var i in shipping_methods[k][\'services\']) {
                                                                if (typeof shipping_methods[k][\'services\'][i] !== \'function\') {
                                                                    result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'value\'] + \'">\' + shipping_methods[k][\'services\'][i][\'text\'] + \'</option>\';
                                                                }
                                                            }
                                
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $(\'#keen_service\').html(\'\');
                                        $(\'#keen_service\').html(result);
                                
                                
                                        set_keen_service_options();
                                
                                    }
								    
                                    function set_keen_service_options() {
                                        var current_product = $(\'#keen_product\').val();
                                        var keen_service = $(\'#keen_service\').val();
                                        result = \'\';
                                
                                        if (Object.keys(shipping_methods).length > 0) {
                                            for (var k in shipping_methods) {
                                                if (typeof shipping_methods[k] !== \'function\') {
                                                    if (k == current_product) {
                                
                                                        if (Object.keys(shipping_methods[k][\'services\']).length > 0) {
                                                            for (var i in shipping_methods[k][\'services\']) {
                                                                if (i == keen_service) {
                                
                                                                    if (Object.keys(shipping_methods[k][\'services\'][i][\'options\']).length > 0) {
                                                                        for (var j in shipping_methods[k][\'services\'][i][\'options\']) {
                                
                                                                            if (typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] != \'hidden\') {
                                                                                result += \'<div class="form-group">\';
                                                                                result += \'<div class="label">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'text\'] + \'</div>\';
                                
                                                                                type = shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'];
                                
                                                                                if (type == \'selectbox\') {
                                                                                    result += \'<select \';
                                                                                    if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                        result += \' required \';
                                                                                    }
                                                                                    result += \' name="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'">\';
                                
                                                                                    if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 0) {
                                                                                        result += \'<option value="">Kies evt. een optie</option>\';
                                                                                    }
                                
                                                                                    if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                        for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                            if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                                result += \'<option value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] + \'</option>\';
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    result += \'</select>\';
                                                                                }
                                
                                
                                                                                if (type == \'radio\') {
                                
                                                                                    if (Object.keys(shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']).length > 0) {
                                                                                        for (var l in shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\']) {
                                                                                            if (typeof shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'] !== \'function\') {
                                                                                                result += \'<input type="radio" \';
                                                                                                result += \' name="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" \';
                                                                                                if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                                    result += \' required \';
                                                                                                }
                                                                                                result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'value\'] + \'">\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][l][\'text\'] + \'</input>\';
                                                                                            }
                                                                                        }
                                                                                    }
                                
                                                                                }
                                
                                
                                                                                if (type == \'checkbox\') {
                                
                                                                                    result += \'<input type="checkbox" \';
                                                                                    if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                        result += \' required \';
                                                                                    }
                                
                                                                                    result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                                
                                                                                }
                                
                                
                                                                                if (type == \'textbox\' || type == \'date\' || type == \'email\') {
                                
                                                                                    result += \'<input type="text" \';
                                
                                                                                    if (type == \'textbox\') {
                                                                                        result += \' type="text" \';
                                                                                    } else if (type == \'email\') {
                                                                                        result += \' type="email" \';
                                                                                    } else if (type == \'date\') {
                                                                                        result += \' type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" \';
                                                                                    }
                                
                                
                                                                                    if (shipping_methods[k][\'services\'][i][\'options\'][j][\'mandatory\'] == 1) {
                                                                                        result += \' required \';
                                                                                    }
                                
                                                                                    result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                                
                                                                                }
                                
                                                                                result += \'</div>\';
                                                                            }
                                                                            if(typeof shipping_methods[k][\'services\'][i][\'options\'] !== \'function\' && shipping_methods[k][\'services\'][i][\'options\'][j][\'type\'] == \'hidden\'){
                                                                                result += \'<div class="form-group">\';
                                                                                result += \'<input type="hidden"\';
                                                                                result += \' value="\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'choices\'][\'value\'] +\'"\';
                                                                                result += \' name="\' + (shipping_methods[k][\'services\'][i][\'options\'][j][\'field\']) + \'" id="keen_service_option_\' + shipping_methods[k][\'services\'][i][\'options\'][j][\'field\'] + \'" />\';
                                                                                result += \'</div>\';
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                
                                        $(\'#keen_service_options\').html(\'\');
                                        $(\'#keen_service_options\').html(result);
                                    }
								
								    generate_shipment_form();
								
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
								<a href="/index.php?fc=module&module=keendelivery&controller=print?id_order='.$id_order.
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

}