<?php
	header('Content-type: application/json');
	$result = json_encode($_REQUEST);
	echo $result;
?>