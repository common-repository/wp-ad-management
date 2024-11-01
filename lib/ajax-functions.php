<?php

function ADS24_LITE_ajax_load_adslot()
{
	$pid 			= $_POST['pid'];
	$id 			= $_POST['id'];
	$max_width 		= ( isset($_POST['max_width']) ? $_POST['max_width'] : null );
	$delay 			= ( isset($_POST['delay']) ? $_POST['delay'] : null );
	$padding_top 	= ( isset($_POST['padding_top']) ? $_POST['padding_top'] : null );
	$attachment 	= ( isset($_POST['attachment']) ? $_POST['attachment'] : null );
	$if_empty 		= ( isset($_POST['if_empty']) ? $_POST['if_empty'] : null );
	$custom_image 	= ( isset($_POST['custom_image']) ? $_POST['custom_image'] : null );
	$powered 		= ( isset($_POST['powered']) ? $_POST['powered'] : null );
	$show_ids 		= ( isset($_POST['show_ids']) ? $_POST['show_ids'] : null );
	$hide_for_id 	= ( isset($_POST['hide_for_id']) ? $_POST['hide_for_id'] : null );

	if ( !in_array($pid, explode(',', $hide_for_id)) && function_exists('ADS24_LITE_adslot') ) {
		echo ADS24_LITE_adslot($id, $max_width, $delay, $padding_top, $attachment, 'ajax', $if_empty, $custom_image, $powered, $show_ids);
	} else {
		echo null;
	}
	die();
}
add_action('wp_ajax_ADS24_LITE_ajax_load_adslot', 'ADS24_LITE_ajax_load_adslot');
add_action( 'wp_ajax_nopriv_ADS24_LITE_ajax_load_adslot', 'ADS24_LITE_ajax_load_adslot' );

function a24p_preview_callback()
{
	if( $_POST && isset($_POST['a24p_template']) ) {
		require dirname(__FILE__) . '/../frontend/template/'.$_POST['a24p_template'].'.php';
	} elseif( $_POST && isset($_POST['a24p_space_id']) ) {
		require dirname(__FILE__) . '/../frontend/template/'.a24p_space($_POST['a24p_space_id'],'template').'.php';
	} else {
		echo 'Templates can not be download.';
	}
	die();
}
add_action('wp_ajax_a24p_preview_callback', 'a24p_preview_callback');
add_action( 'wp_ajax_nopriv_a24p_preview_callback', 'a24p_preview_callback' );

function a24p_required_inputs_callback()
{
	if( $_POST && $_POST['a24p_space_id'] && $_POST['a24p_get_required_inputs'] ) {
		echo require dirname(__FILE__) . '/../frontend/template/'.a24p_space($_POST['a24p_space_id'],'template').'.php';
	} else {
		echo 'Required inputs can not be download.';
	}
	die();
}
add_action('wp_ajax_a24p_required_inputs_callback', 'a24p_required_inputs_callback');
add_action( 'wp_ajax_nopriv_a24p_required_inputs_callback', 'a24p_required_inputs_callback' );

