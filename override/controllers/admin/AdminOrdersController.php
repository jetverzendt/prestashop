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
class AdminOrdersController extends AdminOrdersControllerCore
{
	public function __construct()
	{

		$this->bootstrap = true;
		$this->table = 'order';
		$this->className = 'Order';
		$this->lang = false;
		$this->addRowAction('view');
		$this->explicitSelect = true;
		$this->allow_export = true;
		$this->deleted = false;
		$this->context = Context::getContext();

	$this->_select = '
		a.id_currency,
		a.id_order AS id_pdf,
		a.id_order AS id_order_good,
		a.id_order AS id_order_good2,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		if ((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer 
		AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
		country_lang.name as cname,
		if (a.valid, 1, 0) badge_success';

		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		LEFT JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
		LEFT JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
		LEFT JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.
		(int)$this->context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.
		(int)$this->context->language->id.')';
		$this->_orderBy = 'id_order';
		$this->_orderWay = 'DESC';
		$this->_use_found_rows = true;

		$statuses = OrderState::getOrderStates((int)$this->context->language->id);
		foreach ($statuses as $status)
			$this->statuses_array[$status['id_order_state']] = $status['name'];

		$this->fields_list = array(
			'id_order' => array(
				'title' => $this->l('ID'),
				'align' => 'text-center',
				'class' => 'fixed-width-xs'
			),
			'reference' => array(
				'title' => $this->l('Reference')
			),
			'new' => array(
				'title' => $this->l('New client'),
				'align' => 'text-center',
				'type' => 'bool',
				'tmpTableFilter' => true,
				'orderby' => false,
				'callback' => 'printNewCustomer'
			),
			'customer' => array(
				'title' => $this->l('Customer'),
				'havingFilter' => true,
			),
		);

		if (Configuration::get('PS_B2B_ENABLE'))
		{
			$this->fields_list = array_merge($this->fields_list, array(
				'company' => array(
					'title' => $this->l('Company'),
					'filter_key' => 'c!company'
				),
			));
		}

		$this->fields_list = array_merge($this->fields_list, array(
			'total_paid_tax_incl' => array(
				'title' => $this->l('Total'),
				'align' => 'text-right',
				'type' => 'price',
				'currency' => true,
				'callback' => 'setOrderCurrency',
				'badge_success' => true
			),
			'payment' => array(
				'title' => $this->l('Payment')
			),
			'osname' => array(
				'title' => $this->l('Status'),
				'type' => 'select',
				'color' => 'color',
				'list' => $this->statuses_array,
				'filter_key' => 'os!id_order_state',
				'filter_type' => 'int',
				'order_key' => 'osname'
			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'align' => 'text-right',
				'type' => 'datetime',
				'filter_key' => 'a!date_add'
			),
			'id_pdf' => array(
				'title' => $this->l('PDF'),
			'align' => 'text-center',
				'callback' => 'printPDFIcons',
				'orderby' => false,
				'search' => false,
				'remove_onclick' => true
			),
			'id_order_good' => array(
				'title' => $this->l('Shipping'),
				'align' => 'text-center',
				'callback' => 'printShippingIcons',
				'orderby' => false,
				'search' => false,
				'remove_onclick' => true
			),
			'id_order_good2' => array(
				'title' => $this->l('Last-Mile'),
				'align' => 'text-center',
				'callback' => 'printShippingLastMile',
				'orderby' => false,
				'search' => false,
				'remove_onclick' => true
			)
		));

		if (Country::isCurrentlyUsed('country', true))
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `'._DB_PREFIX_.'orders` o
			'.Shop::addSqlAssociation('orders', 'o').'
			INNER JOIN `'._DB_PREFIX_.'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `'._DB_PREFIX_.'country` c ON a.id_country = c.id_country
			INNER JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			ORDER BY cl.name ASC');

			$country_array = array();
			foreach ($result as $row)
				$country_array[$row['id_country']] = $row['name'];

			$part1 = array_slice($this->fields_list, 0, 3);
			$part2 = array_slice($this->fields_list, 3);
			$part1['cname'] = array(
				'title' => $this->l('Delivery'),
				'type' => 'select',
				'list' => $country_array,
				'filter_key' => 'country!id_country',
				'filter_type' => 'int',
				'order_key' => 'cname'
			);
			$this->fields_list = array_merge($part1, $part2);
		}

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_ORDER;

		if (Tools::isSubmit('id_order'))
		{
			// Save context (in order to apply cart rule)
			$order = new Order((int)Tools::getValue('id_order'));
			$this->context->cart = new Cart($order->id_cart);
			$this->context->customer = new Customer($order->id_customer);
		}

		$this->bulk_actions = array(
			'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh'),
			'updatePrintLabels' => array('text' => $this->l('Print labels'), 'icon' => 'icon-print'),
            'updateShippingStatus' => array('text' => $this->l('Create shipment'), 'icon' => 'icon-truck'),
            'updateShippingStatusDefault' => array('text' => $this->l('Create shipment last-mile'), 'icon' => 'icon-truck')
		);

		AdminController::__construct();
	}

	//handle bulk button input
	public function renderList()
	{
		if (Tools::isSubmit('submitBulkupdateOrderStatus'.$this->table))
		{
			if (Tools::getIsset('cancel'))
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

			$this->tpl_list_vars['updateOrderStatus_mode'] = true;
			$this->tpl_list_vars['order_statuses'] = $this->statuses_array;
			$this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
			$this->tpl_list_vars['POST'] = $_POST;
		}
		if (Tools::isSubmit('submitBulkupdateShippingStatus'.$this->table))
		{
			$novijetverzendt = new Keendelivery();
			$novijetverzendt_text = $novijetverzendt->getShipmentInfoList();

			if (Tools::getIsset('cancel'))
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

			$this->tpl_list_vars['updateShippingStatus_mode'] = true;
			$this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
			$this->tpl_list_vars['POST'] = $_POST;
			$this->tpl_list_vars['novijetverzendt_text'] = $novijetverzendt_text;
			$this->tpl_list_vars['novijetverzendt_default'] = 0;
		}
		if (Tools::isSubmit('submitBulkupdateShippingStatusDefault'.$this->table))
    {
        $novijetverzendt = new Keendelivery();
        $novijetverzendt_text = $novijetverzendt->getShipmentInfoListDefault();
        if (Tools::getIsset('cancel'))
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

        $this->tpl_list_vars['updateShippingStatus_mode'] = true;
        //$this->tpl_list_vars['shipping_statuses'] = $shipping_array;
        $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->tpl_list_vars['POST'] = $_POST;
        $this->tpl_list_vars['novijetverzendt_text'] = $novijetverzendt_text;
        $this->tpl_list_vars['novijetverzendt_default'] = 1;
    }
		if (Tools::isSubmit('submitBulkupdatePrintLabels'.$this->table))
			$this->processBulkPrintLabels();
		return AdminController::renderList();
	}
	public function processBulkUpdateShippingStatus()
	{
		$errors = 0;
		if (Tools::isSubmit('submitUpdateShippingStatus'))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
			else
			{
				$this->errors = '';
                $post = $_POST;
				foreach (Tools::getValue('orderBox') as $id_order)
				{
				    $options = array();
                    //$options = $post;
                    foreach ($_POST as $key => $value) {
                        $options[$key] = isset($post[$key]) ? $value : '';
                    }
                    //unset($options['orderBox']);
                    unset($options['submitUpdateShippingStatusDefault']);

					$novijetverzendt = new Keendelivery();
					$errs = $novijetverzendt->addShipment((int)$id_order, $options);

					if (count($errs) > 0)
					{
						if (is_array($errs))
							foreach ($errs as $item)
							{
								$this->errors .= $novijetverzendt->l('Order').' '.$id_order.' - '.$item.'<br>';
								$errors = 1;
							}
					}
				}
			}
			if ($errors == 0) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
		}
        if (Tools::isSubmit('submitUpdateAutoShippingStatus'))
        {
            $options = array();
            foreach ($_POST as $key => $value) {
                $options[$key] = isset($_POST[$key]) ? $value : '';
            }
            //unset($options['orderBox']);
            unset($options['submitUpdateAutoShippingStatus']);

            Configuration::updateValue('KEENDELIVERY_AUTOPROCESSENABLE', true);
            Configuration::updateValue('KEENDELIVERY_AUTOPROCESSVALUE', json_encode($options));
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        }
	}


    public function processBulkUpdateShippingStatusDefault()
    {
        $errors = 0;
        if (Tools::isSubmit('submitUpdateShippingStatusDefault'))
        {
            if ($this->tabAccess['edit'] !== '1')
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            else
            {
                $post = $_POST;
                unset($post['orderBox']);
                unset($post['id_order']);
                unset($post['is_lastmile']);
                unset($post['submitUpdateShippingStatusDefault']);
                $this->errors = '';
                foreach (Tools::getValue('orderBox') as $id_order)
                {
                    $string = '';
                    $options = array();
                    foreach ($_POST['shipment'][$id_order] as $key => $value) {
                        $options[$key] = isset($post['shipment'][$id_order][$key]) ? $value : '';
                    }
                    $novijetverzendt = new Keendelivery();
                    $errs = array();
                    $errs = $novijetverzendt->addShipment((int)$id_order, $options);
                    if (count($errs) > 0)
                    {
                        if (is_array($errs))
                            foreach ($errs as $item)
                            {
                                $this->errors .= $novijetverzendt->l('Order').' '.$id_order.' - '.$item.'<br>';
                                $errors = 1;
                            }
                    }
                }
            }
            if ($errors == 0)
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        }
    }

	public function processBulkPrintLabels()
	{
		$id_array = array();
		if ($this->tabAccess['edit'] !== '1')
			$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		else
		{
			$api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
			$label_type = Configuration::get('JETVERZENDT_CLIENT_LABEL');
			foreach (Tools::getValue('orderBox') as $id_order){
				$shippings = Db::getInstance()->executeS('
					SELECT * FROM  `'._DB_PREFIX_.'keendelivery` WHERE id_order="'.(int)$id_order.'"');
				if (count($shippings) > 0){
					$id = $shippings[0]['shipment_id'];
					$id_array[] = $id;
				}
			}
			$keen = new KeenDelivery();
            $shippingMethods = json_decode($keen->get_shipping_methods());
            if(!empty($shippingMethods)){
                $label_sizes = array();
                foreach($shippingMethods as $method){
                    $label_sizes[$method->value] = ['size' => Configuration::get('JETVERZENDT_PRINT_SIZE')];
                }
            }
			// Create label collection
			$label_data = Tools::jsonEncode(
				[
					'shipments' => $id_array,
					'type' => $label_type,
                    'options' => $label_sizes
				]
			);

			$testmode = Configuration::get('JETVERZENDT_STATUS');
			if ($testmode == 0) $apiurl = 'http://testportal.keendelivery.com';
			else $apiurl = 'https://portal.keendelivery.com';
			$ch = curl_init($apiurl.'/api/v2/label?api_token='.$api_key);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $label_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: '.Tools::strlen($label_data)
				)
			);
			$res = curl_exec($ch);
			$result = Tools::jsonDecode($res);
			chmod(_PS_ROOT_DIR_.'/modules/keendelivery/labels.'.Tools::strtolower($label_type), 0777);
			file_put_contents(_PS_ROOT_DIR_.'/modules/keendelivery/labels.'.Tools::strtolower($label_type), base64_decode($result->labels));
			header('Content-disposition: attachment; filename=label.'.Tools::strtolower($label_type));
			header('Content-type: application/'.Tools::strtolower($label_type));
			readfile(_PS_ROOT_DIR_.'/modules/keendelivery/labels.'.Tools::strtolower($label_type));
			unlink(_PS_ROOT_DIR_.'/modules/keendelivery/labels.'.Tools::strtolower($label_type));
		}
		if (!count($this->errors))
			Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
	}

	public function printShippingIcons($id_order)
	{
		$html = '';
		$shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'keendelivery` WHERE id_order="'.(int)$id_order.'"');
		$track_and_trace_url = '';
		$track_and_trace_code = '';
		$label = '';
		if (count($shippings) > 0)
		{
            $label = $shippings[0]['label'];
			$track_and_trace_url = $shippings[0]['track_and_trace_url'];
			$track_and_trace_code = $shippings[0]['track_and_trace_code'];
		}
		$html = (($label != '')?'
								<a href="' . __PS_BASE_URI__ . 'index.php?fc=module&module=keendelivery&controller=print&id_order='.$id_order.'" target="_blank" class="">
										<i class="icon-print"></i>
								</a>
								':'');
		$html .= ' '.(($track_and_trace_code != '')?'
								<a href="'.$track_and_trace_url.'" target="_blank" class="" style="margin-left:10px;">
										<i class="icon-truck"></i>
								</a>
								':'');
		return $html;
	}

	public function printShippingLastMile($id_order)
	{
		$novijetverzendt = new Keendelivery();
		$html = '';
		$order = new Order($id_order);
		$cart_shippings = Db::getInstance()->executeS('
						SELECT * FROM  `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.(int)$order->id_cart.'"');
		if (count($cart_shippings) > 0)
		{
			$selected_shipping_text = '';
			if ($cart_shippings[0]['shipping_service'] == 'DPD')
			{
				$selected_shipping_text = '<b>'.$novijetverzendt->l('DPD Zaterdaglevering').'</b><br>';
				if ($cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
					$selected_shipping_text = '<b>'.$novijetverzendt->l('DPD Zaterdaglevering').'</b><br>'.$novijetverzendt->l('Zaterdag').
					' '.date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'].' + 1 days'));
				if ($cart_shippings[0]['parcelshop_id'] != '')
					$selected_shipping_text = '<b>'.$novijetverzendt->l('Parcelshop - DPD').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
			}
			else
				if ($cart_shippings[0]['shipping_service'] == 'DHL')
				{
					$selected_shipping_text = $novijetverzendt->l('Bezorgmoment - DHL');
					if ($cart_shippings[0]['deliverperiod'] != '' && $cart_shippings[0]['deliverdate'] != '' && $cart_shippings[0]['deliverdate'] != '0000-00-00')
						$selected_shipping_text = '<b>'.$novijetverzendt->l('Bezorgmoment - DHL').'</b>
							<br>'.$novijetverzendt->l('Bezorgdatum').': '.date('d-m-Y', strtotime($cart_shippings[0]['deliverdate'])).
							'<br>'.$novijetverzendt->l('Tijdvak').': '.$cart_shippings[0]['deliverperiod'];
					if ($cart_shippings[0]['parcelshop_id'] != '')
						$selected_shipping_text = '<b>'.$novijetverzendt->l('Parcelshop - DHL').'</b><br>'.$cart_shippings[0]['parcelshop_description'];
				}
                //Capitals where used in previous version, still in here to prevent issues in case of overwriting
				else if ($cart_shippings[0]['shipping_service'] == 'FADELLO' || $cart_shippings[0]['shipping_service'] == 'Fadello')
					$selected_shipping_text = $novijetverzendt->l('Same Day Delivery');
				else $selected_shipping_text = $novijetverzendt->l('Next Day Premium');
			$html = $selected_shipping_text;
		}

		return $html;
	}
}