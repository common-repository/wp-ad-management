<?php
require_once dirname(__FILE__) . '/ADS24_LITE_Ordering_form.php'; // require ordering form if stats
$ad_id = isset($_GET['ADS24_LITE_id']) ? $_GET['ADS24_LITE_id'] : 0;
$model = new ADS24_LITE_Model();
$model->a24pGenerateStats($ad_id);
$statsFrom = (($model->a24pIntervalStats($ad_id, 'from') != null) ? $model->a24pIntervalStats($ad_id, 'from') : 0);
$countClicks = (($model->a24pIntervalStats($ad_id, 'from') != null) ? a24p_counter($ad_id, 'click') : 0);
$countViews = (($model->a24pIntervalStats($ad_id, 'from') != null) ? a24p_counter($ad_id, 'view') : 0);
echo '
<div class="a24pStatsWrapperBg"></div>
<div class="a24pStatsWrapper" data-ad-id="'.$ad_id.'" data-days="7" data-from="'.$statsFrom[0].'" data-time="'.time().'">
	<div class="a24pStatsWrapperInner">
		<h2><span>'.get_option("ADS24_LITE_plugin_trans_stats_header").'</span> <span class="a24pLoader a24pLoaderStats" style="display: none"></span></h2>
		<div class="a24pStatsButtons">
			<a class="a24pPrevWeek" href="#" onclick="a24pPrevStats()">'.get_option("ADS24_LITE_plugin_trans_stats_prev_week").'</a>
			<a class="a24pNextWeek" href="#" onclick="a24pNextStats()">'.get_option("ADS24_LITE_plugin_trans_stats_next_week").'</a>
		</div>
		<div class="a24pStatsChart">
			<div class="a24pSumStats">
				'.get_option("ADS24_LITE_plugin_trans_stats_clicks").' <strong>'.$countClicks.'</strong>
			</div>
			<div class="a24pSumStats" style="margin: 0 5%">
				'.get_option("ADS24_LITE_plugin_trans_stats_views").' <strong>'.$countViews.'</strong>
			</div>
			<div class="a24pSumStats">
				'.get_option("ADS24_LITE_plugin_trans_stats_ctr").' <strong>'.(($countViews > 1) ? number_format(($countClicks / $countViews) * 100, 2)." %" : " - " ).'</strong>
			</div>
		</div>';
$report = plugin_dir_path( __FILE__ ) . 'PDF/reports/ad-'.$ad_id.'.txt';
if ( file_exists( $report ) ) {
echo 	'<div style="text-align: right">
			<span>'.a24p_get_trans('statistics', 'full_stats').'</span>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=90">'.a24p_get_trans('statistics', 'last_90').'</a>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=30">'.a24p_get_trans('statistics', 'last_30').'</a>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=7">'.a24p_get_trans('statistics', 'last_7').'</a>
		</div>';
}
echo '	<div class="a24pChart ct-chart"></div>';
$title = get_option("ADS24_LITE_plugin_trans_stats_clicks");
$title = apply_filters( "a24p-lite-changeTitle", $title, $ad_id);
echo '<h3 class="a24pHeaderClicks">'.$title.'</h3>';
echo '<div class="a24pStatsClicks"></div>';
echo '<span class="a24pStatsClose"></span>
	</div>
