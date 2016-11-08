<?php
include_once '../../helper/mem.php';
include_once '../lib/excel.php';

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
	  $output_file = '../lib/data/data.xlsx';
	  
      if(empty($errors)==true){
         $result = move_uploaded_file($file_tmp, $output_file);
         echo "Success_" . $result . '_';
		 $result = read_xlsx('../lib/data/data.xlsx');
		 //echo '<pre>';
		 //var_dump($result);
		 //echo '</pre>';
		 store_to_mem('init_data', $result);
      }else{
         print_r($errors);
      }
   }
?>
<html>
   <body>
      
      <form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="data_file" />
         <input type="submit"/>
      </form>
      
   </body>
</html>