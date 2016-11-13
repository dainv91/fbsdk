<?php
	include_once 'msg_test_v2.php';
	include_once 'lst_page_id.php';
	
	$g_txt_admin_id = '';
	$g_txt_admin_name = '';
	$g_message = '';
	
	//var_dump($lst_page_id);
	function set_admin_for_page(){
		global $lst_page_id;
		global $g_message;
		$uploaded_page_id = '';
		if(isset($_REQUEST['sl_pages'])){
			$sl_page = $_REQUEST['sl_pages'];
			if(isset($lst_page_id[$sl_page])){
				$uploaded_page_id = $sl_page;
			}else{
				exit('Invalid page');
			}
		}
		if(isset($_REQUEST['btnSubmit'])){
			$btnSubmit = $_REQUEST['btnSubmit'];
			
		}
		if(isset($_REQUEST['txtAdminId'])){
			global $g_txt_admin_id;
			$txt_admin_id = $_REQUEST['txtAdminId'];
			$g_txt_admin_id = trim($txt_admin_id);
		}
		if(isset($_REQUEST['txtAdminName'])){
			global $g_txt_admin_name;
			$txt_admin_name = $_REQUEST['txtAdminName'];
			$g_txt_admin_name = trim($txt_admin_name);
		}
		
		if($uploaded_page_id != '' && $g_txt_admin_id != '' && $g_txt_admin_name != ''){
			save_admin_of_page($uploaded_page_id, $g_txt_admin_id, $g_txt_admin_name);
			$g_message = "Added user $g_txt_admin_name to admin of page $lst_page_id[$uploaded_page_id]";
		}
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
		<title>Add admin for page</title>
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
			<h3>Add admin for page</h3>
			<form action="?func=set_admin_for_page" method="POST" enctype="application/x-www-form-urlencoded">
				<div class="form-group">
					<label>Chọn page:</label>
					<select name="sl_pages" class="form-control">
					<?php foreach($lst_page_id as $p_k => $p_v) {?>
					<option value="<?php echo $p_k; ?>"><?php echo $p_v; ?></option>
					<?php }?>
					</select>
				</div>
				
				<div class="form-group">
					<label>Admin ID: </label><label name="lblXpath"></label>
					<input type="text" name="txtAdminId" class="form-control" value="<?php echo urldecode($g_txt_admin_id); ?>" />
					<label>Admin NAME: </label><label name="lblXpath"></label>
					<input type="text" name="txtAdminName" class="form-control" value="<?php echo urldecode($g_txt_admin_name); ?>" />
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