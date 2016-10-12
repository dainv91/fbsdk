<?php
	include_once 'messenger.php';
	include_once '../../helper/mem.php';
	include_once 'msg_test.php';
	
	$msg = 'iadd';
	$msg_data = array(
		'message' => $msg,
	);
	
	$msg = http_build_query($msg_data);
		
	//$sender_id = '1305653869458699';
	//$ret = send_button_template_test($sender_id);
	//var_dump($ret);
	function test(){
		$sender_id = '1305653869458699';
		//$ret = send_button_template_test($sender_id);
		$ret = send_generic_template_test($sender_id);
		echo '<pre>';
		var_dump($ret);
		echo '</pre>';
	}
	if($_REQUEST['iadd'] == 'iadd'){
		test();
		exit();
	}
	//var_dump(load_from_mem('time'));
	echo $msg;
?>