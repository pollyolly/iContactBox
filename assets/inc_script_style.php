<?php
function icb_style_js(){
    wp_register_style('icb_css', plugins_url('/icb-style.css', __FILE__), array(), 1.0);
    wp_register_script('icb_js_vue', plugins_url('/vue.min.js', __FILE__), array(), 1.0);
    wp_register_script('icb_js', plugins_url('/icb-script.js', __FILE__), array(), 1.0, true);
    wp_localize_script('icb_js', 'ajaxUrl', array(
      'ajax_url' => admin_url('admin-ajax.php')
   ));
   wp_enqueue_style('icb_css');
   wp_enqueue_script('icb_js_vue');
   wp_enqueue_script('icb_js');
}
add_action('wp_enqueue_scripts', 'icb_style_js');

function icb_admin_style(){
    wp_register_style('icb_admin_css', plugins_url('/icb-admin-style.css', __FILE__), array(), 1.0);
    wp_enqueue_style('icb_admin_css');
}
add_action('admin_enqueue_scripts', 'icb_admin_style');
?>