<?php
	//include_once 'messenger.php';
	include_once '../../helper/mem.php';
	//include_once 'msg_test.php';
	
	$msg = 'iadd';
	$msg_data = array(
		'message' => $msg,
	);
	$msg = http_build_query($msg_data);
		
	//$sender_id = '1305653869458699';
	//$ret = send_button_template_test($sender_id);
	//var_dump($ret);
	function _test(){
		//$sender_id = '1305653869458699';
		$sender_id = '1305653869458699';
		//$ret = send_button_template_test($sender_id);
		//$ret = send_generic_template_test($sender_id);
		$ret = send_quick_replies_test($sender_id);
		echo '<pre>';
		var_dump($ret);
		echo '</pre>';
	}
	
	function clone_arr_obj($arr_obj){
		$result = array();
		foreach($arr_obj as $obj){
			$result[] = clone $obj;
		}
		return $result;
	}
	
	function test_obj(){
		$arr_btn = array();
		$obj = new stdclass();
		$obj->name = 'iadd';
		$arr_btn[] = $obj;
		
		$obj = new stdclass();
		$obj->name = 'iadd2';
		$arr_btn[] = $obj;
		
		echo '<pre>';
		var_dump($arr_btn);
		echo '========================';
		echo '</pre>';
		
		foreach($arr_btn as $btn){
			$btn->age = 20;
			$btn->name = 'newn';
		}
		$obj2 = clone_arr_obj($arr_btn);
		
		foreach($obj2 as $btn){
			$btn->age = 30;
			$btn->name = 'obj2';
		}
		
		echo '<pre>';
		var_dump($arr_btn);
		var_dump($obj2);
		echo '</pre>';
	}
	if($_REQUEST['iadd'] == 'iadd'){
		_test();
		exit();
	}
	//var_dump(load_from_mem('time'));
	//_test();
	echo $msg;
	test_obj();
	
?>