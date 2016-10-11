<html>
	<head>
		<title> FB app </title>
	</head>
	<body>
		<script>
		  window.fbAsyncInit = function() {
			FB.init({
			  appId      : '849657341831100',
			  xfbml      : true,
			  version    : 'v2.7'
			});
		  };

		  (function(d, s, id){
			 var js, fjs = d.getElementsByTagName(s)[0];
			 if (d.getElementById(id)) {return;}
			 js = d.createElement(s); js.id = id;
			 js.src = "//connect.facebook.net/en_US/sdk.js";
			 fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));
		</script>
		
		<div>
			<div
			  class="fb-like"
			  data-share="true"
			  data-width="450"
			  data-show-faces="true">Like me
			</div>
		</div>
	</body>
</html>