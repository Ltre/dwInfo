<?php

extract(ActionUtil::getTplArgs());

?>

<html>

<head>
	<title>通用执行器</title>
	<style>
	input{
		width: 500px;
	}
	label{
		width: 200px;
		text-align: right;
		display: inline-block;
	}
	button{
		margin-top: 10px;
		width: 700px;
	}
	</style>
</head>

<body>

	<div>
		<label>URL SHELL [ + PARAMS ] </label>
		<input type="text" id="shell" value="collectDwpassportOnLocal|50"><br>
		<label>刷新间隔：</label>
		<input type="text" id="sleep" value="3000" />毫秒<br>
		<label>刷新次数：</label>
		<input type="text" id="limit" value="3600" /><br>
		<button id="exec">执行</button>
	</div>

	<div id="logs"></div>

	<iframe width="504" height="709" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://j.map.baidu.com/aGdDy">

</body>

<script type="text/javascript" src="<?php echo JQUERY_LIB_PATH ?>"></script>

<script type="text/javascript">

$(function(){

	var execute = function(){
		var sleep = $('#sleep').val();
		var shell = $('#shell').val();
		var limit = $('#limit').val();

		while (limit > 0 && limit --) {
			/*setInterval(function(){
				$.get('?x=' + shell, function(data){
					$('#logs').append('<p>' + data + '</p>');
				});
			}, sleep);*/
			$.get('?x=' + shell, function(data){
				$('#logs').append('<p>' + data + '</p>');
			});
		}
	};
	
	$('#exec').click(execute);

});

</script>

</html>