
<html lang="en">
<head>
    <meta charset="GBK">
    <title>主页</title>
</head>
<frameset rows="100,*" cols="*" scrolling="No" framespacing="0" frameborder="no" border="0">
	<frame src="<?php echo base_url('/frame/head.php')?>" name="headmenu" id="mainFrame" title="mainFrame"><!-- 引用头部 -->
<!-- 引用左边和主体部分 -->
<frameset rows="100*" cols="220,*" scrolling="No" framespacing="0" frameborder="no" border="0">
	<frame src="<?php echo base_url('/frame/left.html')?>" name="leftmenu" id="mainFrame" title="mainFrame">
	<frame src="<?php echo base_url('/frame/main.php')?>" name="main" scrolling="yes" noresize="noresize" id="rightFrame" title="rightFrame">
</frameset>
</frameset>
</html>