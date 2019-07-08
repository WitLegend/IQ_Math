<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="gbk">
	<title>智力算数管理系统登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('/css/login.css')?>">
    <script type="text/javascript" src="<?php echo base_url('/js/jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('/js/public.js')?>"></script>
    <script type="text/javascript">
        function login_btn() {
            var schoolnum = document.getElementById("schoolnum").value;
            var password = document.getElementById("password").value;
            verify_account(schoolnum, password);
        }
        $(function () {
            $("#tijiao").click(function () {
                $.ajax({
                    type: 'post',
                    url: "http://www.iq.com/admin/user_login",
                    dataType:'json',
                    data: {
                        'username':$(" input[ name='username' ] ").val(),
                        'password':$(" input[ name='password' ] ").val(),
                    },
                }).success(function (data) {
                    if (data.code == 0 && data.data == 'ok') {
                        // redirect('http://www.iq.com/admin/index')
                        window.location.replace("http://www.iq.com/admin/index");
                    }else {
                        alert("账号或密码错误");
                        $(".type_input").val("");
                    }
                }).error(function () {
                    alert("登陆失败");
                    $(".type_input").val("");
                });
            });
        });
    </script>
</head>
<body>
    <div class="denglu">
      <div class="title">智力算数管理系统</div>
            <h3>登录</h3>
        <form id="myform">
<!--            <form action="//www.iq.com/admin/login" method="post">-->
               <input name="username" type="text" id="username" class="kuang_txt schoolnum" placeholder="账号">
               <input name="password" type="password" id="password" class="kuang_txt password" placeholder="密码">
               <div class="forget">
<!--               	  <a href="#">忘记密码？</a>-->
                   <input name="" type="checkbox" value="" checked><span> 记住我</span>
               </div>
               <input name="登录" type="button" class="btn_zhuce" value="登录" id="tijiao">
            </form>
     <div class="jump">木有账号？<a href="./register.html">立即注册</a></div>
    </div>
</body>
</html>