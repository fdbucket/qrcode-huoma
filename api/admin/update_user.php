<?php
// 设置页面返回的字符编码为json格式
header("Content-type:application/json");

// 开启session，验证登录状态
session_start();
if (isset($_SESSION["session_admin"])) {

    // 数据库配置
    include '../../db_config/db_config.php';

    // 创建连接
    $conn = new mysqli($db_url, $db_user, $db_pwd, $db_name);

    // 获得表单POST过来的数据
    $email = $_POST["email"];
    $user_id = $_POST["user_id"];

    if (empty($email)) {
        $result = array(
            "code" => "101",
            "msg" => "邮箱不得为空"
        );
    } else if (empty($user_id)) {
        $result = array(
            "code" => "103",
            "msg" => "非法请求"
        );
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = array(
            "code" => "104",
            "msg" => "邮箱无效"
        );
    } else {
        // 设置字符编码为utf-8
        mysqli_query($conn, "SET NAMES UTF-8");
        // 更新数据库
        mysqli_query($conn, "UPDATE qrcode_user SET email='$email' WHERE user_id=" . $user_id);
        $result = array(
            "code" => "100",
            "msg" => "更新成功"
        );
    }
} else {
    $result = array(
        "code" => "105",
        "msg" => "未登录"
    );
}

// 输出JSON格式的数据
echo json_encode($result, JSON_UNESCAPED_UNICODE);
