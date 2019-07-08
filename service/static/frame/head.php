
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>头部</title>
<link rel="stylesheet" type="text/css" href="../css/public.css" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/public.js"></script>
    <script type="text/javascript">
        $(function () {
            $("#out").click(function () {
                $.ajax({
                    type: 'post',
                    url: "http://www.iq.com/admin/out_login",
                    dataType:'json',
                    data: {},
                }).success(function (data) {
                    if (data.code == 0 && data.data == 'ok') {
                        top.location.replace("http://www.iq.com/admin");
                        // location.href("http://www.iq.com/admin");
                        // redirect("http://www.iq.com/admin")
                    }else {
                        alert("退出失败");
                    }
                }).error(function () {
                    alert("未知错误");
                });
            });
        });
    </script>
</head>

<body>
	<!-- 头部 -->
	<div class="head">
		<div class="headL">
			智 力 算 术 小 程 序 后 台 管 理 系 统
		</div>
		<div class="headR">
			<span style="color:#FFF;">欢迎：admin</span> <a href="#" rel="external" id="out">【退出】</a>
		</div>
	</div>
</body>
</html>