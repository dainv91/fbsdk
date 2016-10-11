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
		$item1->desc = 'desc1';
		$item1->price = 45000;
		$item1->action = 'Mua ngay';
		
		$item2 = new stdclass();
		$item2->img = 'img2';
		$item2->desc = 'desc2';
		$item2->price = 320000;
		$item2->action = 'Mua ngay';
		
		$item3 = new stdclass();
		$item3->img = 'img3';
		$item3->desc = 'desc3';
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
	$result = init_data();
	$r = load_from_mem('init_data');
	if($r === false){
		store_to_mem('init_data', $result);
	}else{
		//clear_cache('init_data');
	}
	//echo '<pre>';
	//var_dump($r);
	//echo '</pre>';
?>