function a24p_get_billing_models_callback()
{
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

	if( $_POST && $_POST['a24p_space_id'] ) {
		$sid = $_POST['a24p_space_id'];
		$admin = (isset($_POST['ADS24_LITE_admin']) ? $_POST['ADS24_LITE_admin'] : null); // if admin panel

		echo '<div class="a24pProInputsGroup a24pProInputsBillingModel">';
			if ( a24p_space($sid, 'cpc_price') != NULL && a24p_space($sid, 'cpc_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpc_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner a24pInputInnerModel">
						<input id="ADS24_LITE_cpc_model" type="radio" name="ad_model" value="cpc" onchange="selectBillingModel()">
						<label for="ADS24_LITE_cpc_model">'.get_option("ADS24_LITE_plugin_trans_"."form_right_cpc_name").'</label>
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpm_price') != NULL && a24p_space($sid, 'cpm_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpm_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner a24pInputInnerModel">
						<input id="ADS24_LITE_cpm_model" type="radio" name="ad_model" value="cpm" onchange="selectBillingModel()">
						<label for="ADS24_LITE_cpm_model">'.get_option("ADS24_LITE_plugin_trans_"."form_right_cpm_name").'</label>
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpd_price') != NULL && a24p_space($sid, 'cpd_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpd_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner a24pInputInnerModel">
						<input id="ADS24_LITE_cpd_model" type="radio" name="ad_model" value="cpd" onchange="selectBillingModel()">
						<label for="ADS24_LITE_cpd_model">'.get_option("ADS24_LITE_plugin_trans_"."form_right_cpd_name").'</label>
					</div>
				</div>';
			}
			do_action( 'a24p-lite-billing-models-callback', $sid );
		echo '</div>';

		$model = new ADS24_LITE_Model();
		$get_free_ads = $model->getUserCol(get_current_user_id(), 'free_ads');
		$free_ads = (isset($get_free_ads['free_ads']) && $get_free_ads['free_ads'] > 0) ? $get_free_ads['free_ads'] : 0;
		if ( a24p_space($sid, 'cpc_price') != NULL && a24p_space($sid, 'cpc_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpc_price') == 0.00 && a24p_role() == 'admin' ) {
			echo '<div class="a24pProInputsGroup a24pProInputsValues a24pProInputsValuesCPC" style="display: none;">';
			do_action( 'a24p-lite-billing-models-before', $sid, 'cpc', $before, $after);
			if ( a24p_space($sid, 'cpc_contract_1') != NULL && a24p_space($sid, 'cpc_contract_1') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpc_1" type="radio" name="ad_limit_cpc" value="'.a24p_space($sid, 'cpc_contract_1').'">
						<label for="ADS24_LITE_ad_limit_cpc_1">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpc_contract_1').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_clicks").'</span>
							';
							if ( isset($_POST['a24p_order']) ) {
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : a24p_space($sid, 'cpc_price'))).$after.'</span>';
							}
							echo '
						</label>
					</div>
				</div>';
			} elseif ( $admin == 1 && a24p_space($sid, 'cpc_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<label for="ADS24_LITE_ad_limit_cpc_1">Display Limit (clicks)</label>
						<input id="ADS24_LITE_ad_limit_cpc_1" type="number" name="ad_limit_cpc" value="" style="margin-top:10px;width: 100%;">
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpc_contract_2') != NULL && a24p_space($sid, 'cpc_contract_2') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpc_2" type="radio" name="ad_limit_cpc" value="'.a24p_space($sid, 'cpc_contract_2').'">
						<label for="ADS24_LITE_ad_limit_cpc_2">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpc_contract_2').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_clicks").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpc_2 = (a24p_space($sid, 'cpc_price') * (a24p_space($sid, 'cpc_contract_2') / a24p_space($sid, 'cpc_contract_1')));
								$d_cpc_2 = ((a24p_space($sid, 'discount_2') > 0) ? $cpc_2 * (a24p_space($sid, 'discount_2') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpc_2 - $d_cpc_2)).$after.'</span>';
								if ( a24p_space($sid, 'discount_2') > 0 && $free_ads == 0 ) {
									echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_2').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpc_contract_3') != NULL && a24p_space($sid, 'cpc_contract_3') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpc_3" type="radio" name="ad_limit_cpc" value="'.a24p_space($sid, 'cpc_contract_3').'">
						<label for="ADS24_LITE_ad_limit_cpc_3">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpc_contract_3').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_clicks").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpc_3 = (a24p_space($sid, 'cpc_price') * (a24p_space($sid, 'cpc_contract_3') / a24p_space($sid, 'cpc_contract_1')));
								$d_cpc_3 = ((a24p_space($sid, 'discount_3') > 0) ? $cpc_3 * (a24p_space($sid, 'discount_3') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpc_3 - $d_cpc_3)).$after.'</span>';
								if ( a24p_space($sid, 'discount_3') > 0 && $free_ads == 0 ) {
									echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_3').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			echo '</div>';
		}

		if ( a24p_space($sid, 'cpm_price') != NULL && a24p_space($sid, 'cpm_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpm_price') == 0.00 && a24p_role() == 'admin' ) {
			echo '<div class="a24pProInputsGroup a24pProInputsValues a24pProInputsValuesCPM" style="display: none;">';
			do_action( 'a24p-lite-billing-models-before', $sid, 'cpm', $before, $after);
			if ( a24p_space($sid, 'cpm_contract_1') != NULL && a24p_space($sid, 'cpm_contract_1') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpm_1" type="radio" name="ad_limit_cpm" value="'.a24p_space($sid, 'cpm_contract_1').'">
						<label for="ADS24_LITE_ad_limit_cpm_1">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpm_contract_1').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_views").'</span>
							';
							if ( isset($_POST['a24p_order']) ) {
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : a24p_space($sid, 'cpm_price'))).$after.'</span>';
							}
							echo '
						</label>
					</div>
				</div>';
			} elseif ( $admin == 1 && a24p_space($sid, 'cpm_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<label for="ADS24_LITE_ad_limit_cpm_1">Display Limit (views)</label>
						<input id="ADS24_LITE_ad_limit_cpm_1" type="number" name="ad_limit_cpm" value="" style="margin-top:10px;width: 100%;">
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpm_contract_2') != NULL && a24p_space($sid, 'cpm_contract_2') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpm_2" type="radio" name="ad_limit_cpm" value="'.a24p_space($sid, 'cpm_contract_2').'">
						<label for="ADS24_LITE_ad_limit_cpm_2">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpm_contract_2').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_views").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpm_2 = (a24p_space($sid, 'cpm_price') * (a24p_space($sid, 'cpm_contract_2') / a24p_space($sid, 'cpm_contract_1')));
								$d_cpm_2 = ((a24p_space($sid, 'discount_2') > 0) ? $cpm_2 * (a24p_space($sid, 'discount_2') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpm_2 - $d_cpm_2)).$after.'</span>';
								if ( a24p_space($sid, 'discount_2') > 0 && $free_ads == 0 ) {
									echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_2').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpm_contract_3') != NULL && a24p_space($sid, 'cpm_contract_3') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpm_3" type="radio" name="ad_limit_cpm" value="'.a24p_space($sid, 'cpm_contract_3').'">
						<label for="ADS24_LITE_ad_limit_cpm_3">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpm_contract_3').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_views").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpm_3 = (a24p_space($sid, 'cpm_price') * (a24p_space($sid, 'cpm_contract_3') / a24p_space($sid, 'cpm_contract_1')));
								$d_cpm_3 = ((a24p_space($sid, 'discount_3') > 0) ? $cpm_3 * (a24p_space($sid, 'discount_3') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpm_3 - $d_cpm_3)).$after.'</span>';
								if ( a24p_space($sid, 'discount_3') > 0 && $free_ads == 0 ) {
								echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_3').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			echo '</div>';
		}

		if ( a24p_space($sid, 'cpd_price') != NULL && a24p_space($sid, 'cpd_price') != 0.00 || $admin == 1 && a24p_space($sid, 'cpd_price') == 0.00 && a24p_role() == 'admin' ) {
			echo '<div class="a24pProInputsGroup a24pProInputsValues a24pProInputsValuesCPD" style="display: none;">';
			do_action( 'a24p-lite-billing-models-before', $sid, 'cpd', $before, $after);
			if ( a24p_space($sid, 'cpd_contract_1') != NULL && a24p_space($sid, 'cpd_contract_1') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpd_1" type="radio" name="ad_limit_cpd" value="'.a24p_space($sid, 'cpd_contract_1').'">
						<label for="ADS24_LITE_ad_limit_cpd_1">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpd_contract_1').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_days").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : a24p_space($sid, 'cpd_price'))).$after.'</span>';
							}
							echo '
						</label>
					</div>
				</div>';
			} elseif ( $admin == 1 && a24p_space($sid, 'cpd_price') == 0.00 && a24p_role() == 'admin' ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<label for="ADS24_LITE_ad_limit_cpd_1">Display Limit (days)</label>
						<input id="ADS24_LITE_ad_limit_cpd_1" type="number" name="ad_limit_cpd" value="" style="margin-top:10px;width: 100%;">
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpd_contract_2') != NULL && a24p_space($sid, 'cpd_contract_2') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpd_2" type="radio" name="ad_limit_cpd" value="'.a24p_space($sid, 'cpd_contract_2').'">
						<label for="ADS24_LITE_ad_limit_cpd_2">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpd_contract_2').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_days").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpd_2 = (a24p_space($sid, 'cpd_price') * (a24p_space($sid, 'cpd_contract_2') / a24p_space($sid, 'cpd_contract_1')));
								$d_cpd_2 = ((a24p_space($sid, 'discount_2') > 0) ? $cpd_2 * (a24p_space($sid, 'discount_2') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpd_2 - $d_cpd_2)).$after.'</span>';
								if ( a24p_space($sid, 'discount_2') > 0 && $free_ads == 0 ) {
									echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_2').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			if ( a24p_space($sid, 'cpd_contract_3') != NULL && a24p_space($sid, 'cpd_contract_3') != 0 ) {
				echo '
				<div class="a24pProInput">
					<div class="a24pInputInner">
						<input id="ADS24_LITE_ad_limit_cpd_3" type="radio" name="ad_limit_cpd" value="'.a24p_space($sid, 'cpd_contract_3').'">
						<label for="ADS24_LITE_ad_limit_cpd_3">
							<span class="a24pProExpiration">'.a24p_space($sid, 'cpd_contract_3').' '.get_option("ADS24_LITE_plugin_trans_"."form_right_days").'</span>';
							if ( isset($_POST['a24p_order']) ) {
								$cpd_3 = (a24p_space($sid, 'cpd_price') * (a24p_space($sid, 'cpd_contract_3') / a24p_space($sid, 'cpd_contract_1')));
								$d_cpd_3 = ((a24p_space($sid, 'discount_3') > 0) ? $cpd_3 * (a24p_space($sid, 'discount_3') / 100) : 0);
								echo '<span class="a24pProPrice">'.$before.a24p_number_format(($free_ads > 0 ? 0 : $cpd_3 - $d_cpd_3)).$after.'</span>';
								if ( a24p_space($sid, 'discount_3') > 0 && $free_ads == 0 ) {
									echo '<span class="a24pProDiscount">(-'.a24p_space($sid, 'discount_3').'%)</span>';
								}
							}
							echo '
						</label>
					</div>
				</div>';
			}
			echo '</div>';
		}
		if ( isset($_POST['a24p_order']) ) {
			do_action( 'a24p-lite-billing-models-callback-sub', $sid, $_POST['a24p_order'], $before, $after );
		}
	} else {
		echo 'Spaces can not be download.';
	}
	die();
}
add_action('wp_ajax_a24p_get_billing_models_callback', 'a24p_get_billing_models_callback');
add_action( 'wp_ajax_nopriv_a24p_get_billing_models_callback', 'a24p_get_billing_models_callback' );

function a24p_stats_chart_callback()
{
	if( isset($_POST) && isset($_POST['ad_id']) ) {
		$model = new ADS24_LITE_Model();
		$ad_id = $_POST['ad_id'];
		$days = $_POST['days'];
		echo json_encode(array(
			"labels" => array(
				date('m.d', time() - ( ($days - 1) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 2) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 3) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 4) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 5) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 6) * 24 * 60 * 60 )),
				date('m.d', time() - ( ($days - 7) * 24 * 60 * 60 ))
			),
			"clicks" => array(
				$model->a24pChartClicks($ad_id, $days),
				$model->a24pChartClicks($ad_id, $days - 1),
				$model->a24pChartClicks($ad_id, $days - 2),
				$model->a24pChartClicks($ad_id, $days - 3),
				$model->a24pChartClicks($ad_id, $days - 4),
				$model->a24pChartClicks($ad_id, $days - 5),
				$model->a24pChartClicks($ad_id, $days - 6)
			),
			"views" => array(
				$model->a24pChartViews($ad_id, $days),
				$model->a24pChartViews($ad_id, $days - 1),
				$model->a24pChartViews($ad_id, $days - 2),
				$model->a24pChartViews($ad_id, $days - 3),
				$model->a24pChartViews($ad_id, $days - 4),
				$model->a24pChartViews($ad_id, $days - 5),
				$model->a24pChartViews($ad_id, $days - 6)
			)
		));
	} else {
		echo 'Stats can not be download.';
	}
	die();
}
add_action('wp_ajax_a24p_stats_chart_callback', 'a24p_stats_chart_callback');
add_action( 'wp_ajax_nopriv_a24p_stats_chart_callback', 'a24p_stats_chart_callback' );

