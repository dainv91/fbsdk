<?php
include_once '../../helper/mem.php';
include_once '../lib/excel.php';

	$lst_page_id = array();
	//$lst_page_id[] = '205662139870141'; // Demo khach san
	//$lst_page_id[] = '1681271828857371'; // Auto all
	
	$lst_page_id['205662139870141'] = 'Demo khách sạn'; // Demo khach san
	$lst_page_id['1681271828857371'] = 'Auto all'; // Auto all

	$uploaded_page_id = '';
	$is_success = false;
	
	if(isset($_REQUEST['sl_pages'])){
		$sl_page = $_REQUEST['sl_pages'];
		if(isset($lst_page_id[$sl_page])){
			$uploaded_page_id = $sl_page;
		}else{
			exit('Invalid page');
		}
	}
	
   if(isset($_FILES['data_file'])){
      $errors= array();
      $file_name = $_FILES['data_file']['name'];
      $file_size =$_FILES['data_file']['size'];
	  
      $file_tmp =$_FILES['data_file']['tmp_name'];
	  //echo $file_tmp;
      $file_type=$_FILES['data_file']['type'];
	  $arr_file_name = explode('.', $file_name);
      $file_ext=strtolower(end($arr_file_name));
      
      $expensions= array("xls","xlsx");
      
      if(in_array($file_ext,$expensions)=== false){
         $errors[]="extension not allowed, please choose a xls or xlsx file.";
      }
      //$output_file = __FILE__ .'/../lib/data/data.xlsx';
	  //$output_file = '../lib/data/data.xlsx';
	  $output_file = "../lib/data/$uploaded_page_id.xlsx";
	  
      if(empty($errors)==true){
         $result = move_uploaded_file($file_tmp, $output_file);
		 $is_success = "Success_" . $result . '_';
         //echo "Success_" . $result . '_';
		 //$result = read_xlsx('../lib/data/data.xlsx');
		 $result = read_xlsx($output_file);
		 
		 if($result == null){
			 exit();
		 }
		 $data_key_name = 'init_data_' .$uploaded_page_id;
		 store_to_mem($data_key_name, $result);
      }else{
         print_r($errors);
      }
   }
?>
<html>
	<body>
		<div class="space">
			<?php
				if($is_success !== false){
					echo $is_success;
				}
			?>
		</div>
		<div class="content">
			<form action="" method="POST" enctype="multipart/form-data">
				<div>
					<label>Chọn page:</label>
					<select name="sl_pages" class="input">
					<?php foreach($lst_page_id as $p_k => $p_v) {?>
					<option value="<?php echo $p_k; ?>"><?php echo $p_v; ?></option>
					<?php }?>
					</select>
				</div>
				<div>
					<label>Chọn file dữ liệu:</label>
					<input type="file" name="data_file" />
				</div>
				<input type="submit"/>
			</form>
		</div>
	</body>
</html>