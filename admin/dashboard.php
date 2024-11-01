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

$model 							= new ADS24_LITE_Model();
$countSpaces 					= $model->countSpaces();
$countAds 						= $model->countAds();
$countEarnings 					= $model->countEarnings();
$getPendingAds 					= $model->getPendingAds('admin_dashboard');
$getLastAds 					= $model->getLastAds();
$getAffiliateWithdrawals 		= $model->getAffiliateWithdrawals('pending', a24p_role());
$getSites 						= $model->getSites(a24p_role(), 'pending');
$getWithdrawals 				= $model->getWithdrawals('pending', a24p_role());
?>
<h2>
	<i class="dashicons dashicons-businessman"></i> Ads24 Lite - Admin Panel
</h2>

<?php a24pProCheckUpdate(); ?>

<?php if ( get_option('ADS24_LITE_plugin_max_button') == '' || get_option('ADS24_LITE_plugin_max_button') == null ): ?>
	<div class="error">
		<h3>After upload new files you need to re-activate Ads Pro Plugin!</h3>
		<p>
			Go to Plugins <strong><a href="<?php echo admin_url(); ?>plugins.php">here</a></strong> to re-activate Ads Pro.
		</p>
	</div>
<?php endif; ?>

<div class="a24pDashboard">
	<div class="a24pDashboard-stats">
        <div class="a24pDashboard-stat">
			<div class="a24pDashboard-inner redBg">
				<span class="a24_dashboard_icon"><img src="<?php echo plugins_url( 'frontend/img/earning-icon.png', dirname(__FILE__) ) ; ?>" /></span>
				<span class="a24pDashboard-title">Total earnings</span>
				<strong class="a24pDashboard-amount"><?php echo ( $countEarnings ) ? $before.a24p_number_format($countEarnings).$after : $before.a24p_number_format(0).$after ?></strong>
			</div>
		</div>
        <div class="a24pDashboard-stat">
			<div class="a24pDashboard-inner violetBg">
				<span class="a24_dashboard_icon"><img src="<?php echo plugins_url( 'frontend/img/ctr-icon.png', dirname(__FILE__) ) ; ?>" /></span>
				<span class="a24pDashboard-title">CTR</span>
				<strong class="a24pDashboard-amount"><?php echo ( $views > 0 ) ? number_format(($clicks / $views) * 100, 2, '.', ' ') : 0; ?> <small>%</small></strong>
			</div>
		</div>
		<div class="a24pDashboard-stat">
			<div class="a24pDashboard-inner greenBg">
				<span class="a24_dashboard_icon"><img src="<?php echo plugins_url( 'frontend/img/ad-slot-icon.png', dirname(__FILE__) ) ; ?>" /></span>
				<span class="a24pDashboard-title">AdSlots / Ads</span>
				<strong class="a24pDashboard-amount"><?php echo ( $countSpaces ) ? $countSpaces : 0 ?> / <?php echo ( $countAds ) ? $countAds : 0 ?> </strong>
			</div>
		</div>
		<div class="a24pDashboard-stat">
			<div class="a24pDashboard-inner blueBg">
				<span class="a24_dashboard_icon"><img src="<?php echo plugins_url( 'frontend/img/click-icon.png', dirname(__FILE__) ) ; ?>" /></span>
				<span class="a24pDashboard-title">Clicks</span>
				<strong class="a24pDashboard-amount"><?php $clicks = get_option('ADS24_LITE_plugin_dashboard_clicks'); echo number_format($clicks, 0, '.', ' ') ?> <small>clicks</small></strong>
			</div>
		</div>
		<div class="a24pDashboard-stat">
			<div class="a24pDashboard-inner yellowBg">
				<span class="a24_dashboard_icon"><img src="<?php echo plugins_url( 'frontend/img/impression-icon.png', dirname(__FILE__) ) ; ?>" /></span>
				<span class="a24pDashboard-title">Impressions</span>
				<strong class="a24pDashboard-amount"><?php $views = get_option('ADS24_LITE_plugin_dashboard_views'); echo number_format($views, 0, '.', ' ') ?> <small>views</small></strong>
			</div>
		</div>
	</div>
</div>

<?php  $model->getAdminAction();
if ( $model->validationAccept() ) {
	echo '
	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><div class="a24pLoader"></div><strong>Ad has been accepted.</strong></p>
	</div>';
} elseif ( $model->validationBlocked() ) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad blocked.</strong></p>
		</div>';
} elseif ($model->validationPaid()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad marked as paid.</strong></p>
		</div>';
} elseif ($model->validationWithdrawalPaid()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Agency Withdrawal marked as paid.</strong></p>
		</div>';
} elseif ($model->validationWithdrawalRejected()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Agency Withdrawal has been rejected.</strong></p>
		</div>';
} elseif ($model->affiliateWithdrawalPaid()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Affiliate Withdrawal marked as paid.</strong></p>
		</div>';
} elseif ($model->affiliateWithdrawalRejected()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Affiliate Withdrawal has been rejected.</strong></p>
		</div>';
} ?>

