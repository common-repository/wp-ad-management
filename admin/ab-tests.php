<div id="lite-upgrade" style="position: relative;">
<div id="lite-ab-test"></div>
<h2>
	<span class="dashicons dashicons-chart-line"></span> A/B Tests
	<?php if ( $ifIssetForm ): ?>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=a24p-lite-sub-menu-ab-tests">back to the <strong>form</strong></a></p>
	<?php endif; ?>
</h2>
<?php if ( $ifIssetForm ):
	$viewsA 	= ( a24p_counter($get_ad_A, 'view') != NULL ) ? a24p_counter($get_ad_A, 'view') : 0;
	$viewsB 	= ( a24p_counter($get_ad_B, 'view') != NULL ) ? a24p_counter($get_ad_B, 'view') : 0;

	$clicksA 	= ( a24p_counter($get_ad_A, 'click') != NULL ) ? a24p_counter($get_ad_A, 'click') : 0;
	$clicksB 	= ( a24p_counter($get_ad_B, 'click') != NULL ) ? a24p_counter($get_ad_B, 'click') : 0;

	$trafficA 	= ( $viewsA > 0 || $viewsB > 0 ? number_format($viewsA / ($viewsA + $viewsB) * 100, 2, '.', '') : 0);
	$trafficB 	= ( $viewsA > 0 || $viewsB > 0 ? number_format($viewsB / ($viewsA + $viewsB) * 100, 2, '.', '') : 0);

	$ctrA		= ($viewsA > 0 ? number_format(($clicksA / $viewsA) * 100, 2, '.', '') : number_format(0, 2, '.', ''));
	$ctrB		= ($viewsB > 0 ? number_format(($clicksB / $viewsB) * 100, 2, '.', '') : number_format(0, 2, '.', ''));
	?>

	<div class="a24pCompareContainer">

		<div class="a24pCompare a24pCompareA <?php echo ( $ctrA >= $ctrB ? 'a24pCompareWinner' : null); ?>">

			<div class="a24pCompareSignature">A</div>

			<div class="a24pCompareAdId">Ad ID: <strong><?php echo $get_ad_A; ?></strong></div>
			<div class="a24pCompareTemplate">Template: <strong><?php echo a24p_space(a24p_ad($get_ad_A, 'space_id'), 'template'); ?></strong></div>
			<div class="a24pCompareWeight">Traffic Weight: <strong><?php echo $trafficA.'%'; ?></strong></div>

			<div class="a24pCompareCTR"><div class="a24pCompareCTRInner"><strong><?php echo $ctrA.'%'; ?></strong><br>CTR</div></div>

			<div class="a24pCompareViews"><span><?php echo $viewsA; ?></span> Views</div>
			<div class="a24pCompareClicks"><span><?php echo $clicksA; ?></span> Clicks</div>

		</div>

		<div class="a24pCompare a24pCompareB <?php echo ( $ctrA <= $ctrB ? 'a24pCompareWinner' : null); ?>">

			<div class="a24pCompareSignature">B</div>

			<div class="a24pCompareAdId">Ad ID: <strong><?php echo $get_ad_B; ?></strong></div>
			<div class="a24pCompareTemplate">Template: <strong><?php echo a24p_space(a24p_ad($get_ad_B, 'space_id'), 'template'); ?></strong></div>
			<div class="a24pCompareWeight">Traffic Weight: <strong><?php echo $trafficB.'%'; ?></strong></div>

			<div class="a24pCompareCTR"><div class="a24pCompareCTRInner"><strong><?php echo $ctrB.'%'; ?></strong><br>CTR</div></div>

			<div class="a24pCompareViews"><span><?php echo $viewsB; ?></span> Views</div>
			<div class="a24pCompareClicks"><span><?php echo $clicksB; ?></span> Clicks</div>

		</div>

	</div>

<?php else: ?>

	<form action="" method="post" class="a24pNewStandardAd">
		
		<table class="a24pAdminTable form-table">
			<tbody class="a24pTbody">
			<tr>
				<th colspan="2">
					<h3><span class="dashicons dashicons-image-flip-horizontal"></span> Compare 2 different Ads</h3>
				</th>
			</tr>
			<tr>
				<th scope="row"><label for="ad_a_id">Select Ad A</label></th>
				<td>
					<select id="ad_a_id" >
						<?php if (is_array($get_ads)) {
							foreach ($get_ads as $entry):
								echo '<option value="'.esc_html( $entry['id'] ).'">' . esc_html( $entry['id'] ) . (($entry['title'] != '') ? ' - '.$entry['title'] : null) . '</option>';
							endforeach;
						} ?>
					</select>
				</td>
			</tr>
			<tr>
				<th class="a24pLast" scope="row"><label for="ad_b_id">Select Ad B</label></th>
				<td class="a24pLast">
					<select id="ad_b_id">
						<?php if (is_array($get_ads)) {
							foreach ($get_ads as $entry):
								echo '<option value="'.esc_html( $entry['id'] ).'">' . esc_html( $entry['id'] ) . (($entry['title'] != '') ? ' - '.$entry['title'] : null) . '</option>';
							endforeach;
						} ?>
					</select>
				</td>
			</tr>

			</tbody>
		</table>
		<p class="submit">
			<input type="submit" value="Compare now!" class="button button-primary" id="ADS24_LITE_submit" name="submit">
		</p>
	</form>

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
	})(jQuery);
</script>
<a href="https://www.witoni.com/product/ads24-pro-powerful-wordpress-advertising-manager-plugin/" id="lite-ab-test-btn" target="_blank" class="lite-ab-test-btn">Upgrade to Pro</a>
</div>