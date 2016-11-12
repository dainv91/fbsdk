<?php
include_once '../../helper/mem.php';
include_once '../lib/excel.php';
//include_once 'messenger_v2.php';
include_once 'msg_test_v2.php';

	//echo '<pre>';
	//var_dump($_REQUEST);
	//echo '</pre>';
	//exit();

	ini_set('max_execution_time', 300);	
	
	function get_selected_value_v2($data, $str_id_selected){
		$arr = explode('_', $str_id_selected);
		
		$obj_result = new stdclass();
		$obj_result->arr = array();
		$obj_result->text = '';
		foreach($arr as $payload){
			if($arr == ''){
				continue;
			}
			$obj = get_obj_by_id($data, $payload);
			if($obj != null){
				
				/*
				if($obj->p_id != '' && $obj->p_id != -1){
					$obj = get_obj_by_id($data, $obj->p_id);
				}
				*/
				
				// DEBUG
				if($obj->rank == RANK_HMENU_BTN || $obj->rank == RANK_BTN){
					$obj = get_obj_by_id($data, $obj->p_id);
				}
				if($obj->id == 1){
					continue;
				}
				
				
				$obj_result->arr[] = $payload;
				$obj_result->text .= '/ ' . $obj->title;
			}
		}
		return $obj_result;
	}
	
	function prepare_create_persistent_menu($page_id, $data, $is_update_menu){
		//$persistant_menu = get_data_by_rank($data, RANK_PERSISTANT_MENU);
		if($is_update_menu !== false){
			$result = create_persistent_menu($page_id, $data);
			echo '<pre>';
			var_dump($result);
			echo '</pre>';
		}
	}

	/*********************SENDING MESSAGE FUNCTION********************************/
	function get_list_conversation_from_json($json){
		$result = new stdclass();
		$result->data = array();
		
		$result->paging = new stdclass();
		$result->paging->previous = 'link1';
		$result->paging->next = 'link2';
		
		$obj_conversation = new stdclass();
		$obj_conversation->id = '';
		$obj_conversation->link = '';
		$obj_conversation->updated_time = '';
		
		$lst = json_decode($json);
		if(isset($lst) && isset($lst->data)){
			$data = $lst->data;
			if(is_array($data)){
				foreach($data as $obj){
					$result->data[] = $obj;
				}
			}
			
			if(isset($lst->paging)){
				while(true){
					//break;
					$link = $lst->paging->next;
					$result1 = send_http($link, array());
					$result1 = $result1['content'];
					
					$lst = json_decode($result1);
					if(isset($lst) && isset($lst->data)){
						$data = $lst->data;
						if(is_array($data) && count($data) > 0){
							foreach($data as $obj){
								$result->data[] = $obj;
							}
						}else{
							break;
						}
					}
				}
				//$result->paging = $lst->paging;
			}
		}
		return $result;
	}
	
	function get_user_id_from_list_msg($lst_msg_json, $page_id){
		$obj = json_decode($lst_msg_json);
		
		foreach($obj->data as $msg_obj){
			if($msg_obj->from->id == $page_id){
				continue;
			}
			return $msg_obj->from->id;
		}
		
		return null;
	}
	
	function get_list_user_id($page_id){
		//$page_id = '1681271828857371'; // Auto all;
		$acc_token = get_access_token($page_id);
		
		$result = call_send_api_get_list_conversations($page_id, $acc_token);
		//echo '<pre>';
		//var_dump($result);
		//echo '</pre>';
		//exit();
		$data = get_list_conversation_from_json($result);
		
		$user_ids = array();
		foreach($data->data as $conversation_obj){
			$result = call_send_api_get_list_msg_of_conversation($conversation_obj->id, $acc_token);
			$user_id = get_user_id_from_list_msg($result, $page_id);
			if($user_id == null){
				continue;
			}
			$user_ids[] = $user_id;
		}
		return $user_ids;
	}
	
	/*****************************************************************************/
	
	$lst_page_id = array();
	//$lst_page_id[] = '205662139870141'; // Demo khach san
	//$lst_page_id[] = '1681271828857371'; // Auto all
	
	$lst_page_id['205662139870141'] = 'Demo khách sạn'; // Demo khach san
	$lst_page_id['1681271828857371'] = 'Auto all'; // Auto all
	
	$lst_page_id['559437760920787'] = 'Vé máy bay';
	$lst_page_id['693558664140347'] = 'Hàng thời trang';
	$lst_page_id['1435247499821906'] = 'Trung tâm đào tạo';
	$lst_page_id['1137226739706218'] = 'Phòng khám';
	
	$lst_page_id['556256761138693'] = 'Học viện robotics';
	$lst_page_id['1591469367824430'] = 'Du học Hàn Quốc Wiscko';
	$lst_page_id['1645086179071672'] = 'David Health Vietnam';

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
	  $update_menu = false;
	  if(isset($_REQUEST['chkUpdateMenu'])){
		  if($_REQUEST['chkUpdateMenu'] == 'update_menu'){
			  $update_menu = true;
		  }
	  }
	  
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
		 
		 // must send request to create persistent menu
		 prepare_create_persistent_menu($uploaded_page_id, $result, $update_menu);
		 store_to_mem($data_key_name, $result);
      }else{
         print_r($errors);
		 //$is_success = $errors;
      }
   }
   
   if(isset($_REQUEST['txtSendMessage']) && isset($_REQUEST['txt_msg'])){
	   $msg = trim($_REQUEST['txt_msg']);
	   if($msg != ''){
		   $sent_result = '';
		   $lst_user_id = get_list_user_id($uploaded_page_id);
			echo '<pre>';
			var_dump($lst_user_id);
			echo '</pre>';
			exit();

		   foreach($lst_user_id as $user_id){
			   $result = send_text_message($uploaded_page_id, $user_id, $msg);
			   $sent_result = $sent_result . $result . '<br />';
		   }
		   $is_success = $uploaded_page_id .'_sent to '. count($lst_user_id) .' user <br />';
		   $is_success .= $sent_result;
	   }
	   //$is_success = $uploaded_page_id . '===' .$msg;
   }
   
   if(isset($_REQUEST['txtExport'])){
		$key = 'user_info';
		$mem = load_from_mem($key);
		$mem = $mem['value'];

		$data_key_name = 'init_data_' .$uploaded_page_id;
		$data = load_from_mem($data_key_name);
		$data = $data['value'];

		$result = array();
		if(isset($mem[$uploaded_page_id])){
			$page_data = $mem[$uploaded_page_id];
			foreach($page_data as $k => $v){
				$sender_id = $k;
				//$result[$sender_id] = $v;
				$result[$sender_id] = array();
				foreach($v as $info){
					$tmp = array();
					//$selected = $info['other'];
					//$obj_selected = get_selected_value_v2($data, $selected);
					//$obj_selected->text;
					//$result[$sender_id][] = $obj_selected->text;
					foreach($info as $key_info => $value_info){
						if($key_info == 'other'){
							continue;
						}
						//$result[$sender_id][$key_info] = $value_info;
						$tmp[$key_info] = $value_info;
					}
					$result[$sender_id][] = $tmp;
				}
			}
		}
		$now = date('Ymd_His');
		$file_name = "data_$now.xlsx";
		
		$data_to_export = new stdclass();
		$data_to_export->column = array();
		$data_to_export->row = array();
		foreach($result as $sender_id => $sender_data){
			if(!in_array('fb_id', $data_to_export->column)){
				$data_to_export->column[] = 'fb_id';
			}
			//$data_to_export->row[] = $sender_id;
			$tmp = array();
			foreach($sender_data as $obj_arr){
				$tmp = array();
				$tmp[] = $sender_id;
				foreach($obj_arr as $obj_key => $obj_data){
					if($obj_key == ''){
						continue;
					}
					if(!in_array($obj_key, $data_to_export->column)){
						$data_to_export->column[] = $obj_key;	
					}
					$tmp[] = $obj_data;
				}
				$data_to_export->row[] = $tmp;
			} // array of item ( $obj_arr => array ['ten'=>.., ['phone'] => ...)
		} // array of sender id (0 => arr, 1 => arr)
		/*
		echo '<pre>';
		var_dump($result);
		echo '=========================<br />';
		var_dump($data_to_export);
		echo '</pre>';
		exit();
		*/
		write_xlsx_to_download($data_to_export, $file_name);
		exit();
   }
   
   
?>
<html>
	<style>
		.space{
			color: red;
			font-weight: bold;
		}
	</style>
	<body>
		<div class="space">
			<?php
				if($is_success !== false){
					echo $is_success;
				}
			?>
		</div>
		<div>
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
					<label>Nhập nội dung tin nhắn: </label>
					<input type="text" name="txt_msg" />
				</div>
				<input type="submit" value="Gửi tin nhắn" name="txtSendMessage" />
				<input type="submit" value="Xuất dữ liệu" name="txtExport" />
			</form>
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
					<input type="checkbox" name="chkUpdateMenu" value="update_menu" />Update menu
				</div>
				<input type="submit"/>
			</form>
		</div>
	</body>
</html>
