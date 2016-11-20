<?php
	include_once 'msg_test_v2.php';
	include_once 'lst_page_id.php';
	
	$g_txt_page_id = '';
	$g_txt_page_name = '';
	$g_txt_page_acc_token = '';
	$g_message = '';
	
	function add_info_for_page(){
		global $lst_page_id;
		global $g_message;
		$uploaded_page_id = '';
		/*
		if(isset($_REQUEST['sl_pages'])){
			$sl_page = $_REQUEST['sl_pages'];
			if(isset($lst_page_id[$sl_page])){
				$uploaded_page_id = $sl_page;
			}else{
				exit('Invalid page');
			}
		}
		*/
		if(isset($_REQUEST['btnSubmit'])){
			$btnSubmit = $_REQUEST['btnSubmit'];
			
		}
		if(isset($_REQUEST['txtPageId'])){
			global $g_txt_page_id;
			$txt_page_id = $_REQUEST['txtPageId'];
			$g_txt_page_id = trim($txt_page_id);
		}
		if(isset($_REQUEST['txtPageName'])){
			global $g_txt_page_name;
			$txt_page_name = $_REQUEST['txtPageName'];
			$g_txt_page_name = trim($txt_page_name);
		}
		if(isset($_REQUEST['txtAccessToken'])){
			global $g_txt_page_acc_token;
			$txt_page_acc_token = $_REQUEST['txtAccessToken'];
			$g_txt_page_acc_token = trim($txt_page_acc_token);
		}
		
		if($g_txt_page_id != '' && $g_txt_page_name != '' && $g_txt_page_acc_token != ''){
			$key_lst_page = 'lst_page_acc_token';
			$lst_page = load_from_mem($key_lst_page);
			
			$obj = new stdclass();
			$obj->page_id = $g_txt_page_id;
			$obj->page_name = $g_txt_page_name;
			$obj->access_token = $g_txt_page_acc_token;
			if($lst_page !== false){
				$lst_page = $lst_page['value'];
				/*
				if(isset($lst_page[$page_id])){
					$page = $lst_page[$page_id];
					$access_token = $page->access_token;
				}
				*/
				$lst_page[$g_txt_page_id] = $obj;
			}else{
				//$obj = new stdclass();
				/*
				$obj->page_id = $g_txt_page_id;
				$obj->page_name = $g_txt_page_acc_token;
				$obj->access_token = $g_txt_page_acc_token;
				*/
				$lst_page = array();
				$lst_page[$g_txt_page_id] = $obj;
			}
			store_to_mem($key_lst_page, $lst_page);
			
			$g_message = "Added info of page: $g_txt_page_name with id: $g_txt_page_id";
		}
		
	}
	if(isset($_REQUEST['load_info'])){
		$key_lst_page = 'lst_page_acc_token';
		$lst_page = load_from_mem($key_lst_page);
		echo '<pre>';
		var_dump($lst_page);
		echo '</pre>';
		exit();
	}
	
	if(isset($_REQUEST['func'])){
		$func = $_REQUEST['func'];
		if(function_exists($func)){
			$func();
		}else{
			echo 'Hey! What are you doing?';
			exit();
		}
	}
	
?>
<html>
	<head>
		<title>Add info for page</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/v1.5.13/dist/clipboard.min.js"></script>
	</head>
	<body>
		<div class="container">
			<h3>Add info for page</h3>
			<form action="?func=add_info_for_page" method="POST" enctype="application/x-www-form-urlencoded">
				<div class="form-group">
					<label>Page ID: </label><label name="lblPageId"></label>
					<input type="text" name="txtPageId" class="form-control" value="<?php echo urldecode($g_txt_page_id); ?>" />
					<label>Page NAME: </label><label name="lblXpath"></label>
					<input type="text" name="txtPageName" class="form-control" value="<?php echo urldecode($g_txt_page_name); ?>" />
				</div>
				<div class="form-group">
					<label>Access token: </label>
					<input type="text" name="txtAccessToken" class="form-control" value="<?php echo urldecode($g_txt_page_acc_token); ?>" />
				</div>
				
				<div class="form-group">
					<input type="submit" class="btn btn-default btn-success" value="Submit" name="btnSubmit" />
				</div>
			</form>
		</div>
		<div class="container">
			<div class="alert alert-success" id="divAlert">
				<a href="#" class="close" aria-label="close">&times;</a>
				<label>Copied</label>
			</div>
			<div>
				<label>Output:</label>
			</div>
			<div id="divOutput" class="bg bg-danger">
				<?php echo "$g_message"; ?>
			</div>
		</div>
		<div class="footer" style="height: 5em">
		</div>
	</body>
	<script type="text/javascript">
		$('#divAlert').hide();
		$('.close').click(function(){
			$('#divAlert').hide();
		});
		function copy(){
			var input = document.getElementById("divOutput");
			var lst_a = input.getElementsByTagName("a");
			var lstLinkArr = [];
			if(lst_a != undefined){
				for(var i=0; i<lst_a.length; i++){
					var a = lst_a[i];
					//lstLink += a.outerText + "\n";
					lstLinkArr[i] = a.outerText + "\r\n";
				}
				var str = lstLinkArr.join("");
				//window.prompt("Copy to clipboard: Ctrl+C, Enter", str);
				this.setAttribute("data-clipboard-text", str);
			} else {
				
			}
		}
		var clipboard = new Clipboard('.btn');
		clipboard.on('success', function(e) {
			$('#divAlert').fadeIn(800);
		});
		clipboard.on('error', function(e) {
			//console.log(e);
		});
		var btnCopy = document.getElementsByName("txtBtnCopy")[0];
		btnCopy.addEventListener("click", copy);
	</script>
</html>