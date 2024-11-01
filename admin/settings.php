<?php
if (get_option('ADS24_LITE_plugin_symbol_position') == 'before') {
	$before = '<small>'.get_option('ADS24_LITE_plugin_currency_symbol').'</small> ';
} else {
	$before = '';
}
if (get_option('ADS24_LITE_plugin_symbol_position') != 'before') {
	$after = ' <small>'.get_option('ADS24_LITE_plugin_currency_symbol').'</small>';
} else {
	$after = '';
}

function selectedOpt($optName, $optValue)
{
	if(get_option('ADS24_LITE_plugin_'.$optName) == $optValue) {
		echo 'selected="selected"';
	}
}

function validValue($variableName)
{
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		echo $_POST[$variableName];
	} else {
		echo get_option('ADS24_LITE_plugin_'.$variableName);
	}
}

function validNewValue($arr, $param)
{
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST[$param] != '') {
		echo stripslashes($_POST[$param]);
	} else {
		$getArr = get_option(ADS24_LITE_ID.$arr);
		echo stripslashes($getArr[$param]);
	}
}

function ifCheckboxEnabled($variableName)
{
	$getArr = get_option(ADS24_LITE_ID.'_settings');
	if ( $variableName == 'woo_item' && $getArr[$variableName] != '' ) {
		echo 'value="1" checked';
	} elseif ( get_option('ADS24_LITE_plugin_'.$variableName) != '' ) {
		echo 'value="1" checked';
	} else {
		echo 'value="0"';
	}
}

function validSelectedOpt($optName, $optValue)
{
	if ( get_option(ADS24_LITE_ID.'_'.$optName) == $optValue || isset($_POST[$optName]) && $_POST[$optName] == $optValue ) {
		echo 'selected="selected"';
	}
}

