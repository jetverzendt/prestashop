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

$sql = array();

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'novijetverzendt` (
  `id_novijetverzendt` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_cart` int(11) NOT NULL,
  `shipping_type` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `shipment_id` text NOT NULL,
  `shipping_service` text NOT NULL,
  `option_1_quantity` int(11) NOT NULL,
  `option_1_reference` varchar(255) NOT NULL,
  `option_1_mail` int(11) NOT NULL,
  `option_1_saturday_delivery` int(11) NOT NULL,
  `option_1_pickup_delivery` int(11) NOT NULL,
  `option_1_pickup_date` date NOT NULL,
  `option_1_amount` float NOT NULL,
  `option_1_weight` int(11) NOT NULL,
  `option_2_quantity` int(11) NOT NULL,
  `option_2_reference` varchar(255) NOT NULL,
  `option_2_weight` int(11) NOT NULL,
  `option_2_signature` int(11) NOT NULL,
  `option_2_no_neighbors` int(11) NOT NULL,
  `option_2_evening` int(11) NOT NULL,
  `option_2_extra_cover` int(11) NOT NULL,
  `option_2_insured_value` double NOT NULL,
  `option_2_pickup_delivery` int(11) NOT NULL,
  `option_2_pickup_date` date NOT NULL,
  `option_2_saturday_delivery` int(11) NOT NULL,
  `option_2_amount` double NOT NULL,
  `option_3_weight` int(11) NOT NULL,
  `label` text NOT NULL,
  `track_and_trace_code` text NOT NULL,
  `track_and_trace_url` text NOT NULL
) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

ALTER TABLE `'._DB_PREFIX_.'novijetverzendt`
  ADD PRIMARY KEY (`id_novijetverzendt`);

ALTER TABLE `'._DB_PREFIX_.'novijetverzendt` CHANGE `id_novijetverzendt` `id_novijetverzendt` INT(11) NOT NULL AUTO_INCREMENT;
';
$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'novijetverzendt_cart` (
  `id_novijetverzendt_cart` int(11) NOT NULL,
  `id_cart` int(11) NOT NULL,
  `shipping_type` int(11) NOT NULL,
  `shipping_service` text NOT NULL,
  `parcelshop_id` varchar(255) NOT NULL,
  `deliverdate` date NOT NULL,
  `deliverperiod` varchar(255) NOT NULL,
  `deliverevening` varchar(255) NOT NULL,
  `last_mile_choice` int(11) NOT NULL,
  `parcelshop_description` text NOT NULL
) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

ALTER TABLE `'._DB_PREFIX_.'novijetverzendt_cart`
ADD PRIMARY KEY (`id_novijetverzendt_cart`);
ALTER TABLE `'._DB_PREFIX_.'novijetverzendt_cart` CHANGE `id_novijetverzendt_cart` `id_novijetverzendt_cart` INT(11) NOT NULL AUTO_INCREMENT;
';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;