<!DOCTYPE html>
<html>
<head>
  <title>用户管理后台 - 二维码管理系统</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../assets/css/huoma.css">
</head>
<body>

<!-- 全局信息提示框 -->
<div id="Result" style="display: none;"></div>

<?php
// 页面字符编码
header("Content-type:text/html;charset=utf-8");
// 判断登录状态
session_start();
if(isset($_SESSION["session_admin"])){

  // 数据库配置
  include '../db_config/db_config.php';
  include '../db_config/VersionCheck.php';

  // 创建连接
  $conn = new mysqli($db_url, $db_user, $db_pwd, $db_name);

  echo '<!-- 顶部导航栏 -->
<div id="topbar">
  <div class="container">
    <span class="topbar-title"><a href="./">二维码管理系统后台</a></span>
    <span class="topbar-login-link">'.$_SESSION["session_admin"].'<a href="logout.php">退出</a></span>
  </div>
</div>

<!-- 操作区 -->
<div class="container">';
  if ($version !== $v_str_v) {
    echo '<br/>
    <div class="alert alert-warning">
      <strong>'.$v_str_m.'<a href="'.$v_str_u.'">点击更新</a></strong>
    </div>';
  }
  echo '<br/>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="./">二维码管理系统</a></li>
      <li class="breadcrumb-item active" aria-current="page">系统设置</li>
    </ol>
  </nav>
  <p>域名、支付接口、SEO、邮件服务的配置</p>
  
  <!-- 左右布局 -->
  <!-- 电脑端横排列表 -->
  <div class="left-nav">
    <button type="button" class="btn btn-dark">域名配置</button>
    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#addym_modal">添加域名</button>
    <a href="./payset.php"><button type="button" class="btn btn-light">支付接口</button></a>
    <a href="./seo_set.php"><button type="button" class="btn btn-light">SEO设置</button></a>
    <a href="./email_set.php"><button type="button" class="btn btn-light">邮件服务</button></a>
    <a href="./index.php"><button type="button" class="btn btn-light">返回首页</button></a>
  </div>';

  // 获取域名列表
  $sql_yuming = "SELECT * FROM qrcode_domain ORDER BY ID DESC";
  $result_yuming = $conn->query($sql_yuming);
  
  if ($result_yuming->num_rows > 0) {
      echo '<!-- 右侧布局 -->
      <div class="right-nav">
        <table class="table">
          <thead>
            <tr>
              <th style="width:80%;">域名</th>
              <th>类型</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>';

          // 遍历数据
          while($row_yuming = $result_yuming->fetch_assoc()) {
            $id = $row_yuming["id"];
            $yuming = $row_yuming["yuming"];
            $ym_type = $row_yuming["ym_type"];

            // 渲染到UI
            echo '<tr>';
              echo '<td class="td-title">'.$yuming.'</td>';
              if ($ym_type == '1') {
                echo '<td class="td-caozuo">入口域名</td>';
              }else{
                echo '<td class="td-caozuo">落地域名</td>';
              }
              echo '<td class="td-caozuo">
              <div class="btn-group dropleft">
                <span class="badge badge-secondary" style="cursor:pointer;" id="'.$id.'" onclick="delym(this);">删除</span>
              </div>
              </td>';
            echo '</tr>';
          }

         echo '</div></tbody></table>';

  }else{
    echo '<div class="right-nav">暂无域名，请添加</div>';
  }

  echo '<!-- 添加域名 -->
  <div class="modal fade" id="addym_modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
   
        <!-- 模态框头部 -->
        <div class="modal-header">
          <h4 class="modal-title">添加域名</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
   
        <!-- 模态框主体 -->
        <div class="modal-body">
          <!-- 域名 -->
          <form onsubmit="return false" id="addym">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">域名</span>
            </div>
            <input type="text" class="form-control" placeholder="请输入域名" name="yuming">
          </div>

    		  <div class="radio">
    			  <input id="radio-1" name="ym_type" type="radio" value="1">
    			  <label for="radio-1" class="radio-label">入口域名</label>
    			  <input id="radio-2" name="ym_type" type="radio" value="2">
    			  <label for="radio-2" class="radio-label">落地域名</label>
    		  </div><br/>

          <p style="font-size:14px;">域名格式：http(s)://www.xxx.com 注意：结尾不得带 <span class="badge badge-secondary" style="cursor:pointer;"> / </span></p>
          <p style="font-size:14px;">添加完成后，还需将域名解析到你的服务器。</p>

          <!-- 提交 -->
          <button type="button" class="btn btn-dark" onclick="addym();">立即添加</button>
          </form>

        </div>
   
        <!-- 模态框底部 -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
        </div>
   
      </div>
    </div>
  </div>
</div>';
}else{
  // 跳转到登陆界面
  header("Location:login.php");
}
?>

</div>

<script>
// 延迟关闭信息提示框
function closesctips(){
  $("#Result").css('display','none');
}

// 添加域名
function addym(){
  $.ajax({
      type: "POST",
      url: "../api/admin/add_ym.php",
      data: $('#addym').serialize(),
      success: function (data) {
        // 添加成功
        if (data.code==100) {
          $("#Result").css("display","block");
          $("#Result").html("<div class=\"alert alert-success\"><strong>"+data.msg+"</strong></div>");
          // 关闭模态框
          $('#addwx_modal').modal('hide');
          // 刷新列表
          location.reload();
        }else{
          $("#Result").css("display","block");
          $("#Result").html("<div class=\"alert alert-danger\"><strong>"+data.msg+"</strong></div>");
        }
      },
      error : function() {
        // 添加失败
        $("#Result").css("display","block");
        $("#Result").html("<div class=\"alert alert-danger\"><strong>服务器发生错误</strong></div>");
      }
  });
  // 关闭信息提示框
  setTimeout('closesctips()', 2000);
}

// 删除微信活码
function delym(event){
  // 获得当前点击的id
  var del_ymid = event.id;
  // 执行删除动作
  $.ajax({
      type: "GET",
      url: "../api/admin/del_ym.php?ymid="+del_ymid,
      success: function (data) {
        if (data.code == "100") {
          $("#Result").css("display","block");
          $("#Result").html("<div class=\"alert alert-success\"><strong>"+data.msg+"</strong></div>");
          // 刷新列表
          location.reload();
        }else{
          $("#Result").css("display","block");
          $("#Result").html("<div class=\"alert alert-danger\"><strong>"+data.msg+"</strong></div>");
        }
      },
      error : function() {
        $("#Result").css("display","block");
        $("#Result").html("<div class=\"alert alert-danger\"><strong>服务器发生错误</strong></div>");
      }
  });
  // 关闭信息提示框
  setTimeout('closesctips()', 2000);
}

</script>
</body>
</html>