function validNewSelectedOpt($arr, $param, $value, $type = null)
{
	$getArr = get_option(ADS24_LITE_ID.$arr);
	if ( isset($getArr[$param]) && $getArr[$param] == $value || isset($_POST[$param]) && $_POST[$param] == $value ) {
		if ( $type == 'checkbox' ) {
			echo 'checked="checked"';
		} else {
			echo 'selected="selected"';
		}
	}
}
?>
	<h2><i class="dashicons-before dashicons-admin-settings"></i> Ads24 Lite Settings</h2>

	<h2 class="nav-tab-white nav-tab-wrapper">
		<a href="#a24pPayment" class="nav-tab nav-tab-active" id="a24pPaymenttab" data-group="a24pTabPayment">Payment</a>
		<a href="#a24pReInstallation" id="a24pReInstallationtab" class="nav-tab" data-group="a24pTabReInstallation">Re-installation</a>
		<a href="#a24pHooks" id="a24pHookstab" class="nav-tab" data-group="a24pTabHooks">Hooks</a>
		<a href="#a24pBuddyPress" id="a24pBuddyPresstab" class="nav-tab" data-group="a24pTabBuddyPress">BuddyPress</a>
		<a href="#a24pBbPress" id="a24pBbPresstab" class="nav-tab" data-group="a24pTabBbPress">bbPress</a>
		<a href="#a24pNotifications" id="a24pNotificationstab" class="nav-tab" data-group="a24pTabNotifications">Notifications</a>
		<a href="#a24pAdmin" id="a24pAdmintab" class="nav-tab" data-group="a24pTabAdmin">Admin</a>
		<a href="#a24pMedia" id="24pMediatab" class="nav-tab" data-group="a24pTabMedia">Media</a>
		<a href="#a24pCustomization" id="a24pCustomizationtab" class="nav-tab" data-group="a24pTabOrderForm">Customization</a>
	</h2>

	<form action="" method="post" novalidate>
		<input type="hidden" value="updateSettings" name="a24pProAction">
		<table class="a24pAdminTable a24pMarTopNull form-table">
			<tbody id="a24pPayment" class="a24pTabPayment a24pTbody">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-cart"></span> Payments Settings</h3>
					</th>
				</tr>
				<!--<tr class="a24pBottomLine">
					<th scope="row"><label for="purchase_code">Purchase Code</label></th>
					<td><input type="text" class="regular-text code" value="<?php validValue('purchase_code'); ?>" id="purchase_code" name="purchase_code">
						<p class="description"><strong style="<?php echo ((validValue('purchase_code') != '') ? '' : 'color:red') ?>">This field is required to unlock all features!</strong> You can download it from <a href="http://codecanyon.net/item/ads-pro-multipurpose-wordpress-ad-manager/10275010?ref=scripteo">CodeCanyon</a></p></td>
				</tr>-->
				<tr>
					<th scope="row"><label for="paypal">PayPal E-mail</label>
						<div class="switch-wrapper"><input class="a24pSwitch" data-section="a24p-paypal-section" type="checkbox" <?php ifCheckboxEnabled('paypal'); ?>></div>
					</th>
					<td><input type="text" class="regular-text code a24p-paypal-section a24p-paypal-section-input" value="<?php validValue('paypal'); ?>" id="paypal" name="paypal">
						<p class="description a24p-paypal-section">At this address you will receive PayPal payments.</p></td>
				</tr>
                
				<tr>
                
                <div id="lite-upgrade" style="position: relative;">
                <div id="lite-pg-methods"></div>
                </tr>
                
                <tr>
					<th scope="row"><label for="secret_key">Stripe Secret Key</label>
						<div class="switch-wrapper"><input class="a24pSwitch"  type="checkbox" checked="checked"></div>
					</th>
					<td><input type="text" class="regular-text ltr a24p-stripe-section a24p-stripe-section-input" value="<?php validValue('secret_key'); ?>" id="stripe_code" name="secret_key">
						<p class="description a24p-stripe-section">Stripe > Your account > Account Settings > API Keys</p></td>
				</tr>
				<tr class="a24p-stripe-section">
					<th scope="row"><label for="publishable_key">Stripe Publishable Key</label></th>
					<td><input type="text" class="regular-text ltr a24p-stripe-section-input" value="<?php validValue('publishable_key'); ?>" id="publishable_key" name="publishable_key">
						<p class="description">Stripe > Your account > Account Settings > API Keys</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="bank_transfer_content">Bank Transfer Details</label>
						<div class="switch-wrapper"><input class="a24pSwitch" data-section="a24p-bank-section" type="checkbox" <?php ifCheckboxEnabled('trans_payment_bank_transfer_content'); ?>></div>
					</th>
					<td>
						<textarea id="bank_transfer_content" name="trans_payment_bank_transfer_content" class="regular-text a24p-bank-section a24p-bank-section-input" rows="3" cols="40"><?php validValue('trans_payment_bank_transfer_content'); ?></textarea>
					</td>
				</tr>
				<tr class="a24pBottomLine">
					<th scope="row"><label for="woo_item">WooCommerce</label>
						<div class="switch-wrapper"><input class="a24pSwitch" type="checkbox" checked="checked"></div>
					</th>
					<td>
						<select id="woo_item" class="a24p-woocommerce-section a24p-woocommerce-section-input">
							<option value="">Select Item</option>
							
						</select>
						<p class="description a24p-woocommerce-section">Choose WooCommerce item. Item will be used in the cart.</p></td>
                        <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-pg-methods-btn" target="_blank" class="upgrade-btn">Upgrade to Pro</a>
				</tr>
                </div>
				<tr>
					<th scope="row"><label for="ordering_form_url">URL to the Order Form</label></th>
					<td><input type="url" class="regular-text code" maxlength="1000" value="<?php validValue('ordering_form_url'); ?>" id="ordering_form_url" name="ordering_form_url">
						<p class="description">Order Form you can display by shortcode <strong>[ADS24_LITE_form_and_stats]</strong></p>
						<p class="description"><strong>Example</strong> http://your_page.com/order_ads</p></td>
				</tr>
				<tr class="a24p-paypal-section">
					<th scope="row"><label for="currency_code">PayPal Currency Code</label></th>
					<td><input type="text" class="regular-text ltr a24p-paypal-section" value="<?php echo get_option('ADS24_LITE_plugin_'.'currency_code') ?>" id="currency_code" name="currency_code">
						<p class="description a24p-paypal-section">More information about PayPal Currency Codes <a href="https://developer.paypal.com/docs/classic/api/currency_codes/">here</a>.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="currency_symbol">Currency symbol</label></th>
					<td><input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'currency_symbol') ?>" id="currency_symbol" name="currency_symbol"></td>
				</tr>
				<tr>
					<th scope="row">Price format (symbol position)</th>
					<td>
						<fieldset>
							<label title="symbol before"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'symbol_position') == 'before') { echo 'checked="checked"'; } ?> value="before" name="symbol_position"><strong>before</strong> price <span>(eg. <strong>$10</strong>)</span></label><br>
							<label title="symbol after"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'symbol_position') == 'after') { echo 'checked="checked"'; } ?>value="after" name="symbol_position"><strong>after</strong> price <span>(eg. <strong>10$</strong>)</span></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Show the Order Form only for logged in users</th>
					<td>
						<fieldset>
							<label title="yes"><input type="radio" <?php if(a24p_get_opt('settings', 'form_restrictions') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="form_restrictions"><strong>yes</strong></label><br>
							<label title="no"><input type="radio" <?php if(a24p_get_opt('settings', 'form_restrictions') == 'no') { echo 'checked="checked"'; } ?>value="no" name="form_restrictions"><strong>no</strong></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Show optional field in the Order Form</th>
					<td>
						<fieldset>
							<label title="show optional field in the order form"><input type="radio" <?php if(a24p_get_opt('order_form', 'optional_field') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="optional_field"><strong>yes</strong></label><br>
							<label title="hide optional field in the order form"><input type="radio" <?php if(a24p_get_opt('order_form', 'optional_field') == 'no') { echo 'checked="checked"'; } ?>value="no" name="optional_field"><strong>no</strong></label>
						</fieldset>
					</td>
				</tr>
			</tbody>
			<tbody id="a24pReInstallation" class="a24pTabReInstallation a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-admin-plugins"></span> Re-installation</h3>
				</th>
			</tr>
			<tr>
				<th class="a24pLast" scope="row">Delete all the data when uninstalling?</th>
				<td class="a24pLast">
					<fieldset>
						<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'installation') == 'no') { echo 'checked="checked"'; } ?> value="no" name="installation"><strong>no</strong>, keep all added adslots and ads</label><br>
						<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'installation') == 'yes') { echo 'checked="checked"'; } ?>value="yes" name="installation"><strong>yes</strong>, remove all data (adslots and ads)</label>
					</fieldset>
				</td>
			</tr>
			</tbody>
			<tbody id="a24pHooks" class="a24pTabHooks a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-editor-insertmore"></span> Hooks</h3>
				</th>
			</tr>
			<tr>
				<th scope="row"><label for="before_hook">Show Ads before content</label></th>
				<td>
					<textarea id="before_hook" name="before_hook" class="regular-text ltr" rows="7" cols="50"><?php echo get_site_option('ADS24_LITE_plugin_'.'before_hook'); ?></textarea>
					<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
				</td>
			</tr>
			<?php for ($i = 1; $i <= 10; $i++): ?>
				<tr>
					<th scope="row"><label for="after_<?php echo $i ?>_paragraph">Show Ads after #<?php echo $i ?> paragraph<br> <small>&lt;/p&gt; tag closing each paragraph</small></label></th>
					<td>
						<textarea id="after_<?php echo $i ?>_paragraph" name="after_<?php echo $i ?>_paragraph" class="regular-text ltr" rows="1" cols="50"><?php echo get_site_option('ADS24_LITE_plugin_'.'after_' . $i . '_paragraph'); ?></textarea>
						<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
					</td>
				</tr>
			<?php endfor; ?>
			<tr>
				<th class="a24pLast" scope="row"><label for="after_hook">Show Ads after content</label></th>
				<td class="a24pLast">
					<textarea id="after_hook" name="after_hook" class="regular-text ltr" rows="7" cols="50"><?php echo get_site_option('ADS24_LITE_plugin_'.'after_hook'); ?></textarea>
					<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
				</td>
			</tr>
			</tbody>
			<tbody id="a24pBuddyPress" class="a24pTabBuddyPress a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-editor-insertmore"></span> BuddyPress Hooks</h3>
				</th>
			</tr>
            
            <div id="lite-upgrade" style="position: relative;">
                <div id="lite-bp-hook"></div>
			<tr>
				<th colspan="2">
					<h3>Stream (ads after activities)</h3>
				</th>
			</tr>
			<?php for ($i = 1; $i <= 20; $i++): ?>
				<tr>
					<th scope="row"><label for="after_<?php echo $i ?>_activity">Show Ads after #<?php echo $i ?> activity</label></th>
					<td>
						<textarea id="after_<?php echo $i ?>_activity" class="regular-text ltr" rows="1" cols="50"></textarea>
						<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
					</td>
				</tr>
			<?php endfor; ?>
            <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-bp-hook-btn" target="_blank" class="upgrade-btn" style="display: block;">Upgrade to Pro</a>
            </div>
			</tbody>
			<tbody id="a24pBbPress" class="a24pTabBbPress a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-editor-insertmore"></span> bbPress Hooks</h3>
				</th>
			</tr>
			<tr>
				<th colspan="2">
					<h3>Forum (ads after topics)</h3>
				</th>
			</tr>
            <div id="lite-upgrade" style="position: relative;">
                <div id="lite-bb-hook"></div>
			<?php for ($i = 1; $i <= get_option( '_bbp_topics_per_page', '15' ); $i++): ?>
				<tr>
					<th scope="row"><label for="after_<?php echo $i ?>_topic">Show Ads after #<?php echo $i ?> topic</label></th>
					<td>
						<textarea id="after_<?php echo $i ?>_topic" class="regular-text ltr" rows="1" cols="50"></textarea>
						<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
					</td>
				</tr>
			<?php endfor; ?>
			<tr>
				<th colspan="2">
					<h3>Topic (ads after replies)</h3>
				</th>
			</tr>
			<?php for ($i = 1; $i <= get_option( '_bbp_replies_per_page', '15' ); $i++): ?>
				<tr>
					<th scope="row"><label for="after_<?php echo $i ?>_reply">Show Ads after #<?php echo $i ?> reply</label></th>
					<td>
						<textarea id="after_<?php echo $i ?>_reply" class="regular-text ltr" rows="1" cols="50"></textarea>
						<p class="description"><strong>Example:</strong> separate semicolon <strong>;</strong><br>[ADS24_LITE_adslot id="1"] ; [ADS24_LITE_adslot id="2"] ; [ADS24_LITE_adslot id="3"]</p>
					</td>
				</tr>
			<?php endfor; ?>
            <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-bb-hook-btn" target="_blank" class="upgrade-btn" style="display: block;">Upgrade to Pro</a>
            </div>
			</tbody>
			<tbody id="a24pNotifications" class="a24pTabNotifications a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-microphone"></span> Notifications</h3>
				</th>
			</tr>
			<tr>
				<th scope="row">Send email reminder to the Buyer if expires Ads</th>
				<td>
					<fieldset>
						<label title="yes"><input type="radio" <?php validNewSelectedOpt('_settings', 'up_expires_notice', 'yes', 'checkbox'); ?> value="yes" name="up_expires_notice"><strong>yes</strong></label><br>
						<label title="no"><input type="radio" <?php validNewSelectedOpt('_settings', 'up_expires_notice', 'no', 'checkbox'); ?>value="no" name="up_expires_notice"><strong>no</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Send email reminder to the Buyer if expired Ads</th>
				<td>
					<fieldset>
						<label title="yes"><input type="radio" <?php validNewSelectedOpt('_settings', 'up_expired_notice', 'yes', 'checkbox'); ?> value="yes" name="up_expired_notice"><strong>yes</strong></label><br>
						<label title="no"><input type="radio" <?php validNewSelectedOpt('_settings', 'up_expired_notice', 'no', 'checkbox'); ?>value="no" name="up_expired_notice"><strong>no</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="up_cpc_notice">Send CPC email reminder if less than</label></th>
				<td>
					<select id="up_cpc_notice" name="up_cpc_notice">
						<option value="5" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 5); ?>>5 clicks to the end</option>
						<option value="6" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 6); ?>>6 clicks to the end</option>
						<option value="7" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 7); ?>>7 clicks to the end</option>
						<option value="8" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 8); ?>>8 clicks to the end</option>
						<option value="9" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 9); ?>>9 clicks to the end</option>
						<option value="10" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 10); ?>>10 clicks to the end</option>
						<option value="15" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 15); ?>>15 clicks to the end</option>
						<option value="20" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 20); ?>>20 clicks to the end</option>
						<option value="30" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 30); ?>>30 clicks to the end</option>
						<option value="40" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 40); ?>>40 clicks to the end</option>
						<option value="50" <?php validNewSelectedOpt('_settings', 'up_cpc_notice', 50); ?>>50 clicks to the end</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="up_cpm_notice">Send CPM email reminder if less than</label></th>
				<td>
					<select id="up_cpm_notice" name="up_cpm_notice">
						<option value="100" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 100); ?>>100 views to the end</option>
						<option value="250" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 250); ?>>250 views to the end</option>
						<option value="500" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 500); ?>>500 views to the end</option>
						<option value="1000" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 1000); ?>>1000 views to the end</option>
						<option value="2500" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 2500); ?>>2500 views to the end</option>
						<option value="5000" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 5000); ?>>5000 views to the end</option>
						<option value="7500" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 7500); ?>>7500 views to the end</option>
						<option value="10000" <?php validNewSelectedOpt('_settings', 'up_cpm_notice', 10000); ?>>10000 views to the end</option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="a24pLast" scope="row"><label for="up_cpd_notice">Send CPD email reminder if less than</label></th>
				<td class="a24pLast">
					<select id="up_cpd_notice" name="up_cpd_notice">
						<option value="2" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 2); ?>>2 days to the end</option>
						<option value="3" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 3); ?>>3 days to the end</option>
						<option value="4" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 4); ?>>4 days to the end</option>
						<option value="5" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 5); ?>>5 days to the end</option>
						<option value="6" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 6); ?>>6 days to the end</option>
						<option value="7" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 7); ?>>7 days to the end</option>
						<option value="8" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 8); ?>>8 days to the end</option>
						<option value="9" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 9); ?>>9 days to the end</option>
						<option value="10" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 10); ?>>10 days to the end</option>
						<option value="14" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 14); ?>>14 days to the end</option>
						<option value="21" <?php validNewSelectedOpt('_settings', 'up_cpd_notice', 21); ?>>21 days to the end</option>
					</select>
				</td>
			</tr>
			</tbody>
			<tbody id="a24pAdmin" class="a24pTabAdmin a24pTbody" style="display:none">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-admin-settings"></span> Admin</h3>
				</th>
			</tr>
			<tr>
				<th scope="row">Users can edit Ads in the frontend / backend panel</th>
				<td>
					<fieldset>
						<label title="backend"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'editable') == 'backend') { echo 'checked="checked"'; } ?> value="backend" name="editable"><strong>backend</strong></label><br>
						<label title="frontend"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'editable') == 'frontend') { echo 'checked="checked"'; } ?> value="frontend" name="editable"><strong>frontend</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">RTL Support</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'rtl_support') == 'no') { echo 'checked="checked"'; } ?> value="no" name="rtl_support"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'rtl_support') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="rtl_support"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Disable preview for HTML Ad</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'html_preview') == 'no') { echo 'checked="checked"'; } ?> value="no" name="html_preview"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'html_preview') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="html_preview"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Hide all ads for logged users</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'hide_if_logged') == 'no') { echo 'checked="checked"'; } ?> value="no" name="hide_if_logged"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'hide_if_logged') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="hide_if_logged"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Disable Admin Bar link</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'link_bar') == 'no') { echo 'checked="checked"'; } ?> value="no" name="link_bar"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'link_bar') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="link_bar"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Selection method of AdSlots</th>
				<td>
					<fieldset>
						<label title="tabs"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'selection') == 'tabs') { echo 'checked="checked"'; } ?> value="tabs" name="selection"><strong>tabs</strong></label><br>
						<label title="select"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'selection') == 'select') { echo 'checked="checked"'; } ?> value="select" name="selection"><strong>select</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Show an Ad Name field on the list</th>
				<td>
					<fieldset>
						<label title="yes"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'ad_name') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="ad_name"><strong>yes</strong></label><br>
						<label title="no"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'ad_name') == 'no') { echo 'checked="checked"'; } ?>value="no" name="ad_name"><strong>no</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Use rel="nofollow" attribute for all links</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'nofollow') == 'no') { echo 'checked="checked"'; } ?> value="no" name="nofollow"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(a24p_get_opt('admin_settings', 'nofollow') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="nofollow"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">Show Coundown inside Ads</th>
				<td>
					<fieldset>
						<label title="no"><input type="radio" <?php if(a24p_get_opt('other', 'countdown') == 'no') { echo 'checked="checked"'; } ?> value="no" name="countdown"><strong>no</strong></label><br>
						<label title="yes"><input type="radio" <?php if(a24p_get_opt('other', 'countdown') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="countdown"><strong>yes</strong></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="clicks_counter">Change Click Dashboard Counter</label></th>
				<td>
					<input type="number" class="regular-text ltr" value="" id="clicks_counter" name="clicks_counter">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="views_counter">Change Views Dashboard Counter</label></th>
				<td>
					<input type="number" class="regular-text ltr" value="" id="views_counter" name="views_counter">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="upload_dir">Upload DIR <br>(as default keep it empty)</label></th>
				<td>
					<input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'upload_dir') ?>" id="upload_dir" name="upload_dir">
					<p class="description"><strong>Use this option carefully because this option affect on upload folder.</strong><br>(default: a24p-lite-upload)</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="prefix">Cache Prefix <br>(as default keep it empty)</label></th>
				<td>
					<input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'prefix') ?>" id="prefix" name="prefix">
					<p class="description">Use unique prefix for each site if you are using multiple wordpress installation with the one domain.</p>
				</td>
			</tr>
			<tr>
				<th class="a24pLast" scope="row"><label for="privileges">Access as Admin for Users with Capability</label></th>
				<td class="a24pLast">
					<input type="text" class="regular-text ltr" value="<?php echo a24p_get_opt('admin_settings', 'privileges') ?>" id="privileges" name="privileges">
					<p class="description"><strong>Use this option carefully because you can give access by unauthorized users.</strong><br>(e.g. manage_option,install_plugins)</p><br><br>
				</td>

			</tr>
			</tbody>
			<tbody id="a24pMedia" class="a24pTabMedia a24pTbody" style="display:none">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-format-image"></span> File & Ads</h3>
					</th>
				</tr>
				<tr>
					<th scope="row">Example Ad if empty AdSlot</th>
					<td>
						<fieldset>
							<label title="no"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'example_ad') == 'no') { echo 'checked="checked"'; } ?> value="no" name="example_ad"><strong>no</strong></label><br>
							<label title="yes"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'example_ad') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="example_ad"><strong>yes</strong></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Optimize All Images (crop tool)</th>
					<td>
						<fieldset>
							<label title="yes, gif animations will not be available"><input type="radio" <?php if(a24p_get_opt('other', 'crop_tool') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="crop_tool"><strong>yes</strong>, gif animations will not be available</label><br>
							<label title="no, gif animations will be available"><input type="radio" <?php if(a24p_get_opt('other', 'crop_tool') == 'no') { echo 'checked="checked"'; } ?>value="no" name="crop_tool"><strong>no</strong>, gif animations will  be available</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Optimize All CSS files into One</th>
					<td>
						<fieldset>
							<label title="no"><input type="radio" <?php if(a24p_get_opt('other', 'optimization') == 'no') { echo 'checked="checked"'; } ?> value="no" name="optimization"><strong>no</strong></label><br>
							<label title="yes"><input type="radio" <?php if(a24p_get_opt('other', 'optimization') == 'yes') { echo 'checked="checked"'; } ?> value="yes" name="optimization"><strong>yes</strong></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Carousel Script for Slider</th>
					<td>
						<fieldset>
							<label title="owl"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'carousel_script') == 'owl') { echo 'checked="checked"'; } ?> value="owl" name="carousel_script"><strong>owlCarousel</strong></label><br>
							<label title="bx"><input type="radio" <?php if(get_option('ADS24_LITE_plugin_'.'carousel_script') == 'bx') { echo 'checked="checked"'; } ?> value="bx" name="carousel_script"><strong>bxSlider</strong></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="thumb_size">Maximum upload file size <br>(default 400kb)</label></th>
					<td><input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'thumb_size') ?>" id="thumb_size" name="thumb_size"> <abbr title="kilobyte">kb</abbr></td>
				</tr>
				<tr>
					<th scope="row"><label for="thumb_w">Image, maximum width <br>(default 1024px)</label></th>
					<td><input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'thumb_w') ?>" id="thumb_w" name="thumb_w"> <abbr title="pixels">px</abbr></td>
				</tr>
				<tr class="a24pBottomLine">
					<th class="a24pLast" scope="row"><label for="thumb_h">Image, maximum height <br>(default 800px)</label></th>
					<td class="a24pLast"><input type="text" class="regular-text ltr" value="<?php echo get_option('ADS24_LITE_plugin_'.'thumb_h') ?>" id="thumb_h" name="thumb_h"> <abbr title="pixels">px</abbr></td>
				</tr>
