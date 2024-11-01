<?php
$template_name = 'html';

if ( isset($_POST['a24p_get_required_inputs']) ) {

	// -- START -- GET REQUIRED INPUTS
	return 'html,url'; // inputs shows in form (default: 'title,desc,url,img or html')
	// -- END -- GET REQUIRED INPUTS

} else {

// -- START -- IF EXAMPLE TEMPLATE
	if ( !isset($ads) && !isset($sid) || isset($sid) && isset($example) && $example == true ) {
		if ( !isset($_POST['a24p_ad_id']) || isset($example) ) { // example content if new ad
			$ads = array(
				array(
					"template" => $template_name,
					"id" => 0,
					"url" => get_option('ADS24_LITE_plugin_trans_example_url'),
					"html" => "HTML Code here"
				)
			);
			if ( isset($example) ) {
				$col_per_row = a24p_space($sid, 'col_per_row');
			} else {
				$col_per_row = 1;
				$sid = NULL;
			}
		} else { // get ad content if edit ad
			$ads = array(
				array(
					"template" => $template_name,
					"id" => 0,
					"url" => a24p_ad($_POST['a24p_ad_id'], "url"),
					"html" => a24p_ad($_POST['a24p_ad_id'], "html")
				)
			);
			$col_per_row = 1;
			$sid = NULL;
		}
	} else { // if ads exists
		$col_per_row = a24p_space($sid, 'col_per_row');
	}
// -- END -- IF EXAMPLE TEMPLATE


// -- START -- TEMPLATE HTML
	echo '<div id="a24p-'.$template_name.'" class="a24pProContainerNew '.((isset($sid)) ? "a24pProContainer-".$sid." " : "").((!isset($sid)) ? "a24pProContainerExample " : "").'a24p-'.$template_name.' a24p-lite-col-'.$col_per_row.'" style="display: block !important">'; // -- START -- CONTAINER

	if ( isset($type) ) { // generate form url
		$form_url = a24pFormURL($sid, $type); // get agency form url
	} else {
		$form_url = a24pFormURL($sid); // get order form url
	}

	if ( isset($sid) && a24p_space($sid, 'title') != '' OR isset($sid) &&  a24p_space($sid, 'add_new') != '' ) {
		// -- START -- HEADER
		echo '<div class="a24pProHeader" style="background-color:'.a24p_space($sid, 'header_bg').'">'; // -- START -- HEADER

		echo '<h3 class="a24pProHeader__title" style="color:'.a24p_space($sid, 'header_color').'"><span>'.a24p_space($sid, 'title').'</span></h3>'; // -- HEADER -- TITLE

		echo '<a class="a24pProHeader__formUrl" href="'.$form_url.'" target="_blank" style="color:'.a24p_space($sid, 'link_color').'"><span>'.a24p_space($sid, 'add_new').'</span></a>'; // -- HEADER -- LINK TO ORDERING FORM

		echo '</div>'; // -- END -- HEADER
	}

	echo '<div class="a24pProItems '.a24p_space($sid, "grid_system").' '.((strpos(a24p_space($sid, 'display_type'), 'carousel') !== false) ? 'a24p-owl-carousel a24p-owl-carousel-'.$sid : '').'" style="background-color:'.a24p_space($sid, 'ads_bg').'">'; // -- START -- ITEMS

	foreach ( $ads as $key => $ad ) {

		if ( $ad['id'] != 0 && a24p_ad($ad['id']) != NULL ) {  // -- COUNTING FUNCTION (DO NOT REMOVE!)
			$model = new ADS24_LITE_Model();
			$model->a24pProCounter($ad['id']);
		}

		echo '<div class="a24pProItem '.(($key % $col_per_row == 0) ? "a24pReset" : "").'" data-animation="'.a24p_space($sid, "animation").'" style="'.((a24p_space($sid, "animation") == "none" OR a24p_space($sid, "animation") == NULL) ? "opacity:1" : "").'">'; // -- START -- ITEM

		echo '<div class="a24pProItemInner" style="background-color:'.a24p_space($sid, 'ad_bg').'">'; // -- START -- ITEM INNER



		echo '<div class="a24pProItemInner__copy">'; // -- START -- ITEM COPY

		echo '<div class="a24pProItemInner__copyInner">'; // -- START -- ITEM COPY INNER


		echo '<div class="a24pProItemInner__html">'; // -- START -- ITEM HTML

		if ( isset($ad['id']) && $ad['id'] != 0 && isset($ad['url']) && $ad['url'] != '' && filter_var($ad['url'], FILTER_VALIDATE_URL) ) {
			echo '<a class="a24pProItem__url"'.(isset($rel) ? $rel : null).' href="'.get_site_url().( (strpos(get_site_url(), '?')) ? '&' : '?' ).'ADS24_LITE_id='.$ad['id'].'&ADS24_LITE_url='.$ad['url'].'" target="_blank">'; // -- START -- LINK
		}

		$html = $ad['html'];
		preg_match_all('[php_file= (.*?) ]', $html, $matches);
		$php = $matches[1];

		if ( isset($php[0]) ) {
			if ( file_exists( plugin_dir_path( __FILE__ ).'../php/'.$php[0] ) ) {
				require_once(plugin_dir_path( __FILE__ ).'../php/'.$php[0].'');
			}
		} else {
			echo do_shortcode( stripslashes( $ad['html'] ) );
		}

		if ( isset($ad['id']) && $ad['id'] != 0 && isset($ad['url']) && $ad['url'] != '' && filter_var($ad['url'], FILTER_VALIDATE_URL) ) {
			echo '</a>'; // -- END -- LINK
		}

		echo '</div>'; // -- END -- ITEM HTML


		echo '</div>'; // -- END -- ITEM COPY INNER

		echo '</div>'; // -- END -- ITEM COPY



		echo '</div>'; // -- END -- ITEM INNER

		a24pProCountdown ( $sid, $ad['id'], (isset($ad['ad_limit']) ? $ad['ad_limit'] : 0), (isset($ad['ad_model']) ? $ad['ad_model'] : 0) );

		echo '</div>'; // -- END -- ITEM

	}
	echo '</div>'; // -- END -- ITEMS

	echo '</div>'; // -- END -- CONTAINER
// -- END -- TEMPLATE HTML

	if ( 1 != 1 ) {
	// -- START -- SCRIPT JS
	echo '<script>
		(function($){
			$(document).ready(function(){



			});
		})(jQuery);
	</script>';
	// -- END -- SCRIPT JS
	}

// horizontal css
	if ( a24p_space($sid, 'random') == 2 ) {
		a24pProSpaceCss($sid, 'vertical', array('items' => $col_per_row));
	}
}