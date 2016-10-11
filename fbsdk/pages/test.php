<?php
	error_reporting(E_ALL);
	session_start(); 
	
	include_once '../lib/facebook.php';
	
	$fbApp = $fb->getApp();
	$access_token = (string)$fb->getDefaultAccessToken();
	
	$page_id = '1681271828857371';
	$request = new Facebook\FacebookRequest(
	  $fbApp,
	  $access_token,
	  'POST',
	  "/$page_id/feed",
	  array (
		'message' => 'This is a test message',
	  )
	);
	$request = $fb->request('POST', "/$page_id/feed", array(
		'message' => 'Test fb post',
	));
	
	//$response = $request->execute();
	//$response = $fb->getClient()->sendRequest($request);
	$response = $fb->getClient();
	echo '<pre>';
	var_dump($response);
	echo '</pre>';
	exit();
	
	$graphObject = $response->getGraphObject();
	echo '<pre>';
	var_dump($graphObject);
	echo '</pre>';
?>