<h3>Pending Ads</h3>
<table class="wp-list-table widefat a24pListTable">
	<thead>
	<tr>
		<th></th>
		<th style="" class="manage-column post-title page-title column-title">Ad Content</th>
		<th style="" class="manage-column">Buyer</th>
		<th style="" class="manage-column">Stats</th>
		<th style="" class="manage-column">Ad Display Limit</th>
		<th style="" class="manage-column">Order Details</th>
	</tr>
	</thead>

	<tbody>
	<?php
	if (count($getPendingAds) > 0) {
		foreach ($getPendingAds as $key => $entry) {

			if ($key % 2) {
				$alternate = '';
			} else {
				$alternate = 'alternate';
			}
			?>

			<tr class="<?php echo $alternate; ?>">
				<td class="a24pAdminImg">
					<img class="a24pAdminThumb"
						 src="<?php echo ($entry['img'] != '') ? a24p_upload_url(null, $entry['img']) : plugins_url('/ads24-lite-plugin/frontend/img/example.png'); ?>">
				</td>
				<td class="post-title page-title column-title">
					<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
					<?php echo ($entry['description'] != '') ? $entry['description'] : ''; ?>
					<?php echo ($entry['html'] != '') ? 'HTML' : ''; ?>
					<div class="row-actions">
						<form action="" method="post">
							<input type="hidden" value="accept" name="a24pProAction">
							<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pPaidBtn"><input type="submit" value="Accept this Ad"
																				id="submitAccept" name="submit"></span>
						</form>						
						<?php if ($entry['paid'] == 0 || $entry['paid'] == NULL): ?>
							<form action="" method="post">
								<input type="hidden" value="paid" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
									<span class="a24pProActionBtn a24pPaidBtn"><input type="submit" value="Mark as paid"
																					id="submitPaid"
																					name="submit"></span>
							</form>
						<?php endif; ?>
						<form action="" method="post">
							<input type="hidden" value="block" name="a24pProAction">
							<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn"><input type="submit" value="Block"
																				 id="submitBlock" name="submit"></span>
						</form>
							<span class="a24pPaidBtn">
								<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-ad&ad_id=<?php echo $entry['id']; ?>">
									Edit
								</a>
							</span>
					</div>
				</td>
				<td>
					<?php echo $entry['buyer_email']; ?>
				</td>
				<td>
					<?php
					$views = a24p_counter($entry['id'], 'view');
					$clicks = a24p_counter($entry['id'], 'click'); ?>
					Views <strong><?php echo ($views != NULL) ? $views : 0; ?></strong><br>
					Clicks <strong><?php echo ($clicks != NULL) ? $clicks : 0; ?></strong><br>
					<?php if ($views != NULL && $clicks != NULL): ?>
						CTR
						<strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '') . '%'; ?></strong>
						<br>
					<?php endif; ?>
					<a target="_blank href="<?php echo get_option('ADS24_LITE_plugin_ordering_form_url') . ((strpos(get_option('ADS24_LITE_plugin_ordering_form_url'), '?') == TRUE) ? '&' : '?') ?>ADS24_LITE_stats=1&ADS24_LITE_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&ADS24_LITE_id=<?php echo $entry['id']; ?>">
						full statistics
					</a>
				</td>
				<td>
					<?php
					if ($entry['ad_model'] == 'cpd') {
						$time = time();
						$limit = $entry['ad_limit'];
						$diff = $limit - $time;
						$limit_value = ($diff < 86400 /* 1 day in sec */) ? ($diff > 0) ? 'less than 1 day' : '0 days' : number_format($diff / 24 / 60 / 60) . ' days';
						$diffTime = date('d M Y (H:m:s)', time() + $diff);
					} else {
						$limit_value = ($entry['ad_model'] == 'cpc') ? $entry['ad_limit'] . ' clicks' : $entry['ad_limit'] . ' views';
						$diffTime = '';
					}
					?>
					<strong><?php echo $limit_value; ?></strong><br>
					<?php echo ($entry['ad_model'] == 'cpd') ? $diffTime : ''; ?>
				</td>
				<td>
					Space ID <strong><?php echo $entry['space_id']; ?></strong><br>
					Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
					Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
					Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
					<?php if ($entry['paid'] == 1): ?>
						<span class="a24pColorGreen">Paid</span>
					<?php elseif ($entry['paid'] == 2): ?>
						<span class="a24pColorGreen">Added via Admin Panel</span>
					<?php else: ?>
						<span class="a24pColorRed">Not paid</span>
					<?php endif; ?><br>
					<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
						<span class="dashicons dashicons-clock"></span> scheduled status change
					<?php endif; ?>
				</td>
			</tr>
			<?php
		}
	} else {
		?>

		<tr>
			<td style="text-align: center" colspan="7">
				List empty.
			</td>
		</tr>

	<?php } ?>
	</tbody>