<!--				<tr>-->
<!--					<th scope="row"><label for="max_title">Maximum length of Ad Title</label></th>-->
<!--					<td><input type="text" class="regular-text ltr" value="--><?php //echo get_option('ADS24_LITE_plugin_'.'max_title') ?><!--" id="max_title" name="max_title"> <abbr>(40-70 characters)</abbr></td>-->
<!--				</tr>-->
<!--				<tr>-->
<!--					<th scope="row"><label for="max_desc">Maximum length of Ad Description</label></th>-->
<!--					<td><input type="text" class="regular-text ltr" value="--><?php //echo get_option('ADS24_LITE_plugin_'.'max_desc') ?><!--" id="max_desc" name="max_desc"> <abbr>(80-140 characters)</abbr></td>-->
<!--				</tr>-->
			</tbody>
			<tbody id="a24pCustomization" class="a24pTabOrderForm a24pTbody" style="display:none">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-admin-appearance"></span> Order Form Customization</h3>
					</th>
				</tr>
                <div id="lite-upgrade" style="position: relative;">
                <div id="lite-custom"></div>
				<tr>
					<th scope="row"><label for="form_bg">Form Background</label></th>
					<td>
						<input id="form_bg"
							   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_c">Form Text Color</label></th>
					<td>
						<input id="form_c"
							   data-default-color="#444444" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_input_bg">Input Background</label></th>
					<td>
						<input id="form_input_bg"
							   data-default-color="#f5f5f5" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_input_c">Input Color</label></th>
					<td>
						<input id="form_input_c"
							   data-default-color="#444444" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_price_c">Price Color</label></th>
					<td>
						<input id="form_price_c"
							   data-default-color="#65cc84" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_discount_bg">Discount Background</label></th>
					<td>
						<input id="form_discount_bg"
							   data-default-color="#df5050" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_discount_c">Discount Color</label></th>
					<td>
						<input id="form_discount_c"
							   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_button_bg">Button Background</label></th>
					<td>
						<input id="form_button_bg"
							   data-default-color="#65cc84" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_button_c">Button Color</label></th>
					<td>
						<input id="form_button_c"
							   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
                <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-custom-btn" target="_blank" class="lite-custom-btn" style="display: block;">Upgrade to Pro</a>
                </div>
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-admin-appearance"></span> Alert Colors</h3>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="form_alert_c">Alert Text Color</label></th>
					<td>
						<input id="form_alert_c"
							   name="form_alert_c"
							   value="<?php echo get_option('ADS24_LITE_plugin_'.'form_alert_c') ?>"
							   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_alert_success_bg">Success Background</label></th>
					<td>
						<input id="form_alert_success_bg"
							   name="form_alert_success_bg"
							   value="<?php echo get_option('ADS24_LITE_plugin_'.'form_alert_success_bg') ?>"
							   data-default-color="#65cc84" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="form_alert_failed_bg">Failed Background</label></th>
					<td>
						<input id="form_alert_failed_bg"
							   name="form_alert_failed_bg"
							   value="<?php echo get_option('ADS24_LITE_plugin_'.'form_alert_failed_bg') ?>"
							   data-default-color="#df5050" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-admin-appearance"></span> Chart Colors</h3>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="stats_views_line">Stats Views Color</label></th>
					<td>
						<input id="stats_views_line"
							   name="stats_views_line"
							   value="<?php echo get_option('ADS24_LITE_plugin_'.'stats_views_line') ?>"
							   data-default-color="#673AB7" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="stats_clicks_line">Stats Clicks Color</label></th>
					<td>
						<input id="stats_clicks_line"
							   name="stats_clicks_line"
							   value="<?php echo get_option('ADS24_LITE_plugin_'.'stats_clicks_line') ?>"
							   data-default-color="#FBCD39" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="table_color">Table Section - Color</label></th>
					<td>
						<input id="table_color" name="table_color" value="<?php echo a24p_get_opt($opt, 'table_color') ?>" data-default-color="#000000" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-admin-appearance"></span> User Panel Customization</h3>
					</th>
				</tr>
				<?php $opt = 'user_panel'; ?>
				<tr>
					<th scope="row"><label for="head_bg">Head Background</label></th>
					<td>
						<input id="head_bg" name="head_bg" value="<?php echo a24p_get_opt($opt, 'head_bg') ?>" data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="head_color">Head Color</label></th>
					<td>
						<input id="head_color" name="head_color" value="<?php echo a24p_get_opt($opt, 'head_color') ?>" data-default-color="#000000" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="body_bg">Body Background</label></th>
					<td>
						<input id="body_bg" name="body_bg" value="<?php echo a24p_get_opt($opt, 'body_bg') ?>" data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="body_color">Body Color</label></th>
					<td>
						<input id="body_color" name="body_color" value="<?php echo a24p_get_opt($opt, 'body_color') ?>" data-default-color="#000000" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="separator">Separator</label></th>
					<td>
						<input id="separator" name="separator" value="<?php echo a24p_get_opt($opt, 'separator') ?>" data-default-color="#ededed" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="link_color">Link Color</label></th>
					<td>
						<input id="link_color" name="link_color" value="<?php echo a24p_get_opt($opt, 'link_color') ?>" data-default-color="#21759b" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pending_bg">Pending Status - Background</label></th>
					<td>
						<input id="pending_bg" name="pending_bg" value="<?php echo a24p_get_opt($opt, 'pending_bg') ?>" data-default-color="#999" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pending_color">Pending Status - Color</label></th>
					<td>
						<input id="pending_color" name="pending_color" value="<?php echo a24p_get_opt($opt, 'pending_color') ?>" data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="active_bg">Active Status - Background</label></th>
					<td>
						<input id="active_bg" name="active_bg" value="<?php echo a24p_get_opt($opt, 'active_bg') ?>" data-default-color="#4DA720" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="active_color">Active Status - Color</label></th>
					<td>
						<input id="active_color" name="active_color" value="<?php echo a24p_get_opt($opt, 'active_color') ?>" data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="expired_bg">Expired Status - Background</label></th>
					<td>
						<input id="expired_bg" name="expired_bg" value="<?php echo a24p_get_opt($opt, 'expired_bg') ?>" data-default-color="#FF2A13" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="expired_color">Expired Status - Color</label></th>
					<td>
						<input id="expired_color" name="expired_color" value="<?php echo a24p_get_opt($opt, 'expired_color') ?>" data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_bg">Button - Background</label></th>
					<td>
						<input id="button_bg" name="button_bg" value="<?php echo a24p_get_opt($opt, 'button_bg') ?>" data-default-color="#673ab7" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_color">Button - Color</label></th>
					<td>
						<input id="button_color" name="button_color" value="<?php echo a24p_get_opt($opt, 'button_color') ?>" data-default-color="#ffd71a" type="text" class="a24pColorPicker">
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-admin-appearance"></span> Custom CSS / JS</h3>
					</th>
				</tr>
                <div id="lite-upgrade" style="position: relative;">
                <div id="lite-custom-code"></div>
				<tr>
					<th scope="row"><label for="custom_css">Custom CSS</label></th>
					<td>
						<textarea id="custom_css" name="custom_css" class="regular-text ltr" rows="17" cols="70"><?php echo get_option('ADS24_LITE_plugin_'.'custom_css') ?></textarea>
					</td>
				</tr>
				<tr>
					<th class="a24pLast" scope="row"><label for="custom_js">Custom JavaScript</label></th>
					<td class="a24pLast">
						<textarea id="custom_js" name="custom_js" class="regular-text ltr" rows="17" cols="70"><?php echo get_option('ADS24_LITE_plugin_'.'custom_js') ?></textarea>
					</td>
				</tr>
                <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-custom-code-btn" target="_blank" class="lite-custom-code-btn" style="display: block;">Upgrade to Pro</a>
                </div>
				<?php if ( get_option('ADS24_LITE_plugin_calendar') == 'yes' ): ?>
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-calendar-alt"></span> Calendar Advanced Settings</h3>
					</th>
				</tr>
				<tr>
					<th class="a24pLast" scope="row"><label for="advanced_calendar">Custom JavaScript</label></th>
					<td class="a24pLast">
						<textarea id="advanced_calendar" name="advanced_calendar" class="regular-text ltr" rows="17" cols="140"><?php echo get_option('ADS24_LITE_plugin_'.'advanced_calendar') ?></textarea>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
	</form>

