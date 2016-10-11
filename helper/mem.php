<?php
	/**
	* Method to store, retrieve from memory
	*
	* Author: dainv
	* Link: https://github.com/dainv91
	*/


	function check_existed($key){
		$value = apc_fetch($key);
		if($value === false){
			return false;
		}else{
			return true;
		}
	}

	function store_to_mem($key, $value){
		$now = time();
		$arr = array(
			'time' => $now,
			'value' => $value,
		);
		apc_store($key, $arr);
	}
	
	function load_from_mem($key){
		$arr = apc_fetch($key);
		return $arr;
	}
	
	function clear_cache($cache_type = null){
		if($cache_type != null){
			apc_delete($cache_type);
		}else{
			apc_clear_cache('user');
		}
	}
	
?>