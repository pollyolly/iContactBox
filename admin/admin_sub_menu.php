<?php
function icb_admin_menu(){
	add_menu_page(
		'iContact Box',
		'iContact Box',
		'activate_plugins',
		'contact-box',
		'icb_contact_page_handler'
	);
	add_submenu_page(
		'contact-box',
		'Topic List',
		'Topic List',
		'activate_plugins',
		'contact-box',
		'icb_contact_page_handler'
	);
	add_submenu_page(
		'contact-box',
		'Add Topics',
		'Add Topics',
		'activate_plugins',
		'contact-topic-form',
		'icb_contact_topic_form_handler'
	);
	add_submenu_page(
		'contact-box',
		'Message List',
		'Message List',
		'activate_plugins',
		'contact-box-message',
		'icb_contact_message_page_handler'
	);
	add_submenu_page(
		'contact-box',
		'Add Setting',
		'Add Setting',
		'activate_plugins',
		'contact-settings-form',
		'icb_email_settings_form_handler'
	);
	add_submenu_page(
		'contact-box',
		'Settings',
		'Settings',
		'activate_plugins',
		'contact-box-settings',
		'icb_settings_page_handler'
	);
}
add_action('admin_menu', 'icb_admin_menu' );
?>