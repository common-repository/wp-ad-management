<?php
$a24pTrans = 'ADS24_LITE_plugin_trans_';

$model = new ADS24_LITE_Model();
if ( isset($_POST) && isset($_POST['a24pProSubmit']) || isset($_GET['oid']) && isset($_GET['cid']) ) {
	$getForm = $model->getForm();
} else {
	$getForm = '';
	unset($_SESSION['a24pProAds']);
}

// get notify action
if (isset($_GET['ADS24_LITE_payment']) && $_GET['ADS24_LITE_payment'] == 'notify' || isset($_POST['stripeToken'])) {
	$model->notifyAction();
}

echo '
<div class="a24pProOrderingForm">
	<div class="a24pProOrderingFormInner">
		';

	if (isset($_GET["ADS24_LITE_notice"]) && $_GET["ADS24_LITE_notice"] == 'success'): ?>
		<div class="a24pProAlert a24pProAlertSuccess a24pProAlertSuccessNotice">
			<strong><?php echo get_option($a24pTrans."alert_success"); ?></strong>
			<p><?php echo get_option($a24pTrans."payment_success"); ?></p>
		</div>
	<?php elseif (isset($_GET["ADS24_LITE_notice"]) && $_GET["ADS24_LITE_notice"] == 'failed'): ?>
		<div class="a24pProAlert a24pProAlertFailed a24pProAlertFailedNotice">
			<strong><?php echo get_option($a24pTrans."alert_failed"); ?></strong>
			<p><?php echo get_option($a24pTrans."payment_failed"); ?></p>
		</div>
	<?php endif; ?>

	<?php if ($getForm == "invalidParams"): ?>
		<div class="a24pProAlert a24pProAlertFailed">
			<strong><?php echo get_option($a24pTrans."alert_failed"); ?></strong>
			<p><?php echo get_option($a24pTrans."form_invalid_params"); ?></p>
		</div>
	<?php elseif ($getForm == "invalidSizeFile"): ?>
		<div class="a24pProAlert a24pProAlertFailed">
			<strong><?php echo get_option($a24pTrans."alert_failed"); ?></strong>
			<p><?php echo get_option($a24pTrans."form_too_high"); ?></p>
		</div>
	<?php elseif ($getForm == "invalidFile"): ?>
		<div class="a24pProAlert a24pProAlertFailed">
			<strong><?php echo get_option($a24pTrans."alert_failed"); ?></strong>
			<p><?php echo get_option($a24pTrans."form_img_invalid"); ?></p>
		</div>
	<?php elseif ($getForm == "fieldsRequired"): ?>
		<div class="a24pProAlert a24pProAlertFailed">
			<strong><?php echo get_option($a24pTrans."alert_failed"); ?></strong>
			<p><?php echo get_option($a24pTrans."form_empty"); ?></p>
		</div>
	<?php elseif ($getForm == "edited"): ?>
		<div class="a24pProAlert a24pProAlertSuccess">
			<strong><?php echo get_option($a24pTrans."alert_success"); ?></strong>
		</div>
	<?php elseif ($getForm == "successAdded"): ?>
		<div class="a24pProAlert a24pProAlertSuccess">
			<strong><?php echo get_option($a24pTrans."alert_success"); ?></strong>
			<p><?php echo get_option($a24pTrans."form_success"); ?></p>
		</div>
		<div id="a24pSuccessProRedirect">
			<div class="a24pPayPalSectionBg"></div>
			<div class="a24pPayPalSectionCenter">
				<span class="a24pLoaderRedirect" style="margin-top:200px;"></span>
			</div>
			<form><input id="a24p_payment_url" type="hidden" name="a24p_payment_url" value="<?php echo (isset($_SESSION['a24p_payment_url'])) ? $_SESSION['a24p_payment_url'] : '' ?>"></form>
		</div>
	<?php endif;

	$spaces = $model->getSpaces('active', 'html');
	$space_verify = NULL;
	if (is_array($spaces))
	{
		foreach ( $spaces as $key => $space ) {
			if ( $space['cpc_price'] == 0 && $space['cpm_price'] == 0 && $space['cpd_price'] == 0 ) {
				$space_verify .= '';
			} else {
				if ( $model->countAds($space["id"]) < a24p_space($space["id"], 'max_items') || a24p_space($space["id"], 'max_items') == 1 && get_option('ADS24_LITE_plugin_calendar') == 'yes' ) {
					$space_verify .= (( $key > 0 ) ? ','.$space["id"] : $space["id"]);
				} else {
					$space_verify .= '';
				}
			}
		}
	}
	$space_verify = (( $space_verify != '') ? explode(',', $space_verify) : FALSE );

	if ( isset($_GET['oid']) && $_GET['oid'] != '' && a24p_ad($_GET['oid'], 'id') != null && a24p_space(a24p_ad($_GET['oid'], 'space_id'), 'status') == 'active' && !isset($_GET['cid']) ||
		 isset($_GET['oid']) && $_GET['oid'] != '' && a24p_ad($_GET['oid'], 'id') != null && isset($_GET['cid']) && $_GET['cid'] != '' && a24p_space(a24p_ad($_GET['oid'], 'space_id'), 'status') == 'active' && a24p_space(a24p_ad($_GET['oid'], 'space_id'), a24p_ad($_GET['oid'], 'ad_model').'_contract_'.$_GET['cid']) > 0 ) { // Payments

		if (empty($_GET)) {
			$checkGET = '?';
		} else {
			$checkGET = '&';
		}
		$orderId = $_GET['oid'];
		$userEmail = a24p_ad($_GET['oid'], 'buyer_email');
		$spaceId = a24p_ad($_GET['oid'], 'space_id');
		$billingModel = a24p_ad($_GET['oid'], 'ad_model');
		if ( $billingModel == 'cpm' ):
			$type = a24p_get_trans('user_panel', 'views');
		elseif ( $billingModel == 'cpc' ):
			$type = a24p_get_trans('user_panel', 'clicks');
		else:
			$type = a24p_get_trans('user_panel', 'days');
		endif;
		if ( isset($_GET['cid']) ) { // renewal payment
			if ( $_GET['cid'] == 2 || $_GET['cid'] == 3 ) {
				$price = a24p_space($spaceId, $billingModel.'_price') * ( a24p_space($spaceId, $billingModel.'_contract_'.$_GET['cid']) / a24p_space($spaceId, $billingModel.'_contract_1') );
				$discount = $price * ( a24p_space($spaceId, 'discount_'.$_GET['cid']) / 100 );
				$total_cost = $price - $discount;
			} else {
				$total_cost = a24p_space($spaceId, $billingModel.'_price');
			}
			$amount = $total_cost;
		} else { // first payment
			$amount = a24p_ad($_GET['oid'], 'cost');
		}

		// reset cache sessions
		unset($_SESSION[a24p_get_opt('prefix').'a24p_ad_'.$orderId]);
		if ( a24p_ad($_GET['oid'], 'paid') == 1 || a24p_ad($_GET['oid'], 'paid') == 2 ) {
			?>
			<div class="a24pProAlert a24pProAlertSuccess">
				<strong><?php echo get_option($a24pTrans."alert_success"); ?></strong>
				<p><?php echo get_option($a24pTrans."payment_paid"); ?></p>
			</div>
			<small style="margin-top: -10px;display: block;text-align: center;">
				<a href="<?php echo get_option("ADS24_LITE_plugin_ordering_form_url") ?>" style="font-size:12px;font-weight: normal;text-decoration: none;">&lt; <?php echo get_option($a24pTrans."payment_return"); ?></a>
			</small>
			<?php
		} else {
			$payments_Stripe 		= ( (get_option("ADS24_LITE_plugin_secret_key") != '' && get_option("ADS24_LITE_plugin_publishable_key") != '' && get_option('ADS24_LITE_plugin_stripe_code') != '') ? 1 : 0 );
			$payments_PayPal 		= ( (get_option("ADS24_LITE_plugin_paypal") != '' && get_option("ADS24_LITE_plugin_currency_code") != '') ? 1 : 0 );
			$payments_BankTransfer 	= ( (get_option("ADS24_LITE_plugin_trans_payment_bank_transfer_content") != '') ? 1 : 0 );
			$payments_WooCommerce 	= ( (a24p_get_opt('settings', 'woo_item') != '') ? 1 : 0 );
			$payments_count = $payments_Stripe + $payments_PayPal + $payments_BankTransfer + $payments_WooCommerce;
			?>
			<div class="a24pProContainerNew a24p-lite-col-<?php echo $payments_count ?>" style="display: block !important">
				<div class="a24pProItems a24pGridGutter">

					<div style="text-align:center;">
						<h2><?php echo get_option($a24pTrans."payment_select"); ?>
							<?php if ( isset($_GET['cid']) ):?>
								(<?php echo a24p_get_trans('user_panel', 'renewal').' '.a24p_space($spaceId, $billingModel."_contract_".$_GET['cid']).' '.strtolower($type); ?>)
							<?php endif; ?>
						</h2>
						<small style="margin-top: -10px;display: block;">
							<a href="<?php echo get_option("ADS24_LITE_plugin_ordering_form_url") ?>" style="font-size:12px;font-weight: normal;text-decoration: none;">&lt; <?php echo get_option($a24pTrans."payment_return"); ?></a>
						</small>
					</div>

					<?php if ( get_option("ADS24_LITE_plugin_secret_key") != '' && get_option("ADS24_LITE_plugin_publishable_key") != '' && get_option('ADS24_LITE_plugin_stripe_code') != '' ): ?>
						<div class="a24pProItem" data-animation="zoomInDown" style="text-align: center; margin-left:0;">
							<? // reset stripe token session
							if ( isset($_SESSION['stripeToken']) ) { unset($_SESSION['stripeToken']); }; ?>
							<h2><?php echo get_option($a24pTrans."payment_stripe_title"); ?></h2>

							<form action="" method="POST">
								<script
									src="https://checkout.stripe.com/checkout.js" class="stripe-button"
									data-key="<?php echo get_option('ADS24_LITE_plugin_publishable_key') ?>"
									data-amount="<?php echo number_format(a24p_number_format($amount), 2, '', '') ?>"
									data-currency="<?php echo get_option('ADS24_LITE_plugin_stripe_code') ?>"
									data-item_number="<?php echo $orderId ?>"
									data-name="<?php echo $userEmail ?>"
									data-description="<?php echo $userEmail ?> (<?php echo a24p_number_format($amount) ?>)">
								</script>
							</form>

						</div>
					<?php endif; ?>

					<?php if ( get_option("ADS24_LITE_plugin_paypal") != '' && get_option("ADS24_LITE_plugin_currency_code") != '' ): ?>
					<?php $protocol = isset($_SERVER['HTTPS']) === true ? 'https://' : 'http://'; ?>
					<div class="a24pProItem" data-animation="zoomInDown" style="text-align: center; margin-left:0;">
						<h2><?php echo get_option($a24pTrans."payment_paypal_title"); ?></h2>

						<form id="a24p-lite-PayPal-Payment" action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_xclick">
							<input type="hidden" name="business" value="<?php echo (get_option("ADS24_LITE_plugin_purc"."hase_code") != '' && get_option("ADS24_LITE_plugin_purc"."hase_code") != null) ? get_option("ADS24_LITE_plugin_paypal") : 'scripteo@gmail.com' ?>">
							<input type="hidden" name="item_name" value="<?php echo $userEmail ?>">
							<input type="hidden" name="item_number" value="<?php echo $orderId ?>">
							<input type="hidden" name="tax" value="0">
							<input type="hidden" name="no_note" value="1">
							<input type="hidden" name="no_shipping" value="1">
							<input type="hidden" name="amount" value="<?php echo a24p_number_format($amount) ?>">
							<input type="hidden" name="custom" value="<?php echo md5($orderId.a24p_number_format($amount)) ?>">
							<input type="hidden" name="currency_code" value="<?php echo get_option("ADS24_LITE_plugin_currency_code") ?>">
							<input type="hidden" name="return" value="<?php echo $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$checkGET ?>ADS24_LITE_notice=success">
							<input type="hidden" name="cancel_return" value="<?php echo $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$checkGET ?>ADS24_LITE_notice=failed">
							<input type="hidden" name="notify_url" value="<?php echo $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$checkGET ?>ADS24_LITE_payment=notify">
							<input type="image" name="submit" border="0" class="a24pProImgSubmit" src="https://www.paypalobjects.com/webstatic/en_US/logo/pp_cc_mark_111x69.png" alt="PayPal">
						</form>

					</div>
					<?php endif;

					if (get_option('ADS24_LITE_plugin_symbol_position') == 'before') {
						$before = get_option('ADS24_LITE_plugin_currency_symbol');
					} else {
						$before = '';
					}
					if (get_option('ADS24_LITE_plugin_symbol_position') != 'before') {
						$after = get_option('ADS24_LITE_plugin_currency_symbol');
					} else {
						$after = '';
					}

					if ( get_option("ADS24_LITE_plugin_trans_payment_bank_transfer_content") != '' ): ?>
					<div class="a24pProItem" data-animation="zoomInDown" style="text-align: center">
						<h2><?php echo get_option($a24pTrans."payment_bank_transfer_title"); ?> (<?php echo $before.a24p_number_format($amount).$after; ?>)</h2>
						<p style="white-space: pre-wrap"><?php echo get_option($a24pTrans."payment_bank_transfer_content"); ?></p>
					</div>
					<?php endif;

					if ( a24p_get_opt('settings', 'woo_item') != '' ): ?>
					<div class="a24pProItem" data-animation="zoomInDown" style="text-align: center">
						<h2><?php echo a24p_get_opt('translations', 'woo_button'); ?> (<?php echo $before.a24p_number_format($amount).$after; ?>)</h2>

						<?php

						// change price
						$getWooItemId 		= a24p_get_opt('settings', 'woo_item');
						a24p_change_product_price( $getWooItemId, a24p_number_format($amount) );

						$_SESSION['a24p_woo_order_id_'.session_id()] = $_GET['oid'];
						$_SESSION['a24p_woo_order_price_'.session_id().$_GET['oid']] = $amount;

						// if item in cart
						if ( a24pCheckInCart($getWooItemId) ) {
							$url = '#';
							if ( function_exists('wc_get_checkout_url') ) {
								$url = wc_get_checkout_url(); // since WC 2.5.0
							}
							echo '<a href="'.$url.'">'.a24p_get_opt('translations', 'woo_button').'</a>';
						} else { // if not
							echo do_shortcode("[add_to_cart id=".$getWooItemId."]");
						}

						?>

					</div>
					<?php endif; ?>

				</div>
			</div>
			<?php
		}

	} else { // Form

		$eid = (isset($_GET['eid']) ? $_GET['eid'] : null);
		$user_info = get_userdata(get_current_user_id());
		if ( isset($user_info->user_email) ) {
			$getUserAds = $model->getUserAds(get_current_user_id(), 'active', $user_info->user_email, 'id');
		} else {
			$getUserAds = null;
		}
		$hasAccess = null;
		if ( is_array($getUserAds) ) {
			foreach ( $getUserAds as $entry ) {
				if ( $entry['id'] == $eid ) {
					$hasAccess = true;
					break;
				}
			}
		}

		if ( !$eid || $eid && $hasAccess || $eid && a24p_role() == 'admin' ) {
			if ( a24p_get_opt('settings', 'form_restrictions') == 'yes' && is_user_logged_in() || a24p_get_opt('settings', 'form_restrictions') != 'yes' ) { // order form
				if ( $space_verify && $spaces ) {
					echo '
					<form action="" method="POST" enctype="multipart/form-data">
						<div class="a24pProInputs a24pProInputsLeft">
							<input type="hidden" name="a24pProAction" value="'.($eid ? 'editAd' : 'buyNewAd').'">
							<input class="a24p_inputs_required" name="inputs_required" type="hidden" value="">
							<h3>'. get_option($a24pTrans.($eid ? 'edit' : 'form')."_left_header").' <span class="a24pLoader a24pLoaderInputs" style="display:none;"></span></h3>
							<div class="a24pProInput'.($eid ? 'Hidden' : '').'" style="'.($eid ? 'display:none' : '').'">
								<label for="ADS24_LITE_space_id">'. get_option($a24pTrans."form_left_select_space").'</label>
								<div class="a24pProSelectSpace">
									<select id="ADS24_LITE_space_id" name="space_id">
									';
					foreach ( $spaces as $space ) {
						if ( in_array($space['id'], $space_verify) ) {
							if ( ($model->countAds($space["id"]) < a24p_space($space["id"], 'max_items')) || a24p_space($space["id"], 'max_items') == 1 && get_option('ADS24_LITE_plugin_calendar') == 'yes' ) {
								echo '<option value="'.$space["id"].'" '.((isset($_POST["space_id"]) && $_POST["space_id"] == $space["id"] or isset($_GET['sid']) && $_GET['sid'] == $space["id"]) ? 'selected="selected"' : "").'>'.$space["name"].'</option>';
							} else {
								echo '<option value="" disabled>'.$space["name"].' ('.$model->countAds($space["id"]).'/'.a24p_space($space["id"], 'max_items').')'.'</option>';
							}
						}
					}
					echo '
									</select>
								</div>
							</div>';

					if ( !$eid ) {
						echo '<div class="a24pProInput">
								<label for="ADS24_LITE_buyer_email">'. get_option($a24pTrans."form_left_email").'</label>
								<input id="ADS24_LITE_buyer_email" name="a24p_buyer_email" type="email" value="'.a24pGetPost('a24p_buyer_email').'" placeholder="'.get_option($a24pTrans."form_left_eg_email").'">
							</div>';
					}
					echo '
							<div class="a24pProInput a24p_title_inputs_load" style="display: none">
								<label for="ADS24_LITE_title">'. get_option($a24pTrans."form_left_title").' (<span class="ADS24_LITE_sign_title">'.get_option('ADS24_LITE_plugin_max_title').'</span>)</label>
								<input id="ADS24_LITE_title" name="a24p_title" type="text" value="'.($eid ? a24p_ad($eid, 'title') : a24pGetPost('a24p_title')).'" placeholder="'.get_option($a24pTrans."form_left_eg_title").'" maxlength="'.get_option('ADS24_LITE_plugin_max_title').'">
							</div>
							<div class="a24pProInput a24p_desc_inputs_load" style="display: none">
								<label for="ADS24_LITE_desc">'. get_option($a24pTrans."form_left_desc").' (<span class="ADS24_LITE_sign_desc">'.get_option('ADS24_LITE_plugin_max_desc').'</span>)</label>
								<input id="ADS24_LITE_desc" name="a24p_description" type="text" value="'.($eid ? a24p_ad($eid, 'description') : a24pGetPost('a24p_description')).'" placeholder="'.get_option($a24pTrans."form_left_eg_desc").'" maxlength="'.get_option('ADS24_LITE_plugin_max_desc').'">
							</div>
							<div class="a24pProInput a24p_button_inputs_load" style="display: none">
								<label for="ADS24_LITE_button">'. get_option($a24pTrans."form_left_button").' (<span class="ADS24_LITE_sign_button">'.get_option('ADS24_LITE_plugin_max_button').'</span>)</label>
								<input id="ADS24_LITE_button" name="a24p_button" type="text" value="'.($eid ? a24p_ad($eid, 'button') : a24pGetPost('a24p_button')).'" placeholder="'.get_option($a24pTrans."form_left_eg_button").'" maxlength="'.get_option('ADS24_LITE_plugin_max_button').'">
							</div>
							<div class="a24pProInput a24p_url_inputs_load" style="display: none">
								<label for="ADS24_LITE_url">'. get_option($a24pTrans."form_left_url").' (<span class="ADS24_LITE_sign_url">255</span>)</label>
								<input id="ADS24_LITE_url" name="a24p_url" type="url" value="'.($eid ? a24p_ad($eid, 'url') : a24pGetPost('a24p_url')).'" placeholder="'.get_option($a24pTrans."form_left_eg_url").'" maxlength="255">
							</div>
							<div class="a24pProInput a24p_img_inputs_load" style="display: none">
								<label for="ADS24_LITE_img">'. get_option($a24pTrans."form_left_thumb").'</label>
								<input type="file" name="a24p_img" id="ADS24_LITE_img" onchange="a24pPreviewThumb(this)">
							</div>
							';
					if ( a24p_get_opt('order_form', 'optional_field') == 'yes' && !$eid ) {
						echo '
							<div class="a24pProInput">
								<label for="ADS24_LITE_optional_field">' . a24p_get_trans('order_form', 'optional_field') . '</label>
								<input type="text" class="ADS24_LITE_optional_field" id="ADS24_LITE_optional_field" name="a24p_optional_field"  value="'.a24pGetPost('a24p_optional_field').'" placeholder="' . a24p_get_trans('order_form', 'eg_optional_field') . '">
							</div>';
					}
					if ( get_option('ADS24_LITE_plugin_'.'calendar') == 'yes' && !$eid ) {
						echo '
							<div class="a24pProInput">
								<label for="ADS24_LITE_calendar">'. get_option($a24pTrans."form_left_calendar").'</label>
								<input type="text" class="ADS24_LITE_calendar" id="ADS24_LITE_calendar" name="a24p_calendar"  value="'.a24pGetPost('a24p_calendar').'" placeholder="'.get_option($a24pTrans."form_left_eg_calendar").'">
							</div>';
					}
					echo '
						</div>
						<div class="a24pProInputs a24pProInputsRight">';
					if ( !$eid ) {
						echo '
							<h3>'. get_option($a24pTrans."form_right_header").' <span class="a24pLoader a24pLoaderModels" style="display:none;"></span></h3>
							<div class="a24pGetBillingModels"></div>';
					}
					echo '
							<h3>'. get_option($a24pTrans."form_live_preview").' <span class="a24pLoader a24pLoaderPreview" style="display:none;"></span></h3>
							<div class="a24pTemplatePreview a24pTemplatePreviewForm">
								<div class="a24pTemplatePreviewInner" data-img="'.($eid ? a24p_upload_url(null, a24p_ad($eid, 'img')) : null).'"></div>
							</div>
						</div>
	
						<button type="submit" name="a24pProSubmit" value="1" class="a24pProSubmit clearfix">'.get_option($a24pTrans.($eid ? 'edit' : 'form').'_right_button_pay').'</button>
				</form>
			';
				} else {
					$notice = get_option($a24pTrans."order_form");
					echo $notice['form_notice'];
				}
			} else {
				$notice = get_option($a24pTrans."order_form");
				echo $notice['login_notice'];
			}
		}
	}
