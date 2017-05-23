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

{extends file="helpers/list/list_header.tpl"}
{nocache}
{block name=leadin}
{if isset($updateOrderStatus_mode) && $updateOrderStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose an order status' mod='keendelivery' }
		</div>
		<form action="{$REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
			<div class="radio">
				<label for="id_order_state">
					<select id="id_order_state" name="id_order_state">
{foreach from=$order_statuses item=order_status_name key=id_order_state}
						<option value="{$id_order_state|intval}">{$order_status_name|escape}</option>
{/foreach}
					</select>
				</label>
			</div>
{foreach $POST as $key => $value}
	{if is_array($value)}
		{foreach $value as $val}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
		{/foreach}
	{elseif strtolower($key) != 'id_order_state'}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />

	{/if}
{/foreach}
			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default">
					<i class="icon-remove"></i>
					{l s='Cancel' mod='keendelivery' }
				</button>
				<button type="submit" class="btn btn-default" name="submitUpdateOrderStatus">
					<i class="icon-check"></i>
					{l s='Update Order Status' mod='keendelivery' }
				</button>
			</div>
		</form>
	</div>
{/if}


{if isset($updateShippingStatus_mode) && $updateShippingStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose a Shipping' mod='keendelivery' }
		</div>
		<form action="{$REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
		{$novijetverzendt_text|escape:'UTF-8'}

			{foreach $POST as $key => $value}
				{if $key == 'orderBox'}
					{foreach $value as $val}
						<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
					{/foreach}
				{/if}
			{/foreach}

			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default" formnovalidate="formnovalidate">
					<i class="icon-remove"></i>
					{l s='Cancel' mod='keendelivery' }
				</button>
				<button type="submit" class="btn btn-default" name="{if $novijetverzendt_default == 1}submitUpdateShippingStatusDefault{else}submitUpdateShippingStatus{/if}">
					<i class="icon-check"></i>
					{l s='Send with KeenDelivery' mod='keendelivery' }
				</button>
				{if $novijetverzendt_default == 0}
					{*<div style="float: right">*}
						{*<button type="submit" class="btn btn-default" name="submitUpdateAutoShippingStatus">*}
							{*<i class="icon-check"></i>*}
							{*{l s='Autosend with these settings' mod='keendelivery' }*}
						{*</button>*}
					{*</div>*}
				{/if}
			</div>
		</form>
	</div>
{/if}
{/block}
{/nocache}