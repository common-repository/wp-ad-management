<?php
function getSpaceValue($val) {
	$ret = NULL;
	$ret = apply_filters( "a24p-lite-getspacevalue", $ret, ((isset($_GET['space_id'])) ? $_GET['space_id'] : null), $val);
	if($ret!=NULL) {
		return $ret;
	}
	if (isset($_GET['space_id'])) {
		if ( $val == 'cpc_contract_1' or $val == 'cpc_contract_2' or $val == 'cpc_contract_3' or
			$val == 'cpm_contract_1' or $val == 'cpm_contract_2' or $val == 'cpm_contract_3' or
			$val == 'cpd_contract_1' or $val == 'cpd_contract_2' or $val == 'cpd_contract_3') {
			if ( a24p_space($_GET['space_id'], $val) == '' or a24p_space($_GET['space_id'], $val) == 0 ) {
				if ( isset($_POST[$val]) ) {
					return $_POST[$val];
				} else {
					return '0';
				}
			} else {
				if ( isset($_POST[$val]) ) {
					return $_POST[$val];
				} else {
					return a24p_space($_GET['space_id'], $val);
				}
			}
		} else {
			if ( isset($_POST[$val]) ) {
				return $_POST[$val];
			} else {
				return a24p_space($_GET['space_id'], $val);
			}
		}
	} else {
		if ( isset($_POST[$val]) || isset($_SESSION['a24p_space_status']) ) {
			if ( isset($_SESSION['a24p_space_status']) && $_SESSION['a24p_space_status'] == 'space_added' ) {
				$_SESSION['a24p_clear_form'] = 'space_added';
				unset($_SESSION['a24p_space_status']);
			}
			$status = (isset($_SESSION['a24p_clear_form']) ? $_SESSION['a24p_clear_form'] : '');
			if ( $status == 'space_added' ) {
				return '';
			} else {
				return $_POST[$val];
			}
		} else {
			return '';
		}
	}
}

function selectedSpaceOpt($optName, $optValue)
{
	if ( $optName == 'show_ads' || $optName == 'show_close_btn' || $optName == 'close_ads' ) {
		if ( isset( $_GET['space_id'] ) || isset( $_POST['show_ads'] ) && isset( $_POST['show_close_btn'] )&& isset( $_POST['close_ads'] ) ) {
			if ( isset( $_GET['space_id'] ) ) {
				$action = explode(',', (a24p_space($_GET['space_id'], 'close_action') != null ? a24p_space($_GET['space_id'], 'close_action') : '0,0,0'));
			} else {
				$action = explode(',', ($_POST['show_ads'] > 0 ? $_POST['show_ads'] : '0').','.($_POST['show_close_btn'] > 0 ? $_POST['show_close_btn'] : '0').','.($_POST['close_ads'] > 0 ? $_POST['close_ads'] : '0'));
			}
			if ( $optName == 'show_ads' ) {
				if ( isset($action[0]) && $action[0] == $optValue ) {
					echo 'selected="selected"';
				}
			} elseif ( $optName == 'show_close_btn' ) {
				if ( isset($action[1]) && $action[1] == $optValue ) {
					echo 'selected="selected"';
				}
			} elseif ( $optName == 'close_ads' ) {
				if ( isset($action[2]) && $action[2] == $optValue ) {
					echo 'selected="selected"';
				}
			}
		}
	} else {
		if ( isset( $_GET['space_id'] ) && a24p_space($_GET['space_id'], $optName) == $optValue || isset($_POST[$optName]) && $_POST[$optName] == $optValue ) {
			echo 'selected="selected"';
		}
	}
}

function checkedSpaceOpt($optName, $optValue)
{
	if ( $optName == 'hide_for_id' && isset( $_GET['space_id'] ) ) {
		$getIds = json_decode(a24p_space($_GET['space_id'], 'advanced_opt'));
		if( isset($getIds) && in_array($optValue, explode(',', $getIds->hide_for_id)) OR isset($_POST['hide_for_id']) && in_array($optValue, $_POST['hide_for_id']) ) {
			echo 'checked="checked"';
		}
	} else {
		if( isset( $_GET['space_id'] ) && in_array($optValue, explode(',', a24p_space($_GET['space_id'], $optName))) OR isset($_POST[$optName]) && in_array($optValue, $_POST[$optName]) ) {
			echo 'checked="checked"';
		}
	}
}
?>
<h2>
	<?php if ( isset($_GET['space_id']) ): ?>
		<span class="dashicons dashicons-edit"></span> Edit <strong>AdSlot ID <?php echo $_GET['space_id']; ?></strong>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces<?php echo ((isset( $_GET['space_id'] )) ? '&space_id='.$_GET['space_id'] : null) ?>">back to <strong>adslots / ads list</strong></a></p>
	<?php else: ?>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces">Back To <strong>AdSlots</strong></a></p>
        <span class="dashicons dashicons-pressthis"></span> Add New AdSlot
		
	<?php endif; ?>
</h2>

<?php if ( isset($_GET['space_id']) && a24p_space($_GET['space_id'], 'id') != NULL || !isset($_GET['space_id']) ): ?>

