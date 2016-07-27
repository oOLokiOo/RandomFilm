<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript">
		function timer(){
			var obj = document.getElementById('timer');
 			obj.innerHTML--;

			if(obj.innerHTML == 0) window.location.href = location.protocol+"//"+location.host+location.pathname+"./?r="+Math.round(1 - 0.5 + Math.random() * (999999 - 1 + 1));
			else setTimeout(timer, 1000);
		}

		setTimeout(timer, 1000);	
	</script>

	<!-- Just for local & github project version, common CSS file, you can remove it from here -->
	<link rel="stylesheet" href="../../../../css/style.css" />
	<style>
		html,
		body {
			height: 100%;
			min-height: 100%;
			text-align: center;
			font-size: 250%;
		}

		main:before {
			width: 100%;
			content: '';
			display: inline-block;
			vertical-align: middle;
		}
	</style>
</head>
<body>
	<main>
		ID - <?=$film_id?><br />
		<b id="timer"><?=$wait_to_redirect_time?></b>
	</main>
</body>
</html>
