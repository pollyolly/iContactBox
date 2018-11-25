<?php
function icb_contact_box_shortcode($atts, $content = null) {
	$atts = shortcode_atts( array(), $atts, 'icb_shortcode');
	ob_start();
		include_once (plugin_dir_path(__FILE__) . '/forms/contact-form.php');
	return ob_get_clean();
}
add_shortcode('icb_shortcode','icb_contact_box_shortcode');
?>