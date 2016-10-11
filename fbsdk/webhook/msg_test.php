<?php
	include_once('messenger.php');

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
			if($leaf->is_leaf && ($leaf->level > $max)){
				$max = $leaf->level;
			}
		}
		return $max;
	}
	
	function get_menu_by_level($data, $level){
		$result = array();
		foreach($data as $obj){
			if($obj->level != $level){
				continue;
			}
			$result[] = $obj;
		}
		return $result;
	}
	
	function msg_test($input){
		$entry = json_decode($msg_data);
		$entry = $entry->entry[0];
		
		// message
		$messaging = $entry->messaging[0];
		if($messaging){
			$message = $messaging->message;	
		}
		$changes = null;
		
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
				}
				
				if($msg == ''){
					$msg = 'Chịu chịu';
					$msg = private_process($msg);
					return send_text_message($sender_id, $msg);
				}
				$msg = private_process($msg);
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
				// cmt	
				$change = $changes[0];
				$value = $change->value;
				if($change->field == 'feed'){
					$comment_id = $value->comment_id;
					$message = $value->message;
					
					$msg = "Ê, ai cho mày comment: $message ?";
					reply_cmt($comment_id, $msg);
				}
			}
		}
	}
	
	function show_menu_of_level($level, $sender_id, $msg, $payload){
		$data = load_from_mem('init_data');
		$data = $data['value'];
		
		$title = 'Mời bạn chọn danh mục';
		$recipient_id = $sender_id;
		$current_level_str = '_current_level';
		$max_leaf_level_str = '_max_leaf_level';
		
		$leaves = get_leaf_from_data($data);
		$max_leaf_level = get_max_level_of_leaves($leaves);
		
		if($level == $max_leaf_level){
			// Clear level
			msg_thread_status_clear($sender_id . $current_level_str);
			return;
		}
		
		//$current_level = msg_thread_status_get($sender_id . $current_level_str);
		$menus = get_menu_by_level($data, $level);
		
		$buttons_obj_arr = array();
		$i = 0;
		foreach($menus as $menu){
			$obj = new stdclass();
			$obj->type = 'postback';
			$obj->title = $menu->title;
			$obj->payload = $id;
			$buttons_obj_arr[] = $obj;
			if($i++ >= 2){
				break;
			}
		}
		return send_button_template($recipient_id, $title, $buttons_obj_arr);		
	}
	
	function process_msg_with_payload($sender_id, $msg, $payload){
		//$data = load_from_mem('init_data');
		//$data = $data['value'];
		
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
		show_menu_of_level($current_level, $sender_id, $msg, $payload);
		
		// Increment current_level
		msg_thread_status_set($sender_id . $current_level_str, $current_level + 1);
	}

?>