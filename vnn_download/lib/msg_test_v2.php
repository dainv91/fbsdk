<?php
	include_once('messenger_v2.php');

	define('RANK_TEXT', 1);
	define('RANK_BTN', 2);
	define('RANK_HMENU', 3);
	define('RANK_QUICK_REPLY', 4);
	define('RANK_HMENU_BTN', 5);
	define('RANK_STATISTIC', 8);
	define('RANK_KEYWORD', 9);
	
	define('ACTION_BTN_URL', 6);
	define('ACTION_BTN_CALL', 7);
	
	function get_leaf_from_data($data){
		$result = array();
		foreach($data as $obj){
			if($obj->is_leaf == false){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function get_max_level_of_leaves($leaves){
		$max = 0;
		foreach($leaves as $leaf){
			//if($leaf->is_leaf && ($leaf->level > $max)){
			if($leaf->level > $max){
				$max = $leaf->level;
			}
		}
		return $max;
	}
	
	function get_menu_by_level($data, $level, $not_accept_text = false){
		$result = array();
		foreach($data as $obj){
			if($not_accept_text != false){
				if($obj->rank == RANK_TEXT){
					continue;
				}
			}
			if($obj->level != $level){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function get_child_by_parent_id($data, $parent_id){
		$result = array();
		foreach($data as $obj){
			//if($obj->p_id != $parent_id || $obj->rank == RANK_TEXT){
			if($obj->p_id != $parent_id){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function remove_rank_text_from_list_child($menus, $rank_to_remove = RANK_TEXT){
		$result = array();
		foreach($menus as $obj){
			if($obj->rank == $rank_to_remove){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function get_title_of_level($data, $level){
		$data_of_level = get_menu_by_level($data, $level);
		$title = '';
		foreach($data_of_level as $obj){
			if($obj->rank != RANK_TEXT){
				continue;
			}
			$title = $obj->title;
			break;
		}
		return $title;
	}
	function get_obj_by_id_v2($data, $id){
		$title = '';
		foreach($data_of_level as $obj){
			if($obj->rank != RANK_TEXT || $obj->id != $id){
				continue;
			}
			$title = $obj;
			break;
		}
		return $title;
	}
	
	function get_rank_action_of_menu($menus){
		$rank_action = RANK_BTN;
		if($menus == '' || !is_array($menus)){
			return RANK_TEXT;
		}
		
		foreach($menus as $obj){
			if($obj){
				$rank_action = $obj->rank;
				break;
			}
		}
		return $rank_action;
	}
	
	function get_data_by_rank($menus, $rank_action){
		$result = array();
		foreach($menus as $obj){
			if($obj->rank != $rank_action){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function set_payload_for_button($arr_btn, $payload){
		/*
		foreach($arr_btn as $btn){
			$btn->payload .= '_'. $payload;
		}
		*/
	}
	
	function clone_arr_obj($arr_obj){
		$result = array();
		foreach($arr_obj as $obj){
			$result[] = clone $obj;
		}
		return $result;
	}
	
	function get_level_need_save_info($data){
		$arr_level = array();
		foreach($data as $obj){
			if($obj->save_info !=null){
				$arr_level[$obj->level] = $obj->save_info;
			}
		}
		return $arr_level;
	}
	
	function get_id_need_save_info($data){
		$arr_level = array();
		foreach($data as $obj){
			if($obj->save_info !=null){
				$arr_level[$obj->id] = $obj->save_info;
			}
		}
		return $arr_level;
	}
	
	function toggle_user_is_done($sender_id){
		// normal = 0 => 2
		// done = 1 => 3
		$key_user_is_done = 'user_is_done';
		$mem = load_from_mem($key_user_is_done);
		
		//write_file('call2.txt', 'ALL_IS_DONE: ' .var_dum_to_string($mem), false);
		
		$arr = array();
		if($mem === false){
			$arr[$sender_id] = 2;
		}else{
			$mem = $mem['value'];
			//$arr = $mem[$sender_id];
			$arr = $mem;
			
			$is_done = $arr[$sender_id];
			if($is_done == 3){
				$is_done = 2;
			}else{
				$is_done = 3;
			}
			$arr[$sender_id] = $is_done;
		}
		store_to_mem($key_user_is_done, $arr);
	}
	
	function check_user_is_done($sender_id){
		$is_done = false;
		
		$key_user_is_done = 'user_is_done';
		$mem = load_from_mem($key_user_is_done);
		write_file('call2.txt', 'ALL_IS_DONE: ' .var_dum_to_string($mem), false);
		if($mem !== false){
			$mem = $mem['value'];
			//$arr = $mem[$sender_id];
			$arr = $mem;
			$is_done_value = -1;
			if(isset($arr[$sender_id])){
				$is_done_value = $arr[$sender_id];
			}
			write_file('call2.txt', $sender_id .'_IS_DONE_VALUE: ' .var_dum_to_string($arr), false);
			if($is_done_value == 3){
				$is_done = true;
				toggle_user_is_done($sender_id);
			}
		}
		return $is_done;
	}
	
	function save_data_for_user($sender_id, $key, $value, $is_append = false){
		write_file('call.txt', $key .'__CALLL_SAVE____' .var_dum_to_string($value), false);
		$key_info = 'user_info';
		$mem = load_from_mem($key_info);
		
		$arr = array();
		$info = array();
		$tmp = array();
		
		$tmp[$key] = $value;
		$info[] = $tmp;
		$arr[$sender_id] = $info;
		
		if($mem === false){
		// Not existed
			store_to_mem($key_info, $arr);
		}else{
			// Existed
			$arr = $mem['value'];
			$tmp = $arr[$sender_id];
			//$info = $tmp[0];
			$index = max(array_keys($tmp));
			
			$user_is_done = check_user_is_done($sender_id);
			write_file('call2.txt', 'IS_DONE: ' .var_dum_to_string($user_is_done), false);
			if($user_is_done == true){
				$info = $tmp[$index + 1];
			}else{
				$info = $tmp[$index];
			}
		
			if($is_append != false){
				// append
				$info[$key] = $info[$key]. '_' .$value;
			}else{
				$info[$key] = $value;
			}		
			
			if($user_is_done == true){
				$tmp[$index + 1] = $info;
			}else{
				$tmp[$index] = $info;
			}
			
			$arr[$sender_id] = $tmp;
			store_to_mem($key_info, $arr);
		}
	}
	
	function save_data_for_user_v2($sender_id, $value){
		save_data_for_user($sender_id, 'other', $value, true);
	}
	
	function get_level_of_payload($data, $payload){
		if(is_numeric($payload)){
			foreach($data as $obj){
				if($obj->id == $payload){
					return $obj->level;
				}
			}
		}
		return -1;
	}
	
	function check_action_type_of_button($btn_action){
		$needle = 'http';
		$len = strlen($needle);
		
		if(substr($btn_action, 0, $len) === $needle){
			// url button
			return ACTION_BTN_URL;
		}
		$phone = str_replace(' ', '', $btn_action);
		$phone = str_replace('+', '', $phone);
		$pattern = '/^[0-9]{9,12}$/';
		if(preg_match($pattern, $phone)){
			// call button
			return ACTION_BTN_CALL;
		}
		return 0;
	}
	

	function get_obj_by_id($data, $id){
		foreach($data as $obj){
			if($obj->id == $id){
				return $obj;
			}
		}
		return null;
	}
	
	function get_selected_value($data, $str_id_selected){
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

	function check_level_need_statistic($data, $level){
		foreach($data as $obj){
			if($obj == null || $obj->level != $level){
				continue;
			}
			if($obj->show_statistic != null && $obj->show_statistic == 1){
				return true;
			}
		}
		return false;
	}
	
	function get_first_element_of_arr($data){
		foreach($data as $obj){
			return $obj;
		}
	}
	
	function check_keyword_in_obj($obj_kw, $keyword){
		if($obj_kw == null){
			return false;
		}
		$lst_kw = $obj_kw->title;
		write_file('call3.txt', 'KW: ' .$lst_kw, false);
		/*
		if(stripos($lst_kw, $keyword) !== false){
			return true;
		}
		*/
		$kws = explode(',', $lst_kw);
		if(is_array($kws)){
			foreach($kws as $kw){
				if($kw == ''){
					continue;
				}
				
				$kw = trim($kw);
				if($kw == $keyword){
					return true;
				}
			}
		}
		return false;
	}
	
	function save_current_data($sender_id, $current_data){
		$current_data_str = '_current_data';
		msg_thread_status_set($sender_id . $current_data_str, $current_data);
	}
	function get_saved_data($sender_id){
		$current_data_str = '_current_data';
		$mem = msg_thread_status_get($sender_id . $current_data_str);
		//write_file('call.txt', var_dum_to_string($mem), false);
		if($mem != false){
			return $mem['value'];
		}
		return '';
	}
	
	function show_menu_by_id($data, $id, $obj_id, $msg){
		$sender_id = $obj_id->sender_id;
		$recipient_id = $obj_id->recipient_id;
		
		$current_data = new stdclass();
		$previous_obj = '';
		
		//$previous_data = get_saved_data($sender_id);
		//write_file('call.txt', 'ID: ' .$id .'_MSG____' .$msg, false);
		if(is_numeric($id)){
			// ** 1. Must save current data for next leve **
			$current_data->id_previous = $id;
			
			// DEBUG
			$previous_data = get_saved_data($sender_id);
			if($previous_data != ''){
				//$id_next = $previous_data->id_next;
				$previous_obj = $previous_data->data_previous;
				//$id = $id_next;
			}
		}else{
			// ** 2. Must get previous data and processing... **
			$previous_data = get_saved_data($sender_id);
			if($previous_data != ''){
				$id_next = $previous_data->id_next;
				$previous_obj = $previous_data->data_previous;
				$id = $id_next;
			}
		}
		
		
		$title = '';
		$obj = get_obj_by_id($data, $id);
		if($obj == '' || $obj->title == ''){
			$title = 'Mời bạn chọn danh mục';
		}else{
			$title = $obj->title;
			
		}
		//return var_dum_to_string($obj);
		
		if($obj == null){
			return 'aaaaa_bbbbb';
		}
		// Check obj is done to toggle_user_is_done
		if(isset($obj->is_done) && $obj->is_done != ''){
			write_file('call2.txt', $obj->id .'_IS_DONE_from_obj: ' .$obj->is_done, false);
			toggle_user_is_done($sender_id);
		}
		
		// *****************Save info ******************
		$arr_id_need_save_info = get_id_need_save_info($data);
		foreach($arr_id_need_save_info as $key => $value){
			if($previous_obj != ''){
				if($key == $previous_obj->id){
					save_data_for_user($sender_id, $value, $msg);
					break;
				}
				//write_file('call.txt', $key .'__CA111____' .var_dum_to_string($previous_obj), false);
			}else{
				//write_file('call.txt', $key .'__CALLL____' .var_dum_to_string($value), false);
			}
			/*
			if($key == $id){
				save_data_for_user($sender_id, $value, $msg);
				break;
			}
			*/
		}
		// ***************END SAVE INFO******************
		
		// ** 1. Must save current data for next leve **
		$current_data->id_previous = $obj->id;
		$current_data->data_previous = $obj;
		$current_data->id_next = $obj->id_next;
		save_current_data($sender_id, $current_data);
		
		//return var_dum_to_string($current_data);
		
		if($obj->id_next != null && $obj->rank != RANK_TEXT && $obj->rank != RANK_STATISTIC){
			/*
			// ** 1. Must save current data for next leve **
			$current_data->id_previous = $obj->id;
			$current_data->data_previous = $obj;
			$current_data->id_next = $obj->id_next;
			save_current_data($sender_id, $current_data);
			*/
			
			return show_menu_by_id($data, $obj->id_next, $obj_id, $msg);
		}else{
			// get list child
			$menus = get_child_by_parent_id($data, $obj->id);
			if(count($menus) == 1){
				$obj = '';
				foreach($menus as $menu){
					$obj = $menu;
					break;
				}
				if($obj->rank == RANK_TEXT){
					$title = $obj->title;
					$menus = get_child_by_parent_id($data, $obj->id);
				}
			}else{
				// Remove rank text from child menu
				//$menus = remove_rank_text_from_list_child($menus);
			}
			//return var_dum_to_string($menus);
			//return 'iadd';
			$rank_action = get_rank_action_of_menu($menus);

			if($obj->rank == RANK_STATISTIC){
				$rank_action = RANK_STATISTIC;
			}
			
			$data_of_level = new stdclass();
			$data_of_level->title = $title;
			$data_of_level->sender_id = $sender_id;
			$data_of_level->recipient_id = $recipient_id;
			//$data_of_level->payload = $payload;
			$data_of_level->data = $menus;
			//$data_of_level->data_by_level = $data_by_level;
			//$data_of_level->level = $level;
			$data_of_level->all_data = $data;
			
			//write_file('call3.txt',$rank_action . '++'. $title . '######' . var_dum_to_string($menus), false);
			write_file('call3.txt','RANK: '. $rank_action . '++'. $title . '######', false);
			//write_file('call3.txt', var_dum_to_string($data_of_level), false);
			return show_menu_by_type_v2($data_of_level, $rank_action);
		}
		
		
		
	}
	
	function msg_test($input){
		$entry = json_decode($input);
		$entry = $entry->entry[0];
		
		// message
		$messaging = $entry->messaging[0];
		if($messaging){
			$message = $messaging->message;	
		}
		$changes = null;
		//return var_dum_to_string($entry);
		if($message){			
			if($message->is_echo){
				// page reply => do not check
			} else if($message->attachments){
				$sender_id = $messaging->sender->id;
				
				$attach = $message->attachments;
				$count = count($attach);
				if($count > 1){
					// multiple attachment
					$msg = 'Đừng cậy nhiều ảnh với anh nhớ :-ww';
					send_text_message($recipient_id, $sender_id, $msg);
					
					/*
					$imgs_arr = array();
					foreach($attach as $att){
						$imgs_arr[] = $att->payload->url;
					}
					send_attachment($sender_id, $imgs_arr);
					*/
				}else{
					// one attach ment
					$attach = $message->attachments[0];
					$img_url = $attach->payload->url;
					send_attachment($sender_id, $img_url);
				}
			}else{
				// nguoi dung chat
				$sender_id = $messaging->sender->id;
				$msg = $message->text;
				$payload = '';
				
				if($messaging->postback){
					$payload = $messaging->postback->payload;
				}else if($message->quick_reply){
					$payload = $message->quick_reply->payload;
				}
				//write_file('call3.txt', $payload, false);
				
				if($msg == ''){
					$msg = 'Chịu chịu';
					$msg = private_process($msg);
					return send_text_message($recipient_id, $sender_id, $msg);
				}
				$msg = private_process($msg);
				//return $sender_id;
				//send_text_message($recipient_id, $sender_id, $msg);
				//return send_button_template_test($sender_id);
				return process_msg_with_payload($sender_id, $msg, $payload);
			}
			if($messaging->delivery || $messaging->read){
				return 'thang thai';
			}
		}else if($messaging->postback){
			// user choose postback
			$sender_id = $messaging->sender->id;
			$payload = $messaging->postback->payload;
			
			return process_msg_with_payload($sender_id, $msg, $payload);
		} else {
			$changes = $entry->changes;
			if($changes){
				$change = $changes[0];
				$value = $change->value;
				
				// cmt	
				if($change->field == 'feed'){
					$comment_id = $value->comment_id;
					$message = $value->message;
					
					$msg = "Ê, ai cho mày comment: $message ?";
					reply_cmt($comment_id, $msg);
				}else if($change->item == 'like'){
					$parent_id = $value->post_id;
					return reply_cmt($parent_id, 'hehe');
				}
			}
		}
	}
	
	function msg_test_v2($input){
		$entry = json_decode($input);
		if($input == ''){
			return 'empty_obj';
		}
		$entry = $entry->entry[0];
		
		// message
		if(isset($entry->messaging)){
			$messaging = $entry->messaging[0];
		}
		if(isset($messaging)){
			if(isset($messaging->message)){
				$message = $messaging->message;		
			}
		}
		$changes = null;
		//return var_dum_to_string($entry);
		$obj_id = new stdclass();
		
		if(isset($message)){
			if(isset($message->is_echo)){
				// page reply => do not check
			} else if(isset($message->attachments)){
				$sender_id = $messaging->sender->id;
				$recipient_id = $messaging->recipient->id;
				
				$attach = $message->attachments;
				$count = count($attach);
				if($count > 1){
					// multiple attachment
					$msg = 'Đừng cậy nhiều ảnh với anh nhớ :-ww';
					send_text_message($recipient_id, $sender_id, $msg);
					
					/*
					$imgs_arr = array();
					foreach($attach as $att){
						$imgs_arr[] = $att->payload->url;
					}
					send_attachment($sender_id, $imgs_arr);
					*/
				}else{
					// one attach ment
					$attach = $message->attachments[0];
					$img_url = $attach->payload->url;
					send_attachment($sender_id, $img_url);
				}
			}else{
				// nguoi dung chat
				$sender_id = $messaging->sender->id;
				$recipient_id = $messaging->recipient->id;
				
				$msg = $message->text;
				$payload = '';
				
				if(isset($messaging->postback)){
					$payload = $messaging->postback->payload;
				}else if(isset($message->quick_reply)){
					$payload = $message->quick_reply->payload;
				}
				//write_file('call3.txt', $payload, false);
				
				if($msg == ''){
					$msg = 'Chịu chịu';
					$msg = private_process($msg);
					return send_text_message($recipient_id, $sender_id, $msg);
				}
				$msg = private_process($msg);
				//return $sender_id;
				//send_text_message($recipient_id, $sender_id, $msg);
				//return send_button_template_test($sender_id);
				
				$obj_id->sender_id = $sender_id;
				$obj_id->recipient_id = $recipient_id;
				return process_msg_with_payload_v2($obj_id, $msg, $payload);
			}
			if(isset($messaging->delivery) || isset($messaging->read)){
				return 'thang thai';
			}
		}else if(isset($messaging->postback)){
			// user choose postback
			$sender_id = $messaging->sender->id;
			$recipient_id = $messaging->recipient->id;
			
			$payload = $messaging->postback->payload;
			
			$obj_id->sender_id = $sender_id;
			$obj_id->recipient_id = $recipient_id;
			return process_msg_with_payload_v2($obj_id, $msg, $payload);
		} else {
			if(isset($entry->changes)){
				$changes = $entry->changes;	
			}
			if($changes){
				$change = $changes[0];
				$value = $change->value;
				
				// cmt	
				if($change->field == 'feed'){
					$comment_id = $value->comment_id;
					$message = $value->message;
					
					$msg = "Ê, ai cho mày comment: $message ?";
					reply_cmt($comment_id, $msg);
				}else if(isset($change->item) && $change->item == 'like'){
					$parent_id = $value->post_id;
					return reply_cmt($parent_id, 'hehe');
				}
			}
		}
	}
	
	//function show_menu_by_type($sender_id, $data, $level, $type_rank){
	function show_menu_by_type($data_of_level, $type_rank, $level){
		//return 'by_type';
		if($type_rank != RANK_TEXT){
			if($data_of_level->data == null || count($data_of_level->data) == 0){
				return show_menu_by_type($data_of_level, RANK_TEXT, $level);
			}
		}
		switch($type_rank){
			case RANK_TEXT:
				//echo 'RANK_TEXT';
				$need_check = check_level_need_statistic($data_of_level->all_data, $level);
				$need_check = false;
				if($need_check != false){
					send_text_message($recipient_id, $data_of_level->sender_id, $data_of_level->title);
					$type_rank = RANK_STATISTIC;
					return show_menu_by_type($data_of_level, $type_rank, $level);
				}else{
					return send_text_message($recipient_id, $data_of_level->sender_id, $data_of_level->title);	
				}
				break;
			case RANK_BTN:
				//echo 'RANK_BTN';
				$buttons_obj_arr = array();
				$i = 0;
				foreach($data_of_level->data as $menu){
					$obj = new stdclass();
					$obj->type = 'postback';
					$obj->title = $menu->title;
					$obj->payload = $menu->id;
					$buttons_obj_arr[] = $obj;
					if($i++ >= 2){
						break;
					}
				}
				return send_button_template($recipient_id, $data_of_level->sender_id, $data_of_level->title, $buttons_obj_arr);
				break;
			case RANK_HMENU:
				$elements_obj_arr = array();
				
				$btns = get_data_by_rank($data_of_level->data_by_level, RANK_HMENU_BTN);
				foreach($data_of_level->data as $leaf){
					if($leaf->rank != RANK_HMENU){
						continue;
					}
					// Element
					$obj = new stdclass();
					$obj->title = $leaf->title;
					$obj->item_url = $leaf->item_url;
					//$obj->image_url = $leaf->image_url;
					$obj->image_url = $leaf->image;
					//$obj->subtitle = $leaf->description;
					$obj->subtitle = 'Giá: '. $leaf->price;
					$btn_arr_obj = array();
					// init btn option
					if($btns != null && count($btns) > 0){
						$i = 0;
						foreach($btns as $btn){
							if($btn->p_id != $leaf->id){
								continue;
							}
							$obj_btn = new stdclass();
							
							// check type of button action
							$btn_action = $btn->action;
							$action = 0;
							if($btn_action){
								$action = check_action_type_of_button($btn_action);
							}
							// fix action = 0
							//$action = 0;
							if($action != 0){
								$title_len = strlen($btn->title);
								$title = $btn->title;
								if($title_len > 19){
									$title = substr($btn->title, 0, 19);
								}
								switch($action){
									case ACTION_BTN_CALL:
										$obj_btn->type = 'phone_number';
										$obj_btn->title = $title;
										$obj_btn->payload = $btn_action;
										break;
									case ACTION_BTN_URL:
										$obj_btn->type = 'web_url';
										$obj_btn->title = $title;
										$obj_btn->url = $btn_action;
										$obj_btn->webview_height_ratio = 'full'; // full - compact - tall
										break;
								}
							}else{
								// postback button
								$obj_btn->type = 'postback';
								$obj_btn->title = $btn->title;
								$obj_btn->payload = $btn->id;
							}
							
							$btn_arr_obj[] = $obj_btn;
							if($i++ >= 10){
								break;
							}
						}
					}
					
					$btn_arr_clone = clone_arr_obj($btn_arr_obj);
					if(count($btn_arr_clone) == 0){
						// Skip item has no button
						continue;
					}
					//return var_dum_to_string($btn_arr_clone);
					set_payload_for_button($btn_arr_clone, $leaf->id);
					
					$obj->buttons = $btn_arr_clone;
					
					$elements_obj_arr[] = $obj;
				}
				//return var_dum_to_string($elements_obj_arr);
				return send_generic_template($recipient_id, $data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
				break;
			case RANK_QUICK_REPLY:
				$i = 0;
				$elements_obj_arr = array();
				foreach($data_of_level->data as $menu){
					$obj = new stdclass();
					$obj->content_type = 'text';
					$obj->title = $menu->title;
					//$obj->payload = $menu->p_id .'_'. $menu->id;
					$obj->payload = $menu->id;
					//$obj->image_url = '';
					$elements_obj_arr[] = $obj;
					if($i++ >= 9){
						break;
					}
				}
				//return var_dum_to_string($elements_obj_arr);
				//return $data_of_level->title;
				return send_quick_replies($recipient_id, $data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
				break;
			case RANK_HMENU_BTN:
				
				break;
			case RANK_STATISTIC:
				$key = 'user_info';
				$mem = load_from_mem($key);
				$mem = $mem['value'];
				
				$selected = $mem[$data_by_level->sender_id]->other;
				$obj_selected = get_selected_value($all_data, $selected);
				
				$text = 'Bạn vừa chọn: ' . $obj_selected->text;
				send_text_message($recipient_id, $data_of_level->sender_id, $text);
				break;
		}
	}
	
	function show_menu_by_type_v2($data_of_level, $type_rank){
		$sender_id = $data_of_level->sender_id;
		$recipient_id = $data_of_level->recipient_id;
		
		//return 'by_type';
		if($type_rank != RANK_TEXT && $type_rank != RANK_STATISTIC){
			if($data_of_level->data == null || count($data_of_level->data) == 0){
				return show_menu_by_type_v2($data_of_level, RANK_TEXT);
			}
		}
		switch($type_rank){
			case RANK_TEXT:
				//echo 'RANK_TEXT';
				//$need_check = check_level_need_statistic($data_of_level->all_data, $level);
				$need_check = false;
				if($need_check != false){
					send_text_message($recipient_id, $data_of_level->sender_id, $data_of_level->title);
					$type_rank = RANK_STATISTIC;
					return show_menu_by_type_v2($data_of_level, $type_rank);
				}else{
					return send_text_message($recipient_id, $data_of_level->sender_id, $data_of_level->title);	
				}
				break;
			case RANK_BTN:
				//echo 'RANK_BTN';
				$buttons_obj_arr = array();
				$i = 0;
				foreach($data_of_level->data as $menu){
					/*
					$obj = new stdclass();
					$obj->type = 'postback';
					$obj->title = $menu->title;
					$obj->payload = $menu->id;
					$buttons_obj_arr[] = $obj;
					if($i++ >= 2){
						break;
					}
					*/
					
					$obj = new stdclass();
							
					// check type of button action
					$btn_action = $menu->action;
					$action = 0;
					if($btn_action != null){
						$action = check_action_type_of_button($btn_action);
					}
					// fix action = 0
					//$action = 0;
					if($action != 0){
						$title_len = strlen($menu->title);
						$title = $menu->title;
						if($title_len > 19){
							$title = substr($menu->title, 0, 19);
						}
						switch($action){
							case ACTION_BTN_CALL:
								$obj->type = 'phone_number';
								$obj->title = $title;
								$obj->payload = $btn_action;
								break;
							case ACTION_BTN_URL:
								$obj->type = 'web_url';
								$obj->title = $title;
								$obj->url = $btn_action;
								$obj->webview_height_ratio = 'full'; // full - compact - tall
								break;
						}
					}else{
						// postback button
						$obj->type = 'postback';
						$obj->title = $menu->title;
						$obj->payload = $menu->id;
					}
					$buttons_obj_arr[] = $obj;
					if($i++ >= 2){
						break;
					}
				}
				return send_button_template($recipient_id, $data_of_level->sender_id, $data_of_level->title, $buttons_obj_arr);
				break;
			case RANK_HMENU:
				$elements_obj_arr = array();
				
				//$btns = get_data_by_rank($data_of_level->data_by_level, RANK_HMENU_BTN);
				$btns = get_data_by_rank($data_of_level->all_data, RANK_HMENU_BTN);
				foreach($data_of_level->data as $leaf){
					if($leaf->rank != RANK_HMENU){
						continue;
					}
					// Element
					$obj = new stdclass();
					$obj->title = $leaf->title;
					//$obj->item_url = $leaf->item_url;
					if(isset($leaf->action)){
						$obj->item_url = $leaf->action;
					}
					//$obj->image_url = $leaf->image_url;
					$obj->image_url = $leaf->image;
					//$obj->subtitle = $leaf->description;
					//$obj->subtitle = 'Giá: '. $leaf->price;
					if($leaf->price != null){
						$obj->subtitle = 'Giá: '. $leaf->price;
					}
					$btn_arr_obj = array();
					// init btn option
					if($btns != null && count($btns) > 0){
						$i = 0;
						foreach($btns as $btn){
							if($btn->p_id != $leaf->id){
								continue;
							}
							$obj_btn = new stdclass();
							
							// check type of button action
							$btn_action = $btn->action;
							$action = 0;
							if($btn_action != null){
								$action = check_action_type_of_button($btn_action);
							}
							// fix action = 0
							//$action = 0;
							if($action != 0){
								$title_len = strlen($btn->title);
								$title = $btn->title;
								if($title_len > 19){
									$title = substr($btn->title, 0, 19);
								}
								switch($action){
									case ACTION_BTN_CALL:
										$obj_btn->type = 'phone_number';
										$obj_btn->title = $title;
										$obj_btn->payload = $btn_action;
										break;
									case ACTION_BTN_URL:
										$obj_btn->type = 'web_url';
										$obj_btn->title = $title;
										$obj_btn->url = $btn_action;
										$obj_btn->webview_height_ratio = 'full'; // full - compact - tall
										break;
								}
							}else{
								// postback button
								$obj_btn->type = 'postback';
								$obj_btn->title = $btn->title;
								$obj_btn->payload = $btn->id;
							}
							
							$btn_arr_obj[] = $obj_btn;
							if($i++ >= 10){
								break;
							}
						}
					}
					
					$btn_arr_clone = clone_arr_obj($btn_arr_obj);
					if(count($btn_arr_clone) == 0){
						// Skip item has no button
						continue;
					}
					//return var_dum_to_string($btn_arr_clone);
					set_payload_for_button($btn_arr_clone, $leaf->id);
					
					$obj->buttons = $btn_arr_clone;
					
					$elements_obj_arr[] = $obj;
				}
				//return var_dum_to_string($data_of_level->data);
				return send_generic_template($recipient_id, $data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
				break;
			case RANK_QUICK_REPLY:
				$i = 0;
				$elements_obj_arr = array();
				//return var_dum_to_string($data_of_level->data);
				foreach($data_of_level->data as $menu){
					$obj = new stdclass();
					$obj->content_type = 'text';
					$obj->title = $menu->title;
					//$obj->payload = $menu->p_id .'_'. $menu->id;
					$obj->payload = $menu->id;
					//$obj->image_url = '';
					$elements_obj_arr[] = $obj;
					if($i++ >= 9){
						break;
					}
				}
				//return var_dum_to_string($elements_obj_arr);
				//return $data_of_level->title;
				return send_quick_replies($recipient_id, $data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
				break;
			case RANK_HMENU_BTN:
				
				break;
			case RANK_STATISTIC:
				///*
				$key = 'user_info';
				$mem = load_from_mem($key);
				$mem = $mem['value'];
				
				$selected = $mem[$data_by_level->sender_id]['other'];
				$obj_selected = get_selected_value($data->all_data, $selected);
				write_file('call3.txt', 'SELECTED_VALUE: ' .$selected, false);
				//*/
				$text = 'Bạn vừa chọn: ' . $obj_selected->text;
				//$text = 'Bạn vừa chọn: ';
				send_text_message($recipient_id, $data_of_level->sender_id, $text);
				break;
		}
	}
	
	function show_menu_of_level($level, $sender_id, $msg, $payload){
		return show_menu_of_level_v2($level, $sender_id, $msg, $payload);
		//return;
		$data = load_from_mem('init_data');
		$data = $data['value'];
		
		//$title = 'Mời bạn chọn danh mục';
		$title = get_title_of_level($data, $level);
		$recipient_id = $sender_id;
		$current_level_str = '_current_level';
		$max_leaf_level_str = '_max_leaf_level';
		
		//$leaves = get_leaf_from_data($data);
		//$max_leaf_level = get_max_level_of_leaves($leaves);
		$max_leaf_level = get_max_level_of_leaves($data);
		
		if($level == $max_leaf_level + 1){
			// Clear level
			//msg_thread_status_clear($sender_id . $current_level_str);
			
			// Test quick replies
			$title = 'Chọn màu: ';
			$elements_obj_arr = array();
			
			$obj = new stdclass();
			$obj->content_type = 'text';
			$obj->title = 'Read';
			$obj->payload = 'RED_' . $payload;
			//$obj->image_url = 'http://www.iconsdb.com/icons/download/red/circle-24.png';
			$elements_obj_arr[] = $obj;
			
			$obj = new stdclass();
			$obj->content_type = 'text';
			$obj->title = 'Green';
			$obj->payload = 'GREEN_' . $payload;
			//$obj->image_url = 'http://www.iconsdb.com/icons/download/guacamole-green/circle-32.png';
			$elements_obj_arr[] = $obj;
			
			return send_quick_replies($recipient_id, $sender_id, $title, $elements_obj_arr);
		}else if($level == $max_leaf_level + 2){
			msg_thread_status_clear($sender_id . $current_level_str);
			return;
		}
		
		//$current_level = msg_thread_status_get($sender_id . $current_level_str);
		$menus = get_menu_by_level($data, $level, true);
		
		if($level > 0){
			$menus = get_child_by_parent_id($menus, $payload);
			
			if($level == $max_leaf_level){
				// Child level
				$elements_obj_arr = array();
				
				foreach($menus as $leaf){
					// Element
					$obj = new stdclass();
					$obj->title = $leaf->title;
					$obj->item_url = $leaf->item_url;
					//$obj->image_url = $leaf->image_url;
					$obj->image_url = $leaf->image;
					//$obj->subtitle = 'This is subtitle_' . $leaf->id;
					//$obj->subtitle = $leaf->description;
					$obj->subtitle = 'Giá: '. $leaf->price;
					$obj->buttons = array();
					
					$obj_btn = new stdclass();
					$obj_btn->type = 'postback';
					$obj_btn->title = 'Mua ngay';
					$obj_btn->payload = 'MN_' .$leaf->id;
					$obj->buttons[] = $obj_btn;
					
					$obj_btn = new stdclass();
					$obj_btn->type = 'postback';
					$obj_btn->title = 'Thanh toán';
					$obj_btn->payload = 'TT_' .$leaf->id;
					$obj->buttons[] = $obj_btn;
					
					$elements_obj_arr[] = $obj;
				}
				return send_generic_template($recipient_id, $recipient_id, $title, $elements_obj_arr);
			}
		}
		
		$buttons_obj_arr = array();
		$i = 0;
		foreach($menus as $menu){
			$obj = new stdclass();
			$obj->type = 'postback';
			$obj->title = $menu->title;
			$obj->payload = $menu->id;
			$buttons_obj_arr[] = $obj;
			if($i++ >= 2){
				break;
			}
		}
		return send_button_template($recipient_id, $recipient_id, $title, $buttons_obj_arr);		
	}
	
	function show_menu_of_level_v2($level, $sender_id, $msg, $payload){
		$data = load_from_mem('init_data');
		$data = $data['value'];
		
		$arr_level_need_save_info = get_level_need_save_info($data);
		foreach($arr_level_need_save_info as $key => $value){
			if($key == $level - 1){
				save_data_for_user($sender_id, $value, $msg);
			}
		}
		
		//$title = 'Mời bạn chọn danh mục';
		$title = get_title_of_level($data, $level);
		if($title == ''){
			$title = 'Mời bạn chọn danh mục';
		}
		
		$recipient_id = $sender_id;
		$current_level_str = '_current_level';
		$max_leaf_level_str = '_max_leaf_level';
		
		// Check level of payload
		$level_of_payload = get_level_of_payload($data, $payload);
		write_file('call3.txt', 'Payload...'. $payload . '+++++' .$level_of_payload . '===Current level==' .$level, false);
		if($level_of_payload != -1){
			if($level != ($level_of_payload + 1)){
				//show_menu_of_level_v2($level_of_payload + 1, $sender_id, $msg, $payload);
				msg_thread_status_set($sender_id . $current_level_str, $level_of_payload + 1);
				return show_menu_of_level_v2($level_of_payload + 1, $sender_id, $msg, $payload);
			}
		}
		
		//$leaves = get_leaf_from_data($data);
		//$max_leaf_level = get_max_level_of_leaves($leaves);
		$max_leaf_level = get_max_level_of_leaves($data);
		
		//$current_level = msg_thread_status_get($sender_id . $current_level_str);
		$data_by_level = get_menu_by_level($data, $level, true);
		$menus = get_menu_by_level($data, $level, true);
		
		if($level > 0){
			$menu_tmp = get_child_by_parent_id($menus, $payload);
			if($menu_tmp != null && is_array($menu_tmp)){
				$menus = $menu_tmp;
			}
		}
		
		
		$rank_action = get_rank_action_of_menu($menus);

		$data_of_level = new stdclass();
		$data_of_level->title = $title;
		$data_of_level->sender_id = $sender_id;
		$data_of_level->payload = $payload;
		$data_of_level->data = $menus;
		$data_of_level->data_by_level = $data_by_level;
		$data_of_level->level = $level;
		$data_of_level->all_data = $data;
		
		return show_menu_by_type($data_of_level, $rank_action, $level);
	}
	
	function process_msg_with_payload($sender_id, $msg, $payload){
		//$data = load_from_mem('init_data');
		//$data = $data['value'];
		
		//write_file('call3.txt', $payload, false);
		write_file('call3.txt', $payload . '_' .$msg, false);
		
		$current_level_str = '_current_level';
		//$max_leaf_level_str = '_max_leaf_level';
		
		//$leaves = get_leaf_from_data($data);
		//$max_leaf_level = get_max_level_of_leaves($leaves);
		
		
		if($msg == 'hi' || $msg == 'Hi' || (stripos($msg, 'chào') !== false) || (stripos($msg, 'mua') !== false)){
			// Reset to level 0
			//msg_thread_status_clear($sender_id . $current_level_str);
			msg_thread_status_set($sender_id . $current_level_str, 0);
			
			// Show menu level 0
		}else{
			/*
			// Get current level
			$current_level = msg_thread_status_get($sender_id . $current_level_str);
			if(!$current_level){
				$msg = 'hi';
				process_msg_with_payload($sender_id, $msg, $payload);
			}
			// Level set
			$current_level = $current_level['value'];
			
			// Get max leaf level
			*/
			
		}
		$current_level = msg_thread_status_get($sender_id . $current_level_str);
		if(!$current_level){
			$msg = 'hi';
			process_msg_with_payload($sender_id, $msg, $payload);
		}
		// Level set
		$current_level = $current_level['value'];
		// Increment current_level
		msg_thread_status_set($sender_id . $current_level_str, $current_level + 1);
		save_data_for_user_v2($sender_id, $payload);
		//return show_menu_of_level($current_level, $sender_id, $msg, $payload);
		return show_menu_of_level_v2($current_level, $sender_id, $msg, $payload);
	}

	function process_msg_with_payload_v2($obj_id, $msg, $payload){
		$sender_id = $obj_id->sender_id;
		$recipient_id = $obj_id->recipient_id;
		
		$current_data_str = '_current_data';
		$current_data = new stdclass();
		
		// 1. Get data from xlsx
		$data_key_name = 'init_data_' .$recipient_id;
		//$data = load_from_mem('init_data');
		$data = load_from_mem($data_key_name);
		$data = $data['value'];
		
		// 2. Check msg =>
		$arr_obj = get_data_by_rank($data, RANK_KEYWORD);
		$kw_obj = get_first_element_of_arr($arr_obj);
		
		// 	2.1 Check rank tu khoa => next_id;
		$condition = check_keyword_in_obj($kw_obj, $msg);
		//$condition = ($msg == 'Hi' || $msg == 'hi');
		write_file('call3.txt', $msg .'__'. $condition .'===Payload: '. $payload, false);
		$id_next = '';
		if( $condition === true){
			$id_next = $kw_obj->id_next;
			// Show menu of id_next;
			return show_menu_by_id($data, $id_next, $obj_id, $msg);
		}else{
			save_data_for_user_v2($sender_id, $payload);
			// Other menu
			if(is_numeric($payload)){
				// ** 1. Must save current data for next leve **
				return show_menu_by_id($data, $payload, $obj_id, $msg);
			}else{
				if($id_next != ''){
					return show_menu_by_id($data, $id_next, $obj_id, $msg);
				}else{
					/*
					// ** 2. Must get previous data and processing... **
					$previous_data = get_saved_data($sender_id);
					if($previous_data != ''){
						$id_next = $previous_data->id_next;
						return show_menu_by_id($data, $id_next, $sender_id, $msg);
					}
					return 'Do not implement...';
					*/
					return show_menu_by_id($data, $id_next, $obj_id, $msg);
				}
			}
		}
	}
	
?>