<?php
	include_once './lib/messenger_v2.php';
	
	function test_dl_web_page(){
		$obj = new stdclass();
		
		$link = 'http://vnnshop.vn/ao-thun-nam/ao-nam-ralph-lauren-dai-tay-co-keo-khoa-mau-xanh-navy-mau-xanh-ghi-sang-p-c2i.html?at=1&atv=246';
		
		if(isset($_REQUEST['link'])){
			$link = $_REQUEST['link'];
		}
		
		//$link = 'https://app.inet.vn:434/iadd/fbsdk/webhook/error.txt';
		$result = send_http($link, array('iadd' => 'abcd'));
		$result = $result['content'];
		//$result = file_get_contents('content.txt');

		
		//write_file('content.txt', $result, false);
		$info = get_product_info($result);
		$imgs = get_image_link($result);
		
		$obj->info = $info;
		$obj->imgs = $imgs;
		
		$result = json_encode($obj);
		return $result;
	}
	function get_product_info($content){
		$obj = new stdclass();
		$obj->code = '';
		$obj->title = '';
		$obj->old_price = 200;
		$obj->new_price = 200;
		$obj->sale_percent = 20;
		
		$doc = new DOMDocument;
		//@$doc->loadXML($content);
		@$doc->loadHTML($content);

		$xpath = new DOMXPath($doc);
		
		// title
		$path = ".//*[@id='main-product-info']//div[@class='data-info-detail']/h1";
		$value = $xpath->query($path);
		$obj->title = trim($value->item(0)->nodeValue);
		
		// code
		$path = ".//*[@id='main-product-info']//div[@class='data-info-detail']/div[@class='info-intro']//ul/li[1]/span";
		$value = $xpath->query($path);
		$obj->code = trim($value->item(0)->nodeValue);
		
		// new price
		$path = ".//*[@id='main-product-info']//div[@class='data-info-detail']//span[@class='vnn-price']/text()";
		$value = $xpath->query($path);
		$obj->new_price = trim($value->item(0)->nodeValue);
		
		// old price
		$path = ".//*[@id='main-product-info']//div[@class='data-info-detail']//span[@class='vnn-old-price']/text()";
		$value = $xpath->query($path);
		$obj->old_price = trim($value->item(0)->nodeValue);
		
		// sale percent
		$path = ".//*[@id='main-product-info']//div[@class='data-info-detail']//span[@class='offInfoDetail']/em";
		$value = $xpath->query($path);
		$obj->sale_percent = trim($value->item(0)->nodeValue);
		
		return $obj;
	}
	
	function get_image_link($content){
		$result = array();
		//$result[] = 'link1';
		//$result[] = 'link2';
		//$result[] = 'link3';
		
		$doc = new DOMDocument;
		//@$doc->loadXML($content);
		@$doc->loadHTML($content);

		$xpath = new DOMXPath($doc);
		
		$path = ".//*[@id='desc-detail']//img";
		$value = $xpath->query($path);
		
		foreach($value as $img_node){
			$img = trim($img_node->getAttribute('src'));
			$result[] = $img;
		}
		
		return $result;
	}
	$result = test_dl_web_page();
	
	//header("content-type: text/html; charset=UTF-8"); 
	header('Content-Type: application/json; charset=UTF-8"');
	echo $result;
?>
