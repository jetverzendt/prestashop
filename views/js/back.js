/**
* 2007-2016 PrestaShop
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
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
function set_keen_shipment_and_order_fields() {
    set_shipment_field();
    set_service_field();
}

function set_shipment_field(){
    if(jQuery('#JETVERZENDT_SERVICE').prop('disabled')) {
        jQuery('#JETVERZENDT_SERVICE').prop('disabled', false);
    }

    var jet_product = jQuery('#JETVERZENDT_SHIPPER').val();
    jQuery(".keendelivery_fields").each(function () {
        jQuery(this).hide();
    });

    jQuery("." + jet_product).each(function () {
        jQuery(this).show();
    });
}

function set_service_field(){
    var jet_product = jQuery('#JETVERZENDT_SHIPPER').val();
    var first_service = jQuery('#JETVERZENDT_SERVICE .'+jet_product).val();
    document.getElementById('JETVERZENDT_SERVICE').value = first_service;
}

$( document ).ready(function() {
    if ($(".timepicker").length > 0) {
        $(".timepicker").timepicker({pickDate: false});
    }

    if($('#JETVERZENDT_SHIPPER').val()) {
        $('#JETVERZENDT_SERVICE').prop('disabled', false)

    }else{
        $('#JETVERZENDT_SERVICE').prop('disabled', true)
    }
    set_shipment_field();
});

