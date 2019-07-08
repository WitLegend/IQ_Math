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
                    url: "http://www.iq.com/admin/insert_question",
                    dataType:'json',
                    data: {
                        'question':$(" input[ name='question' ] ").val(),
                        'choice_a':$(" input[ name='choice_a' ] ").val(),
                        'choice_b':$(" input[ name='choice_b' ] ").val(),
                        'choice_c':$(" input[ name='choice_c' ] ").val(),
                        'choice_d':$(" input[ name='choice_d' ] ").val(),
                        'ture_choice':$(" input[ name='ture_choice' ] ").val()
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
	<link rel="stylesheet" type="text/css" href="../css/myscore.css" />
</head>

<body>
	<div class="score_show">
		<form id="myform">
			<table class="score_table">
				<tr>
					<th>题目内容</th><th>选择a</th><th>选择b</th><th>选择c</th><th>选择d</th><th>正确选项</th>
				</tr>
				<tr>
					<td><input name="question" type="text" class="type_input"></td>
					<td><input name="choice_a" type="text" class="type_input"></td>
					<td><input name="choice_b" type="text" class="type_input"></td>
					<td><input name="choice_c" type="text" class="type_input"></td>
					<td><input name="choice_d" type="text" class="type_input"></td>
					<td><input name="ture_choice" type="text" class="type_input"></td>
				</tr>
				<tr>
					<td colspan="6"><input type="button" class="search_btn" value="新增题目" id="tijiao"></td>
				</tr>
		  </table>
		</form>
	</div>
</body>
</html>