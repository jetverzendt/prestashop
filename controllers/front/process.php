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

class KeenDeliveryProcessModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		setlocale(LC_ALL, 'nl_NL');
//		parent::initContent();
		if (Tools::getValue('getDates') == 'yes')
			$this->getDates();
		if (Tools::getValue('getMap') == 'yes')
			$this->getMap();
		if (Tools::getValue('getPlaces') == 'yes')
			$this->getPlaces();
		if (Tools::getValue('getDeliverySchedule') == 'yes')
			$this->getDeliverySchedule();
		if (Tools::getValue('updateCart') == 'yes')
			$this->updateCart();
		exit();
	}
	public function updateCart()
	{

		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'keendelivery_cart` WHERE id_cart="'.$this->context->cookie->id_cart.'"');
		$shipping_type = 1;
		if (Tools::getValue('lastmile_type') == 'DHL') $shipping_type = 2;
		if (Tools::getValue('lastmile_type') == 'fadello') $shipping_type = 3;
		$shipping_service = pSQL(Tools::getValue('lastmile_service'));
		$date = date('Y-m-d', strtotime(Tools::getValue('lastmile_deliverdate')));
		if(Tools::getValue('lastmile_type') == 'fadello'){
            $date = date('Y-m-d', strtotime('today UTC'));
        }elseif(Tools::getValue('lastmile_type') == 'NextDayPremium'){
		    if(strtotime('tomorrow') != strtotime('next sunday')) {
                $date = date('Y-m-d', strtotime('tomorrow'));
            }else{
                $date = date('Y-m-d', strtotime('next monday'));
            }
        }
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'keendelivery_cart 
        (extra_shipping, id_cart, shipping_type, shipping_service, parcelshop_id, deliverdate, deliverperiod, 
        deliverevening, last_mile_choice, parcelshop_description
        ) 
        VALUES 
        (
        "'.pSQL(Tools::getValue('extra_costs_shipping')).'", 
        "'.$this->context->cookie->id_cart.'", 
        "'.$shipping_type.'", 
        "'.$shipping_service.'", 
        "'.pSQL(Tools::getValue('lastmile_parcelshop_id')?pSQL(Tools::getValue('lastmile_parcelshop_id')): "").'",
        "'.$date.'",
        "'.pSQL(Tools::getValue('lastmile_deliverperiod')).'",
        "'.(Tools::getValue('lastmile_deliverevening')?(int)Tools::getValue('lastmile_deliverevening'):"").'",
        "'.(Tools::getValue('jet_last_mile_choice')?(int)Tools::getValue('jet_last_mile_choice'):"").'",
        "'.addslashes(pSQL(Tools::getValue('lastmile_parcelshop_description'))).'"
        )');
	}
	public function getDeliverySchedule()
	{
		$error = 0;
		$lm_opt_1_time = Configuration::get('JETVERZENDT_LM_OPT_1_TIME');
		$lm_opt_1_price = Configuration::get('JETVERZENDT_LM_OPT_1_PRICE');
		$api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
		//$shared_secret = Configuration::get('JETVERZENDT_CLIENT_SECRET');
		$search_data = Tools::jsonEncode(
			[
				'zip_code' => Tools::getValue('jet_postcode'),
				'products' => 'DHL'
			]
		);

		$testmode = Configuration::get('JETVERZENDT_STATUS');
		if ($testmode == 0) $apiurl = 'http://testportal.keendelivery.com';
		else $apiurl = 'https://portal.keendelivery.com';
		$ch = curl_init($apiurl.'/api/v2/delivery-schedule/search?api_token='.$api_key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$shared_secret);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $search_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Content-Length: '.Tools::strlen($search_data))
		);

		$result = Tools::jsonDecode(curl_exec($ch));

		//print_r($result);
		$novijetverzendt = new Keendelivery();
		$info = '
			<h3>'.$novijetverzendt->l('Kies een tijdstip uit:').'</h3>
			<table width="92%" class="deliver_dates">
				<thead>
					<tr>  <th>'.$novijetverzendt->l('Datum').'</th>  <th>'.$novijetverzendt->l('Ochtend').'</th>  
					<th>'.$novijetverzendt->l('Voormiddag').'</th>  <th>'.$novijetverzendt->l('Namiddag').'</th>  <th>'.$novijetverzendt->l('Avond').'</th></tr>
				</thead>
				<tbody>';
		if (date('H:i') < $lm_opt_1_time) $today = time();
		else $today = time() + 86400;

		if (isset($result->schedule) && isset($result->schedule->DHL))
		{
			foreach ($result->schedule->DHL as $date => $item)
			{
				if (strtotime($date) < $today)
					continue;
				$info .= '<tr><td>'.strftime('%a %d %B %Y', strtotime($date)).'</td>';
				$i = 1;
				foreach ($item as $times)
				{
					if (($times->from == '11:00' && $i == 1) || ($times->from == '14:00' && $i == 2) || ($times->from == '18:00' && $i == 3))
					{
						$info .= '<td></td>';
						$i++;
					}
					if (($times->from == '14:00' && $i == 1) || ($times->from == '18:00' && $i == 2))
					{
						$info .= '<td></td><td></td>';
						$i++;
						$i++;
					}
					if (($times->from == '18:00' && $i == 1))
					{
						$info .= '<td></td><td></td><td></td>';
						$i++;
						$i++;
						$i++;
					}
					$info .= '
						<td>
							<a onclick="$(\'#jet_last_mile_choice\').show(); 
							$(\'#lastmile_deliverperiod\').val(\''.$times->from.'-'.$times->to.'\');  
							$(\'#lastmile_parcelshop_id\').val(\'\'); 
							$(\'#lastmile_service\').val(\'DHL\'); $(\'#lastmile_deliverdate\').val(\''.$date.'\'); 
							$(\'#lastmile_deliverevening\').val(\''.(($times->from == '18:00')?'1':'0').'\'); 
							$(\'#lastmile_type\').val(\'dhl_deliverdate\'); $(\'#jet_name_lastmile\').html(\''.$date.' | tijdstip: '.$times->from.'-'.$times->to.'\'); 
							'.(($times->from == '18:00')?'
							$(\'#transport_price_value\').prev().html(((parseFloat($(\'#transport_price_value\').html()) + '.
							$lm_opt_1_price.').toFixed(2))+\' € (incl. btw)\');
							$(\'#extra_costs_shipping\').val('.$lm_opt_1_price.');
							':
							'$(\'#transport_price_value\').prev().html((parseFloat($(\'#transport_price_value\').html()))+\' € (incl. btw)\');
							$(\'#extra_costs_shipping\').val(0);
							').
							'updateCart();
							$(\'.fancybox-close\').trigger(\'click\'); return false; ">'.$times->from.'-'.$times->to.(($times->from == '18:00')?'
							<span class="jet_special_price">'.(($lm_opt_1_price > 0)?' + €&nbsp;'.number_format($lm_opt_1_price, 2, ',', ''):'').'</span>':'').'</a>
							</td>
						';
					$i++;
				}
				if ($i == 2) $info .= '<td></td><td></td><td></td>';
				if ($i == 3) $info .= '<td></td><td></td>';
				if ($i == 4) $info .= '<td></td>';
				$info .= '</tr>';
			}
		}
		else $error = 1;
		$info .= '</tbody></table>';
		if ($error == 1) $info = $novijetverzendt->l('There is a problem with the address you filled in. It isn\'t compatible with DHL service');
		echo $info;
		//echo Tools::jsonEncode($addresses);

	}
	public function getPlaces()
	{
		$lm_opt_3_1 = Configuration::get('JETVERZENDT_LM_OPT_3_1');
		$lm_opt_3_2 = Configuration::get('JETVERZENDT_LM_OPT_3_2');
		$lm_opt_3_1_price = Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE');
		$lm_opt_3_2_price = Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE');
		$api_key = Configuration::get('JETVERZENDT_CLIENT_ID');
		//$shared_secret = Configuration::get('JETVERZENDT_CLIENT_SECRET');
		if ($lm_opt_3_1 == 1 && $lm_opt_3_2 == 1) $products = ['DPD', 'DHL'];
		else if ($lm_opt_3_2 == 1) $products = ['DPD'];
		else $products = ['DHL'];
		$search_data = Tools::jsonEncode(
			[
				'zip_code' => Tools::getValue('zip_code'),
				'number_line_1' => Tools::getValue('number'),
				'country' => Tools::getValue('country'),
				'products' => $products
			]
		);

		$testmode = Configuration::get('JETVERZENDT_STATUS');
		if ($testmode == 0) $apiurl = 'http://testportal.keendelivery.com';
		else $apiurl = 'https://portal.keendelivery.com';
		$ch = curl_init($apiurl.'/api/v2/parcel-shop/search?api_token='.$api_key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$shared_secret);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $search_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Content-Length: '.Tools::strlen($search_data))
		);

		$result = Tools::jsonDecode(curl_exec($ch));
		$novijetverzendt = new Keendelivery();
		$addresses = array();
		$i = 0;
		if (isset($result->parcel_shops))
			if (isset($result->parcel_shops->DPD) && is_array($result->parcel_shops->DPD))
				foreach ($result->parcel_shops->DPD as $item)
				{
					$addresses[$i]['lat'] = $item->latitude;
					$addresses[$i]['lng'] = $item->longitude;
					$addresses[$i]['html'] = '
						<div style="overflow: auto;" class="jet_map_pin">
							<h3>'.$item->name.''.(($lm_opt_3_2_price > 0)?' ( + €&nbsp;'.number_format($lm_opt_3_2_price, 2, ',', '').')':'').
							'</h3>'.$item->street.' '.$item->house_number.'<br>
							'.$item->zip_code.' '.$item->city.'<br><br>
							<i>'.$novijetverzendt->l('Openingstijden').'</i>
							<table width="500" class="opening_hours"><tbody>
								<tr>
									<td valign="top"><strong>'.$novijetverzendt->l('Maandag').'</strong><br>'.
									(($item->opening_hours[0]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[0]->morning_open.' - '.
									$item->opening_hours[0]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Dinsdag').'</strong><br>'.
									(($item->opening_hours[1]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[1]->morning_open.
									' - '.$item->opening_hours[1]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Woensdag').'</strong><br>'.
									(($item->opening_hours[2]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[2]->morning_open.
									' - '.$item->opening_hours[2]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Donderdag').'</strong><br>'.
									(($item->opening_hours[3]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[3]->morning_open.
									' - '.$item->opening_hours[3]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Vrijdag').'</strong><br>'.
									(($item->opening_hours[4]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[4]->morning_open.
									' - '.$item->opening_hours[4]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Zaterdag').'</strong><br>'.
									(($item->opening_hours[5]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[5]->morning_open.
									' - '.$item->opening_hours[5]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Zondag').'</strong><br>'.
									(($item->opening_hours[6]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[6]->morning_open.
									' - '.$item->opening_hours[6]->afternoon_close).'</td>
								</tr></tbody>
							</table><br>
							<button onclick="$(\'#clear_last_mile\').show(); 
							//$(\'#lastmile_parcelshop_id\').val(\'\'); 
							$(\'#lastmile_parcelshop_id\').val(\''.$item->id.'\'); 
							$(\'#lastmile_service\').val(\'DPD\');  
							$(\'#lastmile_parcelshop_description\').val(\''.addslashes($item->name.' '.$item->street.' '.$item->house_number.' - '.$item->city).'\');  
							$(\'#jet_name_lastmile\').html(\''.addslashes($item->name.' '.$item->street.' '.$item->house_number.' - '.$item->city).'\'); 
							$(\'#transport_price_value\').prev().html(((parseFloat($(\'#transport_price_value\').html())+'.$lm_opt_3_2_price.
							').toFixed(2))+\' € (incl. btw)\');
							$(\'#extra_costs_shipping\').val('.$lm_opt_3_2_price.');
							$(\'#lastmile_type\').val(\'parcelshop\');  
							updateCart();
							$(\'.fancybox-close\').trigger(\'click\'); return false; " 
							class="button">'.$novijetverzendt->l('Parcelshop uitkiezen').'</button></div>
					';
					$addresses[$i]['marker'] = __PS_BASE_URI__ . '/modules/keendelivery/views/img/marker_dpd.png';
					$i++;
				}
			if (isset($result->parcel_shops->DHL) && is_array($result->parcel_shops->DHL))
				foreach ($result->parcel_shops->DHL as $item)
				{
					$addresses[$i]['lat'] = $item->latitude;
					$addresses[$i]['lng'] = $item->longitude;
					$addresses[$i]['html'] = '
						<div style="overflow: auto;" class="jet_map_pin">
							<h3>'.$item->name.''.(($lm_opt_3_1_price > 0)?' ( + €&nbsp;'.number_format($lm_opt_3_1_price, 2, ',', '').')':'').'</h3>'.
							$item->street.' '.$item->house_number.'<br>
							'.$item->zip_code.' '.$item->city.'<br><br>
							<i>'.$novijetverzendt->l('Openingstijden').'</i>
							<table width="500" class="opening_hours"><tbody>
								<tr>
									<td valign="top"><strong>'.$novijetverzendt->l('Maandag').'</strong><br>'.
									(($item->opening_hours[0]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[0]->morning_open.
									' - '.$item->opening_hours[0]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Dinsdag').'</strong><br>'.
									(($item->opening_hours[1]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[1]->morning_open.
									' - '.$item->opening_hours[1]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Woensdag').'</strong><br>'.
									(($item->opening_hours[2]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[2]->morning_open.
									' - '.$item->opening_hours[2]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Donderdag').'</strong><br>'.
									(($item->opening_hours[3]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[3]->morning_open.
									' - '.$item->opening_hours[3]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Vrijdag').'</strong><br>'.
									(($item->opening_hours[4]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[4]->morning_open.
									' - '.$item->opening_hours[4]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Zaterdag').'</strong><br>'.
									(($item->opening_hours[5]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[5]->morning_open.
									' - '.$item->opening_hours[5]->afternoon_close).'</td>
									<td valign="top"><strong>'.$novijetverzendt->l('Zondag').'</strong><br>'.
									(($item->opening_hours[6]->morning_open == '')?$novijetverzendt->l('gesloten'):$item->opening_hours[6]->morning_open.
									' - '.$item->opening_hours[6]->afternoon_close).'</td>
								</tr></tbody>
							</table><br>
							<button onclick="$(\'#clear_last_mile\').show(); $(\'#lastmile_service\').val(\'DHL\'); 
							$(\'#lastmile_parcelshop_id\').val(\''.$item->id.'\');  
							$(\'#lastmile_parcelshop_description\').val(\''.addslashes($item->name.' '.$item->street.' '.$item->house_number.' - '.$item->city).'\');  
							$(\'#jet_name_lastmile\').html(\''.addslashes($item->name.' '.$item->street.' '.$item->house_number.' - '.$item->city).'\');  
							$(\'#transport_price_value\').prev().html(((parseFloat($(\'#transport_price_value\').html())+'.$lm_opt_3_1_price.
							').toFixed(2))+\' € (incl. btw)\');
							$(\'#extra_costs_shipping\').val('.$lm_opt_3_1_price.');
							$(\'#lastmile_type\').val(\'parcelshop\');  
							updateCart();
							$(\'.fancybox-close\').trigger(\'click\'); return false; " 
							class="button">'.$novijetverzendt->l('Parcelshop uitkiezen').'</button></div>
					';
					$addresses[$i]['marker'] = __PS_BASE_URI__ . '/modules/keendelivery/views/img/marker_dhl.png';
					$i++;
				}
		echo Tools::jsonEncode($addresses);

	}
	public function getDates()
	{
		//$lm_opt_1_time = Configuration::get('JETVERZENDT_LM_OPT_1_TIME');
		$lm_opt_1_price = Configuration::get('JETVERZENDT_LM_OPT_1_PRICE');
		$lm_opt_2_time = Configuration::get('JETVERZENDT_LM_OPT_2_TIME');
		$lm_opt_2_price = Configuration::get('JETVERZENDT_LM_OPT_2_PRICE');
		$novijetverzendt = new Keendelivery();
		$sunday_dates = '<h3>'.$novijetverzendt->l('Kies een zaterdag uit:').'</h3>';
		$sunday = strtotime('next Saturday');
		$friday = strtotime('next Friday');
		$max = 4;
		if (!(date('N') == 3 && date('H:i') > $lm_opt_2_time))
			$sunday_dates .= '<div class="jet_getDates">'.strftime('%A %d %B %Y', $sunday).'
			<a onclick="$(\'#clear_last_mile\').show(); $(\'#lastmile_deliverdate\').val(\''.date('Ymd', $friday).'\'); 
			$(\'#lastmile_parcelshop_id\').val(\'\'); 
			$(\'#lastmile_service\').val(\'DPD\'); $(\'#lastmile_type\').val(\'dpd_saterday\'); 
			$(\'#jet_name_lastmile\').html(\''.$novijetverzendt->l('Zaterdaglevering').': '.strftime('%d %B %Y', $sunday).'\'); 
			$(\'#transport_price_value\').prev().html(((parseFloat($(\'#transport_price_value\').html())+'.
			$lm_opt_2_price.').toFixed(2))+\' € (incl. btw)\');
			$(\'#extra_costs_shipping\').val('.$lm_opt_2_price.');
			updateCart();
			$(\'.fancybox-close\').trigger(\'click\'); return false; ">'.$novijetverzendt->l('Selecteer').'</a>
			<span class="jet_special_price">'.(($lm_opt_1_price > 0)?' + € '.number_format($lm_opt_2_price, 2, ',', ''):'').'</span></div>';
		else $max = 5;
		for ($i = 1; $i <= $max; $i++)
		{
			$friday = $friday + 60 * 60 * 24 * 7;
			$sunday = $sunday + 60 * 60 * 24 * 7;
			$sunday_dates .= '<div class="jet_getDates">'.strftime('%A %d %B %Y', $sunday).'
			<a onclick="$(\'#clear_last_mile\').show(); $(\'#lastmile_deliverdate\').val(\''.date('Ymd', $friday).'\'); 
			$(\'#lastmile_parcelshop_id\').val(\'\'); 
			$(\'#lastmile_service\').val(\'DPD\'); $(\'#lastmile_type\').val(\'dpd_saterday\'); 
			$(\'#jet_name_lastmile\').html(\''.$novijetverzendt->l('Zaterdaglevering').': '.strftime('%d %B %Y', $sunday).'\'); 
			$(\'#transport_price_value\').prev().html(((parseFloat($(\'#transport_price_value\').html())+'.
			$lm_opt_2_price.').toFixed(2))+\' € (incl. btw)\');
			$(\'#extra_costs_shipping\').val('.$lm_opt_2_price.');
			updateCart();
			$(\'.fancybox-close\').trigger(\'click\'); return false; ">'.$novijetverzendt->l('Selecteer').'</a>
			<span class="jet_special_price">'.(($lm_opt_1_price > 0)?' + € '.number_format($lm_opt_2_price, 2, ',', ''):'').'</span></div>';
		}
		echo $sunday_dates;
	}
	public function getMap()
	{
		//$lm_opt_3_1 = Configuration::get('JETVERZENDT_LM_OPT_3_1');
		//$lm_opt_3_2 = Configuration::get('JETVERZENDT_LM_OPT_3_2');
		//$lm_opt_3_1_price = Configuration::get('JETVERZENDT_LM_OPT_3_1_PRICE');
		//$lm_opt_3_2_price = Configuration::get('JETVERZENDT_LM_OPT_3_2_PRICE');
		$novijetverzendt = new Keendelivery();
		$map = '
		<h3>'.$novijetverzendt->l('Kies een Parcelshop').':</h3>

			<label>'.$novijetverzendt->l('Zoek op adres:').'</label>
			<input type="text" value="" id="jet_address"/>

			<input type="button" value="'.$novijetverzendt->l('Zoeken').'" id="jet_submit_address"/>

			<div id="lastmile_lightbox_map"></div>
			<div id="lastmile_lightbox_loading" style="display: none">'.$novijetverzendt->l('bezig met laden').'</div>


			<input type="hidden" name="lastmile_parcelshop_id" id="lastmile_parcelshop_id"/>
			<input type="hidden" name="lastmile_parcelshop_description" id="lastmile_parcelshop_description"/>

			<script type="text/javascript">

				$("#jet_address").val($("#jet_address_first").val());
				var map;
				var infowindow = [];
				var marker = [];
				var lookup = [];
				var geocoder;

				function initialize() {
					var myLatlng = new google.maps.LatLng(52.040638, 5.5626736);
					var myOptions = {
						zoom: 11,
						center: myLatlng
					}
					if($("#lastmile_lightbox_map").size() > 1)
						$("#lastmile_lightbox_map").eq(0).remove();
					map = new google.maps.Map(document.getElementById("lastmile_lightbox_map"), myOptions);
					geocoder = new google.maps.Geocoder();

					map.addListener(\'idle\', function () {
						getParcelShopsByAddress(map.getCenter().lat() + \',\' + map.getCenter().lng());

					});
				}

				function createMarker(lat, lng, html, icon, timeout) {
					window.setTimeout(function () {
						var newmarker = new google.maps.Marker({
							position: new google.maps.LatLng(lat, lng),
							map: map,
							icon: icon,
							animation: google.maps.Animation.DROP
						});

						newmarker[\'infowindow\'] = new google.maps.InfoWindow({
							content: html
						});

						google.maps.event.addListener(newmarker, \'click\', function () {
							closeInfowindows();
							this[\'infowindow\'].open(map, this);
						});

						marker.push(newmarker);

						lookup.push([lat, lng]);
					}, timeout);
				}

				function closeInfowindows() {
					for (var i = 0; i < marker.length; i++) {
						marker[i].infowindow.close();
					}
				}

				function getMarkerByPosition(lat, lng) {
					for (var i = 0, l = lookup.length; i < l; i++) {
						if (lookup[i][0] === lat && lookup[i][1] === lng) {
							return true;
						}
					}
					return false;
				}

				function getParcelShopsData(zip_code, number, country) {
					$.ajax({
						url : window.location.pathname  + "/index.php?fc=module&module=keendelivery&controller=process?getPlaces=yes&zip_code="+zip_code+"&number="+number+"&country="+country,
						type : "POST",
						data : "getPlaces=yes&zip_code="+zip_code+"&number="+number+"&country="+country,
						processData: false,  // tell jQuery not to process the data
						contentType: false,  // tell jQuery not to set contentType
						success : function(data) {
							result = JSON.parse(data);
							for (var index = 0; index < result.length; ++index) {

								var item = result[index];

								if (getMarkerByPosition(item.lat, item.lng) == false) {
									createMarker(item.lat, item.lng, item.html, item.marker, index * 125);
								}
							}
							$(\'#lastmile_lightbox_loading\').fadeIn();

						}
					});
				}

				function getZipCodeFromGeoCode(results) {
					for (i = 0; i < results.length; i++) {
						for (var j = 0; j < results[i].address_components.length; j++) {
							for (var k = 0; k < results[i].address_components[j].types.length; k++) {
								if (results[i].address_components[j].types[k] == "postal_code") {
									return results[i].address_components[j].short_name;
								}
							}
						}
					}
					alert(\'Er kon geen postcode van dit adres worden gevonden\');
				}

				function getNumberFromGeoCode(results) {
					for (i = 0; i < results.length; i++) {
						for (var j = 0; j < results[i].address_components.length; j++) {
							for (var k = 0; k < results[i].address_components[j].types.length; k++) {
								if (results[i].address_components[j].types[k] == "street_number") {
									return results[i].address_components[j].short_name;
								}
							}
						}
					}
					return 1;
				}


				function getCountryFromGeoCode(results) {
					for (i = 0; i < results.length; i++) {
						for (var j = 0; j < results[i].address_components.length; j++) {
							for (var k = 0; k < results[i].address_components[j].types.length; k++) {
								if (results[i].address_components[j].types[k] == "country") {
									return results[i].address_components[j].short_name;
								}
							}
						}
					}
					return \'NL\';
				}

				function getCoordinatesFromGeoCode(results) {
					for (i = 0; i < results.length; i++) {
						for (var j = 0; j < results[i].address_components.length; j++) {
							for (var k = 0; k < results[i].address_components[j].types.length; k++) {
								if (results[i].address_components[j].types[k] == "country") {
									return results[i].address_components[j].short_name;
								}
							}
						}
					}
					alert(\'Er konden geen coördinaten van dit adres gevonden worden\');
				}


				function getParcelShopsByAddress(address) {
					$(\'lastmile_lightbox_loading\').show();
					geocoder.geocode({\'address\': address}, function (results, status) {

						if (status === google.maps.GeocoderStatus.OK) {

							var latitude = results[0].geometry.location.lat();
							var longitude = results[0].geometry.location.lng();

							geocoder.geocode({\'address\': latitude + "," + longitude}, function (results, status)
							{
								zip_code = getZipCodeFromGeoCode(results);
								number = getNumberFromGeoCode(results);
								country = getCountryFromGeoCode(results);

								getParcelShopsData(zip_code, number, country);

								map.setCenter(results[0].geometry.location);
								var marker = new google.maps.Marker({
									position: results[0].geometry.location
								});
							});
						} else {
							alert(\'Van deze locatie kon geen adres bepaald worden\');
						}
					});
					return;
				}

				$(\'jet_address\').on(\'keypress\', function (event) {
					var key = event.which || event.keyCode;
					switch (key) {
						default:
							break;
						case Event.KEY_RETURN:
							getParcelShopsByAddress($(\'#jet_address\').val());
							break;
					}
				});


				$(\'#jet_submit_address\').on(\'click\', function () {
					getParcelShopsByAddress($(\'#jet_address\').val());

					return false;
				});

				function stopRKey(evt) {
					var evt = (evt) ? evt : ((event) ? event : null);
					var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
					if ((evt.keyCode == 13) && (node.type == "text")) {
						return false;
					}
				}

				document.onkeypress = stopRKey;

				initialize();
				getParcelShopsByAddress($(\'#jet_address\').val());


			</script>
		
		';
		echo $map;
	}



}