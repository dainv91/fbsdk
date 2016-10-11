<?php
	//echo __DIR__;
	$lib_fb_path = '/lib/Facebook/autoload.php';
	require_once __DIR__ . $lib_fb_path;

	$fb = new Facebook\Facebook([
		'app_id' => '849657341831100',
		'app_secret' => '60a1bd1266a8daf44bd33a17cb4f8ff8',
		'default_graph_version' => 'v2.7',
	]);
	echo 'ok';
?>
