<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 注册接口
 * 
 * 提供注册操作，需要POST输入用户名与密码参数。
 * 
 * @author  jiangxm9
 * @version 1.0
 */

include ( '../includes/auth.php' );
include_once ( '../includes/functions.php' );

$username = strtolower($_POST['username']);
$password = $_POST['password'];

//检查用户名及密码格式的合法性
if (!checkUsername($username) || !checkPassword($password)) returnJson(403);

//检查用户名是否存在
if (isResExists($username)) returnJson(403);

//生成Token
$token = registRes($username, $password);
if ($token) {
    returnJson(200, array('token' => $token));
} else {
    returnJson(404);
}

