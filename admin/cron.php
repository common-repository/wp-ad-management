
<div id="lite-upgrade" style="position: relative;">
<div id="lite-scheduling"></div>
<h2>
		<span class="dashicons dashicons-calendar-alt"></span> Ads Scheduling
<?php if ( isset($_GET['a24p-form']) ): ?>
	<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-cron">back to <strong>tasks list</strong></a></p>
<?php else: ?>
	<a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-cron&a24p-form=task-ad" class="add-new-h2">Set task for Ad</a> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-cron&a24p-form=task-space" class="add-new-h2">Set task for AdSlot</a>
<?php endif; ?>
</h2>

<?php
if ($model->taskClosed()) {
	echo '
		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><div class="a24pLoader"></div><strong>Task has been blocked.</strong></p>
		</div>';
}

if ( $ifAdForm || $ifSpaceForm ): ?>

	<?php if ( $ifAdForm && $get_ads || $ifSpaceForm && $get_spaces ): ?>

		<form action="" method="post" class="a24pNewStandardAd">
			<input type="hidden" value="task-<?php echo (($ifAdForm) ? 'ad' : 'space'); ?>">
			<table class="a24pAdminTable form-table">
				<tbody class="a24pTbody">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-clock"></span> Set task for <?php echo (($ifAdForm) ? null : 'AdSlot'); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="<?php echo (($ifAdForm) ? 'ad' : 'space'); ?>_id">Select <?php echo (($ifAdForm) ? 'Ad' : 'AdSlot'); ?> ID</label></th>
					<td>
						<select id="<?php echo (($ifAdForm) ? 'ad' : 'space'); ?>_id" name="<?php echo (($ifAdForm) ? 'ad' : 'space'); ?>_id">
							<?php foreach ((($ifAdForm) ? $get_ads : $get_spaces) as $entry):
								echo '<option value="'.esc_html( $entry['id'] ).'">' . esc_html( $entry['id'] ) . (($entry[(($ifAdForm) ? 'title' : 'name')] != '') ? ' - '.$entry[(($ifAdForm) ? 'title' : 'name')] : null) . ' ('.$entry['status'].')' . '</option>';
							endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cron_action">Select action</label></th>
					<td>
						<select id="cron_action" name="cron_action">
							<?php if ($ifAdForm): ?>
								<option value="active">change status to active</option>
								<option value="blocked">change status to blocked</option>
							<?php else: ?>
								<option value="active">change status to active</option>
								<option value="inactive">change status to inactive</option>
							<?php endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="a24pLast" scope="row">
						<label for="start_date">
							Start-up time<br><br>
							<span style="font-weight:normal;">current server time: <br><strong><?php echo date('Y-m-d H:i'); ?></strong></span>
						</label>
					</th>
					<td class="a24pLast">
						<input type="text" class="start_date" id="start_date" name="start_date" value="" placeholder="select date" style="width:100%;max-width:307px;"/>
						<br>
						<select id="start_date" name="hour" style="width:100%;max-width:150px;">
							<option value="">select hour</option>
							<?php for ( $i = 0; $i <= 23; $i++ ) {
								echo '<option value="'.(($i<=9) ? 0 : null).$i.'">' . (($i<=9) ? 0 : null) . $i . '</option>';
							} ?>
						</select>
						<select id="start_date" name="minutes" style="width:100%;max-width:150px;">
							<option value="">select minutes</option>
							<?php for ( $i = 0; $i <= 5; $i++ ) {
								echo '<option value="'.$i.'0">' . $i . '0</option>';
							} ?>
						</select>
						<p><strong>Note!</strong><br>Start-up time should be greater than the current.<br>The interval between tasks should be a minimum of 10 minutes</p>
					</td>
				</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" value="Save" class="button button-primary" id="ADS24_LITE_submit" name="submit">
			</p>
		</form>

	<?php else: ?>

		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><strong>Error!</strong> Active or <?php echo (($ifAdForm) ? 'blocked Ads' : 'inactive AdSlots'); ?> not exists!</p>
		</div>

	<?php endif; ?>

<?php else: ?>

	<h3>Pending Tasks</h3>
	<table class="wp-list-table widefat a24pListTable">
		<thead>
		<tr>
			<th class="manage-column"><strong>ID</strong></th>
			<th class="manage-column"><strong>Ad / AdSlot ID</strong></th>
			<th class="manage-column"><strong>Action type</strong></th>
			<th class="manage-column"><strong>Start-up time</strong></th>
			<th class="manage-column"><strong>When repeat?</strong></th>
			<th class="manage-column"><strong>Actions</strong></th>
		</tr>
		</thead>

		<tbody>
		<?php
		$tasksPagination = new AdsProPagination();
		if (count($getTasks) > 0 && $tasksPagination->getTasksPages() && $tasksPagination->getTasksPages() != 'not_found') {
			foreach ($tasksPagination->getTasksPages() as $key => $entry) {

				if ($key % 2) {
					$alternate = '';
				} else {
					$alternate = 'alternate';
				}
				?>

				<tr class="<?php echo $alternate; ?>">
					<td>
						<?php echo $entry['id']; ?>
					</td>
					<td>
						<?php echo '<strong>Ad '.(($entry['item_type'] == 'space') ? 'Space' : null).' ID:</strong> '.$entry['item_id']; ?>
					</td>
					<td>
						<?php echo 'change status to <strong>'.$entry['action_type'].'</strong>'; ?>
					</td>
					<td>
						<strong><?php echo date('Y-m-d', $entry['start_time']); ?></strong> <?php echo date('H:i', $entry['start_time']); ?>
					</td>
					<td>
						<?php if ( $entry['when_repeat'] == 0 ) {
							echo 'only <strong>once</strong>';
						} else {
							echo 'repeat <strong>every '.(($entry['when_repeat'] > 1) ? $entry['when_repeat'].' days' : 'day').'</strong>';
						} ?>
					</td>
					<td>
						<form action="" method="post">
							<input type="hidden" value="close-task" name="a24pProAction">
							<input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
								<span class="a24pProActionBtn a24pBlockBtn">
									<input type="submit" value="Close this task" id="submit" name="submit">
								</span>
						</form>
					</td>
				</tr>

			<?php }

			if ( count($getTasks) > 40 ): ?>
				<tr>
					<td colspan="6">
						<?php
						if($prev = $tasksPagination->getPrev()): ?>
							<a href="<?php echo admin_url('admin.php?page=a24p-lite-sub-menu-cron&pagination='.$prev); ?>">< Prev Page</a>
						<?php endif ?>
						<?php if($next = $tasksPagination->getNext('tasks')): ?>
							<a href="<?php echo admin_url('admin.php?page=a24p-lite-sub-menu-cron&pagination='.$next); ?>" style="float:right;">Next Page ></a>
						<?php endif ?>
					</td>
				</tr>
			<?php
			endif;

		} else {
			?>

			<tr>
				<td style="text-align: center" colspan="5">
					List empty.
				</td>
			</tr>

		<?php } ?>
		</tbody>
	</table>

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

		<?php if ($model->taskClosed()) { ?>
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

		$(document).ready(function() {
			$('.start_date').datepicker({
				dateFormat : 'yy-mm-dd',
				beforeShow: function(input, inst) {
					$('#ui-datepicker-div').addClass('a24pProCalendar');
				}
			});
		});
	})(jQuery);
</script>
<a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-pg-methods-btn" target="_blank" class="upgrade-scheduling-btn">Upgrade to Pro</a>
</div>