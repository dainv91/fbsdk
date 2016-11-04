<?php
	include_once '../../helper/mem.php';
	include_once 'msg_test.php';
	
	function get_info(){
		$key = 'user_info';
		$mem = load_from_mem($key);
		$mem = $mem['value'];
		
		echo '<pre>';
		var_dump($mem);
		echo '</pre>';
	}
	
	function get_info_v2(){
		$key = 'user_info';
		$mem = load_from_mem($key);
		$mem = $mem['value'];
		
		$data = load_from_mem('init_data');
		$data = $data['value'];
		
		//$sender_id = '';
		//$selected = $mem[$sender_id]['other'];
		//$obj_selected = get_selected_value($data, $selected);
		
		$result = array();
		foreach($mem as $k => $v){
			$sender_id = $k;
			$selected = $mem[$sender_id]['other'];
			$obj_selected = get_selected_value($data, $selected);
			//$v['data_saved'] = $obj_selected;
			$result[$sender_id] = $obj_selected->text;
		}
		
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
	}
	
	function clear_info(){
		clear_cache('user_info');
		clear_cache('user_is_done');
	}
	
	if(isset($_REQUEST['iadd'])){
		if($_REQUEST['iadd'] == 'clear'){
			clear_info();
		}else if($_REQUEST['iadd'] == '2'){
			get_info_v2();
			return;
		}
	}
	get_info();
	//get_info_v2();
?>