function a24p_stats_clicks_callback()
{
	if( isset($_POST) && isset($_POST['ad_id']) ) {
		do_action( 'a24p-lite-stats-clicks', $_POST);
		$model = new ADS24_LITE_Model();
		$ad_id = $_POST['ad_id'];
		$days = $_POST['days'];
		$clicks = $model->a24pGetClicks($ad_id, $days);
		if ( $clicks != null ) {
			echo '	<table>
			<tbody>';
			foreach ( $clicks as $click ) {
				echo '
				<tr class="'.(( date('d', $click['action_time']) % 2 == 0) ? "a24pEven" : "a24pOdd").'">
					<td width="20%">'.date('Y/m/d', $click['action_time']).'</td>
					<td width="40%">'.substr($click['user_ip'], 0, -3).'***</td>
					<td width="30%">'.$click['browser'].'</td>
					<td width="10%">'.( ( $click['status'] == "correct" ) ? "<span class='a24pCorrectIcon'></span>" : "<span class='a24pInCorrectIcon'></span>" ).'</td>
				</tr>
				';
			};
			echo '
			</tbody>
			</table>';
		} else {
			return null;
		}
	} else {
		echo 'Stats can not be download.';
	}
	die();
}
add_action('wp_ajax_a24p_stats_clicks_callback', 'a24p_stats_clicks_callback');
add_action( 'wp_ajax_nopriv_a24p_stats_clicks_callback', 'a24p_stats_clicks_callback' );

