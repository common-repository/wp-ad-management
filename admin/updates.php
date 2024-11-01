<h2><span class="dashicons dashicons-cloud"></span> Updates</h2>
<!--<h3>Updates</h3>-->
<?php //if ( get_option('ADS24_LITE_plugin_purchase_code') != NULL && get_option('ADS24_LITE_plugin_purchase_code') != '' ): ?>
<!--<p>Under construction..</p>-->
<?php// elseif ( get_option('ADS24_LITE_plugin_purchase_code') == 0 ): ?>
<!--<p>Please enter the Purchase Code in Settings.</p>-->
<?php// endif ?>
<!--<p>Under construction..</p>-->

<h3>ADS PRO</h3>
<p>Version <?php echo get_option('ADS24_LITE_plugin_version') ?></p>

<?php if ( get_option('ADS24_LITE_plugin_agency_api_version') != NULL ): ?>
<h3>Marketing Agency Add-on</h3>
<p>Version <?php echo get_option('ADS24_LITE_plugin_agency_api_version') ?></p>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" value="updateAdd-on" name="a24pProAction">
	<table class="a24pAdminTable form-table">
		<tbody class="a24pTbody">
		<tr>
			<th colspan="2">
				<h3><span class="dashicons dashicons-admin-plugins"></span> Add new / Upgrade Add-on</h3>
			</th>
		</tr>
		<tr>
			<th scope="row"><label for="ADS24_LITE_file_update">File</label></th>
			<td>
				<input type="file" id="ADS24_LITE_file_update" name="file_update">
				<p class="description"><strong>Note!</strong> Only .zip format allowed!</p>
			</td>
		</tr>
		</tbody>
	</table>
	<input class="a24p_inputs_required" name="inputs_required" type="hidden" value="">
	<p class="submit">
		<input type="submit" value="Upload" class="button button-primary" id="ADS24_LITE_submit" name="submit">
	</p>
</form>