<?php
$template_name = 'paper-note-3';

if ( isset($_POST['a24p_get_required_inputs']) ) {

	// -- START -- GET REQUIRED INPUTS
	return 'title,desc,url'; // inputs shows in form (default: 'title,desc,url,img or html')
	// -- END -- GET REQUIRED INPUTS

} else {

// -- START -- IF EXAMPLE TEMPLATE
	if ( !isset($ads) && !isset($sid) || isset($sid) && isset($example) && $example == true ) {
		if ( !isset($_POST['a24p_ad_id']) || isset($example) ) { // example content if new ad
			$ads = array(
				array(
					"template" => $template_name,
					"id" => 0,
					"title" => get_option('ADS24_LITE_plugin_trans_example_title'),
					"description" => get_option('ADS24_LITE_plugin_trans_example_desc'),
					"url" => get_option('ADS24_LITE_plugin_trans_example_url')
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
					"title" => a24p_ad($_POST['a24p_ad_id'], "title"),
					"description" => a24p_ad($_POST['a24p_ad_id'], "description"),
					"url" => a24p_ad($_POST['a24p_ad_id'], "url")
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

		echo '<div class="a24pProItemInner a24pAnimateCircle">'; // -- START -- ITEM INNER


		echo '
		<div class="a24pReveal a24pCircle_wrapper a24pProItemInner__copy">
			<div class="a24pCircle">
			';

		echo '<p class="a24pProItemInner__desc" style="color:'.a24p_space($sid, 'ad_desc_color').'">'.$ad['description'].'</p>'; // -- ITEM -- DESCRIPTION

		echo '
			</div>
		</div>

		<div class="a24pSticky a24pAnimateCircle">
			<div class="a24pFront a24pCircle_wrapper a24pAnimateCircle">
				<div class="a24pCircle a24pAnimateCircle"></div>
			</div>
		</div>
		';

		echo '<h3 class="a24pProItemInner__title" style="color:'.a24p_space($sid, 'ad_title_color').'">'.$ad['title'].'</h3>'; // -- ITEM -- TITLE

		echo '
		<div class="a24pSticky a24pAnimateCircle">
			<div class="a24pBack a24pCircle_wrapper a24pAnimateCircle">
			<div class="a24pCircle a24pAnimateCircle"></div>
			</div>
		</div>
		';


//		echo '<div class="a24pProItemInner__copy">'; // -- START -- ITEM COPY
//
//		echo '<div class="a24pProItemInner__copyInner">'; // -- START -- ITEM COPY INNER
//
//		echo '<h3 class="a24pProItemInner__title" style="color:'.a24p_space($sid, 'ad_title_color').'">'.$ad['title'].'</h3>'; // -- ITEM -- TITLE
//
//		echo '<p class="a24pProItemInner__desc" style="color:'.a24p_space($sid, 'ad_desc_color').'">'.$ad['description'].'</p>'; // -- ITEM -- DESCRIPTION
//
//		echo '</div>'; // -- END -- ITEM COPY INNER
//
//		echo '</div>'; // -- END -- ITEM COPY



		echo '</div>'; // -- END -- ITEM INNER

		echo '</a>'; // -- END -- LINK

		a24pProCountdown ( $sid, $ad['id'], (isset($ad['ad_limit']) ? $ad['ad_limit'] : 0), (isset($ad['ad_model']) ? $ad['ad_model'] : 0) );

		echo '</div>'; // -- END -- ITEM

	}
	echo '</div>'; // -- END -- ITEMS

	echo '</div>'; // -- END -- CONTAINER
// -- END -- TEMPLATE HTML

	$background = (a24p_space($sid, "ad_bg") != '') ? a24p_space($sid, "ad_bg") : NULL;
	$bgGradientFront = (a24p_space($sid, "ad_extra_color_1") != '') ? 'bottom, transparent 75%, '.a24p_space($sid, "ad_extra_color_1").' 95%' : NULL;
	$bgGradientBack = (a24p_space($sid, "ad_extra_color_1") != '') ? 'bottom, transparent, '.a24p_space($sid, "ad_extra_color_1") : NULL;
	$backgroundBack = (a24p_space($sid, "ad_extra_color_2") != '') ? a24p_space($sid, "ad_extra_color_2") : NULL;

	echo '
<style>
#a24p-paper-note-3 .a24pProItemInner .a24pFront .a24pCircle{
	margin-top: -10px;
	background: '.$background.';

	background-image: -webkit-linear-gradient('.$bgGradientFront.');
	background-image: -moz-linear-gradient('.$bgGradientFront.');
	background-image: linear-gradient('.$bgGradientFront.');
}
#a24p-paper-note-3 .a24pProItemInner:hover .a24pFront .a24pCircle {
	background-color: '.$background.';
}
#a24p-paper-note-3 .a24pProItemInner .a24pBack .a24pCircle{
	margin-top: -130px;
	background-color: '.$background.';

	background-image: -webkit-linear-gradient('.$bgGradientBack.');
	background-image: -moz-linear-gradient('.$bgGradientBack.');
	background-image: linear-gradient('.$bgGradientBack.');
}
#a24p-paper-note-3 .a24pProItemInner .a24pReveal .a24pCircle{
	background: '.$backgroundBack.';
}
</style>
';

// horizontal css
	if ( a24p_space($sid, 'random') == 2 ) {
		a24pProSpaceCss($sid, 'vertical', array('items' => $col_per_row));
	}
}