<form action="" method="post" enctype="multipart/form-data">
	<?php if ( isset($_GET['space_id']) ): ?>
		<input type="hidden" value="updateSpace" name="a24pProAction">
	<?php else: ?>
		<input type="hidden" value="addNewSpace" name="a24pProAction">
	<?php endif; ?>
	<table class="a24pAdminTable a24pSpaces form-table">
		<tbody class="a24pTbody">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-admin-settings"></span>  AdSlot Settings</h3>
				</th>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_status"> AdSlot Status</label></th>
				<td>
					<select id="ADS24_LITE_status" name="status">
						<option value="active" <?php selectedSpaceOpt('status', 'active'); ?>>active</option>
						<option value="inactive" <?php selectedSpaceOpt('status', 'inactive'); ?>>inactive</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_name">AdSlot Name</label><p class="description">(shown in the order form)</p></th>
				<td>
					<input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('name')) ?>"
						   id="ADS24_LITE_name" name="name" placeholder="Sidebar Ad Section">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_title">AdSlot Title</label><p class="description">(shown in the adslot)</p></th>
				<td>
					<input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('title')) ?>"
						   id="ADS24_LITE_title" name="title" placeholder="Featured section">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_add_new">AdSlot Button</label><p class="description">(shown in the adslot)</p></th>
				<td>
					<input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('add_new')) ?>"
						   id="ADS24_LITE_add_new" name="add_new" placeholder="add advertising here">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_cpc_price">Billing Models</label><p class="description">(shown in the order form)</p></th>
				<td>
					<p class="description"><strong>Note!</strong><br>
						Fill price field only for the 1st contract because prices for other contracts will be generated in automatically (containing discount).<br>
						Enter 0 into all price fields if you want to hide AdSlots in the Order Form.</p>

					<div class="billing-col">
						<h3>CPC - Cost per Click</h3>

						<label for="ADS24_LITE_cpc_price" class="billing-label"><strong>CPC Price</strong> (contract 1st)</label>
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'before' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<input type="text" class="regular-text code billing-input" id="ADS24_LITE_cpc_price" name="cpc_price"
							   maxlength="10" value="<?php echo (getSpaceValue('cpc_price') >= 0) ? a24p_number_format(getSpaceValue('cpc_price')) : getSpaceValue('cpc_price') ; ?>" placeholder="1.00">
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'after' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<br>

						<label for="ADS24_LITE_cpc_contract_1" class="billing-label"><strong>Contract 1st</strong> (target clicks)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpc_contract_1" name="cpc_contract_1"
							   maxlength="10" value="<?php echo getSpaceValue('cpc_contract_1') ?>" placeholder="10"> clicks
						<br>

						<label for="ADS24_LITE_cpc_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target clicks)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpc_contract_2" name="cpc_contract_2"
							   maxlength="10" value="<?php echo getSpaceValue('cpc_contract_2') ?>" placeholder="100"> clicks
						<br>

						<label for="ADS24_LITE_cpc_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target clicks)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpc_contract_3" name="cpc_contract_3"
							   maxlength="10" value="<?php echo getSpaceValue('cpc_contract_3') ?>" placeholder="1000"> clicks

						<?php do_action( 'a24p-lite-addcontract', 'cpc' ); ?>
					</div>

					<div class="billing-col">
						<h3>CPM - Cost per Mille (Views)</h3>

						<label for="ADS24_LITE_cpm_price" class="billing-label"><strong>CPM Price</strong> (contract 1st)</label>
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'before' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<input type="text" class="regular-text code billing-input" id="ADS24_LITE_cpm_price" name="cpm_price"
							   maxlength="10" value="<?php echo (getSpaceValue('cpm_price') >= 0) ? a24p_number_format(getSpaceValue('cpm_price')) : getSpaceValue('cpm_price') ; ?>" placeholder="1.00">
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'after' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<br>

						<label for="ADS24_LITE_cpm_contract_1" class="billing-label"><strong>Contract 1st</strong> (target views)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpm_contract_1" name="cpm_contract_1"
							   maxlength="10" value="<?php echo getSpaceValue('cpm_contract_1') ?>" placeholder="1000"> views
						<br>

						<label for="ADS24_LITE_cpm_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target views)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpm_contract_2" name="cpm_contract_2"
							   maxlength="10" value="<?php echo getSpaceValue('cpm_contract_2') ?>" placeholder="10000"> views
						<br>

						<label for="ADS24_LITE_cpm_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target views)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpm_contract_3" name="cpm_contract_3"
							   maxlength="10" value="<?php echo getSpaceValue('cpm_contract_3') ?>" placeholder="100000"> views

						<?php do_action( 'a24p-lite-addcontract', 'cpm' ); ?>
					</div>

					<div class="billing-col">
						<h3>CPD - Cost per Days</h3>

						<label for="ADS24_LITE_cpd_price" class="billing-label"><strong>CPD Price</strong> (contract 1st)</label>
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'before' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<input type="text" class="regular-text code billing-input" id="ADS24_LITE_cpd_price" name="cpd_price"
							   maxlength="10" value="<?php echo (getSpaceValue('cpd_price') >= 0) ? a24p_number_format(getSpaceValue('cpd_price')) : getSpaceValue('cpd_price') ; ?>" placeholder="1.00">
						<?php if ( get_option('ADS24_LITE_plugin_'.'symbol_position') == 'after' ): echo get_option('ADS24_LITE_plugin_'.'currency_symbol'); endif; ?>
						<br>

						<label for="ADS24_LITE_cpd_contract_1" class="billing-label"><strong>Contract 1st</strong> (target days)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpd_contract_1" name="cpd_contract_1"
							   maxlength="10" value="<?php echo getSpaceValue('cpd_contract_1') ?>" placeholder="30"> days
						<br>

						<label for="ADS24_LITE_cpd_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target days)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpd_contract_2" name="cpd_contract_2"
							   maxlength="10" value="<?php echo getSpaceValue('cpd_contract_2') ?>" placeholder="60"> days
						<br>

						<label for="ADS24_LITE_cpd_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target days)</label>
						<input type="number" class="regular-text code billing-input" id="ADS24_LITE_cpd_contract_3" name="cpd_contract_3"
							   maxlength="10" value="<?php echo getSpaceValue('cpd_contract_3') ?>" placeholder="90"> days

						<?php do_action( 'a24p-lite-addcontract', 'cpd' ); ?>
					</div>
					<?php do_action( 'a24p-lite-addbilling' ); ?>
				</td>
			</tr>
            <div id="lite-upgrade" style="position: relative;">
                <div id="lite-discount"></div>
			<tr>
				<th scope="row">
					<label for="ADS24_LITE_discount_2">Discount (<strong>2nd</strong> contract)</label>
				</th>
				<td>
					<input type="number" class="regular-text code" id="ADS24_LITE_discount_2"
						   maxlength="2" value="" placeholder="10"> <em>%</em>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ADS24_LITE_discount_3">Discount (<strong>3rd</strong> contract)</label>
				</th>
				<td>
					<input type="number" class="regular-text code" id="ADS24_LITE_discount_3"
						   maxlength="2" value="" placeholder="25"> <em>%</em>
				</td>
			</tr>
            <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-discount-btn" target="_blank" class="upgrade-discount-btn">Upgrade to Pro</a>
            </div>

			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-admin-appearance"></span> AdSlot Layout Settings</h3>
				</th>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_grid_system">Grid System</label></th>
				<td>
					<select id="ADS24_LITE_grid_system" name="grid_system">
						<option value="a24pGridGutter" <?php selectedSpaceOpt('grid_system', 'a24pGridGutter'); ?>>Grid with Gutter between Ads</option>
						<option value="a24pGridGutVer" <?php selectedSpaceOpt('grid_system', 'a24pGridGutVer'); ?>>Grid with Vertical Gutter between Ads</option>
						<option value="a24pGridGutHor" <?php selectedSpaceOpt('grid_system', 'a24pGridGutHor'); ?>>Grid with Horizontal Gutter between Ads</option>
						<option value="a24pGridNoGutter" <?php selectedSpaceOpt('grid_system', 'a24pGridNoGutter'); ?>>Grid without Gutter between Ads</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_template">Template</label></th>
				<td>
					<select id="ADS24_LITE_template" name="template" onchange="a24pGetTemplate()">
						<?php

						$styles = array();
						$templates = glob(plugin_dir_path( __FILE__ )."../frontend/template/*");
						foreach ( $templates as $file ) {
							$files = $file;
							$styles = explode('/',$files);
							$style = array_reverse($styles);
							$name = explode('.', $style[0]);
							if ($name[0] != 'asset') {
								?>
								<option value="<?php echo $name[0]; ?>" <?php selectedSpaceOpt('template', $name[0]); ?>> <?php echo ucfirst ( str_replace('-',' ',$name[0]) ); ?></option>
							<?php
							}
						}

						?>
					</select>

					<h3>Ad Live Preview <span class="a24pLoader" style="display:none;"></span></h3>
					<div class="a24pTemplatePreview">
						<div class="a24pTemplatePreviewInner"></div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_display_type">Display Type</label></th>
				<td>
					<select id="ADS24_LITE_display_type" name="display_type">
						<option value="default" <?php selectedSpaceOpt('display_type', 'default'); ?>>default</option>
						<option value="top_scroll_bar" <?php selectedSpaceOpt('display_type', 'top_scroll_bar'); ?>>top scroll bar</option>
						<option value="bottom_scroll_bar" <?php selectedSpaceOpt('display_type', 'bottom_scroll_bar'); ?>>bottom scroll bar</option>
						<option value="floating-bottom-right" <?php selectedSpaceOpt('display_type', 'floating-bottom-right'); ?>>floating - bottom right</option>
						<option value="floating-bottom-left" <?php selectedSpaceOpt('display_type', 'floating-bottom-left'); ?>>floating - bottom left</option>
						<option value="floating-top-right" <?php selectedSpaceOpt('display_type', 'floating-top-right'); ?>>floating - top right</option>
						<option value="floating-top-left" <?php selectedSpaceOpt('display_type', 'floating-top-left'); ?>>floating - top left</option>
						<option value="popup" <?php selectedSpaceOpt('display_type', 'popup'); ?>>pop-up</option>					
						<option value="exit_popup" <?php selectedSpaceOpt('display_type', 'exit_popup'); ?>>exit pop-up</option>
						<option value="link" <?php selectedSpaceOpt('display_type', 'link'); ?>>link</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">Show Ads Randomly</th>
				<td>
					<fieldset>
						<label title="show all ads statically"><input type="radio" <?php if(!isset( $_GET['space_id'] ) or a24p_space($_GET['space_id'], 'random') == 0) { echo 'checked="checked"'; } ?>value="0" name="random"> <strong>no</strong>, show all ads statically</label><br>
						<label title="show ads randomly in one row"><input type="radio" <?php if(isset( $_GET['space_id'] ) && a24p_space($_GET['space_id'], 'random') == 1) { echo 'checked="checked"'; } ?> value="1" name="random"> <strong>yes</strong>, show ads randomly in one row</label><br>
						<label title="show ads randomly in one column"><input type="radio" <?php if(isset( $_GET['space_id'] ) && a24p_space($_GET['space_id'], 'random') == 2) { echo 'checked="checked"'; } ?> value="2" name="random"> <strong>yes</strong>, show ads randomly in one column</label><br>
						<label title="show all ads randomly"><input type="radio" <?php if(isset( $_GET['space_id'] ) && a24p_space($_GET['space_id'], 'random') == 3) { echo 'checked="checked"'; } ?> value="3" name="random"> <strong>yes</strong>, show all ads randomly</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_max_items">Maximum Ads in AdSlot</label></th>
				<td>
					<select id="ADS24_LITE_max_items" name="max_items">
						<?php
						for ($i = 1; $i <= 24; $i++) {
							echo $i;
							?>
							<option value="<?php echo $i; ?>" <?php selectedSpaceOpt('max_items', $i); ?>> <?php echo $i; ?> item<?php if($i != 1) { echo 's'; } ?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_col_per_row">Number of Ads per one <br>Row or Column</label></th>
				<td>
					<select id="ADS24_LITE_col_per_row" name="col_per_row">
						<?php

						for ($i = 1; $i <= 12; $i++) {
							echo $i;
							if ( $i <= 4 || $i == 8 || $i == 12 ) {
								?>
								<option value="<?php echo $i; ?>" <?php selectedSpaceOpt('col_per_row', $i); ?>> <?php echo $i; ?> <?php if($i == 1) { echo 'item in row / column'; } else { echo 'items in rows / columns'; } ?></option>
							<?php
							}
						}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_animation">Ads Animation if visible</label></th>
				<td>
					<select id="ADS24_LITE_animation" name="animation">
						<option value="none" <?php selectedSpaceOpt('animation', 'none'); ?>>none</option>
						<optgroup label="Attention Seekers">
							<option value="bounce" <?php selectedSpaceOpt('animation', 'bounce'); ?>>bounce</option>
							<option value="flash" <?php selectedSpaceOpt('animation', 'flash'); ?>>flash</option>
							<option value="pulse" <?php selectedSpaceOpt('animation', 'pulse'); ?>>pulse</option>
							<option value="rubberBand" <?php selectedSpaceOpt('animation', 'rubberBand'); ?>>rubberBand</option>
							<option value="shake" <?php selectedSpaceOpt('animation', 'shake'); ?>>shake</option>
							<option value="swing" <?php selectedSpaceOpt('animation', 'swing'); ?>>swing</option>
							<option value="tada" <?php selectedSpaceOpt('animation', 'tada'); ?>>tada</option>
							<option value="wobble" <?php selectedSpaceOpt('animation', 'wobble'); ?>>wobble</option>
						</optgroup>

						<optgroup label="Bouncing Entrances">
							<option value="bounceIn" <?php selectedSpaceOpt('animation', 'bounceIn'); ?>>bounceIn</option>
							<option value="bounceInDown" <?php selectedSpaceOpt('animation', 'bounceInDown'); ?>>bounceInDown</option>
							<option value="bounceInLeft" <?php selectedSpaceOpt('animation', 'bounceInLeft'); ?>>bounceInLeft</option>
							<option value="bounceInRight" <?php selectedSpaceOpt('animation', 'bounceInRight'); ?>>bounceInRight</option>
							<option value="bounceInUp" <?php selectedSpaceOpt('animation', 'bounceInUp'); ?>>bounceInUp</option>
						</optgroup>

						<optgroup label="Fading Entrances">
							<option value="fadeIn" <?php selectedSpaceOpt('animation', 'fadeIn'); ?>>fadeIn</option>
							<option value="fadeInDown" <?php selectedSpaceOpt('animation', 'fadeInDown'); ?>>fadeInDown</option>
							<option value="fadeInDownBig" <?php selectedSpaceOpt('animation', 'fadeInDownBig'); ?>>fadeInDownBig</option>
							<option value="fadeInLeft" <?php selectedSpaceOpt('animation', 'fadeInLeft'); ?>>fadeInLeft</option>
							<option value="fadeInLeftBig" <?php selectedSpaceOpt('animation', 'fadeInLeftBig'); ?>>fadeInLeftBig</option>
							<option value="fadeInRight" <?php selectedSpaceOpt('animation', 'fadeInRight'); ?>>fadeInRight</option>
							<option value="fadeInRightBig" <?php selectedSpaceOpt('animation', 'fadeInRightBig'); ?>>fadeInRightBig</option>
							<option value="fadeInUp" <?php selectedSpaceOpt('animation', 'fadeInUp'); ?>>fadeInUp</option>
							<option value="fadeInUpBig" <?php selectedSpaceOpt('animation', 'fadeInUpBig'); ?>>fadeInUpBig</option>
						</optgroup>

						<optgroup label="Flippers">
							<option value="flip" <?php selectedSpaceOpt('animation', 'flip'); ?>>flip</option>
							<option value="flipInX" <?php selectedSpaceOpt('animation', 'flipInX'); ?>>flipInX</option>
							<option value="flipInY" <?php selectedSpaceOpt('animation', 'flipInY'); ?>>flipInY</option>
						</optgroup>

						<optgroup label="Lightspeed">
							<option value="lightSpeedIn" <?php selectedSpaceOpt('animation', 'lightSpeedIn'); ?>>lightSpeedIn</option>
						</optgroup>

						<optgroup label="Rotating Entrances">
							<option value="rotateIn" <?php selectedSpaceOpt('animation', 'rotateIn'); ?>>rotateIn</option>
							<option value="rotateInDownLeft" <?php selectedSpaceOpt('animation', 'rotateInDownLeft'); ?>>rotateInDownLeft</option>
							<option value="rotateInDownRight" <?php selectedSpaceOpt('animation', 'rotateInDownRight'); ?>>rotateInDownRight</option>
							<option value="rotateInUpLeft" <?php selectedSpaceOpt('animation', 'rotateInUpLeft'); ?>>rotateInUpLeft</option>
							<option value="rotateInUpRight" <?php selectedSpaceOpt('animation', 'rotateInUpRight'); ?>>rotateInUpRight</option>
						</optgroup>

						<optgroup label="Specials">
							<option value="hinge" <?php selectedSpaceOpt('animation', 'hinge'); ?>>hinge</option>
							<option value="rollIn" <?php selectedSpaceOpt('animation', 'rollIn'); ?>>rollIn</option>
						</optgroup>

						<optgroup label="Zoom Entrances">
							<option value="zoomIn" <?php selectedSpaceOpt('animation', 'zoomIn'); ?>>zoomIn</option>
							<option value="zoomInDown" <?php selectedSpaceOpt('animation', 'zoomInDown'); ?>>zoomInDown</option>
							<option value="zoomInLeft" <?php selectedSpaceOpt('animation', 'zoomInLeft'); ?>>zoomInLeft</option>
							<option value="zoomInRight" <?php selectedSpaceOpt('animation', 'zoomInRight'); ?>>zoomInRight</option>
							<option value="zoomInUp" <?php selectedSpaceOpt('animation', 'zoomInUp'); ?>>zoomInUp</option>
						</optgroup>
					</select>
				</td>
			</tr>
            <div id="lite-upgrade" style="position: relative;">
                <div id="lite-device"></div>
			<tr>
				<th scope="row"><label for="ADS24_LITE_devices">Show in specific devices</label></th>
				<td>
					<ul id="inside-hide-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
						<li class="a24pProSpecificDevice">
							<label class="selectit">
								<input value="mobile" type="checkbox" ><br><br>
								<img src="<?php echo plugins_url('/ads24-lite-plugin/frontend/img/icon-mobile.png'); ?>"/><br>
								Mobile
							</label>
						</li>
						<li class="a24pProSpecificDevice">
							<label class="selectit">
								<input value="tablet" type="checkbox" ><br><br>
								<img src="<?php echo plugins_url('/ads24-lite-plugin/frontend/img/icon-tablet.png'); ?>"/><br>
								Tablet
							</label>
						</li>
						<li class="a24pProSpecificDevice">
							<label class="selectit">
								<input value="desktop" type="checkbox" ><br><br>
								<img src="<?php echo plugins_url('/ads24-lite-plugin/frontend/img/icon-desktop.png'); ?>"/><br>
								Desktop
							</label>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_show_ads">Show AdSlot after X seconds</label></th>
				<td>
					<select id="ADS24_LITE_show_ads" name="show_ads">
                    
                    <option>none</option>
						
					</select>
					<p class="description">You can use it for <strong>default, carousel, sliding bar, floating, pop-up, corner peel, layer</strong> and <strong>pop-up</strong> display types.</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_show_close_btn">Show Close Button after X seconds</label></th>
				<td>
					<select id="ADS24_LITE_show_close_btn" name="show_close_btn">
						
						<option>none</option>
					</select>
					<p class="description">You can use it for <strong>sliding bar, floating, layer</strong> and <strong>pop-up</strong> display types.</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ADS24_LITE_close_ads">Close AdSlot after X seconds</label></th>
				<td>
					<select id="ADS24_LITE_close_ads" name="close_ads">
						<option>none</option>
					</select>
					<p class="description">You can use it for <strong>default, carousel, sliding bar, floating, corner peel, layer</strong> and <strong>pop-up</strong> display types.</p>
				</td>
			</tr>
            <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-pg-methods-btn" target="_blank" class="upgrade-device-btn">Upgrade to Pro</a>
            </div>
			<tr id="a24pShowPagePost" style="padding-top: 25px;">
				<th class="a24pLast" scope="row"><strong>Hide</strong> for specific <br>Posts / Pages / Custom Types or Taxonomies</th>
				<td class="a24pLast">
					<div style="max-width: 500px;">
						<div class="inside">
							<div id="taxonomy-category" class="categorydiv">
								<ul id="category-tabs" class="category-tabs">
									<li class="tabs a24pProTabPP" data-tab="a24pAllPages"><a href="#a24pShowPagePost">Select Pages</a></li>
									<li class="a24pProTabPP" data-tab="a24pAllPosts"><a href="#a24pShowPagePost">Select Posts</a></li>
									<li class="a24pProTabPP" data-tab="a24pAllHideCustoms"><a href="#a24pShowPagePost">Custom Types or Taxonomies</a></li>
								</ul>

								<div class="a24pAllHideCustoms tabs-panel" style="display: none;">
									<br><strong>Note!</strong><br>
									We recommend use this options really carefully. Introduced rules can really restrict your ads.
									Remember to paste exact <strong>slug of CPT or Taxonomies</strong>.
									<?php $getAdvanced = json_decode(a24p_space((isset($_GET['space_id']) && $_GET['space_id'] > 0 ? $_GET['space_id'] : 0), 'advanced_opt')); ?>
									<input type="hidden" name="hide_customs" class="spaceHideCustoms" value="<?php echo (isset($getAdvanced->hide_customs) ? $getAdvanced->hide_customs : '') ?>" />
									<input type="text" id="spaceHideCustoms" class="spaceChips tagfield" value="<?php echo (isset($getAdvanced->hide_customs) ? $getAdvanced->hide_customs : '') ?>" placeholder=""/>
								</div>

								<?php $ajaxLimit = 200; ?>
								<?php $count_posts = wp_count_posts(); ?>
								<?php $count_pages = wp_count_posts('page'); ?>
								<div class="a24pAllPosts tabs-panel" style="display: none;">
									<ul id="inside-list-posts" class="categorychecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedPO"></div>
										<h4>Unselected</h4>
										<div class="uncheckedPO" offset="0" count="<?php echo $count_posts->publish ?>">
											<?php
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
													if ( isset($_GET['space_id']) ) {
														$getIds = json_decode(a24p_space($_GET['space_id'], 'advanced_opt'));
														if ( isset($getIds->hide_for_id) ) {
															foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
																	$getEntryIds[] = substr($getId, 1);
																}
															}
														}
													}

													// get args
													if ( $count_posts->publish <= $ajaxLimit || $getEntryIds != null ) {
														if ( $count_posts->publish <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array('include' => $getEntryIds, 'posts_per_page' => $ajaxLimit);
														$allPosts = get_posts($args);
														if ($allPosts) {
															foreach ($allPosts as $post) {
																?>
																<li class="a24pProSpecificItem a24pCheckItem-PO<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>">
																	<label class="selectit"><input
																				value="<?php echo $blog['blog_id']; ?><?php echo $post->ID; ?>"
																				class="a24pCheckItem" section="PO"
																				itemId="PO<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>"
																				type="checkbox"
																				name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $blog['blog_id'] . $post->ID); ?>>
																		<?php echo $post->post_title; ?> (site
																		id: <?php echo $blog['blog_id']; ?>)</label>
																</li>
																<?php
															}
														}
													}

												}

												// return to the current site
												switch_to_blog( $current->id );

											} else {

												// get only selected posts
												$getEntryIds = null;
												if ( isset($_GET['space_id']) ) {
													$getIds = json_decode(a24p_space($_GET['space_id'], 'advanced_opt'));
													foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
														$getEntryIds[] = $getId;
													}
												}

												// get args
												if ( $count_posts->publish <= $ajaxLimit || $getEntryIds != null ) {
													if ( $count_posts->publish <= $ajaxLimit ) { $getEntryIds = array(); }
													$args = array('include' => $getEntryIds, 'posts_per_page' => $ajaxLimit);
													$allPosts = get_posts($args);
													if ($allPosts) {
														foreach ($allPosts as $post) {
															?>
															<li class="a24pProSpecificItem a24pCheckItem-PO<?php echo $post->ID; ?>">
																<label class="selectit"><input
																			value="<?php echo $post->ID; ?>"
																			class="a24pCheckItem" section="PO"
																			itemId="PO<?php echo $post->ID; ?>"
																			type="checkbox"
																			name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $post->ID); ?>>
																	<?php echo $post->post_title; ?></label>
															</li>
															<?php
														}
													}
												}

											}

											if ( $count_posts->publish > $ajaxLimit ) {
												?>
												<a href="#a24pShowPagePost" class="a24pLinkPO" onclick="a24pGetUnselected('posts', 'PO', <?php echo $ajaxLimit; ?>)">show unselected posts</a> <span class="a24pLoader a24pLoaderPO" style="display:none;"></span>
												<?php
											}
											?>
										</div>
									</ul>
								</div>

								<div class="a24pAllPages tabs-panel" style="display: block;">
									<ul id="inside-list-pages" data-wp-lists="list:page" class="pagechecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedPA"></div>
										<h4>Unselected</h4>
										<div class="uncheckedPA" offset="0" count="<?php echo $count_pages->publish ?>">
											<?php
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
													if ( isset($_GET['space_id']) ) {
														$getIds = json_decode(a24p_space($_GET['space_id'], 'advanced_opt'));
														if ( isset($getIds->hide_for_id) ) {
															foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
																	$getEntryIds[] = substr($getId, 1);
																}
															}
														}
													}

													// get args
													if ( $count_pages->publish <= $ajaxLimit || $getEntryIds != null ) {
														if ( $count_pages->publish <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array('include' => $getEntryIds, 'number' => $ajaxLimit);
														$allPosts = get_pages($args);
														if ($allPosts) {
															foreach ($allPosts as $post) {
																?>
																<li class="a24pProSpecificItem a24pCheckItem-PA<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>">
																	<label class="selectit"><input
																				value="<?php echo $blog['blog_id']; ?><?php echo $post->ID; ?>"
																				class="a24pCheckItem" section="PA"
																				itemId="PA<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>"
																				type="checkbox"
																				name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $blog['blog_id'] . $post->ID); ?>>
																		<?php echo $post->post_title; ?> (site
																		id: <?php echo $blog['blog_id']; ?>)</label>
																</li>
																<?php
															}
														}
													}

												}

												// return to the current site
												switch_to_blog( $current->id );

											} else {

												// get only selected posts
												$getEntryIds = null;
												if ( isset($_GET['space_id']) ) {
													$getIds = json_decode(a24p_space($_GET['space_id'], 'advanced_opt'));
													foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
														$getEntryIds[] = $getId;
													}
												}

												// get args
												if ( $count_pages->publish <= $ajaxLimit || $getEntryIds != null ) {
													if ( $count_pages->publish <= $ajaxLimit ) { $getEntryIds = array(); }
													$args = array('include' => $getEntryIds, 'number' => $ajaxLimit);
													$allPosts = get_pages($args);
													if ($allPosts) {
														foreach ($allPosts as $post) {
															?>
															<li class="a24pProSpecificItem a24pCheckItem-PA<?php echo $post->ID; ?>">
																<label class="selectit"><input
																			value="<?php echo $post->ID; ?>"
																			class="a24pCheckItem" section="PA"
																			itemId="PA<?php echo $post->ID; ?>"
																			type="checkbox"
																			name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $post->ID); ?>>
																	<?php echo $post->post_title; ?></label>
															</li>
															<?php
														}
													}
												}

											}

											if ( $count_pages->publish > $ajaxLimit ) {
												?>
												<a href="#a24pShowPagePost" class="a24pLinkPA" onclick="a24pGetUnselected('pages', 'PA', <?php echo $ajaxLimit; ?>)">show unselected pages</a> <span class="a24pLoader a24pLoaderPA" style="display:none;"></span>
												<?php
											}
											?>
										</div>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr id="a24pShowCatTag" style="padding-top: 25px;">
				<th class="a24pLast" scope="row">Show for specific <br>Categories / Tags / Custom Types or Taxonomies</th>
				<td class="a24pLast">
					<div style="max-width: 500px;">
						<div class="inside">
							<div id="taxonomy-category" class="categorydiv">
								<ul id="category-tabs" class="category-tabs">
									<li class="tabs a24pProTab" data-tab="a24pAllCategories"><a href="#a24pShowCatTag">Select Categories</a></li>
									<li class="a24pProTab" data-tab="a24pAllTags"><a href="#a24pShowCatTag">Select Tags</a></li>
									<li class="a24pProTab" data-tab="a24pAllShowCustoms"><a href="#a24pShowCatTag">Custom Types or Taxonomies</a></li>
								</ul>

								<div class="a24pAllShowCustoms tabs-panel" style="display: none;">
									<br><strong>Note!</strong><br>
									We recommend use this options really carefully. Introduced rules can really restrict your ads.
									Remember to paste exact <strong>slug of CPT or Taxonomies</strong>.
									<input type="hidden" name="show_customs" class="spaceShowCustoms" value="<?php echo (isset($getAdvanced->show_customs) ? $getAdvanced->show_customs : '') ?>" />
									<input type="text" id="spaceShowCustoms" class="spaceChips tagfield" value="<?php echo (isset($getAdvanced->show_customs) ? $getAdvanced->show_customs : '') ?>" placeholder=""/>
								</div>

								<?php $count_tags = count(get_tags()); ?>
								<div class="a24pAllTags tabs-panel" style="display: none;">
									<ul id="inside-list-tags" class="categorychecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedT"></div>
										<h4>Unselected</h4>
										<div class="uncheckedT" offset="0" count="<?php echo $count_tags ?>">
											<?php
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
													if ( isset($_GET['space_id']) ) {
														$getIds = a24p_space($_GET['space_id'], 'has_tags');
														foreach ( explode(',', $getIds) as $getId ) {
															$getId = get_term_by('name', $getId, 'post_tag');
															if ( isset( $getId->term_id ) ) {
																$getEntryIds[] = $getId->term_id;
															}
														}
													}

													if ( $count_tags <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
														if ( $count_tags <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array( 'taxonomy' => 'post_tag', 'include' => $getEntryIds, 'number' => $ajaxLimit );
														$posttags = get_terms($args);
														if ( !is_wp_error( $posttags ) ) {
															if ( is_array($posttags) ) {
																foreach ($posttags as $key => $tag) {
																	?>
																	<li class="a24pProSpecificItem a24pCheckItem-T<?php echo $key; ?>-<?php echo $blog['blog_id']; ?>">
																		<label class="selectit"><input
																				value="<?php echo $tag->name; ?>"
																				class="a24pCheckItem" section="T"
																				itemId="T<?php echo $key; ?>-<?php echo $blog['blog_id']; ?>"
																				type="checkbox"
																				name="space_tags[]" <?php checkedSpaceOpt('has_tags', $tag->name); ?>>
																			<?php echo $tag->name; ?> (site
																			id: <?php echo $blog['blog_id']; ?>)</label>
																	</li>
																<?php
																}
															} else { echo "No tags found."; }
														}
													}

												}

												// return to the current site
												switch_to_blog( $current->id );

											} else {

												// get only selected tags
												$getEntryIds = null;
												if ( isset($_GET['space_id']) ) {
													$getIds = a24p_space($_GET['space_id'], 'has_tags');
													foreach ( explode(',', $getIds) as $getId ) {
														$getId = get_term_by('name', $getId, 'post_tag');
														$getEntryIds[] = $getId->term_id;
													}
												}

												if ( $count_tags <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
													if ( $count_tags <= $ajaxLimit ) { $getEntryIds = array(); }
													$args = array( 'taxonomy' => 'post_tag', 'include' => $getEntryIds, 'number' => $ajaxLimit );
													$posttags = get_terms($args);
													if ( !is_wp_error( $posttags ) ) {
														if ( is_array($posttags) ) {
															foreach ($posttags as $key => $tag) {
																?>
																<li class="a24pProSpecificItem a24pCheckItem-T<?php echo $key; ?>">
																	<label class="selectit"><input
																			value="<?php echo $tag->name; ?>"
																			class="a24pCheckItem" section="T"
																			itemId="T<?php echo $key; ?>" type="checkbox"
																			name="space_tags[]" <?php checkedSpaceOpt('has_tags', $tag->name); ?>>
																		<?php echo $tag->name; ?></label>
																</li>
															<?php
															}
														} else { echo "No tags found."; }
													}
												}

											}

											if ( $count_tags > $ajaxLimit ) {
												?>
												<a href="#a24pShowPagePost" class="a24pLinkT" onclick="a24pGetUnselected('tags', 'T', <?php echo $ajaxLimit; ?>)">show unselected tags</a> <span class="a24pLoader a24pLoaderT" style="display:none;"></span>
											<?php
											}
											?>
										</div>
									</ul>
								</div>

								<?php $count_categories = count(get_categories()) ?>
								<div class="a24pAllCategories tabs-panel" style="display: block;">
									<ul id="inside-list-categories" data-wp-lists="list:category" class="categorychecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedCT"></div>
										<h4>Unselected</h4>
										<div class="uncheckedCT" offset="0" count="<?php echo $count_categories ?>">
											<?php
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
													if ( isset($_GET['space_id']) ) {
														$getIds = a24p_space($_GET['space_id'], 'in_categories');
														foreach ( explode(',', $getIds) as $getId ) {
															$getEntryIds[] = $getId;
														}
													}

													if ( $count_categories <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
														if ( $count_categories <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array( 'taxonomy' => 'category', 'include' => $getEntryIds, 'number' => $ajaxLimit );
														$postcategories = get_terms($args);
														if ( !is_wp_error( $postcategories ) ) {
															if ( is_array($postcategories) ) {
																foreach ($postcategories as $postcategory) {
																	?>
																	<li class="a24pProSpecificItem a24pCheckItem-CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>">
																		<label class="selectit"><input
																				value="<?php echo $postcategory->term_id; ?>"
																				class="a24pCheckItem" section="CT"
																				itemId="CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>"
																				type="checkbox"
																				name="space_categories[]" <?php checkedSpaceOpt('in_categories', $postcategory->term_id); ?>>
																			<?php echo $postcategory->name; ?> (site
																			id: <?php echo $blog['blog_id']; ?>)</label>
																	</li>
																<?php
																}
															} else { echo "No categories found."; }
														}
													}

												}

												// return to the current site
												switch_to_blog( $current->id );

											} else {

												// get only selected tags
												$getEntryIds = null;
												if ( isset($_GET['space_id']) ) {
													$getIds = a24p_space($_GET['space_id'], 'in_categories');
													foreach ( explode(',', $getIds) as $getId ) {
														$getEntryIds[] = $getId;
													}
												}

												if ( $count_categories <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
													if ($count_categories <= $ajaxLimit) { $getEntryIds = array(); }
													$args = array( 'taxonomy' => 'category', 'include' => $getEntryIds, 'number' => $ajaxLimit );
													$postcategories = get_terms($args);
													if ( !is_wp_error( $postcategories ) ) {
														if ( is_array($postcategories) ) {
															foreach ($postcategories as $postcategory) {
																?>
																<li class="a24pProSpecificItem a24pCheckItem-CT<?php echo $postcategory->term_id; ?>">
																	<label class="selectit"><input
																			value="<?php echo $postcategory->term_id; ?>"
																			class="a24pCheckItem" section="CT"
																			itemId="CT<?php echo $postcategory->term_id; ?>"
																			type="checkbox"
																			name="space_categories[]" <?php checkedSpaceOpt('in_categories', $postcategory->term_id); ?>>
																		<?php echo $postcategory->name; ?></label>
																</li>
															<?php
															}
														} else { echo "No categories found."; }
													}
												}

											}

											if ( $count_categories > $ajaxLimit ) {
												?>
												<a href="#a24pShowPagePost" class="a24pLinkCT" onclick="a24pGetUnselected('categories', 'CT', <?php echo $ajaxLimit; ?>)">show unselected categories</a> <span class="a24pLoader a24pLoaderCT" style="display:none;"></span>
											<?php
											}
											?>
										</div>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
            <div id="lite-upgrade" style="position: relative;">
            <div id="lite-country"></div>
			<tr id="a24pShowGeo" style="padding-top: 25px;">
				<th class="a24pLast" scope="row">Show / Hide in specific <br>Countries</th>
				<td class="a24pLast">
					<div style="max-width: 500px;">
						<div class="inside">
							<div id="taxonomy-category" class="categorydiv">
								<ul id="category-tabs" class="category-tabs">
									<li class="tabs a24pProTabCountry" data-tab="a24pShowCountries"><a href="#a24pShowGeo">Show in Countries</a></li>
									<li class="a24pProTabCountry" data-tab="a24pHideCountries"><a href="#a24pShowGeo">Hide in Countries</a></li>
									<li class="a24pProTabCountry" data-tab="a24pAdvanced"><a href="#a24pShowGeo">Advanced</a></li>
								</ul>

								<div id="a24pAdvanced" class="a24pAdvanced tabs-panel" style="display: none;">
									<ul id="inside-advanced" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
										<li>
											<strong>Note!</strong><br>
											We recommend to use Advanced options really carefully. Introduced rules can really restrict your ads.
											Remember that Internet Providers don't always return the real position of the user (it all depends of their internet central point).
										</li>
										<li class="a24pProSpecificItem">
											<div style="margin-bottom: 10px"><br><strong>Show</strong> in states / provinces, cities or zip-codes</div>
											<input type="hidden" name="show_in_advanced" class="show_in_advanced" value="<?php echo getSpaceValue('show_in_advanced') ?>" />
											<input type="text" class="regular-text code spaceChips tagfield" id="show_in_advanced" name="show_in_advanced"
												   value="<?php echo getSpaceValue('show_in_advanced') ?>" />
										</li>
										<li class="a24pProSpecificItem">
											<div style="margin: 10px 0"><strong>Hide</strong> in states / provinces, cities or zip-codes</div>
											<input type="hidden" name="hide_in_advanced" class="hide_in_advanced" value="<?php echo getSpaceValue('hide_in_advanced') ?>" />
											<input type="text" class="regular-text code spaceChips tagfield" id="hide_in_advanced"
												   value="<?php echo getSpaceValue('hide_in_advanced') ?>" />
										</li>
									</ul>
								</div>

								<div id="a24pHideCountries" class="a24pHideCountries tabs-panel" style="display: none;">
									<ul id="inside-hide-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedHC"></div>
										<h4>Unselected</h4>
										<div class="uncheckedHC">
											<?php
											$postcategories = a24p_get_country_codes();
											if ($postcategories) {
												foreach($postcategories as $postcategory) {
													?>
													<li class="a24pProSpecificItem a24pCheckItem-HC<?php echo $postcategory['Code']; ?>">
														<label class="selectit"><input value="<?php echo $postcategory['Code']; ?>" class="a24pCheckItem" section="HC" itemId="HC<?php echo $postcategory['Code']; ?>" type="checkbox" >
															<?php echo $postcategory['Name']; ?></label>
													</li>
												<?php
												}
											}
											?>
										</div>
									</ul>
								</div>

								<div id="a24pShowCountries" class="a24pShowCountries tabs-panel" style="display: block;">
									<ul id="inside-show-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
										<h4>Selected</h4>
										<div class="checkedC"></div>
										<h4>Unselected</h4>
										<div class="uncheckedC">
											<?php
											$postcategories = a24p_get_country_codes();
											if ($postcategories) {
												foreach($postcategories as $postcategory) {
													?>
													<li class="a24pProSpecificItem a24pCheckItem-C<?php echo $postcategory['Code']; ?>">
														<label class="selectit"><input value="<?php echo $postcategory['Code']; ?>" class="a24pCheckItem" section="C" itemId="C<?php echo $postcategory['Code']; ?>" type="checkbox" name="show_in_country[]" <?php checkedSpaceOpt('show_in_country', $postcategory['Code']); ?>>
															<?php echo $postcategory['Name']; ?></label>
													</li>
												<?php
												}
											}
											?>
										</div>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
            <a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-pg-methods-btn" target="_blank" class="upgrade-country-btn">Upgrade to Pro</a>
            </div>
			<?php if ( get_option('ADS24_LITE_plugin_calendar') == 'yes' ): ?>
			<tr>
				<th scope="row"><label for="ADS24_LITE_unavailable_dates">Unavailable Dates in Calendar</label></th>
				<td>
					<input type="text" class="regular-text code" maxlength="1000" value="<?php echo getSpaceValue('unavailable_dates') ?>"
						   id="ADS24_LITE_unavailable_dates" name="unavailable_dates" placeholder="2015-10-17,2015-10-21">
					<p class="description"><strong>Example</strong> 2015-10-17,2015-10-21,2015-10-24</p>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>Customization</th>
				<td>
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
							<div id="a24pSpaceCustomization" class="postbox closed">
								<div class="handlediv a24pSpaceCustomization" title="Click to toggle"><br></div><p class="hndle ui-sortable-handle a24pSpaceCustomization" style="margin: 0; padding: 10px; cursor: pointer;"><span>Options</span></p>
								<div class="inside">
									<table>
										<tbody>
										<tr>
											<th scope="row"><label for="ADS24_LITE_font">Google Font</label></th>
											<td>
												<input type="text" class="regular-text code" value="<?php echo str_replace("\\'", "", getSpaceValue('font')) ?>" id="ADS24_LITE_font" name="font">
												<p class="description">
													Example: <strong>font-family: 'Open Sans', sans-serif;</strong><br>
													Choose from 650+ fonts available here <a href="https://www.google.com/fonts" target="_blank">https://www.google.com/fonts</a>
												</p>
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_font_url">Google Font URL</label></th>
											<td>
												<input type="text" class="regular-text code" value="<?php echo getSpaceValue('font_url') ?>" id="ADS24_LITE_font_url" name="font_url">
												<p class="description">Example: <strong>@import url(http://fonts.googleapis.com/css?family=Open+Sans);</strong></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_header_bg">Header Background</label></th>
											<td>
												<input id="ADS24_LITE_header_bg"
													   name="header_bg"
													   value="<?php echo getSpaceValue('header_bg') ?>"
													   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_header_color">Header Color</label></th>
											<td>
												<input id="ADS24_LITE_header_color"
													   name="header_color"
													   value="<?php echo getSpaceValue('header_color') ?>"
													   data-default-color="#000000" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_link_color">Header Link Color</label></th>
											<td>
												<input id="ADS24_LITE_link_color"
													   name="link_color"
													   value="<?php echo getSpaceValue('link_color') ?>"
													   data-default-color="#000000" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ads_bg">Ads Whole Section Background</label></th>
											<td>
												<input id="ADS24_LITE_ads_bg"
													   name="ads_bg"
													   value="<?php echo getSpaceValue('ads_bg') ?>"
													   data-default-color="#f5f5f5" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_bg">Ad Background</label></th>
											<td>
												<input id="ADS24_LITE_ad_bg"
													   name="ad_bg"
													   value="<?php echo getSpaceValue('ad_bg') ?>"
													   data-default-color="#f5f5f5" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_title_color">Ad Title Color</label></th>
											<td>
												<input id="ADS24_LITE_ad_title_color"
													   name="ad_title_color"
													   value="<?php echo getSpaceValue('ad_title_color') ?>"
													   data-default-color="#000000" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_desc_color">Ad Description Color</label></th>
											<td>
												<input id="ADS24_LITE_ad_desc_color"
													   name="ad_desc_color"
													   value="<?php echo getSpaceValue('ad_desc_color') ?>"
													   data-default-color="#000000" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_url_color">Ad URL Color</label></th>
											<td>
												<input id="ADS24_LITE_ad_url_color"
													   name="ad_url_color"
													   value="<?php echo getSpaceValue('ad_url_color') ?>"
													   data-default-color="#000000" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_extra_color_1">Ad Extra Color 1</label></th>
											<td>
												<input id="ADS24_LITE_ad_extra_color_1"
													   name="ad_extra_color_1"
													   value="<?php echo getSpaceValue('ad_extra_color_1') ?>"
													   data-default-color="#FFFFFF" type="text" class="a24pColorPicker">
											</td>
										</tr>
										<tr>
											<th scope="row"><label for="ADS24_LITE_ad_extra_color_2">Ad Extra Color 2</label></th>
											<td>
												<input id="ADS24_LITE_ad_extra_color_2"
													   name="ad_extra_color_2"
													   value="<?php echo getSpaceValue('ad_extra_color_2') ?>"
													   data-default-color="#444444" type="text" class="a24pColorPicker">
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" value="Save AdSlot" class="button button-primary" id="ADS24_LITE_submit" name="ADS24_LITE_submit">
	</p>
</form>
<style>
	<?php
		foreach ( $templates as $file ) {
			$styles = explode('/', $file);
			$style = array_reverse($styles);
			$name = explode('.', $style[0]);
			$size = explode('--', str_replace('block-', '', $name[0]));
			$width = (isset($size[0]) ? $size[0] : 0);
			$height = (isset($size[1]) ? $size[1] : 0);
			if ( $width > 0 && $height > 0 ) { ?>
	.a24pTemplatePreview .a24p-<?php echo $name[0]; ?> {
		width: <?php echo $width; ?>px;
		height: <?php echo $height; ?>px;
	}
	<?php }
}
?>
</style>

<?php else: ?>

	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><strong>Error!</strong> Space not exists!</p>
	</div>

<?php endif; ?>

<script>
	(function($){
		// - start - open page
		var a24pItemsWrap = $('.wrap');
		a24pItemsWrap.hide();

		setTimeout(function(){
			a24pItemsWrap.fadeIn(400);
		}, 400);
		// - end - open page

		a24pGetTemplate();
		$(document).ready(function(){
			$('.a24pColorPicker').wpColorPicker();
			a24pGetTemplate();

			var a24pAnimation = $("#ADS24_LITE_animation");
			var a24pTemplatePreviewInner = $(".a24pTemplatePreviewInner");
			a24pAnimation.bind('change',function() {
				a24pTemplatePreviewInner.addClass(('animated ' + a24pAnimation.val()));
				setTimeout(function(){
					a24pTemplatePreviewInner.removeClass().addClass('a24pTemplatePreviewInner');
				}, 1500);
			});
			a24pAnimation.trigger('change');
		});

		$('.a24pProTabPP').click(function() {
			var clicked = $(this).attr('data-tab');
			$('.a24pProTabPP').removeClass('tabs');
			$(this).addClass('tabs');
			if ( clicked === 'a24pAllPosts' ) {
				$('.a24pAllPosts').show();
				$('.a24pAllPages').hide();
				$('.a24pAllHideCustoms').hide();
			} else if ( clicked === 'a24pAllPages' ) {
				$('.a24pAllPages').show();
				$('.a24pAllPosts').hide();
				$('.a24pAllHideCustoms').hide();
			} else {
				$('.a24pAllHideCustoms').show();
				$('.a24pAllPosts').hide();
				$('.a24pAllPages').hide();
			}
		});

		$('.a24pProTab').click(function() {
			var clicked = $(this).attr('data-tab');
			$('.a24pProTab').removeClass('tabs');
			$(this).addClass('tabs');
			if ( clicked === 'a24pAllCategories' ) {
				$('.a24pAllCategories').show();
				$('.a24pAllTags').hide();
				$('.a24pAllShowCustoms').hide();
			} else if ( clicked === 'a24pAllTags' ) {
				$('.a24pAllTags').show();
				$('.a24pAllCategories').hide();
				$('.a24pAllShowCustoms').hide();
			} else {
				$('.a24pAllShowCustoms').show();
				$('.a24pAllCategories').hide();
				$('.a24pAllTags').hide();
			}
		});

		$('.a24pProTabCountry').click(function() {
			var clicked = $(this).attr('data-tab');
			$('.a24pProTabCountry').removeClass('tabs');
			$(this).addClass('tabs');
			if ( clicked === 'a24pShowCountries' ) {
				$('.a24pShowCountries').show();
				$('.a24pHideCountries').hide();
				$('.a24pAdvanced').hide();
			} else if ( clicked === 'a24pHideCountries' ) {
				$('.a24pHideCountries').show();
				$('.a24pShowCountries').hide();
				$('.a24pAdvanced').hide();
			} else {
				$('.a24pAdvanced').show();
				$('.a24pShowCountries').hide();
				$('.a24pHideCountries').hide();
			}
		});

		// selected / unselected checkbox
		(function($){
			$(document).ready(function(){
				var a24pCheckItem = $( '.a24pCheckItem' );
				a24pCheckItem.live('change', function() {
					if ( $( this ).is(":checked") ) {
						$( '.a24pCheckItem-' + $(this).attr('itemId') ).appendTo( ".checked" + $(this).attr('section') );
					} else {
						$( '.a24pCheckItem-' + $(this).attr('itemId') ).prependTo( ".unchecked" + $(this).attr('section') );
					}
				});
				a24pCheckItem.each( function(){
					if ( $( this ).is(":checked") ) {
						$( '.a24pCheckItem-' + $(this).attr('itemId') ).appendTo( ".checked" + $(this).attr('section') );
					} else {
						$( '.a24pCheckItem-' + $(this).attr('itemId') ).appendTo( ".unchecked" + $(this).attr('section') );
					}
				});
			});
		})(jQuery);

		$('.a24pSpaceCustomization').click(function() {
			var a24pSpaceCustomization = $('#a24pSpaceCustomization');
			if ( a24pSpaceCustomization.hasClass('closed') ) {
				a24pSpaceCustomization.removeClass('closed');
			} else {
				a24pSpaceCustomization.addClass('closed');
			}
		});

		// chips
		$('.spaceChips').tagsInput({
			'height':'140px',
			'width':'97%',
			'interactive':true,
			'defaultText':'new',
			'onAddTag':function(tag){
				$('.' + $(this).attr('id')).val($(this).val());
			},
			'onRemoveTag':function(tag){
				$('.' + $(this).attr('id')).val($(this).val());
			},
//			'minChars':2,
//			'maxChars':30,
//			'placeholderColor':'#777',
			'removeWithBackspace':true
		});

	})(jQuery);

	function a24pGetTemplate()
	{
		(function($) {
			var a24pTemplatePreviewInner = $('.a24pTemplatePreviewInner');
			var a24pLoader = $('.a24pLoader');

			a24pTemplatePreviewInner.slideUp(400);
			a24pLoader.fadeIn(400);
			setTimeout(function(){
				$.post(ajaxurl, {action:'a24p_preview_callback',a24p_template:$("#ADS24_LITE_template").val()}, function(result) {
					a24pTemplatePreviewInner.html(result).slideDown(400);
					a24pLoader.fadeOut(400);
				});
			}, 1100);
		})(jQuery);
	}

	function a24pGetUnselected(type, short, ajaxLimit)
	{
		(function($) {
			var a24pUnchecked = $('.unchecked' + short);
			var countUnchecked = $('.unchecked' + short + ' > .a24pProSpecificItem').length;
			var countChecked = $('.checked' + short + ' > .a24pProSpecificItem').length;
			var a24pLink = $('.a24pLink' + short);
			var a24pLoader = $('.a24pLoader' + short);
			var a24pCount = a24pUnchecked.attr( "count" );
			a24pLoader.fadeIn(400);
			setTimeout(function(){
				$.post(ajaxurl, {action:'a24p_unselected',type:type,space_id:<?php echo isset($_GET['space_id']) ? $_GET['space_id'] : 0 ?>,a24p_offset:countUnchecked,ajax_limit:ajaxLimit}, function(result) {
					a24pUnchecked.attr( "offset", countUnchecked).prepend(result);
					if ( countChecked + countUnchecked >= a24pCount ) {
						a24pLink.fadeOut(400);
					}
					a24pLoader.fadeOut(400);
				});
			}, 1100);
		})(jQuery);
	}
</script>
