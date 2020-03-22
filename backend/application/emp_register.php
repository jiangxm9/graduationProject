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
if (isEmpExists($username)) returnJson(403);

//返回店员id
$empId = registEmp($username, $password);
if ($empId) {
    returnJson(200, array('id' => $empId));
} else {
    returnJson(404);
}

