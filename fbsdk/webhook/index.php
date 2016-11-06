<?php
	include_once 'file.php';
	//include_once 'messenger.php';
	//include_once 'msg_test.php';
	include_once 'msg_test_v2.php';
	
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
	
	//$input = json_decode(file_get_contents('php://input'), true);
	$input = file_get_contents('php://input');
	write_file($file_name, $input, false);
	//$entry = process_msg($input);
	//$entry = msg_test($input);
	$entry = msg_test_v2($input);
	//write_file('call2.txt', json_encode($input), false);
	write_file('call2.txt', $entry, false);
	// receive msg: {"object":"page","entry":[{"id":"1681271828857371","time":1474009466178,"messaging":[{"sender":{"id":"1078614338912257"},"recipient":{"id":"1681271828857371"},"timestamp":1474009466130,"message":{"mid":"mid.1474009466121:220c1279719466a349","seq":3,"text":"\u1edd"}}]}]}
	
	// send msg:{"id":"1078614338912257"},"timestamp":1474009537404,"message":{"is_echo":true,"mid":"mid.1474009537378:e32abf7e4ec0571e00","seq":5,"text":"c\u00f3 g\u00ec hot"}}]}]}{"object":"page","entry":[{"id":"1681271828857371","time":1474009538668,"messaging":[{"sender":{"id":"1078614338912257"},"recipient":{"id":"1681271828857371"},"timestamp":0,"delivery":{"mids":["mid.1474009537378:e32abf7e4ec0571e00"],"watermark":1474009537404,"seq":6}}]}]}
	
	// new comment from page
	// {"entry": [{"changes": [{"field": "feed", "value": {"parent_id": "1681271828857371_1681272105524010", "sender_name": "Auto all", "comment_id": "1681272105524010_1681276162190271", "sender_id": 1681271828857371, "item": "comment", "verb": "add", "created_time": 1474009742, "post_id": "1681271828857371_1681272105524010", "message": "Fuck tam"}}], "id": "1681271828857371", "time": 1474009742}], "object": "page"}
	
	// new comment from user: 
	// {"entry": [{"changes": [{"field": "feed", "value": {"parent_id": "1681271828857371_1681272105524010", "sender_name": "C\u00f3 M\u00e0 \u0110i\u00ean M\u1edbi", "comment_id": "1681272105524010_1681276792190208", "sender_id": 536069159937612, "item": "comment", "verb": "add", "created_time": 1474009827, "post_id": "1681271828857371_1681272105524010", "message": "H\u00ec h\u00ec"}}], "id": "1681271828857371", "time": 1474009827}], "object": "page"}
	
	
?>