<?php
if (isset($_GET['ADS24_LITE_action'])) {
	$getParam = $_GET['ADS24_LITE_action'];
} else {
	$getParam = NULL;
}

//echo get_num_queries().'queries in '.timer_stop(1).' seconds.';
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

// active order by column
function get_order_ads($sid, $column)
{
	if ( isset($sid) && a24p_space($sid, 'order_ads') == $column ) {
		return 'a24pOrderActive';
	} else {
		return null;
	}
}

$model = new ADS24_LITE_Model();
$get_spaces = $model->getSpaces('both');
$first_space = $get_spaces[0]['id'];
if ( isset($_GET['space_id']) && $_GET['space_id'] != NULL && $_GET['space_id'] != '' ) {
	$space_id = $_GET['space_id'];
} elseif ( $get_spaces != NULL ) {
	$space_id = $first_space;
} else {
	$space_id = 0;
}

$getActiveAds = $model->getActiveAds($space_id, a24p_space($space_id, 'max_items'), 'admin');
$getPendingAds = $model->getPendingAds('pending_ads', $space_id);
$getNotPaidAds = $model->getNotPaidAds($space_id);
$getBlockedAds = $model->getBlockedAds($space_id);
$getArchiveAds = $model->getArchiveAds($space_id);
?>

<div class="a24pActionNotice a24pSortableNotice" style="display:none">
	Changes has been saved.
</div>

<h2>
	<span class="dashicons dashicons-welcome-widgets-menus"></span> Manage AdSlots and Ads
</h2>

