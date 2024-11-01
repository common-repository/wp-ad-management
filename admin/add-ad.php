<?php
$model = new ADS24_LITE_Model();
$role = ((a24p_role() == 'admin') ? 'a' : 'u');
$decode_ids = $model->getUserCol(get_current_user_id());
$get_ids = json_decode($decode_ids['ad_ids']);
$get_free_ads = $model->getUserCol(get_current_user_id(), 'free_ads');

function getAdValue($val) {
	if (isset($_GET['ad_id'])) {
		return a24p_ad($_GET['ad_id'], $val);
	} else {
		if ( isset($_POST[$val]) || isset($_SESSION['a24p_ad_status']) ) {
			if ( isset($_SESSION['a24p_ad_status']) == 'ad_added' ) {
				$_SESSION['a24p_clear_form'] = 'ad_added';
				unset($_SESSION['a24p_ad_status']);
			}
			$status = (isset($_SESSION['a24p_clear_form']) ? $_SESSION['a24p_clear_form'] : '');
			if ( $status == 'ad_added' ) {
				return '';
			} else {
				return stripslashes($_POST[$val]);
			}
		} else {
			return '';
		}
	}
}
?>
<h2>
	<?php if ( isset($_GET['ad_id']) ): ?>
		<span class="dashicons dashicons-edit"></span> Edit <strong>Ad ID <?php echo $_GET['ad_id']; ?></strong> added to <strong>Space ID <?php echo getAdValue('space_id'); ?></strong> <small>(<strong><?php echo a24p_ad($_GET['ad_id'], 'ad_model') ?></strong> billing model)</small>
		<?php if ( $role == 'a' ): ?>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces<?php echo ((getAdValue('space_id')) ? '&space_id='.getAdValue('space_id') : null) ?>">back to <strong>adslots / ads list</strong></a></p>
		<?php endif; ?>
	<?php else: ?>
		<span class="dashicons dashicons-pressthis"></span> Add new Ad
		<?php if ( $role == 'a' ): ?>
			<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces">back to <strong>adslots / ads list</strong></a></p>
		<?php endif; ?>
	<?php endif; ?>
</h2>