</div>'; ?>
<script>
	(function($){
		var a24pStatsWrapperBg = $(".a24pStatsWrapperBg");
		var a24pStatsWrapper = $(".a24pStatsWrapper");
		var a24pBody = $("body");
		a24pBody.css({"overflow" : "hidden", "height" : ( a24pBody.hasClass("logged-in") ) ? $( window ).height() - 32 : $( window ).height()});
		a24pStatsWrapper.appendTo(document.body).addClass("animated zoomInDown");
		a24pStatsWrapperBg.appendTo(document.body).addClass("animated zoomInDown");
		a24pInitStatsChart();
		a24pInitClicksList();
		$(document).ready(function() {
			var a24pStatsClose = $(".a24pStatsClose");
			var a24pChartDirect = $(".a24pStatsChart");
			var a24pStatsClicks = $(".a24pStatsClicks");
			a24pChartDirect.css({"max-height" : "300px"});
			a24pStatsClicks.css({"max-height" : "400px"});
			a24pStatsClose.click(function () {
				a24pBody.css({"overflow" : "", "height" : ""});
				a24pChartDirect.addClass("animated zoomOut");
				a24pStatsClose.addClass("animated zoomOut");
				a24pStatsClicks.addClass("animated zoomOut");
				setTimeout(function(){
					a24pStatsWrapper.removeClass("zoomInDown").addClass("animated zoomOutUp");
					a24pStatsWrapperBg.removeClass("zoomInDown").addClass("animated zoomOutUp");
				}, 400);
			});
		});
	})(jQuery);
	function a24pInitStatsChart()
	{
		(function($) {
			var a24pStatsWrapper = $(".a24pStatsWrapper");
			var a24pChartDirect = $(".a24pChart");
			var a24pLoader = $(".a24pLoaderStats");
			var a24pPrevWeek = $(".a24pPrevWeek");
			a24pChartDirect.addClass("animated zoomOut");
			a24pLoader.fadeIn(400);
			if ( parseInt(a24pStatsWrapper.attr("data-time")) - parseInt(a24pStatsWrapper.attr("data-days")) * 24 * 60 * 60 < a24pStatsWrapper.attr("data-from") ) {
				a24pPrevWeek.fadeOut();
			} else {
				a24pPrevWeek.fadeIn();
			}
			$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"a24p_stats_chart_callback",ad_id:a24pStatsWrapper.attr("data-ad-id"),days:a24pStatsWrapper.attr("data-days")}, function(result) {
				a24pChartDirect.removeClass("zoomOut").addClass("animated zoomIn");
				a24pLoader.fadeOut(400);
				var chart = $.parseJSON(result);
				var data = {
					labels: chart.labels,
					series: [
						{
							name: "<?php echo get_option("ADS24_LITE_plugin_trans_stats_clicks") ?>",
							data: chart.clicks
						},
						{
							name: "<?php echo get_option("ADS24_LITE_plugin_trans_stats_views") ?>",
							data: chart.views
						}
					]
				};
				var options = {
					height: "200px"
				};
				new Chartist.Line(".ct-chart", data, options);
			});
		})(jQuery);
	}
	function a24pInitClicksList()
	{
		(function($) {
			var a24pStatsWrapper = $(".a24pStatsWrapper");
			var a24pListDirect = $(".a24pStatsClicks");
			var a24pHeaderClicks = $(".a24pHeaderClicks");
			var a24pLoader = $(".a24pLoaderStats");
			a24pListDirect.addClass("animated zoomOut");
			a24pLoader.fadeIn(400);
			$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"a24p_stats_clicks_callback",ad_id:a24pStatsWrapper.attr("data-ad-id"),days:a24pStatsWrapper.attr("data-days")}, function(result) {
				if ( result != 0 ) {
					a24pHeaderClicks.fadeIn();
					a24pListDirect.html(result).removeClass("zoomOut").addClass("animated zoomIn");
				} else {
					a24pHeaderClicks.fadeOut();
				}
				a24pLoader.fadeOut(400);
			});
		})(jQuery);
	}
	function a24pPrevStats()
	{
		(function($) {
			var a24pStatsWrapper = $(".a24pStatsWrapper");
			var a24pNextWeek = $(".a24pNextWeek");
			var a24pPrevWeek = $(".a24pPrevWeek");
//			console.log(parseInt(a24pStatsWrapper.attr("data-time")));
//			console.log(parseInt(a24pStatsWrapper.attr("data-days")) * 24 * 60 * 60);
//			console.log(parseInt(a24pStatsWrapper.attr("data-time")) + parseInt(a24pStatsWrapper.attr("data-days")) * 24 * 60 * 60);
//			console.log(a24pStatsWrapper.attr("data-from"));
			if ( parseInt(a24pStatsWrapper.attr("data-time")) - parseInt(a24pStatsWrapper.attr("data-days")) * 24 * 60 * 60 < a24pStatsWrapper.attr("data-from") ) {
				a24pPrevWeek.fadeOut();
			} else {
				a24pPrevWeek.fadeIn();
			}
			a24pStatsWrapper.attr( "data-days", (parseInt(a24pStatsWrapper.attr("data-days")) + 7) );
			if ( parseInt(a24pStatsWrapper.attr("data-days")) >= 7 ) {
				a24pNextWeek.fadeIn();
			} else {
				a24pNextWeek.fadeOut();
			}
			a24pInitStatsChart();
			a24pInitClicksList();
		})(jQuery);
	}
	function a24pNextStats()
	{
		(function($) {
			var a24pStatsWrapper = $(".a24pStatsWrapper");
			var a24pNextWeek = $(".a24pNextWeek");
			if ( parseInt(a24pStatsWrapper.attr("data-days")) >= 21 ) {
				a24pNextWeek.fadeIn();
			} else {
				a24pNextWeek.fadeOut();
			}
			a24pStatsWrapper.attr( "data-days", a24pStatsWrapper.attr("data-days") - 7 );
			a24pInitStatsChart();
			a24pInitClicksList();
		})(jQuery);
	}
</script>