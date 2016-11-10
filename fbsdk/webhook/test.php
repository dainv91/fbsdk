<?php
	//include_once 'messenger.php';
	//include_once '../../helper/mem.php';
	//include_once 'msg_test.php';
	include_once 'msg_test_v2.php';
	
	$msg = 'iadd';
	$msg_data = array(
		'message' => $msg,
	);
	$msg = http_build_query($msg_data);
		
	//$sender_id = '1305653869458699';
	//$ret = send_button_template_test($sender_id);
	//var_dump($ret);
	
	function get_obj_by_id1($data, $id){
		foreach($data as $obj){
			if($obj->id == $id){
				return $obj;
			}
		}
		return null;
	}
	
	function get_selected_value1($data, $str_id_selected){
		$arr = explode('_', $str_id_selected);
		
		$obj_result = new stdclass();
		$obj_result->arr = array();
		$obj_result->text = '';
		foreach($arr as $payload){
			if($arr == ''){
				continue;
			}
			$obj = get_obj_by_id1($data, $payload);
			if($obj != null){
				if($obj->p_id != -1){
					$obj = get_obj_by_id1($data, $obj->p_id);
				}
				$obj_result->arr[] = $payload;
				$obj_result->text = $obj_result->text .'/' . $obj->title;
			}
		}
		return $obj_result;
		return 'iadd';
	}
	
	function test_selected_obj1(){
		$selected = '_12_41_57_62_66';
		$data = load_from_mem('init_data');
		$data = $data['value'];
		
		$result = get_selected_value1($data, $selected);
		echo 'Bạn vừa chọn: ';
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
		
	}
	
	/*
	function _test_test(){
		//$sender_id = '1305653869458699';
		$sender_id = '1305653869458699';
		//$ret = send_button_template_test($sender_id);
		//$ret = send_generic_template_test($sender_id);
		$ret = send_quick_replies_test($sender_id);
		echo '<pre>';
		var_dump($ret);
		echo '</pre>';
	}
	*/
	
	/*
	function clone_arr_obj1($arr_obj){
		$result = array();
		foreach($arr_obj as $obj){
			$result[] = clone $obj;
		}
		return $result;
	}
	*/
	
	function test_obj1(){
		/*
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
		$obj2 = clone_arr_obj1($arr_btn);
		
		foreach($obj2 as $btn){
			$btn->age = 30;
			$btn->name = 'obj2';
		}
		
		echo '<pre>';
		var_dump($arr_btn);
		var_dump($obj2);
		echo '</pre>';
		*/
	}
	
	//https://app.inet.vn:434/iadd/fbsdk/webhook/test.php?iadd=send&msg=Test%20send%20msg&user_id=1231308980276237
	function msg_test_send_first(){
		$page_id = '1681271828857371'; // Auto all;
		if(isset($_REQUEST['iadd']) && $_REQUEST['iadd'] == 'send'){
			$msg = $_REQUEST['msg'];
			$user_id = $_REQUEST['user_id'];
			
			$result = send_text_message($page_id, $user_id, $msg);
			echo $result;
			exit();
		}
	}
	function ecc(){
		echo 'ecc';
	}
	function test_date(){
		$str_start = '22:00';
		$str_end = '05:00';
		
		$num_end = (int)str_replace(':', '', $str_end);
		$num_start = (int)str_replace(':', '', $str_start);
		
		$start_date = strtotime($str_start);
		if($num_end < $num_start){
			$date_format = 'Y-m-d H:i:s';
			$end_date = strtotime($str_end);
			$end_date = strtotime(date($date_format, $end_date) .'+1 day');
		}else{
			$end_date = strtotime($str_end);
		}
		
		$now = time();
		var_dump($start_date);
		var_dump($end_date);
		
		$start_date = date('Y-m-d H:i:s', $start_date);
		$end_date = date('Y-m-d H:i:s', $end_date);
		
		var_dump($start_date);
		var_dump($end_date);

		var_dump(date('Y-m-d H:i:s', $now));
		var_dump($now < $end_date);
		var_dump($now > $start_date);
		echo 'date...'.'_ddd';
	}
	
	function test_date_v2(){
		$str_start = '22:00';
		$str_end = '05:00';
		$str_start = trim($str_start);
		$str_end = trim($str_end);
		
		$num_end = (int)str_replace(':', '', $str_end);
		$num_start = (int)str_replace(':', '', $str_start);
		
		$start_date = strtotime($str_start);
		$end_date = strtotime($str_end);
		
		if($num_end < $num_start){
			//$date_format = 'Y-m-d H:i:s';
			//$end_date = strtotime(date($date_format, $end_date) .'+1 day');
		}
		//$now = time();
		$now = strtotime('23:00');
		if($num_start <= $num_end){
			if($now < $start_date || $now > $end_date){
				echo 'Ngoài khoảng';
				return;
			}
		}else{
			if($now > $end_date && $now < $start_date){
				echo 'Ngoài rồi';
				return;
			}
		}
		echo 'Trong';
	}
	
	if(isset($_REQUEST['iadd'])){
		$value = $_REQUEST['iadd'];
		if($value == 'exe'){
			$func = 'ecc';
			if(isset($_REQUEST['func'])){
				$func = $_REQUEST['func'];
				$func();
			}
			exit();
		}
		/*
		if(isset($_REQUEST['func'])){
			echo 'dd';
			$func = $_REQUEST['func'];
			if (function_exists($func)){
				$func();
			}else{
				echo 'not existed...';
			}
		}
		*/
	}
	//var_dump(load_from_mem('time'));
	//_test();
	//echo $msg;
	//test_selected_obj1();
	//test_obj();
	//var_dump(preg_match('/^[0-9]{9,12}$/', '+84988907560'));
	//msg_test_send_first();
	echo 'Nothing...';
	
?>