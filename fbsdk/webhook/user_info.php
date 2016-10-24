<?php
	include_once '../../helper/mem.php';
	
	function get_info(){
		$key = 'user_info';
		$mem = load_from_mem($key);
		$mem = $mem['value'];
		
		echo '<pre>';
		var_dump($mem);
		echo '</pre>';
	}
	
	function clear_info(){
		clear_cache('user_info');
	}
	
	if(isset($_REQUEST['iadd'])){
		if($_REQUEST['iadd'] == 'clear'){
			clear_info();
		}
	}
	get_info();
?>