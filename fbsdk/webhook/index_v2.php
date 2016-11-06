<?php
	include_once 'file.php';
	//include_once 'messenger.php';
	include_once 'msg_test_v2.php';
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$file_name = 'call.txt';
	write_file($file_name, $_REQUEST, false);
	
	if(isset($_REQUEST['hub_mode'])){
		$mode = $_REQUEST['hub_mode'];	
		$token = $_REQUEST['hub_verify_token'];
		$challenge = $_REQUEST['hub_challenge'];
		if($mode == 'subscribe'){
			echo $challenge;
			exit();
		}
	}
	echo 'OK';
	//die();
	
	//$input = json_decode(file_get_contents('php://input'), true);
	$input = file_get_contents('php://input');
	write_file($file_name, $input, false);
	//$entry = process_msg($input);
	//$entry = msg_test($input);
	
	$entry = msg_test_v2($input);
	
	//write_file('call2.txt', json_encode($input), false);
	write_file('call2.txt', $entry, false);
	
?>