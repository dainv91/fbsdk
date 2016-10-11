<?php
	//echo __DIR__;
	$lib_fb_path = '/Facebook/autoload.php';
	require_once __DIR__ . $lib_fb_path;

	$fb = new Facebook\Facebook([
		'app_id' => '849657341831100',
		'app_secret' => '60a1bd1266a8daf44bd33a17cb4f8ff8',
		'default_graph_version' => 'v2.3',
	]);
	$fb->setDefaultAccessToken('EAAMEwkQKZA7wBAH6rr7dlhz4Aw1EM5mjyYq0VxJAHbscW7UFI3azpdag5RkfKB7ygA4QGZAroL7jZBP3H89h9QewDCRgxHLLONI5oSYtxuNZAJ3lSB1DoZCRRYhyL5TYAEcKx3kHgyipHEiTBkfaeL3IUvz7Qtcqk9B2OLixnwdu6BR7gntwr');
	//$fb->setDefaultAccessToken('EAAMEwkQKZA7wBADliZAti9j0YWSbtmqmyQ6PDveBAZAUg8RfeBewwBop6yqZBuIeNINOf5GEHs392q0Dpbf2baEmsWGZCodjn9s0qvYDB5tazrT1EK62z9Ly4sJ68TNrqal3QCNC2kGei3UXt3tvvbRCjZBvFKGCUZD');
	echo '<input type="hidden" name="test" value="init facebook sdk. "></input><br />';
?>
