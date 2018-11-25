<?php
function icb_phpmailer( $phpmailer ) {
    global $wpdb;
    $table_name = "{$wpdb->prefix}icb_email_setting";
    $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name"), ARRAY_A);
    $phpmailer->isSMTP();
    $phpmailer->Host = esc_attr( $item['host'] );
    $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
    $phpmailer->Port = esc_attr( $item['port'] );
    $phpmailer->Username = esc_attr( $item['username'] );
    $phpmailer->Password = esc_attr( $item['userpass'] );
    // Additional settings…
    $phpmailer->SMTPSecure = esc_attr( $item['security'] ); // Choose SSL or TLS, if necessary for your server
    // $phpmailer->From = "sampleou@gmail.com";
    $phpmailer->FromName = esc_attr( $item['fromname'] );
}
add_action( 'phpmailer_init', 'icb_phpmailer');
?>