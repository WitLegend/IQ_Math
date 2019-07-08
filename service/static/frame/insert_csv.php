<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/public.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#avatsel1").click(function(){
                $("input[type='file']").trigger('click');
            });
            $("#avatval").click(function(){
                $("input[type='file']").trigger('click');
            });
            $("input[type='file']").change(function(){
                $("#avatval").val($(this).val());
            });
        });
        $(function () {
            $("#tijiao").click(function () {
                var formData = new FormData($('#myform')[0]);
                $.ajax({
                    type: 'post',
                    url: "http://www.iq.com/admin/insert_question_by_csv",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                }).success(function (data) {
                    if (data.code == 0 && data.data == 'ok') {
                        alert("添加成功");
                        $("#avatval,#avatar").val("");
                    }else {
                        alert("文件格式错误");
                    }
                }).error(function () {
                    alert("上传失败");
                });
            });
        });
    </script>
	<title>导入题目</title>
	<link rel="stylesheet" type="text/css" href="../css/score_insert.css" />
</head>

<body>
  <div class="all">
  <div class="tishi"><a href="//www.iq.com/admin/down_csv" style="color: red">下载scv模板</a></div>
      <div class="">
          <form enctype="multipart/form-data" id="myform">
<!--              <form action="//www.iq.com/admin/insert_question_by_csv" name="form" method="post" enctype="multipart/form-data" id="myform">-->
              <a href="javascript:void(0);" class="button-selectimg" id="avatsel1">选择文件</a>
              <input type="text" id="avatval" placeholder="请选择文件···" readonly="readonly" style="vertical-align: middle;"/>
              <input type="file" name="avatar" id="avatar"/>
              <input type="button" name="submit" value="上传" class="search_btn" id="tijiao"/>
<!--              <input type="button" name="submit" value="上传" class="search_btn" onclick="tijiao()"/>-->
          </form>
      </div>

  </div>
</body>
</html>
<style type="text/css">
    a[class="button-selectimg"]{color:#00A2D4;padding:6px 35px;border:1px dashed #00A2D4;border-radius:2px;}
    .input-file{margin:200px 300px;}
    input[id="avatval"]{padding:3px 6px;padding-left:10px;border:1px solid #E7EAEC;width:230px;height:25px;line-height:25px;border-left:3px solid #3FB7EB;background:#FAFAFB;border-radius:2px;}
    input[type='file']{border:0px;display:none;}
</style>