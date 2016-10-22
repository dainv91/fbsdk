<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/./Classes/PHPExcel.php';

function conver_column_arr_to_obj($column_arr){
	if(!$column_arr[0] || !$column_arr[1]){
		return null;
	}
	
	$obj = new stdclass();
	$obj->id = $column_arr[0];
	$obj->title = $column_arr[1];
	$obj->level = $column_arr[2];
	//$obj->is_leaf = $column_arr[3];
	$obj->p_id = $column_arr[3];
	$obj->price = $column_arr[4];
	$obj->image = $column_arr[5];
	$obj->description = $column_arr[6];
	//$obj->action = $column_arr[8];
	$obj->rank = $column_arr[7]; // action: 1 - text, 2 - button, 3 - menu ngang. 4 - quick reply
	$obj->save_info = $column_arr[8];
	return $obj;
}

function read_xlsx($input_file){
	$arr_data = array();
	$input_file_name = $input_file;
	$obj_php_excel = PHPExcel_IOFactory::load($input_file_name);
	
	$total_sheets=$obj_php_excel->getSheetCount();
 
	//$allSheetName=$objPHPExcel->getSheetNames();
	$objWorksheet  = $obj_php_excel->setActiveSheetIndex(0);
	$highestRow    = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
	for ($row = 2; $row <= $highestRow;++$row)
	{
		/*
		for ($col = 0; $col <$highestColumnIndex;++$col)
		{
			$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			$arr_data[$row-2][$col]=$value;
		}
		*/
		$col_arr = array();
		for($col = 0; $col < $highestColumnIndex; ++$col){
			$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			//$arr_data[$row-2][$col]=$value;
			$col_arr[] = $value;
		}
		$obj = conver_column_arr_to_obj($col_arr);
		if($obj){
			$arr_data[] = $obj;
		}
	}
	//echo '<pre>';
	//var_dump($arr_data);
	//echo '</pre>';
	return $arr_data;
}

function test_read_xlsx(){
	$input_file_name = './data.xlsx';
	$arr_data = read_xlsx($input_file_name);
	echo '<pre>';
	var_dump($arr_data);
	echo '</pre>';
}

//test_read_xlsx();

?>