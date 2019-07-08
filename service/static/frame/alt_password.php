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
                    type: 'post',
                    url: "http://www.iq.com/admin/alt_password",
                    dataType:'json',
                    data: {
                        'old_password':$(" input[ name='old_password' ] ").val(),
                        'new_password':$(" input[ name='new_password' ] ").val(),
					},
                }).success(function (data) {
                    if (data.code == 0 && data.data == 'ok') {
                        console.log(data)
                        alert("修改成功");
                        $(".type_input").val("");
                    }else {
                        alert("修改失败");
                        $(".type_input").val("");
                    }
                }).error(function () {
                    alert("修改失败");
                    $(".type_input").val("");
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
					<th>原密码</th><th><input name="old_password" type="text" class="type_input"></th>
				</tr>
				<tr>
					<td>新密码</td><td><input name="new_password" type="text" class="type_input"></td>
				</tr>
				<tr>
					<td colspan="6"><input type="button" class="search_btn" value="修改密码" id="tijiao"></td>
				</tr>
		  </table>
		</form>
	</div>
</body>
</html>