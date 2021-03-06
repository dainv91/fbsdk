<?php
	include_once('init_data.php');
	include_once 'file.php';

	//define("ACCESS_TOKEN", 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD');
	// Demo khach san - second bot
	define("ACCESS_TOKEN", 'EAAZAn88IClRQBAEVAAAWguZC2wGZA1w1ClDRY3EKC5VVZC3UjVuSZClApjR2hGxbiJ6eF3Bt6YFcgkw9MLAq1SZAzslr6pgIkr6JZBIJX46Nzl74vhZBOgQhntrEuJZBDT3lZCgprWSvohN96bmX7V3EfZAdSpCUWFZCfv9A5TPWJ2ZCPZCQZDZD');
	
	function msg_thread_status_get($sender_id){
		return load_from_mem($sender_id);
	}
	function msg_thread_status_set($sender_id, $value){
		store_to_mem($sender_id, $value);
	}
	function msg_thread_status_clear($sender_id){
		clear_cache($sender_id);
	}
	
	function private_process($msg){
		$pattern = 'kiên';
		$msg_new = 'Đừng đừng';
		if(stripos($msg, $pattern) !== false){
			return $msg_new;
		}
		
		if(stripos($msg, 'Đại') !== false){
			return 'Đại đẹp trai';
		}
		if(stripos($msg, 'dung') !== false || stripos($msg, 'chilly') !== false){
			return 'Bạn gái xinh đệp';
		}
		return $msg;
	}
	
	function var_dum_to_string($var){
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		return $result;
	}

	function process_msg($msg_data){
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
					send_text_message_1($sender_id, $msg);
					
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
					return send_text_message_1($sender_id, $msg);
				}
				$msg = private_process($msg);
				//send_text_message_1($sender_id, $msg);
				//return send_button_template_test($sender_id);
				return process_msg_when_chatting($sender_id, $msg, $payload);
			}
			if($messaging->delivery || $messaging->read){
				return 'thang thai';
			}
		}else if($messaging->postback){
			// user choose postback
			$sender_id = $messaging->sender->id;
			$payload = $messaging->postback->payload;
			
			return process_msg_when_chatting($sender_id, $msg, $payload);
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
					return reply_cmt_1($comment_id, $msg);
				}
			}
		}
		
		return var_dum_to_string($messaging);
	}
	
	function process_when_user_cmt(){
		
	}

	function process_msg_when_chatting($sender_id, $msg, $payload){
		$data = load_from_mem('init_data');
		
		$data = $data['value'];
		$data_dic = $data['dic'];
		unset($data['dic']);
		
		$recipient_id = $sender_id;
		$dic_cat1 = $data_dic['dic_cat1'];
		$dic_cat2 = $data_dic['dic_cat2'];
		$title = 'Mời bạn chọn danh mục?';
		
		$count = msg_thread_status_get($sender_id);
		if($count === false){
			// Not existed
			msg_thread_status_set($sender_id, 0);
			$count = 0;
		}else{
			// Existed
		}
		write_file('call2.txt', $count, false);
		if($msg){
			if($msg == 'hi' || $msg == 'Hi' || (stripos($msg, 'chào') !== false)){
				msg_thread_status_clear($sender_id);
				//$title = 'Mời bạn chọn danh mục?';
				// Lua chon danh muc
				$buttons_obj_arr = array();
				
				$i = 0;
				foreach($dic_cat1 as $key => $value){
					$obj = new stdclass();
					$obj->type = 'postback';
					$obj->title = $value;
					$obj->payload = $key;
					$buttons_obj_arr[] = $obj;
					if($i++ >= 2){
						break;
					}
				}
				return send_button_template_1($recipient_id, $title, $buttons_obj_arr);
			}
		}else{
			if($payload != ''){
				
				// Choose option
				$key = $payload;
				msg_thread_status_set($sender_id, 2);
				if($count == 1){
					// Level 2
					$arr = explode("_", $key);
					$k0 = $arr[0];
					$k1 = $arr[1];
					
					//$cat2 = $data[$k0][$k1];
					//return var_dum_to_string($cat2);
					return 'abcd_'.$k0.'---'.$k1;
					if(is_array($cat2)){
						return var_dum_to_string($cat2);
					}
				}else if($count > 1){
					return 'iadd';
				}
				
				if(array_key_exists($key, $data)){
					$count = 1;
					// Chon danh muc 2
					$buttons_obj_arr = array();
				
					$i = 0;
					foreach($dic_cat2 as $k => $v){
						$obj = new stdclass();
						$obj->type = 'postback';
						$obj->title = $v;
						$obj->payload = $key .'_'. $k;
						$buttons_obj_arr[] = $obj;
						if($i++ >= 2){
							break;
						}
					}
					msg_thread_status_set($sender_id, $count);
					return send_button_template_1($recipient_id, $title, $buttons_obj_arr);
				}
			}
			//return $payload;
		}
	}
	
	/*
	function send_attachment($recipient_id, $img_url, $access_token = null){
		if($access_token == null){
			$access_token = 'EAAMEwkQKZA7wBAL1mc4emdwqNjywiUgZBhV3ojjAYoI4xWau3yZCx9zBngTc1b5c8nq9rATw6yyZAcNfTqthEqeUpBRXNMDqrRWOW5rn9iWMW4cH2n1sas38SfD98U5VwNNKQzuJA4T7HcXeKQq8K39JqdCYnmd1eOi9JA8dFQZDZD';
			$access_token = 'EAAMEwkQKZA7wBADA7a9Lnv4x7bddtW5kRKcyIGN6dTcgYOz0UKblOf3bqxmAX6RqDxXi8EvkfezvO7i5sGKZAy71Uh9ONnF2Efl0Jk6seQycBRkBnpiAv0IJwk1qbXmSddgnK9IDPAwQcAKafooacEkp52yu40SARdYAmWHQZDZD';
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
		}
		
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		$obj_msg->message->attachment = new stdclass();
		$obj_msg->message->attachment->payload = new stdclass();
		
		$obj_msg->recipient->id = $recipient_id;
		$obj_msg->message->attachment->type = 'image';
		$obj_msg->message->attachment->payload->url = $img_url;
		
		$msg_data = json_encode($obj_msg);
		return call_send_api($msg_data, $access_token);
	}
	*/

	///*
	function send_attachment($recipient_id, $img_arr, $access_token = null){
		if($access_token == null){
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
			$access_token = ACCESS_TOKEN;
		}
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		
		if(is_array($img_arr)){
			// multiple img
			$obj_msg->message->attachment = array();
			foreach($img_arr as $url){
				$attach = new stdclass();
				$attach->payload = new stdclass();
				$attach->type = 'image';
				$attach->payload->url = $url;
				
				$obj_msg->message->attachment[] = $attach;
			}
		}else{
			$img_url = $img_arr;
			// one img
			$obj_msg->message->attachment = new stdclass();
			$obj_msg->message->attachment->payload = new stdclass();
			$obj_msg->message->attachment->type = 'image';
			$obj_msg->message->attachment->payload->url = $img_url;
		}
		
		
		$obj_msg->recipient->id = $recipient_id;
		
		$msg_data = json_encode($obj_msg);
		return call_send_api($msg_data, $access_token);
	}
	//*/
	
	function send_text_message_1($recipient_id, $msg, $access_token = null){
		if($access_token == null){
			$access_token = 'EAAMEwkQKZA7wBAL1mc4emdwqNjywiUgZBhV3ojjAYoI4xWau3yZCx9zBngTc1b5c8nq9rATw6yyZAcNfTqthEqeUpBRXNMDqrRWOW5rn9iWMW4cH2n1sas38SfD98U5VwNNKQzuJA4T7HcXeKQq8K39JqdCYnmd1eOi9JA8dFQZDZD';
			$access_token = 'EAAMEwkQKZA7wBADA7a9Lnv4x7bddtW5kRKcyIGN6dTcgYOz0UKblOf3bqxmAX6RqDxXi8EvkfezvO7i5sGKZAy71Uh9ONnF2Efl0Jk6seQycBRkBnpiAv0IJwk1qbXmSddgnK9IDPAwQcAKafooacEkp52yu40SARdYAmWHQZDZD';
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
			$access_token = ACCESS_TOKEN;
		}
		
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		
		$obj_msg->recipient->id = $recipient_id;
		//$obj_msg->recipient->phone_number = '+841699733008';
		$obj_msg->message->text = $msg;
		
		$msg_data = json_encode($obj_msg);
		return call_send_api($msg_data, $access_token);
	}
	
	function send_text_message($page_id, $recipient_id, $msg){
		$access_token = get_access_token($page_id);
		return send_text_message_1($recipient_id, $msg, $access_token);
	}
	
	function send_button_template_1($recipient_id, $title, $buttons_obj_arr, $access_token = null){
		if($access_token == null){
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
			$access_token = ACCESS_TOKEN;
		}
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		
		$obj_msg->message->attachment = new stdclass();
		$obj_msg->message->attachment->type = 'template';
		
		$obj_msg->message->attachment->payload = new stdclass();
		$obj_msg->message->attachment->payload->template_type = 'button';
		$obj_msg->message->attachment->payload->text = $title;
		$obj_msg->message->attachment->payload->buttons = $buttons_obj_arr;
		
		$obj_msg->recipient->id = $recipient_id;
		
		$msg_data = json_encode($obj_msg);
		//return $msg_data;
		return call_send_api($msg_data, $access_token);
	}

	function send_button_template($page_id, $recipient_id, $title, $buttons_obj_arr){
		$access_token = get_access_token($page_id);
		return send_button_template_1($recipient_id, $title, $buttons_obj_arr, $access_token);
	}
	
	function send_button_template_test($recipient_id, $title = 'test button'){
		$buttons_obj_arr = array();
		for($i=0; $i<2; $i++){
			$obj = new stdclass();
			if($i % 2== 0){
				$obj->type = 'web_url';
				$obj->title = 'Show web';
				$obj->url = 'http://streaming.inet.vn:6790/monitor';
			}else{
				$obj->type = 'postback';
				$obj->title = 'Chat me';
				$obj->payload = 'http://streaming.inet.vn:6790/monitor';
			}
			$buttons_obj_arr[] = $obj;
		}
		//return $buttons_obj_arr;
		return send_button_template_1($recipient_id, $title, $buttons_obj_arr);
	}
	
	function send_generic_template_1($recipient_id, $title, $elements_obj_arr, $access_token = null){
		if($access_token == null){
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
			$access_token = ACCESS_TOKEN;
		}
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		
		$obj_msg->message->attachment = new stdclass();
		$obj_msg->message->attachment->type = 'template';
		
		$obj_msg->message->attachment->payload = new stdclass();
		$obj_msg->message->attachment->payload->template_type = 'generic';
		//$obj_msg->message->attachment->payload->text = $title;
		//$obj_msg->message->attachment->payload->buttons = $buttons_obj_arr;
		$obj_msg->message->attachment->payload->elements = $elements_obj_arr;
		
		$obj_msg->recipient->id = $recipient_id;
		
		$msg_data = json_encode($obj_msg);
		//return $msg_data;
		return call_send_api($msg_data, $access_token);
	}
	
	function send_generic_template($page_id, $recipient_id, $title, $elements_obj_arr){
		$access_token = get_access_token($page_id);
		return send_generic_template_1($recipient_id, $title, $elements_obj_arr, $access_token);
	}
	
	function send_generic_template_test($recipient_id){
		$title = 'test';
		$elements_obj_arr = array();
		
		// Element
		$obj = new stdclass();
		$obj->title = 'This is title 1';
		$obj->item_url = 'https://stream.inet.vn:6969/monitor';
		$obj->image_url = 'http://hstatic.net/143/1000057143/1/2016/3-24/1934764_994606787293903_6605892986235271264_n_master.jpg';
		$obj->subtitle = 'This is subtitle 1';
		$obj->buttons = array();
		
		$obj_btn = new stdclass();
		$obj_btn->type = 'postback';
		$obj_btn->title = 'Mua ngay';
		$obj_btn->payload = 'MN';
		$obj->buttons[] = $obj_btn;
		
		$obj_btn = new stdclass();
		$obj_btn->type = 'postback';
		$obj_btn->title = 'Thanh toán';
		$obj_btn->payload = 'TT';
		$obj->buttons[] = $obj_btn;
		
		$elements_obj_arr[] = $obj;
		
		// Element
		$obj = new stdclass();
		$obj->title = 'This is title 2';
		$obj->item_url = 'https://stream.inet.vn:6969/monitor';
		$obj->image_url = 'http://hstatic.net/143/1000057143/1/2016/4-20/binh-thia-vt-farlin-bf-193a-1_copy_master.jpg';
		$obj->subtitle = 'This is subtitle 1';
		$obj->buttons = array();
		
		$obj_btn = new stdclass();
		$obj_btn->type = 'postback';
		$obj_btn->title = 'Mua ngay';
		$obj_btn->payload = 'MN';
		$obj->buttons[] = $obj_btn;
		
		$obj_btn = new stdclass();
		$obj_btn->type = 'postback';
		$obj_btn->title = 'Thanh toán';
		$obj_btn->payload = 'TT';
		$obj->buttons[] = $obj_btn;
		
		$elements_obj_arr[] = $obj;
		
		
		return send_generic_template_1($recipient_id, $title, $elements_obj_arr);
	}
	
	function send_receipt_template(){
		
	}
	
	function send_quick_replies_1($recipient_id, $title, $elements_obj_arr, $access_token = null){
		if($access_token == null){
			$access_token = ACCESS_TOKEN;
		}
		
		$obj_msg = new stdclass();
		$obj_msg->recipient = new stdclass();
		$obj_msg->message = new stdclass();
		
		$obj_msg->recipient->id = $recipient_id;
		//$obj_msg->recipient->phone_number = '+841699733008';
		$obj_msg->message->text = $title;
		$obj_msg->message->quick_replies = $elements_obj_arr;
		
		$msg_data = json_encode($obj_msg);
		return call_send_api($msg_data, $access_token);
	}
	
	function send_quick_replies($page_id, $recipient_id, $title, $elements_obj_arr){
		$access_token = get_access_token($page_id);
		return send_quick_replies_1($recipient_id, $title, $elements_obj_arr, $access_token);
	}
	
	function send_quick_replies_test($recipient_id = '1285422414804001'){
		//$recipient_id = '1285422414804001';
		//return $recipient_id;
		$title = 'Chọn màu:';
		$elements_obj_arr = array();
			
		$obj = new stdclass();
		$obj->content_type = 'text';
		$obj->title = 'Red';
		$obj->payload = 'RED_' . $payload;
		//$obj->image_url = 'http://www.iconsdb.com/icons/download/red/circle-24.png';
		$elements_obj_arr[] = $obj;
		
		$obj = new stdclass();
		$obj->content_type = 'text';
		$obj->title = 'Green';
		$obj->payload = 'GREEN_' . $payload;
		//$obj->image_url = 'http://www.iconsdb.com/icons/download/guacamole-green/circle-32.png';
		$elements_obj_arr[] = $obj;
		
		
		/*
		// test location
		$elements_obj_arr = array();
		
		$obj = new stdclass();
		$obj->content_type = 'location';
		//$obj->title = 'Green';
		//$obj->payload = 'GREEN_' . $payload;
		//$obj->image_url = 'http://www.iconsdb.com/icons/download/guacamole-green/circle-32.png';
		$elements_obj_arr[] = $obj;
		*/
		return send_quick_replies_1($recipient_id, $title, $elements_obj_arr);
	}
	
	function reply_cmt_1($cmt_id, $msg, $access_token = null){
		if ($access_token == null){			
			$access_token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
			$access_token = ACCESS_TOKEN;
		}
		return call_send_api_reply_cmt_1($cmt_id, $msg, $access_token);
	}
	function reply_cmt($page_id, $cmt_id, $msg){
		$access_token = get_access_token($page_id);
		return call_send_api_reply_cmt_1($cmt_id, $msg, $access_token);
	}
	
	function hide_cmt_of_page($page_id, $cmt_id){
		$access_token = get_access_token($page_id);
		$msg_data = array(
			'is_hidden' => 'true',
		);
		
		return call_send_api_cmt('', $cmt_id, $msg_data, $access_token);
	}
	
	
	function send_persistent_menu($page_id, $call_to_action_arr_obj){
		$access_token = get_access_token($page_id);
		
		$obj_msg = new stdclass();
		$obj_msg->setting_type = 'call_to_actions';
		$obj_msg->thread_state = 'existing_thread';
		//$obj_msg->call_to_actions = array();
		$obj_msg->call_to_actions = $call_to_action_arr_obj;
		
		//$menu_items = array();
		
		$msg_data = json_encode($obj_msg);
		return call_send_api_persistent_menu($msg_data, $access_token);
	}

	function call_send_api($msg_data, $access_token){
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $access_token;
		$result = send_http($url, $msg_data, 'POST', array('Content-Type: application/json'));
		return $result['content'];
	}
	
	function call_send_api_reply_cmt_1($comment_id, $msg, $access_token){
		$msg_data = array(
			'message' => $msg,
		);
		//$url = "https://graph.facebook.com/v2.7/$comment_id/private_replies?access_token=" . $access_token;
		$url_plus = '/private_replies';
		return call_send_api_cmt($url_plus, $comment_id, $msg_data, $access_token);
	}
	
	function call_send_api_cmt($url_plus, $comment_id, $msg_data, $access_token){
		$url = "https://graph.facebook.com/v2.7/$comment_id$url_plus?access_token=" . $access_token;
		$result = send_http($url, $msg_data, 'POST', array('Content-Type: application/json'));
		return $result['content'];
	}
	
	function call_send_api_persistent_menu($msg_data, $access_token){
		$url = 'https://graph.facebook.com/v2.6/me/thread_settings?access_token=' . $access_token;
		$result = send_http($url, $msg_data, 'POST', array('Content-Type: application/json'));
		return $result['content'];
	}
	
	function call_send_api_get_list_conversations($page_id, $access_token){
		$url = "https://graph.facebook.com/v2.8/$page_id/conversations?limit=1000&access_token=" . $access_token;
		//return $url;
		$result = send_http($url, array('iadd'=>'abc'));
		return $result['content'];
	}
	
	function call_send_api_get_list_msg_of_conversation($conversation_id, $access_token){
		$url = "https://graph.facebook.com/v2.8/$conversation_id/messages?fields=from,to&limit=3&access_token=" . $access_token;
		$result = send_http($url, array('iadd'=>'abc'));
		return $result['content'];
	}
	
	function call_send_api_send_msg_for_conversation_id($conversation_id, $msg, $access_token){
		$url = "https://graph.facebook.com/v2.8/$conversation_id/messages?access_token=" . $access_token;
		$params = array(
			"message" => "$msg"
		);
		$result = send_http($url, $params, 'POST');
		return $result['content'];
	}
	
	function send_http($url, $params, $method = 'GET', $headers = null){
		if(is_array($params)){
			$params = http_build_query($params);
		}
		///*
		if($headers == null){
			$headers = array(
				'Content-Type: application/json',
				//'Content-Length'=> strlen($params),
			);
		}else{
			//$headers['Content-Type'] = 'application/json';
			//$headers['Content-Length'] = strlen($params);
		}
		//*/
		//var_dump($headers);
		//exit();
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			//CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			//CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_HTTPHEADER => $headers,
		);
		if($method == 'POST'){
			$options[CURLOPT_POST] = true;
		}else if($method == 'GET'){
			$options[CURLOPT_HTTPGET] = 1;
		}

		$ch      = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$header  = curl_getinfo($ch);
		curl_close($ch);

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		return $header;
	}
	
	function get_access_token($page_id){
		$access_token = null;
		$arr = array();
		
		// auto all
		$pid = '1681271828857371';
		//$token = 'EAAMEwkQKZA7wBAL9DjYj0hBbMzzdNoBUbXmnG2ma6kw6P4DtsqijouIVZBZCjv8WYu5KF3rCv2GGyeHpgIwyHMRaa4TLK4vDAufQkZCQg1fB8ZCU4avJBfeQGWvOtm7TTMd763dBzLdz4ZBBrQY1cCqHtJrsL2DZCMpfg3PA0gscAZDZD';
		// Van Dai Nguyen - first
		$token = 'EAAMEwkQKZA7wBAJyccEkIiPXBunoeIslfIZBAPBcgRpcLgehen7UMw9XzqgZAhqM8UjzPQ6j9prWNGlTxIipqKycHyxEIYnW78BK00A4QC9mgnjoDkiJzMkhHgZCNuJKyRZCKDFTly10Sv3EeCsvbE3vzUi7N2QqVx4jFGZCUPSwZDZD';
		$arr[$pid] = $token;
		
		// phong kham
		$pid = '1137226739706218';
		//$token = 'EAAMEwkQKZA7wBAGCjfynLPy1KNeozNQK6nKrJaJ8VdJiZBZAKINyYZAGZCl8BU1Vmqe0jU8PfocEPEWjorebZBEp9mACtXPOLVcXovwdYQtcuPUpfxFXRJXnc3mvT8XyoZAZCpcEOgcu9pUcZCVpXcEMTX7GaZAnWnZCFtUyF05xkoyOQZDZD';
		$token = 'EAAMEwkQKZA7wBACQp4GNv0ZAjSjaZBssfqgsgGHjf09hanMyRyxh9XetqwM7XHeij36zeazYYNJQZCWIsJCUHVXRnD2APKD1gqfCpwiWUk0W7o4O4d6u4vwGnxWAIZC1DUbrKZCdytzrdmdZCaud76UBRMmO3MYZAutdNULgFElIDwZDZD';
		$arr[$pid] = $token;
		
		// ve may bay
		$pid = '559437760920787';
		//$token = 'EAAMEwkQKZA7wBAENJZCMjPXXcDJeaCYyZAEWZChrUEFK22IwkKfUv6xwqQltAUMKif2AxEyT6URG16tYT7NuZBbfr1s8AWZB5vniDcdZChJ1ZBWqcJV1TEkIwdCsZAGBMeRLaYHoZCXYZAS9s4sfcuOxUZCJmT5shWuJG8iu45wYA7q1CAZDZD';
		$token = 'EAAMEwkQKZA7wBANaPrZBZA5hSXsPAnfwMeA7FjcCq0wG4xOuFLx4IcURlGoQaYenPg9qZBdr3er9NZBzLMqlLBsVew1KUeGmEWptYbtG2ZBfrsFLGa5GHZAnlOZBvQaCRJtjRtnxoOERl7ZBjFo3wVa5thZBvj6FMasdK7rcATwVWQIAZDZD';
		//$arr[$pid] = $token;
		
		// khach san, 2nd bot
		$pid = '205662139870141';
		$token = 'EAAZAn88IClRQBAEVAAAWguZC2wGZA1w1ClDRY3EKC5VVZC3UjVuSZClApjR2hGxbiJ6eF3Bt6YFcgkw9MLAq1SZAzslr6pgIkr6JZBIJX46Nzl74vhZBOgQhntrEuJZBDT3lZCgprWSvohN96bmX7V3EfZAdSpCUWFZCfv9A5TPWJ2ZCPZCQZDZD';
		$arr[$pid] = $token;
		
		// hang thoi trang, 1st bot
		$pid = '693558664140347';
		$token = 'EAAMEwkQKZA7wBAAJgcc1c7CVfLr7ZCGTjzfmZAZCovKPg7ZCTTThhKib8RTkHXZClSDkK0nxrSVpCCm3V3LpizWU9OITadE7862waxUlMFS4yDIeZBZBkESn5K2gDcQTblnGBD0aCaP5ZAyG6ruaJdteqTwDJYOIlR29zDurhozt2xgZDZD';
		$arr[$pid] = $token;
		
		// trung tam dao tao, 1st bot
		$pid = '1435247499821906';
		$token = 'EAAMEwkQKZA7wBAPIXB3TNS9U38ZBgNmtymxYJbCt14dHJ6scmCxQpoc8ZA50XXXzkSgTiDZBf43wDpWxXAZBrrZBE4dj2JfpOGlAUtQkZCSFbZAGYQ2c60ktUoupr6AtjkBcI9SUH3nKOVygWZBnDDJYlKkSZA06ZAGDfy8ZANouv6wZBmAZDZD';
		$arr[$pid] = $token;
		
		// hoc vien robotics
		$pid = '556256761138693';
		$token = 'EAAMEwkQKZA7wBAPYcJjcKym48Dz8g6KPJNYHQqd4ZBjHwn9w79QkncJOQOKzBVkO3phhJXElpOvDZBVtFiZCRvzRjCgjkMlrMhcHNgLplLNAcceginxT8izvkDSHB4TMDTfbQwo1I2E6E27VLnS7NUWutxN9HS7dgjZCqyUY1YAZDZD';
		$arr[$pid] = $token;
		
		// Du hoc han quoc
		$pid = '1591469367824430';
		$token = 'EAAMEwkQKZA7wBANhS5RRFuDeuyr0gRZBUSZBBgrtZAPbw0CPl0gV2ZCoqK7KqYPjzHdZBppOytlFLnZBPjmEpQTrZCvHASwaWNZA21l7BAwpS5mI6ZAoEEFFXjhw87ukAQCZCGGRQNDb8bMRoKK1xeN9gkcdBeAklnKosnZCaLYzsM1yIgZDZD';
		$arr[$pid] = $token;
		
		// David Health VN: https://www.facebook.com/davidhealthvietnam.official/
		$pid = '1645086179071672';
		$token = 'EAAMEwkQKZA7wBAMxyr25ajzWqnKo5ZBR6CVCetbqbP0HskCZBhrDRirx4TA8j5XgxT8TJsDic9OEVyb658Cl9buZA8Fio2JPrYxSSsZBqHTRs1tvLAOb4qN9XdV51wLSMufkweUgZAVf6qwJHRMLEZBgA6gFgHfsPEHNxW2tqzCugZDZD';
		$arr[$pid] = $token;
		
		if(isset($arr[$page_id])){
			$access_token = $arr[$page_id];
		}
		if($access_token == null){
			// Get from memory
			$key_lst_page = 'lst_page_acc_token';
			$lst_page = load_from_mem($key_lst_page);
			if($lst_page !== false){
				$lst_page = $lst_page['value'];
				if(isset($lst_page[$page_id])){
					$page = $lst_page[$page_id];
					$access_token = $page->access_token;
				}
			}
		}
		return $access_token;
	}
	
	function test_messenger(){
		//$url = 'https://app.inet.vn:434/iadd/fbsdk/webhook/echo.php';
		//$result = send_http($url, array('a'=>1, 'b'=>2), 'GET', null);
		//var_dump($result['content']);
		$sender_id = '1387642324596666';
		$access_token = 'EAAPOqpDSQGgBAKoZAbRfzUZB815G9h1DKz64pZBoXQpI7TugNduO36BenuhPhL4Q6zrXSWCD5zoUyz1anSTsLpin4KeWGh5MSklRbf7TNiJuvPhSz0kNzC9PQhEoSYcchrPQwpwl20WAIcG6ALGS47c7Q8CSkUZD';
		$msg = 'xin cam on';
		echo send_text_message_1($sender_id, $msg, $access_token);
	}
	//header('Content-type: application/json');
	//test();
	$page_access_token = 'EAAMEwkQKZA7wBAH7cjZADM47S7H62vXjQruPlWrnB1OVkn6oQZA0gNEDfxcIE7MKsBZBXJKdpiVFimbieKyg43TIaYIR58pbBZCdv1TpM0jrkY1BvCsQew4yq4vHOOK0FKmEjoA0jc7FBahjI788ZAhdwgY4Imqp0yHZBaWZCJ33IQZDZD';
	$page_access_token_v2 = 'EAAMEwkQKZA7wBAL1mc4emdwqNjywiUgZBhV3ojjAYoI4xWau3yZCx9zBngTc1b5c8nq9rATw6yyZAcNfTqthEqeUpBRXNMDqrRWOW5rn9iWMW4cH2n1sas38SfD98U5VwNNKQzuJA4T7HcXeKQq8K39JqdCYnmd1eOi9JA8dFQZDZD';
	//$result = send_text_message_1('100006035972260', 'xin chao', $page_access_token);
	//$result = send_text_message_1('1078614338912257', 'xin cam on', $page_access_token);
	//var_dump($result);
	//var_dump(load_from_mem('init_data'));
	//echo private_process('bạn dung');
	//echo '<br />';
	//echo ACCESS_TOKEN;
	//test();
?>