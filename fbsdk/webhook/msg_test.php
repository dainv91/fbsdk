<?php
	include_once('messenger.php');

	define('RANK_TEXT', 1);
	define('RANK_BTN', 2);
	define('RANK_HMENU', 3);
	define('RANK_QUICK_REPLY', 4);
	define('RANK_HMENU_BTN', 5);
	
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
			if($obj->p_id != $parent_id || $obj->rank == RANK_TEXT){
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
	
	function get_rank_action_of_menu($menus){
		$rank_action = RANK_BTN;
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
	
	function save_data_for_user($sender_id, $key, $value, $is_append = false){
		$key_info = 'user_info';
		$mem = load_from_mem($key_info);
		
		$arr = array();
		$info = array();
		$info[$key] = $value;
		$arr[$sender_id] = $info;
		
		if($mem == false){
		// Not existed
			store_to_mem($key_info, $arr);
		}else{
			// Existed
			$arr = $mem['value'];
			$info = $arr[$sender_id];
		
			if($is_append != false){
				// append
				$info[$key] = $info[$key]. '_' .$value;
			}else{
				$info[$key] = $value;
			}		
			
			$arr[$sender_id] = $info;
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
					send_text_message($sender_id, $msg);
					
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
					return send_text_message($sender_id, $msg);
				}
				$msg = private_process($msg);
				//return $sender_id;
				//send_text_message($sender_id, $msg);
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
	
	//function show_menu_by_type($sender_id, $data, $level, $type_rank){
	function show_menu_by_type($data_of_level, $type_rank){
		//return 'by_type';
		if($type_rank != RANK_TEXT){
			if($data_of_level->data == null || count($data_of_level->data) == 0){
				return show_menu_by_type($data_of_level, RANK_TEXT);
			}
		}
		switch($type_rank){
			case RANK_TEXT:
				//echo 'RANK_TEXT';
				return send_text_message($data_of_level->sender_id, $data_of_level->title);
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
				return send_button_template($data_of_level->sender_id, $data_of_level->title, $buttons_obj_arr);
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
							$obj_btn->type = 'postback';
							$obj_btn->title = $btn->title;
							$obj_btn->payload = $btn->id;
							
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
					//return var_dum_to_string($btns);
					set_payload_for_button($btn_arr_clone, $leaf->id);
					
					$obj->buttons = $btn_arr_clone;
					
					$elements_obj_arr[] = $obj;
				}
				//return var_dum_to_string($elements_obj_arr);
				return send_generic_template($data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
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
				return send_quick_replies($data_of_level->sender_id, $data_of_level->title, $elements_obj_arr);
				break;
			case RANK_HMENU_BTN:
				
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
			
			return send_quick_replies($sender_id, $title, $elements_obj_arr);
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
				return send_generic_template($recipient_id, $title, $elements_obj_arr);
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
		return send_button_template($recipient_id, $title, $buttons_obj_arr);		
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
			//msg_thread_status_set($sender_id . $current_level_str, $current_level + 1);
			//write_file('call3.txt', $payload . '_' .$level_of_payload . '===' .$level, false);
			if($level != ($level_of_payload + 1)){
				//show_menu_of_level_v2($level_of_payload + 1, $sender_id, $msg, $payload);
				msg_thread_status_set($sender_id . $current_level_str, $level_of_payload + 1);
				show_menu_of_level_v2($level_of_payload + 1, $sender_id, $msg, $payload);
				return;
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
		/*
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
		return send_button_template($recipient_id, $title, $buttons_obj_arr);		
		*/
		$data_of_level = new stdclass();
		$data_of_level->title = $title;
		$data_of_level->sender_id = $sender_id;
		$data_of_level->payload = $payload;
		$data_of_level->data = $menus;
		$data_of_level->data_by_level = $data_by_level;
		$data_of_level->level = $level;
		
		return show_menu_by_type($data_of_level, $rank_action);
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
		return show_menu_of_level($current_level, $sender_id, $msg, $payload);
	}

?>