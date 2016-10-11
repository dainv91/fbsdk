<?php
	require_once 'helper/mem.php';
	function cache(){
		$now = time();
		$value = load_from_mem('time');
		if($value === false){
			store_to_mem('time', $now);
		}else{
			// Exists
			//clear_cache('time');
		}
		echo '<pre>';
		var_dump($value);
		//clear_cache();
		echo '</pre>';
	}
	
	cache();
?>
