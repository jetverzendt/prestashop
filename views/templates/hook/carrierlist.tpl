{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{nocache}
{if $lm_active == 1}
	<div class='jet_shipping_options'>
		<span id="jet_name_lastmile">{$jet_name_lastmile|escape:'htmlall':'UTF-8'}</span>
		<a id="clear_last_mile" class="jet_small" href="javascript:void(0)" style="">{l s='(wissen)' mod='keendelivery'}</a>
		{if $lm_opt_1 == 1}
			<div class='jet_shipping_option' {if $jet_country != 'NL'}style='display:none;'{/if}>
				<a href='' id='jet_link_deliver_dates'>{l s='Kies een bezorgmoment' mod='keendelivery'}</a>
			</div>
		{/if}
		{if $lm_opt_2 == 1}
			<div class='jet_shipping_option'>
				<a href='' id='jet_link_dpd_saterday'>{l s='Zaterdaglevering' mod='keendelivery'}{if $lm_opt_2_price_display > 0} ( + € {$lm_opt_2_price_display|escape:'htmlall':'UTF-8'} ){/if}</a>
			</div>
		{/if}
		{if $lm_opt_3_1 == 1 || $lm_opt_3_2 == 1}
			<div class='jet_shipping_option'>
				<a href='' id='jet_link_parcelshops'>{l s='Kies een Parcelshop uit' mod='keendelivery'}
				{if $lm_opt_3_1_price_display > 0 || $lm_opt_3_2_price_display > 0}
					( + {if $lm_opt_3_1 == 1}{if $lm_opt_3_1_price_display > 0}€ {$lm_opt_3_1_price_display|escape:'htmlall':'UTF-8'}{/if}{/if}
					{if $lm_opt_3_1 == 1 && $lm_opt_3_2 == 1 && $lm_opt_3_1_price_display > 0} / {/if}
					{if $lm_opt_3_2 == 1}{if $lm_opt_3_2_price_display > 0}€ {$lm_opt_3_2_price_display|escape:'htmlall':'UTF-8'}{/if}{/if} )
				{/if}
				</a>
			</div>
		{/if}
		{if $lm_opt_4 == 1}
			<div class='jet_shipping_option' {if $jet_country != 'NL'}style='display:none;'{/if}>
				<a href='' id='jet_link_fadello'>{l s='Same Day Delivery' mod='keendelivery'}{if $lm_opt_4_price_display > 0} ( + € {$lm_opt_4_price_display|escape:'htmlall':'UTF-8'} ){/if}</a>
			</div>
		{/if}
		{if $lm_opt_5 == 1}
			<div class='jet_shipping_option' {if $jet_country != 'NL'}style='display:none;'{/if}>
				<a href='' id='jet_link_NextDayPremium'>{l s='Next Day Premium' mod='keendelivery'}{if $lm_opt_5_price_display > 0} ( + € {$lm_opt_5_price_display|escape:'htmlall':'UTF-8'} ){/if}</a>
			</div>
		{/if}
		<a href='#lastmile_lightbox' id='lastmile_lightbox_link'></a>
		<div id="lastmile_lightbox" style="display: none">
			<div id="lastmile_lightbox_content"></div>
		</div>
		<input id="lastmile_type" type="hidden" value="" name="lastmile_type">
		<input id="lastmile_service" type="hidden" name="lastmile_service">
		<input id="lastmile_deliverdate" type="hidden" name="lastmile_deliverdate">
		<input id="lastmile_parcelshop_id" type="hidden" name="lastmile_parcelshop_id">
		<input id="lastmile_parcelshop_description" type="hidden" name="lastmile_parcelshop_description">
		<input type="hidden" id="lastmile_deliverperiod" name="lastmile_deliverperiod">
		<input type="hidden" id="lastmile_deliverevening" name="lastmile_deliverevening">
		<input id="jet_address_first" type="hidden" name="jet_address_first" value="{$jet_address|escape:'htmlall':'UTF-8'}">
		<input id="jet_postcode" type="hidden" name="jet_postcode" value="{$jet_postcode|escape:'htmlall':'UTF-8'}">
		<input type="hidden" id="jet_last_mile_choice" name="jet_last_mile_choice">
		<input type="hidden" id="jet_carrier" name="{$jet_carrier|escape:'htmlall':'UTF-8'}">
		<input type="hidden" val="{$jet_extra_costs|escape:'htmlall':'UTF-8'}" name="extra_costs_shipping" id="extra_costs_shipping">
	</div>
	<script>
		function updateCart(){
			$.ajax({
				url : window.location.pathname + '/index.php?fc=module&module=keendelivery&controller=process?updateCart=yes&extra_costs_shipping='+$('#extra_costs_shipping').val()+'&lastmile_type='+$('#lastmile_type').val()+'&lastmile_service='+$('#lastmile_service').val()+'&lastmile_deliverdate='+$('#lastmile_deliverdate').val()+'&lastmile_parcelshop_id='+$('#lastmile_parcelshop_id').val()+'&lastmile_parcelshop_description='+$('#lastmile_parcelshop_description').val()+'&lastmile_deliverperiod='+$('#lastmile_deliverperiod').val()+'&lastmile_deliverevening='+$('#lastmile_deliverevening').val(),
				type : 'POST',
				data : 'updateCart=yes&lastmile_type='+$('#lastmile_type').val()+'&lastmile_service='+$('#lastmile_service').val()+'&lastmile_deliverdate='+$('#lastmile_deliverdate').val()+'&lastmile_parcelshop_id='+$('#lastmile_parcelshop_id').val()+'&lastmile_parcelshop_description='+$('#lastmile_parcelshop_description').val()+'&lastmile_deliverperiod='+$('#lastmile_deliverperiod').val()+'&lastmile_deliverevening='+$('#lastmile_deliverevening').val(),
				processData: false,  // tell jQuery not to process the data
				contentType: false,  // tell jQuery not to set contentType
				success : function(data) {
				}
			});
		}


		$(document).ready(function(){

			{if $jet_name_lastmile != ''}
				$('#jet_name_lastmile').show();
				$('#clear_last_mile').show();
			{/if}

			var our_carrier = '';
			//show lastmile options when keenDelivery carrier is autoselected after loading the page
			$('input.delivery_option_radio').each(function(){
				if(document.getElementById($(this).attr('id')).checked == true && $(this).val() == {$jet_carrier|escape:'htmlall':'UTF-8'}+',') $('.jet_shipping_options').show();
				if($(this).val() == {$jet_carrier|escape:'htmlall':'UTF-8'}+',') our_carrier = $(this).attr('id');
			});

			//show lastmile options when KeenDelivery carrier is selected
			$('input.delivery_option_radio').on('click', function(){
				var ref = $(this);
                if(document.getElementById($(ref).attr('id')).checked == true && $(ref).val() == {$jet_carrier|escape:'htmlall':'UTF-8'}+',') $('.jet_shipping_options').show();

			});

			//Set KeenDelivery carrier price in a seperate element
            var transp_price = $('#'+our_carrier).closest('td').parent().find('div.delivery_option_price').text().split(' ');
            var transp_price = parseFloat($('#'+our_carrier).closest('td').parent().find('div.delivery_option_price').text().replace('€ ', '').replace(' (Incl. BTW) ', '').replace(',', '.'));
            if(isNaN(transp_price)){
                transp_price = 0.00;
            }
            var transport_price = transp_price;
            if(!document.getElementById('transport_price_value'))
            $('#'+our_carrier).closest('td').parent().find('div.delivery_option_price').after('<div id="transport_price_value" class="hidden">'+transport_price+'</div>');

			$("#lastmile_lightbox_link").fancybox({
				'hideOnContentClick': false,
				'openEffect': 'elastic',
				'closeEffect': 'elastic',
				'showCloseButton':'false',
				'width': 1050,
				'autoDimensions': false,
				afterShow: function(){

				}
			});

			$('#jet_link_deliver_dates').click(function(e){
				e.preventDefault();
				setTimeout(function(){
					$.ajax({
						url : window.location.pathname + '/index.php?fc=module&module=keendelivery&controller=process?getDeliverySchedule=yes&jet_postcode='+$('#jet_postcode').val(),
						type : 'POST',
						data : 'getDeliverySchedule=yes&jet_postcode='+$('#jet_postcode').val(),
						processData: false,  // tell jQuery not to process the data
						contentType: false,  // tell jQuery not to set contentType
						success : function(data) {
							$('#lastmile_lightbox_content').html(data);
							$('#lastmile_lightbox_link').trigger('click');
						}
					});
				}, 600);
			});

			$('#jet_link_fadello').click(function(e){
				e.preventDefault();
				$('#clear_last_mile').show();
				$('#lastmile_service').val('Fadello');
				$('#lastmile_parcelshop_id').val('');
				$('#lastmile_parcelshop_description').val('');
				$('#jet_name_lastmile').html('{l s='Same Day Delivery' mod='keendelivery'}');
				$('#lastmile_type').val('fadello');
				$('#transport_price_value').prev().html('€ '+((parseFloat($('#transport_price_value').html()) + {$lm_opt_4_price|escape:'htmlall':'UTF-8'}).toFixed(2))+' (Incl. BTW) ');
				$('#total_shipping').html('€ '+((parseFloat($('#transport_price_value').html()) + {$lm_opt_4_price|escape:'htmlall':'UTF-8'}).toFixed(2)));
				$('#extra_costs_shipping').val({$lm_opt_4_price|escape:'htmlall':'UTF-8'});
				updateCart();
				return false;

			});

			$('#jet_link_NextDayPremium').click(function(e){
				e.preventDefault();
				$('#clear_last_mile').show();
				$('#lastmile_service').val('NextDayPremium');
				$('#lastmile_parcelshop_id').val('');
				$('#lastmile_parcelshop_description').val('');
				$('#jet_name_lastmile').html('{l s='Next Day Premium' mod='keendelivery'}');
				$('#lastmile_type').val('NextDayPremium');
				$('#transport_price_value').prev().html('€ '+((parseFloat($('#transport_price_value').html()) + {$lm_opt_5_price|escape:'htmlall':'UTF-8'}).toFixed(2))+' (Incl. BTW) ');
				$('#total_shipping').html('€ '+((parseFloat($('#transport_price_value').html()) + {$lm_opt_5_price|escape:'htmlall':'UTF-8'}).toFixed(2)));
				$('#extra_costs_shipping').val({$lm_opt_5_price|escape:'htmlall':'UTF-8'});
				updateCart();
				return false;

			});

			$('#jet_link_dpd_saterday').click(function(e){
				e.preventDefault();
				$.ajax({
					url : window.location.pathname + '/index.php?fc=module&module=keendelivery&controller=process?getDates=yes',
					type : 'POST',
					data : 'getDates=yes',
					processData: false,  // tell jQuery not to process the data
					contentType: false,  // tell jQuery not to set contentType
					success : function(data) {
						$('#lastmile_lightbox_content').html(data);
						$('#lastmile_lightbox_link').trigger('click');
					}
				});
			});

			$('#clear_last_mile').on('click', function(e){
				e.preventDefault();
				$('#jet_name_lastmile').html('');
				$('#clear_last_mile').hide();
				$('#lastmile_type').val('');
				$('#lastmile_service').val('');
				$('#lastmile_deliverdate').val('');
				$('#transport_price_value').prev().html('€ '+((parseFloat($('#transport_price_value').html())).toFixed(2))+' (Incl. BTW) ');
				$('#extra_costs_shipping').val(0);
			});

			$('#jet_link_parcelshops').click(function(e){
				e.preventDefault();
				setTimeout(function(){
					$.ajax({
						url : window.location.pathname + '/index.php?fc=module&module=keendelivery&controller=process?getMap=yes',
						type : 'POST',
						data : 'getMap=yes',
						processData: false,  // tell jQuery not to process the data
						contentType: false,  // tell jQuery not to set contentType
						success : function(data) {
							$('#lastmile_lightbox_link').trigger('click');
							$('#lastmile_lightbox_content').html(data);
						}
					});
				}, 600);
			});

		});
	</script>
{/if}
{/nocache}