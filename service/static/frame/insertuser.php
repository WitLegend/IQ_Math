<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/public.js"></script>
	<script type="text/javascript">
        $(function () {
            $("#tijiao").click(function () {
                $.ajax({
                    type: 'get',
                    url: "http://www.iq.com/admin/add_user",
                    dataType:'json',
                    data: {
                        'username':$(" input[ name='username' ] ").val(),
                        'password':$(" input[ name='password' ] ").val(),
					},
                }).success(function (data) {
                    if (data.code == 0 && data.data == 'ok') {
                        console.log(data)
                        alert("添加成功");
                        $(".type_input").val("");
                    }else {
                        alert("未知错误");
                    }
                }).error(function () {
                    alert("添加失败");
                });
            });
        });
	</script>
	<title>我的成绩</title>
	<link rel="stylesheet" type="text/css" href="../css/insertuser.css" />
</head>

<body>
	<div class="score_show">
		<form id="myform">
			<table class="score_table">
				<tr>
					<th>用户名</th><th><input name="username" type="text" class="type_input"></th>
				</tr>
				<tr>
					<td>密码</td><td><input name="password" type="text" class="type_input"></td>
				</tr>
				<tr>
					<td colspan="6"><input type="button" class="search_btn" value="新增用户" id="tijiao"></td>
				</tr>
		  </table>
		</form>
	</div>
</body>
</html>