echo '
	</div>
</div>';
$getUnavailableDates 	= $model->getUnavailableDates();
?>
<style>
	<?php
		foreach ( $spaces as $space ) {
			$size = explode('--', str_replace('block-', '', $space['template']));
			$width = (isset($size[0]) ? $size[0] : 0);
			$height = (isset($size[1]) ? $size[1] : 0);
			if ( $width > 0 && $height > 0 ) { ?>
				.a24pTemplatePreview .a24p-<?php echo $space['template']; ?> {
					width: <?php echo $width; ?>px;
					height: <?php echo $height; ?>px;
				}
			<?php }
		}
	?>
</style>
<script>
	(function($) {
		$(document).ready(function(){
			var a24pProCalendar = $(".ADS24_LITE_calendar");
			var sid = $("#ADS24_LITE_space_id");
			var dates = <?php echo ($getUnavailableDates != null && $getUnavailableDates != '') ? $getUnavailableDates : '' ?>;
			if ( dates ) {
				if ( dates !== '' ) {
					if ( dates !== null ) {
						sid.bind("change",function() {
							a24pProCalendar.datepicker({
								dateFormat : "yy-mm-dd",
								<?php echo (get_option('ADS24_LITE_plugin_advanced_calendar') != '' ? get_option('ADS24_LITE_plugin_advanced_calendar') : null) ?>
								isRTL: <?php echo (get_option('ADS24_LITE_plugin_rtl_support') == 'yes' ? 'true' : 'false' ) ?>,
								minDate: 0,
								beforeShowDay: function(date){
									var string = jQuery.datepicker.formatDate("yy-mm-dd", date);
									return [ dates[sid.val()].indexOf(string) === -1, "a24pProUnavailableDate" ]
								},
								beforeShow: function(input, inst) {
									$('#ui-datepicker-div').addClass('a24pProCalendar');
								}
							});
						});
					}
				}
			} else {
				var d = new Date();
				a24pProCalendar.datepicker({
					dateFormat : "yy-mm-dd",
					<?php echo (get_option('ADS24_LITE_plugin_advanced_calendar') != '' ? get_option('ADS24_LITE_plugin_advanced_calendar') : null) ?>
					isRTL: <?php echo (get_option('ADS24_LITE_plugin_rtl_support') == 'yes' ? 'true' : 'false' ) ?>,
					minDate: 0,
					beforeShowDay: function(date){
						var string = jQuery.datepicker.formatDate("yy-mm-dd", date);
						return [ (d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + (d.getDay() - 1) ).indexOf(string) === -1, "a24pProUnavailableDate" ]
					},
					beforeShow: function(input, inst) {
						$('#ui-datepicker-div').addClass('a24pProCalendar');
					}
				});
			}
			var inputTitle = $("#ADS24_LITE_title");
			var inputDesc = $("#ADS24_LITE_desc");
			var inputButton = $("#ADS24_LITE_button");
			var inputUrl = $("#ADS24_LITE_url");
			inputTitle.keyup(function() { a24pPreviewInput("title"); });
			inputDesc.keyup(function() { a24pPreviewInput("desc"); });
			inputButton.keyup(function() { a24pPreviewInput("button"); });
			inputUrl.keyup(function() { a24pPreviewInput("url"); });
			sid.bind("change",function() {
				a24pGetBillingModels();
				a24pTemplatePreview();
				$(".a24pUrlSpaceId").html($("#ADS24_LITE_space_id").val());
			});
			sid.trigger("change");
		});
	})(jQuery);
	function a24pGetBillingModels() {
		(function($) {
			var getBillingModels = $(".a24pGetBillingModels");
			var a24pLoaderModels = $(".a24pLoaderModels");
			getBillingModels.slideUp();
			a24pLoaderModels.fadeIn(400);
			setTimeout(function(){
				$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"a24p_get_billing_models_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),a24p_order:1}, function(result) {
					getBillingModels.html(result).slideDown();
					a24pLoaderModels.fadeOut(400);
				});
			}, 700);
		})(jQuery);
	}
	function a24pTemplatePreview() {
		(function($) {
			var a24pTemplatePreviewInner = $(".a24pTemplatePreviewInner");
			var a24pLoaderPreview = $(".a24pLoaderPreview");
			a24pTemplatePreviewInner.slideUp(400);
			a24pLoaderPreview.fadeIn(400);
			setTimeout(function(){
				$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"a24p_preview_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),a24p_ad_id:$("#ADS24_LITE_ad_id").val()}, function(result) {
					a24pTemplatePreviewInner.html(result).slideDown(400);
					a24pGetRequiredInputs();
					var inputTitle = $("#ADS24_LITE_title");
					var inputDesc = $("#ADS24_LITE_desc");
					var inputButton = $("#ADS24_LITE_button");
					var inputUrl = $("#ADS24_LITE_url");
					if ( inputTitle.val().length > 0 ) { a24pPreviewInput("title"); }
					if ( inputDesc.val().length > 0 ) { a24pPreviewInput("desc"); }
					if ( inputButton.val().length > 0 ) { a24pPreviewInput("button"); }
					if ( inputUrl.val().length > 0 ) { a24pPreviewInput("url"); }
					a24pLoaderPreview.fadeOut(400);
				});
			}, 700);
		})(jQuery);
	}
	function a24pGetRequiredInputs() {
		(function($) {
			var a24pLoaderInputs = $(".a24pLoaderInputs");
			a24pLoaderInputs.fadeIn(400);
			$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"a24p_required_inputs_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),a24p_get_required_inputs:1}, function(result) {
				$(".a24p_inputs_required").val($.trim(result));
				if ( result.indexOf("title") !== -1 ) { // show if title required
					$(".a24p_title_inputs_load").fadeIn();
				} else {
					$(".a24p_title_inputs_load").fadeOut();
				}
				if ( result.indexOf("desc") !== -1 ) { // show if description required
					$(".a24p_desc_inputs_load").fadeIn();
				} else {
					$(".a24p_desc_inputs_load").fadeOut();
				}
				if ( result.indexOf("button") !== -1 ) { // show if button required
					$(".a24p_button_inputs_load").fadeIn();
				} else {
					$(".a24p_button_inputs_load").fadeOut();
				}
				if ( result.indexOf("url") !== -1 ) { // show if url required
					$(".a24p_url_inputs_load").fadeIn();
				} else {
					$(".a24p_url_inputs_load").fadeOut();
				}
				if ( result.indexOf("img") !== -1 ) { // show if img required
					$(".a24p_img_inputs_load").fadeIn();
					var a24pTemplatePreviewInner = $('.a24pTemplatePreviewInner');
					if ( a24pTemplatePreviewInner.attr('data-img') ) {
						$(".a24pTemplatePreviewForm .a24pProItemInner__img").css({"background-image" : "url(" + a24pTemplatePreviewInner.attr('data-img') + ")"});
					}
				} else {
					$(".a24p_img_inputs_load").fadeOut();
				}
				if ( result.indexOf("html") !== -1 ) { // show if html required
					$(".a24p_html_inputs_load").fadeIn();
				} else {
					$(".a24p_html_inputs_load").fadeOut();
				}
				a24pLoaderInputs.fadeOut(400);
			});
		})(jQuery);
	}
	function a24pPreviewInput(inputName) {
		(function($){
  			$(document);
			var input = $("#ADS24_LITE_" + inputName);
			var sign = $(".ADS24_LITE_sign_" + inputName);
			var limit = input.attr("maxLength");
			var a24pProContainerExample = $(".a24pProContainerExample");
			var exampleTitle = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_title") ?>";
			var exampleDesc = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_desc") ?>";
			var exampleButton = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_button") ?>";
			var exampleUrl = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_url") ?>";
			sign.text(limit - input.val().length);
			input.keyup(function() {
				if (input.val().length > limit) {
					input.val($(this).val().substring(0, limit));
				}
			});
			if (input.val().length > 0) {
				if ( inputName === "title" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "desc" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "button" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "url" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html("http://" + input.val().replace("http://","").replace("https://","").replace("www.","").split(/[/?#]/)[0]);
				}
			} else {
				if ( inputName === "title" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleTitle);
				} else if ( inputName === "desc" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleDesc);
				} else if ( inputName === "button" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleButton);
				} else if ( inputName === "url" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html("http://" + exampleUrl.val().replace("http://","").replace("https://","").replace("www.","").split(/[/?#]/)[0]);
				}
			}
		})(jQuery);
	}
	function a24pPreviewThumb(input) {
		(function($){
			if (input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$(".a24pTemplatePreviewForm .a24pProItemInner__img").css({"background-image" : "url(" + e.target.result + ")"});
				};
				reader.readAsDataURL(input.files[0]);
			}
		})(jQuery);
	}
</script>
