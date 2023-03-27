<?php
// 判断登录状态
session_start();
if (!isset($_SESSION["session_user"])) {
    // 跳转到登陆界面
    header("Location:./login.php");
}

// 当前登录的用户
$lguser = $_SESSION["session_user"];

// 页面字符编码
header("Content-type:text/html;charset=utf-8");

// 数据库配置
include '../db_config/db_config.php';

// 创建连接
$conn = new mysqli($db_url, $db_user, $db_pwd, $db_name);

// 获取设置项
$sql_set = "SELECT * FROM qrcode_settings";
$result_set = $conn->query($sql_set);
if ($result_set->num_rows > 0) {
    while ($row_set = $result_set->fetch_assoc()) {
        $title = $row_set['title'];
        $keywords = $row_set['keywords'];
        $description = $row_set['description'];
        $favicon = $row_set['favicon'];
    }
    if ($title == null || empty($title) || $title == '') {
        $title = "二维码管理系统";
        $keywords = "活码,群活码,微信群活码系统,活码系统,群活码,不过期的微信群二维码,永久群二维码";
        $description = "这是一套开源、免费、可上线运营的二维码管理系统，便于协助自己、他人进行微信私域流量资源获取，更大化地进行营销推广活动！降低运营成本，提高工作效率，获取更多资源。";
        $favicon = "../assets/images/favicon.png";
    }
} else {
    $title = "二维码管理系统";
    $keywords = "活码,群活码,微信群活码系统,活码系统,群活码,不过期的微信群二维码,永久群二维码";
    $description = "这是一套开源、免费、可上线运营的二维码管理系统，便于协助自己、他人进行微信私域流量资源获取，更大化地进行营销推广活动！降低运营成本，提高工作效率，获取更多资源。";
    $favicon = "../assets/images/favicon.png";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>客服活码 - <?php echo $title; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/huoma.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/theme.css">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="description" content="<?php echo $description; ?>">
    <link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon" />
</head>

<body>

    <!-- 全局信息提示框 -->
    <div id="Result" style="display: none;"></div>

    <!-- 顶部导航栏 -->
    <div id="topbar">
        <div class="container">
            <span class="topbar-title"><?php echo $title; ?></span>
            <span class="topbar-login-link"><?php echo $lguser; ?><a href="logout.php">退出</a></span>
        </div>
    </div>

    <!-- 操作区 -->
    <div class="container">
        <br />
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">二维码管理系统</a></li>
                <li class="breadcrumb-item active" aria-current="page">图片二维码</li>
            </ol>
        </nav>
        <!-- 左右布局 -->
        <!-- 左侧布局 -->
        <div class="left-nav">
            <button type="button" class="btn btn-zdy">二维码列表</button>
            <button type="button" class="btn btn-zdylight" data-toggle="modal" data-target="#addimg_modal">新建</button>
            <a href="./"><button type="button" class="btn btn-zdylight">返回首页</button></a>
        </div>

        <?php

        //计算总活码数量
        $sql_img = "SELECT * FROM qrcode_img WHERE img_user='$lguser'";
        $result_img = $conn->query($sql_img);
        $allimg_num = $result_img->num_rows;

        //每页显示的活码数量
        $lenght = 10;

        //当前页码
        @$page = $_GET['p'] ? $_GET['p'] : 1;

        //每页第一行
        $offset = ($page - 1) * $lenght;

        //总数页
        $allpage = ceil($allimg_num / $lenght);

        //上一页
        $prepage = $page - 1;
        if ($page == 1) {
            $prepage = 1;
        }

        //下一页
        $nextpage = $page + 1;
        if ($page == $allpage) {
            $nextpage = $allpage;
        }

        // 获取落地页域名
        $sql_ldym = "SELECT * FROM qrcode_domain WHERE ym_type='2'";
        $result_ldym = $conn->query($sql_ldym);

        // 获取群活码列表
        $sql = "SELECT * FROM qrcode_img WHERE img_user='$lguser' ORDER BY ID DESC limit {$offset},{$lenght}";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<!-- 右侧布局 -->
      <div class="right-nav">
        <table class="table">
          <thead>
            <tr>
              <th>标题</th>
              <th>状态</th>
              <th>模式</th>
              <th>时间</th>
              <th>访问</th>
              <th style="text-align: center;">操作</th>
            </tr>
          </thead>
          <tbody>';

            // 遍历数据
            while ($row = $result->fetch_assoc()) {
                $img_title = $row["img_title"];
                $img_id = $row["img_id"];
                $img_qrcode = $row["img_qrcode"];
                $img_num = $row["img_num"];
                $img_shuoming = $row["img_shuoming"];
                $img_update_time = $row["img_update_time"];
                $img_fwl = $row["img_fwl"];
                $img_status = $row["img_status"];
                $img_moshi = $row["img_moshi"];

                // 渲染到UI
                echo '<tr>';
                echo '<td class="td-title">' . $img_title . '</td>';
                if ($img_status == 1) {
                    echo '<td class="td-status"><span class="badge badge-success">正常</span></td>';
                } else if ($img_status == 2) {
                    echo '<td class="td-status"><span class="badge badge-danger">暂停</span></td>';
                } else if ($img_status == 3) {
                    echo '<td class="td-status"><span class="badge badge-danger">封禁</span></td>';
                }
                if ($img_moshi == 1) {
                    echo '<td class="td-status">阈值</td>';
                } else if ($img_moshi == 2) {
                    echo '<td class="td-status">随机</td>';
                }
                echo '<td class="td-status">' . $img_update_time . '</td>
              <td class="td-fwl">' . $img_fwl . '</td>
              <td class="td-caozuo" style="text-align: center;">
              <div class="btn-group dropleft">
              <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-secondary" style="cursor:pointer;">•••</span></span>
              <div class="dropdown-menu">
              <a class="dropdown-item" href="./edi_img.php?imgid=' . $img_id . '&home=ediimg">编辑</a>
              <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#share_img" id="' . $img_id . '" onclick="shareimg(this);">分享</a>
              <a class="dropdown-item" href="javascript:;" id="' . $img_id . '" onclick="delimg(this);" title="点击后马上就删除的哦！">删除</a>
              </div>
              </div>
              </td>';
                echo '</tr>';
            }

            // 分页
            echo '<div class="fenye"><ul class="pagination pagination-sm">';
            if ($page == 1 && $allpage == 1) {
                // 当前页面是第一页，并且仅有1页
                // 不显示翻页控件
            } else if ($page == 1) {
                // 当前页面是第一页，还有下一页
                echo '<li class="page-item"><a class="page-link" href="./img.php">首页</a></li>
            <li class="page-item"><a class="page-link" href="./img.php?p=' . $nextpage . '">下一页</a></li>
            <li class="page-item"><a class="page-link" href="#">当前是第' . $page . '页</a></li>';
            } else if ($page == $allpage) {
                // 当前页面是最后一页
                echo '<li class="page-item"><a class="page-link" href="./img.php">首页</a></li>
            <li class="page-item"><a class="page-link" href="./img.php?p=' . $prepage . '">上一页</a></li>
            <li class="page-item"><a class="page-link" href="#">当前页面是最后一页</a></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="./img.php">首页</a></li>
            <li class="page-item"><a class="page-link" href="./img.php?p=' . $prepage . '">上一页</a></li>
            <li class="page-item"><a class="page-link" href="./img.php?p=' . $nextpage . '">下一页</a></li>
            <li class="page-item"><a class="page-link" href="#">当前是第' . $page . '页</a></li>';
            }
            echo '</ul></div></div></tbody></table>';
        } else {
            echo '<div class="right-nav">暂无二维码，请点击创建活码</div>';
        }

        echo '<!-- 分享模态框 -->
  <div class="modal fade" id="share_img">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
   
        <!-- 模态框头部 -->
        <div class="modal-header">
          <h4 class="modal-title">分享微信活码</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
   
        <!-- 模态框主体 -->
        <div class="modal-body">
          <p class="link"></p>
          <p class="qrcode"></p>
        </div>
   
        <!-- 模态框底部 -->
        <div class="modal-footer">
          <button type="button" class="btn btn-tjzdy" data-dismiss="modal">关闭</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 新建 -->
  <div class="modal fade" id="addimg_modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
   
        <!-- 模态框头部 -->
        <div class="modal-header">
          <h4 class="modal-title">新建图片二维码</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- 模态框主体 -->
        <div class="modal-body">
          <!-- 标题 -->
          <form onsubmit="return false" id="addimg" enctype="multipart/form-data">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">标题</span>
            </div>
            <input type="text" class="form-control" placeholder="请输入标题" name="img_title">
          </div>';

        // 落地页域名
        echo '<select class="form-control" name="img_ldym" style="-webkit-appearance:none;">
          <option value="">请选择落地页域名</option>';

        if ($result_ldym->num_rows > 0) {
            while ($row_ldym = $result_ldym->fetch_assoc()) {
                $ldym = $row_ldym["yuming"];
                echo '<option value="' . $ldym . '">' . $ldym . '</option>';
            }
            // 同时也可以选择当前系统使用的域名
            echo '<option value="http://' . $_SERVER['HTTP_HOST'] . '">http://' . $_SERVER['HTTP_HOST'] . '</option>';
        } else {
            // 没有绑定落地页，使用当前系统使用的域名
            echo '<option value="http://' . $_SERVER['HTTP_HOST'] . '">http://' . $_SERVER['HTTP_HOST'] . '</option>';
        }
        echo '</select>';

        // 选择模式
        echo '<div class="radio">
            <input id="radio-1" name="img_moshi" type="radio" value="1" checked>
            <label for="radio-1" class="radio-label">阈值模式</label>
            <input id="radio-2" name="img_moshi" type="radio" value="2">
            <label for="radio-2" class="radio-label">随机模式</label>
          </div>

          <!-- 说明 -->
          <br/>
          <p>创建完成后，点击 <span class="badge badge-secondary">•••</span> 编辑，上传客服二维码。</p>

          <div class="upload_status"></div>

        </div>
   
        <!-- 模态框底部 -->
        <div class="modal-footer">
          <button type="button" class="btn btn-tjzdy" onclick="addimg();">立即创建</button>
        </div>
        </form>
   
      </div>
    </div>
  </div>