// Ads Sortable Function
function a24p_sortable_callback()
{
	if( $_POST && isset($_POST['a24p_order']) ) {
		$ads = $_POST['a24p_order'];
		$model = new ADS24_LITE_Model();

		foreach ( $ads as $key => $ad )
			$model->changeAdPriority($ad, count($ads) - $key);
	}
	die();
}
add_action('wp_ajax_a24p_sortable_callback', 'a24p_sortable_callback');
add_action( 'wp_ajax_nopriv_a24p_sortable_callback', 'a24p_sortable_callback' );

// Get All Unselected Elements
function a24p_unselected()
{
	$ajax_limit = $_POST['ajax_limit'];
	$get_type = $_POST['type'];
	$space_id = $_POST['space_id'];
	$offset = $_POST['a24p_offset'];

	if ( $get_type == 'posts' ) {
		if ( is_multisite() ) {

			// Current Site
			$current = get_current_site();

			// All Sites
			$blogs = json_decode(json_encode(get_sites()), true);

			foreach ( $blogs as $blog ) {

				// switch to the blog
				switch_to_blog( $blog['blog_id'] );

				// get only selected entry
				$getEntryIds = null;
				if ( isset($space_id) ) {
					$getIds = json_decode(a24p_space($space_id, 'advanced_opt'));
					if ( isset($getIds->hide_for_id) ) {
						foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
							if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
								$getEntryIds[] = substr($getId, 1);
							}
						}
					}
				}

				// get_categories args
				$args = array( 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'posts_per_page' => $ajax_limit );
				$allPosts = get_posts( $args );
				if ($allPosts) {
					foreach($allPosts as $key => $post) {
						echo '
						<li class="a24pProSpecificItem a24pCheckItem-PO'.$post->ID.'-'.$blog['blog_id'].'">
							<label class="selectit"><input value="'.$blog['blog_id'].$post->ID.'" class="a24pCheckItem" section="PO" itemId="PO'.$post->ID.'-'.$blog['blog_id'].'" type="checkbox" name="hide_for_id[]">
							'.$post->post_title.' (site id: '.$blog['blog_id'].')</label>
						</li>';
					}
				}

			}

			// return to the current site
			switch_to_blog( $current->id );

		} else {

			// get only selected entry
			$getEntryIds = null;
			if ( isset($space_id) ) {
				$getIds = json_decode(a24p_space($space_id, 'advanced_opt'));
				if ( isset($getIds->hide_for_id) ) {
					foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
						$getEntryIds[] = $getId;
					}
				}
			}

			$args = array( 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'posts_per_page' => $ajax_limit );
			$allPosts = get_posts( $args );
			if ($allPosts) {
				foreach($allPosts as $post) {
					echo '<li class="a24pProSpecificItem a24pCheckItem-PO'.$post->ID.'">
						<label class="selectit"><input value="'.$post->ID.'" class="a24pCheckItem" section="PO" itemId="PO'.$post->ID.'" type="checkbox" name="hide_for_id[]"> '.$post->post_title.'</label>
					</li>';
				}
			}

		}

	} elseif ( $get_type = 'pages' ){
		if ( is_multisite() ) {

			// Current Site
			$current = get_current_site();

			// All Sites
			$blogs = json_decode(json_encode(get_sites()), true);

			foreach ( $blogs as $blog ) {

				// switch to the blog
				switch_to_blog( $blog['blog_id'] );

				// get only selected entry
				$getEntryIds = null;
				if ( isset($space_id) ) {
					$getIds = json_decode(a24p_space($space_id, 'advanced_opt'));
					if ( isset($getIds->hide_for_id) ) {
						foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
							if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
								$getEntryIds[] = substr($getId, 1);
							}
						}
					}
				}

				// get_categories args
				$args = array( 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $ajax_limit );
				$allPosts = get_pages( $args );
				if ($allPosts) {
					foreach($allPosts as $key => $post) {
						echo '
						<li class="a24pProSpecificItem a24pCheckItem-PA'.$post->ID.'-'.$blog['blog_id'].'">
							<label class="selectit"><input value="'.$blog['blog_id'].$post->ID.'" class="a24pCheckItem" section="PA" itemId="PA'.$post->ID.'-'.$blog['blog_id'].'" type="checkbox" name="hide_for_id[]">
							'.$post->post_title.' (site id: '.$blog['blog_id'].')</label>
						</li>';
					}
				}

			}

			// return to the current site
			switch_to_blog( $current->id );

		} else {

			// get only selected entry
			$getEntryIds = null;
			if ( isset($space_id) ) {
				$getIds = json_decode(a24p_space($space_id, 'advanced_opt'));
				if ( isset($getIds->hide_for_id) ) {
					foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
						$getEntryIds[] = $getId;
					}
				}
			}

			$args = array( 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $ajax_limit );
			$allPosts = get_pages( $args );
			if ($allPosts) {
				foreach($allPosts as $post) {
					echo '<li class="a24pProSpecificItem a24pCheckItem-PA'.$post->ID.'">
						<label class="selectit"><input value="'.$post->ID.'" class="a24pCheckItem" section="PA" itemId="PA'.$post->ID.'" type="checkbox" name="hide_for_id[]"> '.$post->post_title.'</label>
					</li>';
				}
			}

		}
	} elseif ( $get_type == 'tags' ) {
		if ( is_multisite() ) {

			// Current Site
			$current = get_current_site();

			// All Sites
			$blogs = json_decode(json_encode(get_sites()), true);

			foreach ( $blogs as $blog ) {

				// switch to the blog
				switch_to_blog( $blog['blog_id'] );

				// get only selected tags
				$getEntryIds = null;
				if ( isset($space_id) ) {
					$getIds = a24p_space($space_id, 'has_tags');
					foreach ( explode(',', $getIds) as $getId ) {
						$getId = get_term_by('name', $getId, 'post_tag');
						if ( isset( $getId->term_id ) ) {
							$getEntryIds[] = $getId->term_id;
						}
					}
				}

				$args = array( 'taxonomy' => 'post_tag', 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $offset + $ajax_limit );
				$posttags = get_terms($args);
				if ($posttags) {
					foreach($posttags as $key => $tag) {
						?>
						<li class="a24pProSpecificItem a24pCheckItem-T<?php echo $key.$tag->term_id; ?>-<?php echo $blog['blog_id']; ?>">
							<label class="selectit"><input value="<?php echo $tag->name; ?>" class="a24pCheckItem" section="T" itemId="T<?php echo $key.$tag->term_id; ?>-<?php echo $blog['blog_id']; ?>" type="checkbox" name="space_tags[]">
								<?php echo $tag->name; ?> (site id: <?php echo $blog['blog_id']; ?>)</label>
						</li>
					<?php
					}
				}

			}

			// return to the current site
			switch_to_blog( $current->id );

		} else {

			// get only selected tags
			$getEntryIds = null;
			if ( isset($space_id) ) {
				$getIds = a24p_space($space_id, 'has_tags');
				foreach ( explode(',', $getIds) as $getId ) {
					$getId = get_term_by('name', $getId, 'post_tag');
					if ( isset( $getId->term_id ) ) {
						$getEntryIds[] = $getId->term_id;
					}
				}
			}

			$args = array( 'taxonomy' => 'post_tag', 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $offset + $ajax_limit );
			$posttags = get_terms($args);
			if ($posttags) {
				foreach($posttags as $key => $tag) {
					$rand = rand(100,1000);
					?>
					<li class="a24pProSpecificItem a24pCheckItem-T<?php echo $key.$rand; ?>">
						<label class="selectit"><input value="<?php echo $tag->name; ?>" class="a24pCheckItem" section="T" itemId="T<?php echo $key.$rand; ?>" type="checkbox" name="space_tags[]">
							<?php echo $tag->name; ?></label>
					</li>
				<?php
				}
			}

		}
	} elseif ( $get_type = 'categories' ){
		if ( is_multisite() ) {

			// Current Site
			$current = get_current_site();

			// All Sites
			$blogs = json_decode(json_encode(get_sites()), true);

			foreach ( $blogs as $blog ) {

				// switch to the blog
				switch_to_blog( $blog['blog_id'] );

				// get only selected tags
				$getEntryIds = null;
				if ( isset($space_id) ) {
					$getIds = a24p_space($space_id, 'in_categories');
					foreach ( explode(',', $getIds) as $getId ) {
						$getEntryIds[] = $getId;
					}
				}

				$args = array( 'taxonomy' => 'category', 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $offset + $ajax_limit );
				$postcategories = get_terms($args);
				if ($postcategories) {
					foreach ($postcategories as $postcategory) {
						?>
						<li class="a24pProSpecificItem a24pCheckItem-CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>">
							<label class="selectit"><input
									value="<?php echo $postcategory->term_id; ?>"
									class="a24pCheckItem" section="CT"
									itemId="CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>"
									type="checkbox"
									name="space_categories[]">
								<?php echo $postcategory->name; ?> (site id: <?php echo $blog['blog_id']; ?>)</label>
						</li>
					<?php
					}
				}

			}

			// return to the current site
			switch_to_blog( $current->id );

		} else {

			// get only selected tags
			$getEntryIds = null;
			if ( isset($space_id) ) {
				$getIds = a24p_space($space_id, 'in_categories');
				foreach ( explode(',', $getIds) as $getId ) {
					$getEntryIds[] = $getId;
				}
			}

			$args = array( 'taxonomy' => 'category', 'exclude' => $getEntryIds, 'offset'=> ($offset > 0) ? $offset : 0, 'number' => $offset + $ajax_limit );
			$postcategories = get_terms($args);
			if ($postcategories) {
				foreach ($postcategories as $postcategory) {
					?>
					<li class="a24pProSpecificItem a24pCheckItem-CT<?php echo $postcategory->term_id; ?>">
						<label class="selectit"><input
								value="<?php echo $postcategory->term_id; ?>"
								class="a24pCheckItem" section="CT"
								itemId="CT<?php echo $postcategory->term_id; ?>"
								type="checkbox"
								name="space_categories[]">
							<?php echo $postcategory->name; ?></label>
					</li>
				<?php
				}
			}

		}
	}
	die();
}
add_action('wp_ajax_a24p_unselected', 'a24p_unselected');
add_action( 'wp_ajax_nopriv_a24p_unselected', 'a24p_unselected' );

