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
	$obj->action = $column_arr[9];
	//$obj->show_statistic = $column_arr[10];
	$obj->id_next = $column_arr[10];
	if(isset($column_arr[11])){
		$obj->is_done = $column_arr[11];
	}
	if(isset($column_arr[12])){
		$obj->is_continue = $column_arr[12];
	}
	
	return $obj;
}

function set_column_auto($sheet, $max_col){
	$pos = 0;
	foreach(range('B', 'Z') as $char){
		if($pos >= $max_col){
			break;
		}
		$sheet->getColumnDimension($char)->setAutoSize(true);
		$pos++;
	}
}

function set_data_for_row($sheet, $data_of_row, $row, $max_col){
	$pos = 0;
	foreach(range('B', 'Z') as $char){
		if($pos > $max_col){
			break;
		}
		if(!isset($data_of_row[$pos])){
			break;
		}
		$value = $data_of_row[$pos];
		$index = $char . $row;
		$sheet->setCellValue($index, $value);
		$pos++;
	}
}

function write_xlsx($data, $file_name){
	/**
	* Format data
	*
	object(stdClass)#86 (2) {
	  ["column"]=>
	  array(5) {
		[0]=>
		string(5) "fb_id"
		[1]=>
		string(12) "loại bánh"
		[2]=>
		string(13) "số lượng"
		[3]=>
		string(5) "Tên:"
		[4]=>
		string(21) "Số điện thoại:"
	  }
	  ["row"]=>
	  array(1) {
		[0]=>
		array(5) {
		  [0]=>
		  int(1305653869458699)
		  [1]=>
		  NULL
		  [2]=>
		  string(9) "3 chiếc"
		  [3]=>
		  string(7) "Van Dai"
		  [4]=>
		  string(10) "0989152588"
		}
	  }
	}
	*
	*/
	
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("iadd")
								 ->setLastModifiedBy("iadd")
								 ->setTitle("Export data")
								 ->setSubject("Export data")
								 ->setDescription("Export data")
								 ->setKeywords("iadd export")
								 ->setCategory("Export data");


	// Add some data
	//echo date('H:i:s') , " Add some data" , EOL;
	$activeSheet = $objPHPExcel->setActiveSheetIndex(0);
	$activeSheet->getStyle('B1:Z200')->getAlignment()->setWrapText(true);

	$max_col = 0;
	$i = 0;
	foreach(range('B', 'Z') as $char){
		if(!isset($data->column[$i])){
			break;
		}
		$index = $char . '1';
		$value = $data->column[$i];
		$activeSheet->setCellValue($index, $value);
		$i++;
		$max_col++;
	}
	set_column_auto($activeSheet, $max_col);

	$i_break = false;
	foreach($data->row as $row_num => $obj_arr){
		set_data_for_row($activeSheet, $obj_arr, 2 + $row_num, $max_col);
	}
	/*
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Hello')
				->setCellValue('B2', 'world!')
				->setCellValue('C1', 'Hello')
				->setCellValue('D2', 'world!');
	*/
	/*
	// Miscellaneous glyphs, UTF-8
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A4', 'Miscellaneous glyphs')
				->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
	*/			
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($file_name);
	//$objWriter->save('php://output');
}

function write_xlsx_to_download($data, $file_name){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=$file_name");
		header("Content-Transfer-Encoding: binary ");
		write_xlsx($data, 'php://output');
}

function read_xlsx($input_file){
	$arr_data = array();
	$input_file_name = $input_file;
	try{
		$obj_php_excel = PHPExcel_IOFactory::load($input_file_name);
	}catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
		return null;
	}
	
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
			//getCalculatedValue $value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
			//$arr_data[$row-2][$col]=$value;
			$col_arr[] = $value;
		}
		$obj = conver_column_arr_to_obj($col_arr);
		if($obj){
			$arr_data[] = $obj;
		}
	}
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