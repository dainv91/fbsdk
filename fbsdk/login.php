<?php
	session_start(); 
	
	include_once 'lib/facebook.php';
	
	$helper = $fb->getRedirectLoginHelper();
	$permissions = ['email', 'user_likes']; // optional
	$loginUrl = $helper->getLoginUrl('http://app.inet.vn:8080/iadd/fbsdk/login-callback.php', $permissions);

	echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
	
	echo '<br />token: '. $_SESSION['facebook_access_token'];
?>
