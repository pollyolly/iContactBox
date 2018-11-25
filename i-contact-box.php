<?php
/*
 * @package iContact Box
 * @version 1.0
 */
/*
Plugin Name: iContact Box
Plugin URI: 
Description: iContact Box Shortcode to use just copy and paste -> [icb_shortcode]
Author: John Mark
Version: 1.0
Author URI: 
*/
global $icb_db_version;
$icb_db_version = '1.1';
function icb_install(){
    global $wpdb;
    global $icb_db_version;
    $tbl_icb_topics = "{$wpdb->prefix}icb_topics";
    $tbl_icb_messages = "{$wpdb->prefix}icb_messages";
    $tbl_icb_email_setting = "{$wpdb->prefix}icb_email_setting";
    $sql .= "CREATE TABLE " . $tbl_icb_topics  . " (
      id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
      topic varchar(100) NULL,
      email varchar(100) NULL,
      stat varchar(20) NULL
    );";
    $sql .= "CREATE TABLE " . $tbl_icb_messages  . " (
        id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
        fullname varchar(100) NOT NULL,
        email varchar(100) NULL,
        subject varchar(100) NULL,
        message varchar(500) NULL
      );";
    $sql .= "CREATE TABLE " . $tbl_icb_email_setting  . " (
        id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
        host varchar(100) NOT NULL,
        port varchar(100) NULL,
        username varchar(30) NULL,
        userpass varchar(30) NULL,
        security varchar(10) NULL,
        fromname varchar(30) NULL
      );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('icb_db_version', $icb_db_version);
    $installed_ver = get_option('icb_db_version');
    if ($installed_ver != $icb_db_version) {
        $sql .= "CREATE TABLE " . $tbl_icb_topics  . " (
            id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            topic varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            stat varchar(20) NULL
          );";
        $sql .= "CREATE TABLE " . $tbl_icb_messages  . " (
            id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            fullname varchar(100) NULL,
            email varchar(100) NULL,
            subject varchar(100) NULL,
            message varchar(500) NULL
          );";
          $sql .= "CREATE TABLE " . $tbl_icb_email_setting  . " (
            id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            host varchar(100) NOT NULL,
            port varchar(100) NULL,
            username varchar(30) NULL,
            userpass varchar(30) NULL,
            security varchar(10) NULL,
            fromname varchar(30) NULL
          );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        // notice that we are updating option, rather than adding it
        update_option('icb_db_version', $icb_db_version);
    }
}
register_activation_hook(__FILE__, 'icb_install');
function icb_install_data(){
    global $wpdb;
    $tbl_icb_topics  = "{$wpdb->prefix}icb_topics";
    $topic_data = array(
            array('topic' => 'Demo1','email'=>'','stat' => 'Active'),
            array('topic' => 'Demo2','email'=>'','stat' => 'Active'),
            array('topic' => 'Demo3','email'=>'','stat' => 'Active'),
            array('topic' => 'Demo4','email'=>'','stat' => 'Active'),
            array('topic' => 'Demo5','email'=>'','stat' => 'Active'),
            array('topic' => 'Demo6','email'=>'','stat' => 'Active')
        );
    foreach($topic_data as $value){
        $wpdb->insert($tbl_icb_topics ,$value);    
    }
}
register_activation_hook(__FILE__, 'icb_install_data');
function icb_update_db_check(){
    global $icb_db_version;
    if (get_site_option('icb_db_version') != $icb_db_version) {
        icb_install();
    }
}
add_action('plugins_loaded', 'icb_update_db_check');
include_once (plugin_dir_path(__FILE__) . '/assets/inc_script_style.php');
include_once (plugin_dir_path(__FILE__) . '/icb_shortcodes.php');
include_once (plugin_dir_path(__FILE__) . '/icb_mailer.php');
// //Post Type
// // include_once (plugin_dir_path(__FILE__)  . '/admin/post_type.php');
// //Tables
include_once (plugin_dir_path(__FILE__)  . '/tables/Topic_table_class.php');
include_once (plugin_dir_path(__FILE__)  . '/tables/Message_table_class.php');
include_once (plugin_dir_path(__FILE__)  . '/tables/Settings_table_class.php');
include_once (plugin_dir_path(__FILE__)  . '/forms/contact-topic-form.php');
include_once (plugin_dir_path(__FILE__)  . '/forms/contact-settings-form.php');
// //Subpage
include_once (plugin_dir_path(__FILE__) . '/admin/admin_sub_menu.php');

// //Ajax Forms
include_once (plugin_dir_path(__FILE__) . '/ajax/ajax-calls.php');
function icb_startSession(){
	if(!session_id()){
		session_start();
    }
}
add_action("init","icb_startSession", 1);