// Admin Ajax Function
function a24p_admin_action_callback()
{
	// Remove Ad Template
	if ( a24p_role() == 'admin' ) // verify roles
	{
		if( $_POST && isset($_POST['id']) && isset($_POST['type']) && $_POST['type'] == 'remove_template' )
		{
			$custom_templates = get_option('ADS24_LITE_plugin_custom_templates');
			$custom_templates = explode(',', $custom_templates);
			$css_file = plugin_dir_path( __DIR__ ) . 'frontend/css/block-'.$_POST['id'].'.css';
			$php_file = plugin_dir_path( __DIR__ ) . 'frontend/template/block-'.$_POST['id'].'.php';
			if ( file_exists($css_file) && file_exists($php_file) )
			{
				unlink($css_file);
				unlink($php_file);
				if ( !file_exists($css_file) && !file_exists($php_file) )
				{
					foreach (array_keys($custom_templates, $_POST['id']) as $key) {
						unset($custom_templates[$key]);
					}
					update_option('ADS24_LITE_plugin_custom_templates', implode(',', $custom_templates));
					echo 'removed';
				}
			} else {
				foreach (array_keys($custom_templates, $_POST['id']) as $key) {
					unset($custom_templates[$key]);
				}
				update_option('ADS24_LITE_plugin_custom_templates', implode(',', $custom_templates));
				echo 'removed';
			}
		}
	}

	die();
}
add_action('wp_ajax_a24p_admin_action_callback', 'a24p_admin_action_callback');
add_action( 'wp_ajax_nopriv_a24p_admin_action_callback', 'a24p_admin_action_callback' );