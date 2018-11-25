<?php
add_action('wp_ajax_nopriv_icb_form_save','icb_form_save');
add_action('wp_ajax_icb_form_save','icb_form_save');
function icb_form_save(){
	global $wpdb;
	$icb_messages = "{$wpdb->prefix}icb_messages";
	$icb_topics = "{$wpdb->prefix}icb_topics";
	$notif = array();
	$crf = icb_generateCRF($_POST['icb_code']);
	if(($crf == $_POST['icb_crf_code']) && ($crf == $_SESSION['_icb_crf_'])){
		$item = $wpdb->get_row($wpdb->prepare("SELECT 'email' FROM $icb_topics WHERE 'topic' = %s", wp_strip_all_tags($_POST['icb_topic'])), ARRAY_A);
		$subject = wp_strip_all_tags($_POST['icb_topic']);
		$email = wp_strip_all_tags($_POST['icb_email']);
		$fullname = wp_strip_all_tags($_POST['icb_fullname']);
		$message = wp_strip_all_tags($_POST['icb_message']);
		$to = $item['email'];
		$header = 'Content-Type: text/html; charset=UTF-8';
		$content =  'Email: '.$email.'<br>'.
					'Fullname: '.$fullname.'<br>'.
					'Subject: '.$subject.
					'<br>===================<br>'.
					'Message:<br>'.$message;
		wp_mail($to, $subject, $content, $header);
		$values = array(
						'subject' => $subject,
						'email' => $email,
						'fullname' => $fullname,
						'message' => $message
					);
		$format = array('%s','%s','%s','%s');
		$result = $wpdb->insert($icb_messages, $values, $format);
		if($result){ $notif[] = "Message sent successfully."; } 
		else { $notif[] = "Can't record message."; }
	} else {
		$notif[] = "Captcha is invalid.";
	}
	echo json_encode($notif); 
	die();
}

add_action('wp_ajax_nopriv_icb_getcaptcha_session','icb_getcaptcha_session');
add_action('wp_ajax_icb_getcaptcha_session','icb_getcaptcha_session');
function icb_getcaptcha_session(){
	$text = '';
	for ($i = 0; $i < 5; $i++) {
		$text .= chr(rand(97, 122));
	}
	$dir = plugins_url('/assets/fonts/', __FILE__);
	$fontSize = 16; 
	// Create image width dependant on width of the string 
	$width  = imagefontwidth($fontSize) * strlen($text); 
	// Set height to that of the font 
	$height = imagefontheight($fontSize); 
	// Create the image pallette 
	$image = imagecreate($width,$height);
	// random number 1 or 2
	$num = rand(1,2);
	if($num==1) {
		$textFont = "Capture it 2.ttf"; // font style
	}
	else {
		$textFont = "Molot.otf";// font style
	}
// random number 1 or 2
	$num2 = rand(1,2);
	if($num2==1) {
		$textColor = imagecolorallocate($image, 113, 193, 217);// color
	}
	else {
		$textColor = imagecolorallocate($image, 163, 197, 82);// color
	}
	$backgroundWhite = imagecolorallocate($image, 255, 255, 255); // background color white
	imagefilledrectangle($image,0,0,399,99,$backgroundWhite);// create rectangle white
	$getfont = imageloadfont($dir.$textFont); //load font
	imagestring($image, $getfont, 7, 3, $text, $textColor); //write text
	ob_start();
	imagepng($image);
	$image_data = ob_get_contents();
	imagedestroy($image);
	ob_end_clean();
	$imageBase64data = base64_encode($image_data);
	$crf = icb_generateCRF($text);
	$_SESSION['_icb_crf_'] = $crf;
	session_write_close();
	$captchaValue = array("image" =>$imageBase64data, "crf"=>$crf);
	echo json_encode($captchaValue); 
	die();
}

function icb_generateCRF($data){
	$salt = "$%shKlMnt_UVWx89";
	return md5($data . $salt);
}