<?php if ( isset($_GET['space_id']) && a24p_space($_GET['space_id'], 'id') != NULL || !isset($_GET['space_id']) ): ?>

	<?php if ( isset($get_spaces) ): ?>

	<h2 class="nav-tab-wrapper">
		<?php if ( a24p_get_opt('admin_settings', 'selection') == 'select' ): ?>
			<label for="ADS24_LITE_space_select">Select Space</label>
			<select id="ADS24_LITE_space_select" name="space_select">
				<?php foreach ( $get_spaces as $space ): ?>
					<option value="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space['id']; ?>" <?php if ( isset($space) && $first_space == $space['id'] && isset($_GET['space_id']) && $_GET['space_id'] == NULL OR isset($_GET['space_id']) && $_GET['space_id'] == $space['id'] ) { echo 'selected="selected"'; } else { echo ''; } ?>>
						<?php echo $space['name']; ?> <?php echo ($space['status'] == 'active') ? '<small>(<span class="a24pGreen">active</span>)</small>' : '<small>(<span class="a24pRed">inactive</span>)</small>' ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php else: ?>
			<?php foreach ( $get_spaces as $space ): ?>
				<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space['id']; ?>"
				   class="nav-tab <?php if ( isset($space) && $first_space == $space['id'] && isset($_GET['space_id']) && $_GET['space_id'] == NULL OR isset($_GET['space_id']) && $_GET['space_id'] == $space['id'] ) { echo 'nav-tab-active'; } else { echo ''; } ?>">
					<?php echo $space['name']; ?> <?php echo ($space['status'] == 'active') ? '<small>(<span class="a24pGreen">active</span>)</small>' : '<small>(<span class="a24pRed">inactive</span>)</small>' ?>
				</a>
			<?php endforeach; ?>
		<?php endif; ?>
		<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-space" class="nav-tab" style="float: right;margin-top: 0;"><span class="a24pGreen">+</span> Add New AdSlot</a>
	</h2>

	<div class="a24pSpaceFilter wp-filter">
		<ul class="filter-links">
			<li class="a24pSpaceShortCode">
				<?php if( $space_id && a24p_space($space_id, 'status') == 'removed' ) {
					?><span style="display: inline-block;padding: 8px 0;"><strong>Note!</strong> This space was removed and you cannot use it!</span><?php
				} else {
					?>Use this AdSlot by shortcode <input class="a24pSpaceShortCodeInner" type="text" value="[ADS24_LITE_adslot id=<?php echo $space_id; ?>]" placeholder=""><?php
				} ?>
			</li>
			<?php if ( $get_spaces && a24p_space($space_id, 'status') != 'removed'): ?>
				<li class="addNewSpace">
					<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-space&space_id=<?php echo $space_id; ?>" class="current currentBlue">Edit AdSlot</a>
					<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-ad" class="current currentGreen">Add new Ad</a>
					<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space_id; ?>&remove_action=1" class="current currentRed">Remove AdSlot</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>

	<?php $model->getAdminAction();
	if ($model->validationBlocked()) {
		echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad has been blocked.</strong></p>
		</div>';
	} elseif ($model->validationUnblocked()) {
		echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad has been unblocked.</strong></p>
		</div>';
	} elseif ($model->validationRemoved()) {
		echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad has been removed.</strong></p>
		</div>';
	} elseif ($model->validationPaid()) {
		echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Ad has been marked as paid.</strong></p>
		</div>';
	} ?>

	<?php
		if( isset($_GET['remove_action']) && !isset($_GET['remove_confirm']) ) {
			echo '
			<div class="updated settings-error" id="setting-error-settings_updated">
				<p><strong>Confirm remove action!</strong> Yes, I <a href="'.admin_url().'admin.php?page=a24p-lite-sub-menu-spaces&space_id='.$_GET['space_id'].'&remove_action=1&remove_confirm=1">want</a> to delete Space ID <strong>'.$_GET['space_id'].'</strong>.</p>
			</div>';
		}
	?>

	<h3 style="margin-bottom: 10px;">Active Ads (<?php echo count($getActiveAds); ?>)</h3>
		<div style="margin:10px 0 20px;">order by
			<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=id" class="a24pOrderBy <?php echo get_order_ads($space_id, 'id'); ?>"><span class="dashicons dashicons-editor-ol"></span> id</a>
			<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=ad_limit" class="a24pOrderBy <?php echo get_order_ads($space_id, 'ad_limit'); ?>"><span class="dashicons dashicons-clock"></span> display limit</a>
			<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=priority" class="a24pOrderBy <?php echo get_order_ads($space_id, 'priority'); ?>"><img src="<?php echo plugins_url('/ads24-lite-plugin/frontend/img/icon-drag-16.png'); ?>" /> priority</a>
			<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=cost" class="a24pOrderBy <?php echo get_order_ads($space_id, 'cost'); ?>"><span class="dashicons dashicons-vault"></span> cost</a>
		</div>
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

		<tbody id="a24pSortable" class="<?php echo ( (get_order_ads($space_id, 'priority')) ? 'a24pSortableOn' : null ); ?>">
		<?php
		if (count($getActiveAds) > 0) {
			foreach ($getActiveAds as $key => $entry) {

				if ($key % 2) {
					$alternate = '';
				} else {
					$alternate = 'alternate';
				}
				?>

				<tr class="<?php echo $alternate; ?>" id="<?php echo $entry['id']; ?>">
					<td class="a24pAdminImg">
						<img class="a24pAdminThumb" src="<?php echo ( $entry['img'] != '' ) ? a24p_upload_url(null, $entry['img']) : plugins_url('/ads24-lite-plugin/frontend/img/example.png'); ?>">
					</td>
					<td class="post-title page-title column-title">
						<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="a24p_ad_name">'.$entry['ad_name'].'</span>' : null; ?>
						<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'] : ''; ?>
						<?php echo ( $entry['html'] != '' ) ? 'HTML' : '' ; ?>
						<div class="row-actions">
							<form action="" method="post">
								<input type="hidden" value="block" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn">
									<input type="submit" value="Block" id="submit" name="submit">
								</span>
							</form>
							|
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
						$limit_value = apply_filters( "a24p-lite-limitValue", $limit_value, $entry);
						?>
						<strong><?php echo $limit_value; ?></strong><br>
						<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime : ''; ?>
					</td>
					<td>
						Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
						Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="a24pColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="a24pColorGreen">Added via Admin Panel</span>
						<?php else: ?>
							<span class="a24pColorRed">Not paid</span>
						<?php endif; ?><br>
						<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
							<span class="dashicons dashicons-clock"></span> scheduled status change
						<?php endif; ?>
						<?php echo ( $entry['optional_field'] != '' ) ? '<br>'.$entry['optional_field'] : '' ; ?>
					</td>
				</tr>

			<?php }
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
						<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="a24p_ad_name">'.$entry['ad_name'].'</span>' : null; ?>
						<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
						<?php echo ($entry['description'] != '') ? $entry['description'] : ''; ?>
						<?php echo ($entry['html'] != '') ? 'HTML' : ''; ?>
						<div class="row-actions">
							<form action="" method="post">
								<input type="hidden" value="block" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn"><input type="submit" value="Block" id="submitBlock" name="submit"></span>
							</form>
							|
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
						<a target="_blank"
						   href="<?php echo get_option('ADS24_LITE_plugin_ordering_form_url') . ((strpos(get_option('ADS24_LITE_plugin_ordering_form_url'), '?') == TRUE) ? '&' : '?') ?>ADS24_LITE_stats=1&ADS24_LITE_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&ADS24_LITE_id=<?php echo $entry['id']; ?>">
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
						<?php endif ?><br>
						<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
							<span class="dashicons dashicons-clock"></span> scheduled status change
						<?php endif; ?>
						<?php echo ( $entry['optional_field'] != '' ) ? '<br>'.$entry['optional_field'] : '' ; ?>
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

	<h3>Not Paid Ads</h3>
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
		if (count($getNotPaidAds) > 0) {
			foreach ($getNotPaidAds as $key => $entry) {

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
						<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="a24p_ad_name">'.$entry['ad_name'].'</span>' : null; ?>
						<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'] : ''; ?>
						<?php echo ( $entry['html'] != '' ) ? 'HTML' : '' ; ?>
						<div class="row-actions">
							<form action="" method="post">
								<input type="hidden" value="paid" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pPaidBtn"><input type="submit" value="Mark as paid" id="submitPaid" name="submit"></span>
							</form>
							|
							<form action="" method="post">
								<input type="hidden" value="block" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn"><input type="submit" value="Block" id="submitBlock" name="submit"></span>
							</form>
							|
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
						Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
						Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="a24pColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="a24pColorGreen">Added via Admin Panel</span>
						<?php else: ?>
							<span class="a24pColorRed">Not paid</span>
						<?php endif; ?><br>
						<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
							<span class="dashicons dashicons-clock"></span> scheduled status change
						<?php endif; ?>
						<?php echo ( $entry['optional_field'] != '' ) ? '<br>'.$entry['optional_field'] : '' ; ?>
					</td>
				</tr>

			<?php }
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

	<h3>Blocked Ads</h3>
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
		if (count($getBlockedAds) > 0) {
			foreach ($getBlockedAds as $key => $entry) {

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
						<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="a24p_ad_name">'.$entry['ad_name'].'</span>' : null; ?>
						<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'] : ''; ?>
						<?php echo ( $entry['html'] != '' ) ? 'HTML' : '' ; ?>
						<div class="row-actions">
							<form action="" method="post">
								<input type="hidden" value="unblock" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pPaidBtn"><input type="submit" value="Unblock" id="submitBlock" name="submit"></span>
							</form>
							|
							<span class="a24pPaidBtn">
								<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-ad&ad_id=<?php echo $entry['id']; ?>">
									Edit
								</a>
							</span>
							|
							<form action="" method="post">
								<input type="hidden" value="remove" name="a24pProAction">
								<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn"><input type="submit" value="Remove" id="submitBlock" name="submit"></span>
							</form>
						</div>
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
						Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
						Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="a24pColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="a24pColorGreen">Added via Admin Panel</span>
						<?php else: ?>
							<span class="a24pColorRed">Not paid</span>
						<?php endif; ?><br>
						<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
							<span class="dashicons dashicons-clock"></span> scheduled status change
						<?php endif; ?>
						<?php echo ( $entry['optional_field'] != '' ) ? '<br>'.$entry['optional_field'] : '' ; ?>
					</td>
				</tr>

			<?php }
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

	<h3>Archived Ads</h3>
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
		if (count($getArchiveAds) > 0) {
			foreach ($getArchiveAds as $key => $entry) {

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
						<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="a24p_ad_name">'.$entry['ad_name'].'</span>' : null; ?>
						<strong><a href="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?></a></strong>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'] : ''; ?>
						<?php echo ( $entry['html'] != '' ) ? 'HTML' : '' ; ?>
						<div class="row-actions">
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
						Ad ID / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
						Cost <strong><?php echo $before . a24p_number_format($entry['cost']) . $after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="a24pColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="a24pColorGreen">Added via Admin Panel</span>
						<?php else: ?>
							<span class="a24pColorRed">Not paid</span>
						<?php endif; ?><br>
						<?php if ( $model->getPendingTask($entry['id'], 'ad') ): ?>
							<span class="dashicons dashicons-clock"></span> scheduled status change
						<?php endif; ?>
						<?php echo ( $entry['optional_field'] != '' ) ? '<br>'.$entry['optional_field'] : '' ; ?>
					</td>
				</tr>

			<?php }
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

	<?php elseif ( $space_id == 0 ): ?>

		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><strong>Spaces not exists!</strong> Go <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-add-new-space">here</a> to add first space.</p>
		</div>

	<?php else: ?>

		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><strong>Error!</strong> Space not exists! Go <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces">back</a>.</p>
		</div>

	<?php endif; ?>

<?php else: ?>

	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><strong>Error!</strong> Space not exists! Go <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-spaces">back</a>.</p>
	</div>

<?php endif; ?>

<script>
	(function($) {
		// - start - open page
		var a24pItemsWrap = $('.wrap');
		a24pItemsWrap.hide();

		setTimeout(function(){
			a24pItemsWrap.fadeIn(400);
		}, 400);
		// - end - open page

		<?php if ( get_order_ads($space_id, 'priority') ): ?>
		var sortList = $('#a24pSortable');
		sortList.sortable({
			stop : function(event, ui){
				var getOrder = $(this).sortable('toArray');
				$.post(ajaxurl, {action:'a24p_sortable_callback',a24p_order:getOrder}, function(result) {
					var a24pSortableNotice = $('.a24pSortableNotice');
					a24pSortableNotice.fadeIn();
					setTimeout(function(){
						a24pSortableNotice.fadeOut();
					}, 2000);
				});
			}
		});
		sortList.disableSelection();
		<?php endif; ?>

		<?php if ($model->validationBlocked() or $model->validationUnblocked() or $model->validationPaid() or $model->validationRemoved()) { ?>
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

		<?php if ( a24p_get_opt('admin_settings', 'selection') == 'select' ): ?>
			$('#ADS24_LITE_space_select').change(function(){ // select redirection
				window.location.href = $(this).val();
			});
		<?php endif; ?>
	})(jQuery);
</script>