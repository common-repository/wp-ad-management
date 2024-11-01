<?php
/*
Template Name: Ads Pro - API
*/

// -- START -- Errors
//
//	704 - invalid site url
//	705 - invalid id param
//	706 - invalid secure key
//	707 - invalid type
//
// -- END -- Errors

// Get Space
$id 			= ( isset($_GET['id']) ) ? $_GET['id'] : NULL;
$type 			= ( isset($_GET['type']) ) ? $_GET['type'] : NULL;
$key 			= ( isset($_GET['secure']) ) ? $_GET['secure'] : NULL;
$url1 			= ( isset($_GET['url1']) ) ? str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $_GET['url1'])))) : NULL;
$url2 			= ( isset($_GET['url2']) ) ? str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $_GET['url2'])))) : NULL;
$max_width 		= ( isset($_GET['max_width']) ) ? $_GET['max_width'] : NULL;
$delay 			= ( isset($_GET['delay']) ) ? $_GET['delay'] : NULL;
$padding_top 	= ( isset($_GET['padding_top']) ) ? $_GET['padding_top'] : NULL;
$attachment 	= ( isset($_GET['attachment']) ) ? $_GET['attachment'] : NULL;
$crop 			= ( isset($_GET['crop']) ) ? $_GET['crop'] : NULL;

$domain 		= ( isset($id) ) ? a24p_site(a24p_space($id, 'site_id'), 'url') : NULL;
$domain_str 	= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $domain))));
$domain1 		= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $url1))));
$domain2 		= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $url2))));

//echo '<pre>';
//var_dump($type);
//var_dump($key);
//var_dump($url1);
//var_dump($url2);
//var_dump($max_width);
//var_dump($delay);
//var_dump($position);
//
//var_dump($domain);
//var_dump($domain_str);
//var_dump($domain1);
//var_dump($domain2);
//var_dump(strpos($domain, $url1));
//var_dump(strpos($domain, $url2));
//var_dump(strpos($url1, $domain));
//var_dump(strpos($url2, $domain));
//echo '</pre>';

if ( (isset($url1) && strpos($domain, $url1) !== false) OR (isset($url2) && strpos($domain, $url2) !== false) ) {

	if ( isset($id) && $id != '' && a24p_space($id, 'id') != NULL && a24p_space($id, 'status') == 'active' && a24p_site(a24p_space($id, 'site_id'), 'status') == 'active' ) {

		if ( ( isset($key) && $key === hash('sha1', $id.$domain1.'ADS24_LITE') || isset($key) && $key === hash('sha1', $id.$domain2.'ADS24_LITE') ) ) {

			// get space
			if ( $type == 'space' ) {

				echo ADS24_LITE_adslot($id, $max_width, $delay, $padding_top, $attachment, $crop); // Print items

				// get css styles
			} elseif ( $type == 'styles' ) {

				echo 'get styles';

				// get js scripts
			} elseif ( $type == 'scripts' ) {

				echo 'get scripts';

				// get domain api
			} elseif ( $type == 'template' ) {

				echo a24p_space($id, 'template');

			} elseif ( $type == 'domain' ) {

				echo plugins_url();

			} else {

				echo '(error 707) No access to the API. Invalid Type.';
			}

		} else {
			echo '(error 706) No access to the API. Invalid Secure Key.';
		}

	} else {
		echo '(error 705) No access to the API. Invalid Id Param.';
	}

} elseif ( isset($_GET['i']) ) { // iframe

	if (isset($id) && $id != '' && a24p_space($id, 'id') != NULL && a24p_space($id, 'status') == 'active' && a24p_site(a24p_space($id, 'site_id'), 'status') == 'active') {

		echo ADS24_LITE_adslot($id, $max_width, $delay, $padding_top, $attachment, $crop); // print items

		?>
<style>
	.a24pProContainerNew .a24pProHeader { font-family: Verdana, Arial, sans-serif !important; }
	.a24pProContainerNew .a24pProItemInner__copy { font-family: Verdana, Arial, sans-serif; }
	<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/asset/style.css'); ?>
	<?php if ( strpos(a24p_space($id, 'template'), 'material-design') === true ): ?>
		<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/asset/material-design.css'); ?>
	<?php endif; ?>
	<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/'.a24p_space($id, 'template').'.css') ?>
	<?php echo (get_option('ADS24_LITE_plugin_custom_css') != '') ? get_option('ADS24_LITE_plugin_custom_css') : null; ?>
</style>
<?php

	} else {
		echo '(error 705) No access to the API. Invalid Id Param.';
	}

} else {
	echo 'Success. API Page configured correctly. Use the Parser Plugin or Iframe option to display Ads on external sites. Learn more about <a href="http://adspro.scripteo.info">Ads Pro</a>.'; // 704
}