<?php if (  isset($_GET['ad_id']) && a24p_ad($_GET['ad_id'], 'id') != NULL && $role == 'a' ||
			!isset($_GET['ad_id']) && $role == 'a' ||
			isset($_GET['ad_id']) && a24p_ad($_GET['ad_id'], 'id') != NULL && is_array($get_ids) && array_search($_GET['ad_id'], $get_ids) !== false && $role == 'u' ||
			!isset($_GET['ad_id']) && $get_free_ads['free_ads'] > 0 && $role == 'u' ):

	if ( $role == 'a' ) { // if admin
		$spaces = (($model->getSpaces('active')) ? $model->getSpaces('active') : NULL);
	} else { // if user
		$spaces = (($model->getSpaces('active', 'html')) ? $model->getSpaces('active', 'html') : NULL);
	}
	$count_ads = NULL;
	$space_verify = NULL;
	if (is_array($spaces))
	{
		foreach ( $spaces as $key => $space ) {
			if ( $role == 'a' ) {
				$count_ads = $model->countAds($space["id"]);
				if ( $model->countAds($space["id"]) < a24p_space($space["id"], 'max_items') ) {
					$space_verify .= (( $key > 0 ) ? ','.$space["id"] : $space["id"]);
				} else {
					$space_verify .= '';
				}
			} else {
				if ( $space['cpc_price'] == 0 && $space['cpm_price'] == 0 && $space['cpd_price'] == 0 ) {
					$space_verify .= '';
				} else {
					$count_ads = $model->countAds($space["id"]);
					if ( $model->countAds($space["id"]) < a24p_space($space["id"], 'max_items') ) {
						$space_verify .= (( $key > 0 ) ? ','.$space["id"] : $space["id"]);
					} else {
						$space_verify .= '';
					}
				}
			}
		}
	}
	$space_verify = (( $space_verify != '') ? explode(',', $space_verify) : FALSE );

	if ( $spaces && $space_verify && !isset($_GET['ad_id']) || $spaces && isset($_GET['ad_id']) && a24p_space(a24p_ad($_GET['ad_id'], 'space_id'), 'max_items') >= $model->countAds(a24p_ad($_GET['ad_id'], 'space_id')) ): ?>
		<form action="" method="post" enctype="multipart/form-data" class="a24pNewAd">
			<?php if ( isset($_GET['ad_id']) ): ?>
				<input type="hidden" value="updateAd" name="a24pProAction">
			<?php else: ?>
				<input type="hidden" value="addNewAd" name="a24pProAction">
			<?php endif; ?>
			<table class="a24pAdminTable form-table">
				<tbody class="a24pTbody">
					<tr>
						<th colspan="2">
							<?php if ( isset($_GET['ad_id']) ): ?>
								<h3><span class="dashicons dashicons-exerpt-view"></span> Edit Ad Content</h3>
							<?php else: ?>
								<h3><span class="dashicons dashicons-exerpt-view"></span> Create new Ad</h3>
							<?php endif; ?>
						</th>
					</tr>
					<?php if ( $role == 'a' && a24p_get_opt('admin_settings', 'ad_name') == 'yes' ): ?>
						<tr>
							<th scope="row"><label for="ADS24_LITE_ad_name">Ad Name (optional) <br>listed in the backend only</label></th>
							<td>
								<input id="ADS24_LITE_ad_name" name="ad_name" type="text" class="regular-text" value="<?php echo getAdValue('ad_name') ?>">
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<th scope="row"><label for="ADS24_LITE_buyer_email">E-mail</label></th>
						<td>
							<input id="ADS24_LITE_buyer_email" name="buyer_email" type="email" class="regular-text" maxlength="255" value="<?php echo getAdValue('buyer_email') ?>">
							<p class="description">E-mail address is need to generate statistics.</p>
						</td>
					</tr>
					<?php if ( !isset($_GET['ad_id']) ): ?>
					<tr>
						<th scope="row"><label for="ADS24_LITE_space_id">Choose Space</label></th>
						<td>
							<select id="ADS24_LITE_space_id" name="space_id" onchange="a24pGetBillingMethods()">

								<?php

								if ( $spaces != NULL ) {
									foreach ( $spaces as $space ) {
										if ( in_array($space['id'], $space_verify) ) {
											if ($role == 'a' || $role == 'u' && $space['template'] != 'html') {
												if ($model->countAds($space["id"]) < a24p_space($space["id"], 'max_items')) {
													echo '<option value="' . $space["id"] . '" ' . ((isset($_POST) && isset($_POST["space_id"]) && $_POST["space_id"] == $space["id"]) ? 'selected="selected"' : "") . '>' . $space["name"] . '</option>';
												} else {
													echo '<option value="" disabled>' . $space["name"] . ' (' . $model->countAds($space["id"]) . '/' . a24p_space($space["id"], 'max_items') . ')' . '</option>';
												}
											}
										}
									}
								}

								?>
							</select> <span class="a24pLoader" style="display:none;"></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>Billing model <br>(display limit)</label></th>
						<td>
							<h3 style="margin-top:0;">Choose Billing Model and Display Limit <span class="a24pLoader" style="display:none;"></span></h3>
							<div class="a24pGetBillingModels"></div>
						</td>
					</tr>
					<?php endif ?>
					<tr>
						<th scope="row">Live Preview</th>
						<td>
							<?php if ( isset($_GET['ad_id']) ): ?>
								<input id="ADS24_LITE_space_id" type="hidden" value="<?php echo getAdValue('space_id'); ?>">
								<input id="ADS24_LITE_ad_id" type="hidden" value="<?php echo $_GET['ad_id']; ?>">
							<?php endif ?>
							<h3 style="margin-top:0;">Ad Live Preview <span class="a24pLoader" style="display:none;"></span></h3>
							<div class="a24pTemplatePreview">
								<div class="a24pTemplatePreviewInner"></div>
							</div>
						</td>
					</tr>
					<tr class="a24p_title_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_title">Title <small>(<span class="ADS24_LITE_sign_title"><?php echo get_option('ADS24_LITE_plugin_max_title') ?></span>)</small></label></th>
						<td>
							<input id="ADS24_LITE_title" name="title" type="text" class="regular-text" maxlength="<?php echo get_option('ADS24_LITE_plugin_max_title') ?>" value="<?php echo getAdValue('title') ?>">
						</td>
					</tr>
					<tr class="a24p_desc_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_desc">Description <small>(<span class="ADS24_LITE_sign_desc"><?php echo get_option('ADS24_LITE_plugin_max_desc') ?></span>)</small></label></th>
						<td>
							<input id="ADS24_LITE_desc" name="description" type="text" class="regular-text" maxlength="<?php echo get_option('ADS24_LITE_plugin_max_desc') ?>" value="<?php echo getAdValue('description') ?>">
						</td>
					</tr>
					<tr class="a24p_button_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_button">Action Button <small>(<span class="ADS24_LITE_sign_button"><?php echo get_option('ADS24_LITE_plugin_max_button') ?></span>)</small></label></th>
						<td>
							<input id="ADS24_LITE_button" name="button" type="text" class="regular-text" maxlength="<?php echo get_option('ADS24_LITE_plugin_max_button') ?>" value="<?php echo getAdValue('button') ?>">
						</td>
					</tr>
					<tr class="a24p_url_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_url">URL <small>(<span class="ADS24_LITE_sign_url">255</span>)</small></label></th>
						<td>
							<input id="ADS24_LITE_url" name="url" type="url" class="regular-text" maxlength="255" value="<?php echo getAdValue('url') ?>">
							<p class="ADS24_LITE_html_desc description" style="display:none;"><strong>Note!</strong> You can use the URL field within clean HTML ads only (you can't use it with AdSense or other external JS codes).</p>
						</td>
					</tr>
					<tr class="a24p_img_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_img">Image</label></th>
						<td>
							<input type="file" id="ADS24_LITE_img" name="img" onchange="a24pPreviewThumb(this)"><br><br>
							- or -<br><br>
							<label for="ADS24_LITE_img_url">Full URL of Image</label><br>
							<input id="ADS24_LITE_img_url" name="img_url" type="url" onchange="a24pPreviewThumb(this.value)" class="regular-text" maxlength="1000" value="" placeholder="http://yoursite.com/image.jpg"><br><br>
							<p class="description"><?php echo get_option('ADS24_LITE_plugin_trans_form_left_thumb'); ?></p>
							<p class="description"><strong>Note!</strong> If you editing the ad and do not want to change the image, skip this field.</p><br>
						</td>
					</tr>
					<tr class="a24p_html_inputs_load" style="display: none">
						<th scope="row"><label for="ADS24_LITE_html">HTML</label></th>
						<td>
							<textarea id="ADS24_LITE_html" name="html" class="regular-text ltr" rows="14" cols="70"><?php echo getAdValue('html') ?></textarea>
						</td>
					</tr>
					<?php do_action( 'a24p-lite-add-input-ads'); ?>
					<tr>
						<th scope="row"><label for="ADS24_LITE_capping">Capping - Number of views per user / session</label></th>
						<td>
							<input id="ADS24_LITE_capping" name="capping" type="text" class="regular-text" maxlength="3" value="<?php echo getAdValue('capping') ?>">
						</td>
					</tr>
					<?php if ( $role == 'a' && a24p_get_opt('order_form', 'optional_field') == 'yes' ): ?>
						<tr>
							<th scope="row"><label for="ADS24_LITE_optional_field">Additional Information</label></th>
							<td>
								<input id="ADS24_LITE_optional_field" name="optional_field" type="text" class="regular-text" value="<?php echo getAdValue('optional_field') ?>">
							</td>
						</tr>
					<?php endif; ?>
					<?php if ( isset($_GET['ad_id']) && $role == 'a' ): ?>
					<tr>
						<th colspan="2">
								<h3><span class="dashicons dashicons-plus"></span> Increase / Decrease display limit</h3>
						</th>
					</tr>
					<tr>
						<?php $diffTime = '';
						if ( a24p_ad($_GET['ad_id'], 'ad_model') == 'cpc' ) {
							$model_type = 'clicks';
							$limit_value = ( a24p_ad($_GET['ad_id'], 'ad_limit') <= 0 ) ? 0 : a24p_ad($_GET['ad_id'], 'ad_limit');
						} elseif ( a24p_ad($_GET['ad_id'], 'ad_model') == 'cpm' ) {
							$model_type = 'views';
							$limit_value = ( a24p_ad($_GET['ad_id'], 'ad_limit') <= 0 ) ? 0 : a24p_ad($_GET['ad_id'], 'ad_limit');
						} else { // if ( a24p_ad($_GET['ad_id'], 'ad_model') == 'cpd' ) // IF CPD BILLING MODEL
							$time = time();
							$limit = a24p_ad($_GET['ad_id'], 'ad_limit');
							$diff = $limit - $time;
							$limit_value = ( $diff < 86400 /* 1 day in sec */ ) ? ( $diff > 0 ) ? 'less than 1' : '0' : number_format($diff / 24 / 60 / 60);
							$diffTime = date('d M Y (H:m:s)', time() + $diff);
							$model_type = ( $diff > 86400 || $diff == -0 ) ? 'days' : 'day';
						} ?>
						<th scope="row"><label>Currently display limit<br>(<?php echo $model_type ?> to finish)</label></th>
						<td>
							<input name="limit" type="text" class="regular-text" placeholder="<?php echo $limit_value ?>" disabled> <em><?php echo $model_type ?></em>
							<p class="description"><?php echo ( a24p_ad($_GET['ad_id'], 'ad_model') == 'cpd' ) ? $diffTime : ''; ?></p>
						</td>
					</tr>
					<tr>
						<th class="a24pLast" scope="row"><label for="increase_limit">Change display limit<br>(add / subtract <?php echo $model_type ?> to currently limit)</label></th>
						<td class="a24pLast">
							<input id="increase_limit" name="increase_limit" type="number" class="regular-text" value=""> <em><?php echo $model_type ?></em>
							<p class="description">Skip this field if you do not want increase limit display.</p>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<input class="a24p_inputs_required" name="inputs_required" type="hidden" value="">
			<p class="submit">
				<input type="submit" value="Save Ad" class="button button-primary" id="ADS24_LITE_submit" name="submit">
			</p>
		</form>
	<?php else: ?>

		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><strong>AdSlots are fully or doesn't exists!</strong> Go <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-space">here</a> to add new AdSlot.</p>
		</div>

	<?php endif; ?>

<?php else: ?>

	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><strong>Error!</strong> Ad doesn't exists or you can't manage this section!</p>
	</div>

<?php endif; ?>

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
		// - start - open page
		var a24pItemsWrap = $(".wrap");
		setTimeout(function(){
			a24pItemsWrap.fadeIn(400);
		}, 400);
		// - end - open page

		var inputTitle = $("#ADS24_LITE_title");
		var inputDesc = $("#ADS24_LITE_desc");
		var inputButton = $("#ADS24_LITE_button");
		var inputUrl = $("#ADS24_LITE_url");
		var inputHtml = $("#ADS24_LITE_html");
		inputTitle.keyup(function() { a24pPreviewInput("title"); });
		inputDesc.keyup(function() { a24pPreviewInput("desc"); });
		inputButton.keyup(function() { a24pPreviewInput("button"); });
		inputUrl.keyup(function() { a24pPreviewInput("url"); });
		inputHtml.keyup(function() { a24pPreviewInput("html"); });

		a24pTemplatePreview();
		var sid = $("#ADS24_LITE_space_id");
		sid.bind("change",function() {
			a24pGetBillingMethods();
			a24pTemplatePreview();
			$(".a24pUrlSpaceId").html($("#ADS24_LITE_space_id").val());
		});
		sid.trigger("change");
	})(jQuery);

	function a24pGetBillingMethods()
	{
		(function($) {
			var getBillingModels = $(".a24pGetBillingModels");
			var a24pLoader = $(".a24pLoader");

			getBillingModels.slideUp();
			a24pLoader.fadeIn(400);
			setTimeout(function(){
				$.post(ajaxurl, {action:"a24p_get_billing_models_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),ADS24_LITE_admin:1}, function(result) {

					getBillingModels.html(result).slideDown();
					a24pLoader.fadeOut(400);

				});
			}, 1100);
		})(jQuery);
	}

	function a24pTemplatePreview()
	{
		(function($) {
			var a24pTemplatePreviewInner = $(".a24pTemplatePreviewInner");
			var a24pLoader = $(".a24pLoader");

			a24pTemplatePreviewInner.slideUp(400);
			a24pLoader.fadeIn(400);
			setTimeout(function(){
				$.post(ajaxurl, {action:"a24p_preview_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),a24p_ad_id:$("#ADS24_LITE_ad_id").val()}, function(result) {

					a24pTemplatePreviewInner.html(result).slideDown(400);

					a24pGetRequiredInputs();
					var inputTitle = $("#ADS24_LITE_title");
					var inputDesc = $("#ADS24_LITE_desc");
					var inputButton = $("#ADS24_LITE_button");
					var inputUrl = $("#ADS24_LITE_url");
					var inputHtml = $("#ADS24_LITE_html");
					if ( inputTitle.val().length > 0 ) { a24pPreviewInput("title"); }
					if ( inputDesc.val().length > 0 ) { a24pPreviewInput("desc"); }
					if ( inputButton.val().length > 0 ) { a24pPreviewInput("button"); }
					if ( inputUrl.val().length > 0 ) { a24pPreviewInput("url"); }
					if ( inputHtml.val().length > 0 ) { a24pPreviewInput("html"); }

					a24pLoader.fadeOut(400);

				});
			}, 1100);
		})(jQuery);
	}

	function a24pGetRequiredInputs()
	{
		(function($) {
			$.post(ajaxurl, {action:"a24p_required_inputs_callback",a24p_space_id:$("#ADS24_LITE_space_id").val(),a24p_get_required_inputs:1}, function(result) {
				$(".a24p_inputs_required").val($.trim(result));

				if ( result.indexOf('title') !== -1 ) { // show if title required
					$(".a24p_title_inputs_load").fadeIn();
				} else {
					$(".a24p_title_inputs_load").fadeOut();
				}
				if ( result.indexOf('desc') !== -1 ) { // show if description required
					$(".a24p_desc_inputs_load").fadeIn();
				} else {
					$(".a24p_desc_inputs_load").fadeOut();
				}
				if ( result.indexOf('button') !== -1 ) { // show if button required
					$(".a24p_button_inputs_load").fadeIn();
				} else {
					$(".a24p_button_inputs_load").fadeOut();
				}
				if ( result.indexOf('url') !== -1 ) { // show if url required
					$(".a24p_url_inputs_load").fadeIn();
				} else {
					$(".a24p_url_inputs_load").fadeOut();
				}
				if ( result.indexOf('img') !== -1 ) { // show if img required
					$(".a24p_img_inputs_load").fadeIn();
				} else {
					$(".a24p_img_inputs_load").fadeOut();
				}
				if ( result.indexOf('html') !== -1 ) { // show if html required
					$(".a24p_html_inputs_load").fadeIn();
					// show html notice
					$('.ADS24_LITE_html_desc').fadeIn();
				} else {
					$(".a24p_html_inputs_load").fadeOut();
					// hide html notice
					$('.ADS24_LITE_html_desc').fadeOut();
				}
			});
		})(jQuery);
	}

	function a24pPreviewInput(inputName)
	{
		(function($){
			var input = $("#ADS24_LITE_" + inputName);
			var sign = $(".ADS24_LITE_sign_" + inputName);
			var limit = input.attr("maxLength");
			var a24pProContainerExample = $(".a24pProContainerExample");
			var exampleTitle = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_title"); ?>";
			var exampleDesc = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_desc"); ?>";
			var exampleButton = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_button"); ?>";
			var exampleUrl = "<?php echo get_option("ADS24_LITE_plugin_trans_form_left_eg_url"); ?>";
			var exampleHTML = "HTML Code Here";

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
				} else if ( inputName === "html" ) {
					<?php if ( get_option('ADS24_LITE_plugin_'.'html_preview') == 'no' || get_option('ADS24_LITE_plugin_'.'html_preview') == NULL ): ?>
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(input.val());
					<?php endif; ?>
				}
			} else {
				if ( inputName === "title" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleTitle);
				} else if ( inputName === "desc" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleDesc);
				} else if ( inputName === "button" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleButton);
				} else if ( inputName === "url" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html("http://" + exampleUrl.replace("http://","").replace("https://","").replace("www.","").split(/[/?#]/)[0]);
				} else if ( inputName === "html" ) {
					a24pProContainerExample.find(".a24pProItemInner__" + inputName).html(exampleHTML);
				}
			}
		})(jQuery);
	}

	function a24pPreviewThumb(input)
	{
		(function($) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$(".a24pProItemInner__img").css({"background-image" : "url(" + e.target.result + ")"});
				};
				reader.readAsDataURL(input.files[0]);
			} else {
				if ( input !== '' ) {
					$(".a24pProItemInner__img").css({"background-image" : "url(" + input + ")"});
				}
			}
		})(jQuery);
	}
</script>