<script>
	(function($){
		// - start - open page
		var a24pItemsWrap = $('.wrap');
		a24pItemsWrap.hide();

		setTimeout(function(){
			a24pItemsWrap.fadeIn(400);
		}, 400);
		// - end - open page

		$(document).ready(function(){

			// open tab after refresh
			var navTab = $('.nav-tab');
			var hash = window.location.hash;
			if ( hash !== "" && hash !== "#/" ) {
				navTab.removeClass('nav-tab-active');
				$('a[href="' + hash + '"]').addClass('nav-tab-active');

				$('.a24pTbody').hide();
				$(hash).show();
			}

			// init color picker
			$('.a24pColorPicker').wpColorPicker();

			// menu actions
			navTab.click(function(){
				var attr = $(this).attr("data-group");

				navTab.removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');

				$('.a24pTbody').hide();
				$('.' + attr).show();
			});

			var a24pSwitch = $(".a24pSwitch");
			options = { /* see below */ };
			a24pSwitch.switchButton(options);
			a24pSwitch.each(function(){
				var section = $(this).data('section');
				if ( $(this).is(':checked') ) {
					$('.'+section).show();
				} else {
					$('.'+section).hide();
					$('.'+section+'-input').val('');
				}
			});
			a24pSwitch.change(function(){
				var section = $(this).data('section');
				if($(this).is(':checked')) {
					$('.'+section).fadeIn();
				} else {
					$('.'+section).fadeOut();
					$('.'+section+'-input').val('');
				}
			});

		});

	})(jQuery);
</script>


<script>
(function($){
    
    $('#a24pPaymenttab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').show(); 
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide();    
        
    });
    
    
    $('#a24pReInstallationtab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide();    
        
    });
    
    $('#a24pHookstab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide();    
        
    });
    
    
    $('#a24pBuddyPresstab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').show();  
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide();   
    
        
    });
    
    
    $('#a24pBbPresstab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').show();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide(); 
        
    });
    
    
    $('#a24pNotificationstab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide(); 
    
        
    });
    
    
    $('#a24pAdmintab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide(); 
        
    });
    
    
    $('#24pMediatab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide();
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').hide();
    $('#lite-custom-code,#lite-custom-code-btn').hide(); 
        
    });
    
    
    $('#a24pCustomizationtab').click(function(evt) {
    
    evt.preventDefault();
    
    $('#lite-pg-methods,#lite-pg-methods-btn').hide(); 
    $('#lite-bp-hook,#lite-bp-hook-btn').hide();
    $('#lite-bb-hook,#lite-bb-hook-btn').hide();
    $('#lite-custom,#lite-custom-btn').show();
    $('#lite-custom-code,#lite-custom-code-btn').show(); 
        
    });
    

    
    
	})(jQuery);
</script>