</table>

<h3>Last Sold Ads</h3>
<table class="wp-list-table widefat a24pListTable">
	<thead>
	<tr>
		<th></th>
		<th style="" class="manage-column post-title page-title column-title">Ad Content</th>
		<th style="" class="manage-column">Buyer</th>
		<th style="" class="manage-column">Stats</th>
		<th style="" class="manage-column">Ad Display Limit</th>
		<th style="" class="manage-column">Order Details</th>
	</tr>
	</thead>

	<tbody>
	<?php
	if (count($getLastAds) > 0) {
		foreach ($getLastAds as $key => $entry) {

			if ($key % 2) {
				$alternate = '';
			} else {
				$alternate = 'alternate';
			}
			?>

			<tr class="<?php echo $alternate; ?>">
				<td class="a24pAdminImg">
					<img class="a24pAdminThumb" src="<?php echo ( $entry['img'] != '' ) ? a24p_upload_url(null, $entry['img']) : plugins_url('/ads24-lite-plugin/frontend/img/example.png'); ?>">
				</td>
				<td class="post-title page-title column-title">
					<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
					<?php echo ( $entry['description'] != '' ) ? $entry['description'] : ''; ?>
					<?php echo ( $entry['html'] != '' ) ? 'HTML' : '' ; ?>
				</td>
				<td>
					<?php echo $entry['buyer_email']; ?>
				</td>
				<td>
					<?php
					$views = a24p_counter($entry['id'], 'view');
					$clicks = a24p_counter($entry['id'], 'click'); ?>
					Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
					Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
					<?php if ( $views != NULL && $clicks != NULL ): ?>
						CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
					<?php endif; ?>
					<a target="_blank" href="<?php echo get_option('ADS24_LITE_plugin_ordering_form_url') . (( strpos(get_option('ADS24_LITE_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>ADS24_LITE_stats=1&ADS24_LITE_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&ADS24_LITE_id=<?php echo $entry['id']; ?>">
						full statistics
					</a>
				</td>
				<td>
					<?php
					if ( $entry['ad_model'] == 'cpd' ) {
						$time = time();
						$limit = $entry['ad_limit'];
						$diff = $limit - $time;
						$limit_value = ( $diff < 86400 /* 1 day in sec */ ) ? ( $diff > 0 ) ? 'less than 1 day' : '0 days' : number_format($diff / 24 / 60 / 60).' days';
						$diffTime = date('d M Y (H:m:s)', time() + $diff);
					} else {
						$limit_value = ($entry['ad_model'] == 'cpc') ? $entry['ad_limit'].' clicks' : $entry['ad_limit'].' views';
						$diffTime = '';
					}
					?>
					<strong><?php echo $limit_value; ?></strong><br>
					<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime : ''; ?>
				</td>
				<td>
					Space ID <strong><?php echo $entry['space_id']; ?></strong><br>
					Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
					Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
					Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
					<?php if ( $entry['paid'] == 1 ): ?>
						<span class="a24pColorGreen">Paid</span>
					<?php elseif ( $entry['paid'] == 2 ): ?>
						<span class="a24pColorGreen">Added via Admin Panel</span>
					<?php else: ?>
						<span class="a24pColorRed">Not paid</span>
					<?php endif; ?>
				</td>
			</tr>

		<?php
		}
	} else {
		?>

		<tr>
			<td style="text-align: center" colspan="7">
				List empty.
			</td>
		</tr>

	<?php } ?>
	</tbody>
</table>


<script>
	(function($){
		// - start - open page
		var a24pItemsWrap = $('.wrap');
		a24pItemsWrap.hide();

		setTimeout(function(){
			a24pItemsWrap.fadeIn(400);
		}, 400);
		// - end - open page

		<?php if ( $model->validationAccept() or $model->validationBlocked() or $model->validationPaid() or $model->validationWithdrawalPaid() or $model->validationWithdrawalRejected() or $model->affiliateWithdrawalPaid() or $model->affiliateWithdrawalRejected() ) { ?>
		var a24pValidationAlert = $('#setting-error-settings_updated');
		a24pValidationAlert.fadeIn(100);
		setTimeout(function(){
			a24pValidationAlert.fadeOut(100);
			a24pItemsWrap.fadeOut(100);
		}, 2000);
		setTimeout(function(){
			window.location=document.location.href;
		}, 2000);
		<?php } ?>
	})(jQuery);
</script>