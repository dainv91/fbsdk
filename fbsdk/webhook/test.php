<?php
	//include_once 'messenger.php';
	//include_once '../../helper/mem.php';
	include_once 'msg_test.php';
	
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
	
	if(isset($_REQUEST['iadd']) && $_REQUEST['iadd'] == 'iadd'){
		_test_test();
		exit();
	}
	//var_dump(load_from_mem('time'));
	//_test();
	echo $msg;
	test_selected_obj1();
	//test_obj();
	//var_dump(preg_match('/^[0-9]{9,12}$/', '+84988907560'));
	
	
?>