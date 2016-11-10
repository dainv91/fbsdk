<?php
	include_once './lib/messenger_v2.php';
	
	$child_links = array();
	
	$g_link = '';
	$g_xpath = urlencode(".//div[@id='center_column']//div[@class='product-image-container']/a");
	
	function get_base_from_link($link){
		$b = '';
		$info = parse_url($link);
		if($info !== false){
			//var_dump($info);
			$b = $info['scheme'] .'://'. $info['host'];
		}
		return $b;
	}
	
	function get_list_link($l, $xpath){
		$l = html_entity_decode($l);
		$xpath = html_entity_decode($xpath);
		
		$link_base = get_base_from_link($l);
		
		$links = array();
		$content = send_http($l, array('iadd' => 'abcd'));
		$content = $content['content'];
		
		$doc = new DOMDocument;
		@$doc->loadHTML($content);
		$d_xpath = new DOMXPath($doc);
		
		$value = $d_xpath->query($xpath);
		foreach($value as $tmp_link){
			$relative_link = $tmp_link->getAttribute('href');
			if(substr($relative_link, 0, 4) != 'http'){
				$relative_link = $link_base . $relative_link;
			}
			$links[] = $relative_link;
		}
		return $links;
	}
	
	if(isset($_REQUEST['action'])){
		$action = trim($_REQUEST['action']);
		
		if($action == 'get_link'){
			if(isset($_POST['txtLink'])){
				$g_link = htmlentities(trim($_POST['txtLink']));
			}
			if(isset($_POST['txtXpath'])){
				$g_xpath = htmlentities(trim($_POST['txtXpath']));
			}
			
			if($g_link != '' && $g_xpath != ''){
				$links = get_list_link($g_link, $g_xpath);
				if($links != null && count($links) > 0){
					foreach($links as $l){
						$child_links[] = $l;
					}
				}
			}
		}
	}
?>

<html>
	<head>
		<title>Get list link</title>
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
			<h3>Test thử lấy link nào :"></h3>
			<form action="?action=get_link" method="POST" enctype="application/x-www-form-urlencoded">
				<div class="form-group">
					<label>Link: </label><label name="lblLink"></label>
					<input type="text" name="txtLink" class="form-control" value="<?php echo urldecode($g_link); ?>" />
				</div>
				
				<div class="form-group">
					<label>Xpath: </label><label name="lblXpath"></label>
					<input type="text" name="txtXpath" class="form-control" value="<?php echo urldecode($g_xpath); ?>" />
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-default btn-success" value="Thử phát nhở" name="txtSubmit" />
					<input type="button" class="btn btn-default btn-info" 
						value="Copy links" name="txtBtnCopy" />
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
				<?php foreach($child_links as $link){ ?>
					<a href="<?php echo $link;?>"><?php echo $link; ?></a><br />
				<?php } ?>
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