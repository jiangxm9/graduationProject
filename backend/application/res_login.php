<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 登录接口
 * 
 * 提供登录操作，需要POST输入用户名与密码参数。
 * 
 * @author  jiangxm9
 * @version 1.0
 */

include ( '../includes/auth.php' );
include ( '../includes/functions.php' );

$username = strtolower($_POST['username']);
$password = $_POST['password'];

//检查用户名及密码格式的合法性
if (!checkUsername($username) || !checkPassword($password)) returnJson(403);

//检查用户名密码是否为空
if (strlen($username) <= 0 || strlen($password) <= 0) {
    returnJson(400);
}

//生成Token
$token = verifyResPassword($username, $password);
if ($token) {
    returnJson(200, array('token' => $token));
} else {
    returnJson(403);
}

