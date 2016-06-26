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
</head>
<body>
	<b id="timer"><?=$this->wait_to_redirect_time?></b>
</body>
</html>