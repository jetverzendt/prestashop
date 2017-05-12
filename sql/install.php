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

//$sql[] = '
//	ALTER TABLE `'._DB_PREFIX_.'keendelivery`
//  DROP COLUMN IF EXISTS `option_1_quantity`,
//  DROP COLUMN IF EXISTS `option_1_mail`,
//  DROP COLUMN IF EXISTS `option_1_saturday_delivery`,
//  DROP COLUMN IF EXISTS `option_1_pickup_delivery`,
//  DROP COLUMN IF EXISTS `id_cart`,
//  DROP COLUMN IF EXISTS `option_2_quantity`,
//  DROP COLUMN IF EXISTS `option_2_reference`,
//  DROP COLUMN IF EXISTS `option_2_weight`,
//  DROP COLUMN IF EXISTS `option_2_signature`,
//  DROP COLUMN IF EXISTS `option_2_no_neighbors`,
//  DROP COLUMN IF EXISTS `option_2_evening`,
//  DROP COLUMN IF EXISTS `option_2_extra_cover`,
//  DROP COLUMN IF EXISTS `option_2_insured_value`,
//  DROP COLUMN IF EXISTS `option_2_pickup_delivery`,
//  DROP COLUMN IF EXISTS `option_2_pickup_date`,
//  DROP COLUMN IF EXISTS `option_2_saturday_delivery`,
//  DROP COLUMN IF EXISTS `option_2_amount`,
//  DROP COLUMN IF EXISTS `option_3_weight`,
//  DROP COLUMN IF EXISTS `option_3_pickup_date`;
//';
//
//$sql[] = '
//	ALTER TABLE `'._DB_PREFIX_.'keendelivery`
//  CHANGE IF EXISTS `shipping_type` `carrier` text null,
//  CHANGE IF EXISTS `option_1_reference` `reference` varchar(255) NULL,
//  CHANGE IF EXISTS `option_1_pickup_date` `pickup_date` date NULL,
//  CHANGE IF EXISTS `option_1_amount` `amount` int(11) NULL,
//  CHANGE IF EXISTS `option_1_weight` `weight` float NULL;
//';
$sql[] = '
	  DROP TABLE IF EXISTS `'._DB_PREFIX_.'keendelivery`;
    ';

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'keendelivery` (
  `id_novijetverzendt` int(11) NULL,
  `id_order` int(11) NULL,
  `carrier` int(11) NULL,
  `date` datetime NULL,
  `shipment_id` text NULL,
  `shipping_service` text NULL,
  `weight` float NULL,
  `reference` varchar(255) NULL,
  `pickup_date` date NULL,
  `amount` int(11) NULL,
  `parcel_shop` int(11) NULL,
  `label` text NULL,
  `track_and_trace_code` text NULL,
  `track_and_trace_url` text NULL
) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

ALTER TABLE `'._DB_PREFIX_.'keendelivery`
  ADD PRIMARY KEY (`id_novijetverzendt`);

ALTER TABLE `'._DB_PREFIX_.'keendelivery` CHANGE `id_novijetverzendt` `id_novijetverzendt` INT(11) NOT NULL AUTO_INCREMENT;';


$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'keendelivery_cart` (
  `id_novijetverzendt_cart` int(11) NULL,
  `id_cart` int(11) NULL,
  `shipping_type` int(11) NULL,
  `shipping_service` text NULL,
  `parcelshop_id` varchar(255) NULL,
  `deliverdate` date NULL,
  `deliverperiod` varchar(255) NULL,
  `deliverevening` varchar(255) NULL,
  `last_mile_choice` int(11) NULL,
  `parcelshop_description` text NULL,
  `extra_shipping` float NULL
) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

ALTER TABLE `'._DB_PREFIX_.'keendelivery_cart`
ADD PRIMARY KEY (`id_novijetverzendt_cart`);
ALTER TABLE `'._DB_PREFIX_.'keendelivery_cart` CHANGE `id_novijetverzendt_cart` `id_novijetverzendt_cart` INT(11) NOT NULL AUTO_INCREMENT;
';



foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;