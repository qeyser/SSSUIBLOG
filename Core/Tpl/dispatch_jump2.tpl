<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<link href="__PUBLIC__/Style/default/Css/public.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .notice_wrap {width: 49.6em;height: 9.92em;overflow: hidden;position: relative;top:20em;box-shadow: 0 2px 3px #4d4d4d;margin: auto;clear:both;filter: progid:DXImageTransform.Microsoft.Shadow(color='#4d4d4d', Direction=185, Strength=3);}
    h1 {font-size: 1.4em;text-indent: 0;position: absolute;top:1em;left: 7.5em;color: #fff;text-shadow:0px 1px 1px #333;filter: progid:DXImageTransform.Microsoft.Shadow(color='#404040', Direction=185, Strength=2);}
    .info,.jump {font-size: 1.2em;position: absolute;top: 3em;left: 8.7em;color: #fff;text-shadow:0px 1px 1px #333;overflow: hidden;filter: progid:DXImageTransform.Microsoft.Shadow(color='#404040', Direction=185, Strength=2);}
    .n_right {background: url(__PUBLIC__/Style/default/Images/notice.png) no-repeat;}
    .n_wrong {background: url(__PUBLIC__/Style/default/Images/notice.png) 0 -100px no-repeat;}
    .jump{top:75px;}
    .info {font-size: 2em;top:1.6em;left:5em;}
    a{color:white;}
</style>
</head>
<body>
<div class="notice_wrap <present name="message">n_right<else/>n_wrong</present>">
	<present name="message">
    	<!-- <h1>操作成功</h1> -->
    	<p class="info"><?php echo($message); ?></p>
	<else/>
    	<!-- <h1>操作失败</h1> -->
    	<p class="info"><?php echo($error); ?></p>
	</present>
	<p class="jump">页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
</p>
</div>
</body>
</html>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>