</div>';
        ?>

        <script>
            // 延迟关闭信息提示框
            function closesctips() {
                $("#Result").css('display', 'none');
                $("#addimg_modal .upload_status").css('display', 'none');
            }

            //监听个人微信二维码的显示状态
            $("#grimg_status").bind('input propertychange', function(e) {
                //获取当前点击的状态
                var grimg_status = $(this).val();
                //如果开启备用群，则需要显示上传二维码和设置最大值
                if (grimg_status == 1) {
                    $("#grimg_upload").css("display", "block");
                } else if (grimg_status == 0) {
                    //否则隐藏，不显示
                    $("#grimg_upload").css("display", "none");
                }
            })

            //监听备用微信群二维码的开启状态
            $("#jimg_sm").bind('input propertychange', function(e) {
                //获取当前点击的状态
                var grimg_status = $(this).val();
                //如果开启备用群，则需要显示上传二维码和设置最大值
                if (grimg_status == 1) {
                    $("#jimg_sm_wenan").css("display", "block");
                } else if (grimg_status == 0) {
                    //否则隐藏，不显示
                    $("#jimg_sm_wenan").css("display", "none");
                }
            })

            // 创建微信活码
            function addimg() {
                $.ajax({
                    type: "POST",
                    url: "../api/user/add_img.php",
                    data: $('#addimg').serialize(),
                    success: function(data) {
                        // 创建成功
                        if (data.code == 100) {
                            $("#addimg_modal .upload_status").css("display", "block");
                            $("#addimg_modal .upload_status").html("<div class=\"alert alert-success\"><strong>" + data.msg + "</strong></div>");
                            // 关闭模态框
                            $('#addimg_modal').modal('hide');
                            // 刷新列表
                            location.reload();
                        } else {
                            $("#addimg_modal .upload_status").css("display", "block");
                            $("#addimg_modal .upload_status").html("<div class=\"alert alert-danger\"><strong>" + data.msg + "</strong></div>");
                        }
                    },
                    error: function() {
                        // 创建失败
                        $("#addimg_modal .upload_status").css("display", "block");
                        $("#addimg_modal .upload_status").html("<div class=\"alert alert-danger\"><strong>服务器发生错误</strong></div>");
                    }
                });
                // 关闭信息提示框
                setTimeout('closesctips()', 2000);
            }

            // 上传微信二维码
            var imgqrcode_lunxun = setInterval("upload_imgqrcode()", 2000);

            function upload_imgqrcode() {
                var imgqrcode_filename = $("#select_imgqrcode").val();
                if (imgqrcode_filename) {
                    clearInterval(imgqrcode_lunxun);
                    var addimg_form = new FormData(document.getElementById("addimg"));
                    $.ajax({
                        url: "upload.php",
                        type: "post",
                        data: addimg_form,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.res == 400) {
                                $("#addimg_modal .upload_status").css("display", "block");
                                $("#addimg_modal .upload_status").html("<div class=\"alert alert-success\"><strong>" + data.msg + "</strong></div>");
                                $("#addimg_modal .imgqrcode").val(data.path);
                                $("#addimg_modal .text").text("已上传");
                            } else {
                                $("#addimg_modal .upload_status").css("display", "block");
                                $("#addimg_modal .upload_status").html("<div class=\"alert alert-danger\"><strong>" + data + "</strong></div>");
                            }
                        },
                        error: function(data) {
                            $("#addimg_modal .upload_status").css("display", "block");
                            $("#addimg_modal .upload_status").html("<div class=\"alert alert-danger\"><strong>" + data.msg + "</strong></div>");
                        },
                        beforeSend: function(data) {
                            $("#addimg_modal .upload_status").css("display", "block");
                            $("#addimg_modal .upload_status").html("<div class=\"alert alert-warning\"><strong>正在上传...</strong></div>");
                        }
                    })
                    // 关闭信息提示框
                    setTimeout('closesctips()', 2000);
                } else {
                    // console.log("等待上传");
                }
            }


            // 删除微信活码
            function delimg(event) {
                // 获得当前点击的微信活码id
                var del_imgid = event.id;
                // 执行删除动作
                $.ajax({
                    type: "POST",
                    url: "../api/user/del_img.php",
                    // url: "../api/user/del_img.php?imgid=" + del_imgid,
                    data: {
                        imgid: del_imgid
                    },
                    success: function(data) {
                        if (data.code == "100") {
                            $("#Result").css("display", "block");
                            $("#Result").html("<div class=\"alert alert-success\"><strong>" + data.msg + "</strong></div>");
                            // 刷新列表
                            // location.reload();
                            setTimeout('location.reload()', 800);
                        } else {
                            $("#Result").css("display", "block");
                            $("#Result").html("<div class=\"alert alert-danger\"><strong>" + data.msg + "</strong></div>");
                        }
                    },
                    error: function() {
                        $("#Result").css("display", "block");
                        $("#Result").html("<div class=\"alert alert-danger\"><strong>服务器发生错误</strong></div>");
                    }
                });
                // 关闭信息提示框
                setTimeout('closesctips()', 2000);
            }


            // 分享微信活码
            function shareimg(event) {
                // 获得当前点击的微信活码id
                var share_imgid = event.id;
                $.ajax({
                    type: "GET",
                    url: "../api/user/share_img.php?imgid=" + share_imgid,
                    success: function(data) {
                        // 分享成功
                        $("#share_img .modal-body .link").text("链接：" + data.url + "");
                        $("#share_img .modal-body .qrcode").html("<img src='./qrcode.php?content=" + data.url + "' width='200'/>");
                    },
                    error: function() {
                        // 分享失败
                        $("#Result").css("display", "block");
                        $("#Result").html("<div class=\"alert alert-danger\"><strong>服务器发生错误</strong></div>");
                    }
                });
                // 关闭信息提示框
                setTimeout('closesctips()', 2000);
            }
        </script>
</body>

</html>