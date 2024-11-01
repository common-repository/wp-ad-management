<?php
$template_name = 'block-468--60';
$crop = (isset($crop) && $crop == 'no' || isset($crop) && $crop == 'ajax') ? $crop : 'yes';

$width = 468;
$height = 60;

if ( isset($_POST['a24p_get_required_inputs']) ) {

	// -- START -- GET REQUIRED INPUTS
	return 'url,img'; // inputs shows in form (default: 'title,desc,url,img or html')
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
					"img" => (isset($custom_image)) ? $custom_image : plugins_url('/ads24-lite-plugin/frontend/img/example.jpg')
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
					"img" => a24p_ad($_POST['a24p_ad_id'], "img")
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



		echo '<div class="a24pProItemInner__thumb">'; // -- START -- ITEM THUMB

		echo '<div class="a24pProAnimateThumb">';

		$url = parse_url($ad['url']); // -- START -- LINK
		$agency_form = get_option('ADS24_LITE_plugin_agency_ordering_form_url');
		if ( $ad['url'] != '' ) {

			if ( isset($example) ) { // url to form if example in ad space
				echo '<a class="a24pProItem__url"'.(isset($rel) ? $rel : null).' href="'.$form_url.'" target="_blank">';
			} else {
				if ( isset($type) && $type == 'agency' ) {
					echo '<a class="a24pProItem__url"'.(isset($rel) ? $rel : null).' href="'.$agency_form.( (strpos($agency_form, '?')) ? '&' : '?' ).'ADS24_LITE_id='.$ad['id'].'&ADS24_LITE_url=1" target="_blank">';
				} else {
					echo '<a class="a24pProItem__url"'.(isset($rel) ? $rel : null).' href="'.get_site_url().( (strpos(get_site_url(), '?')) ? '&' : '?' ).'ADS24_LITE_id='.$ad['id'].'&ADS24_LITE_url=1" target="_blank">';
				}
			}

		} else {

			echo '<a href="#">';
		}

		echo '<div class="a24pProItemInner__img" style="background-image: url(&#39;'.a24p_crop_tool($crop, ((!isset($sid) && !isset($_POST['a24p_ad_id']) || isset($example)) ? $ad['img'] : a24p_upload_url(null, $ad['img'])), 468, 60).'&#39;)"></div>'; // -- ITEM -- IMG

		echo '</a>'; // -- END -- LINK

		echo '</div>';

		echo '</div>'; // -- END -- ITEM THUMB



		echo '</div>'; // -- END -- ITEM INNER

		a24pProCountdown ( $sid, $ad['id'], (isset($ad['ad_limit']) ? $ad['ad_limit'] : 0), (isset($ad['ad_model']) ? $ad['ad_model'] : 0) );

		echo '</div>'; // -- END -- ITEM

	}
	echo '</div>'; // -- END -- ITEMS

	echo '</div>'; // -- END -- CONTAINER
// -- END -- TEMPLATE HTML

// resize function
	a24pProResize($sid, $width, $height);
// horizontal css
	if ( a24p_space($sid, 'random') == 2 ) {
		a24pProSpaceCss($sid, 'vertical', array('items' => $col_per_row));
	}
}