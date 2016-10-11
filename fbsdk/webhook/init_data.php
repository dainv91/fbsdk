<?php
	include_once 'messenger.php';
	include_once '../../helper/mem.php';
	
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
		$ret = send_button_template_test($sender_id);
		echo '<pre>';
		var_dump($ret);
		echo '</pre>';
	}
	if($_REQUEST['iadd'] == 'iadd'){
		test();
		exit();
	}
	//echo $msg;
	
	function init_data(){
		$cat1 = array();
		$cat1[] = 'Bé mặc';
		$cat1[] = 'Bé ăn';
		$cat1[] = 'Bé ngủ';
		$cat1[] = 'Bé tắm và vệ sinh';
		$cat1[] = 'Đồ cho mẹ & set sơ sinh';
		$cat1[] = 'Liên hệ hot line';
		
		$cat2 = array();
		$cat2[] = 'Bình sữa';
		$cat2[] = 'Thìa';
		$cat2[] = 'Bát';
		$cat2[] = 'Cọ rửa & nước rửa';
		
		$item1 = new stdclass();
		$item1->img = 'img1';
		$item1->desc = 'binh sua 1';
		$item1->price = 45000;
		$item1->action = 'Mua ngay';
		
		$item2 = new stdclass();
		$item2->img = 'img2';
		$item2->desc = 'binh sua 2';
		$item2->price = 320000;
		$item2->action = 'Mua ngay';
		
		$item3 = new stdclass();
		$item3->img = 'img3';
		$item3->desc = 'binh sua 3';
		$item3->price = 155000;
		$item3->action = 'Mua ngay';
		
		// result
		$result = array();
		$dic = array();
		$dic['dic_cat1'] = $cat1;
		$dic['dic_cat2'] = $cat2;
		$result['dic'] = $dic;
		foreach($cat1 as $key => $value){
			if($key == 1){
				$result[$key] = array();
				foreach($cat2 as $k2 => $v2){
					if($k2 == 0){
						$result[$key][$k2] = array();
						$result[$key][$k2][] = $item1;
						$result[$key][$k2][] = $item2;
						$result[$key][$k2][] = $item3;
						break;
					}
				}
				break;
			}
		}
		return $result;
	}
	
	function init_data_v2(){
		$result = array();
		
		$obj = new stdclass();
		$obj->id = 1;
		$obj->title = 'Bé ăn';
		$obj->level = 0;
		$obj->is_leaf = false;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 2;
		$obj->title = 'Bé mặc';
		$obj->level = 0;
		$obj->is_leaf = false;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 3;
		$obj->title = 'Bình sữa';
		$obj->level = 1;
		$obj->is_leaf = false;
		$obj->p_id = 1;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 4;
		$obj->title = 'Thìa';
		$obj->level = 1;
		$obj->is_leaf = false;
		$obj->p_id = 1;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 5;
		$obj->title = 'Bình sữa 1';
		$obj->level = 2;
		$obj->is_leaf = true;
		$obj->p_id = 3;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 6;
		$obj->title = 'Bình sữa 2';
		$obj->level = 2;
		$obj->is_leaf = true;
		$obj->p_id = 3;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 7;
		$obj->title = 'Thìa 1';
		$obj->level = 2;
		$obj->is_leaf = true;
		$obj->p_id = 4;
		$result[] = $obj;
		
		$obj = new stdclass();
		$obj->id = 8;
		$obj->title = 'Thìa 2';
		$obj->level = 2;
		$obj->is_leaf = true;
		$obj->p_id = 4;
		$result[] = $obj;		
		
		return $result;
	}
	
	//$result = init_data();
	$result = init_data_v2();
	$r = load_from_mem('init_data');
	if($r === false){
		store_to_mem('init_data', $result);
	}else{
		//clear_cache('init_data');
	}
	$obj = new stdclass();
	echo '<pre>';
	var_dump($r);
	echo '</pre>';
?>