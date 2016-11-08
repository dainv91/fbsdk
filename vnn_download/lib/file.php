<?php
	function write_file($file_name, $content, $append = true){
		if($append != false){
			$myfile = fopen($file_name, "w") or die("Unable to open file! " . $file_name);
		}else{
			//echo 'heee';
			$myfile = fopen($file_name, "a") or die("Unable to open file! " . $file_name);
		}
		$txt = '';
		if(is_array($content)){
			foreach($content as $key=>$value){
				$txt .= $key . '=>' .$value . "\n";
			}
			fwrite($myfile, $txt);	
		}else{
			$txt .= $content . "\n";
			fwrite($myfile, $txt);	
		}
		
		fclose($myfile);
	}
?>