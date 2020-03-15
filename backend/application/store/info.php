<?php
/**
 * 商家信息修改类
 * 
 * 负责商家信息修改接口的实现
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getInfo();
        break;
    case 'POST':
        postInfo();
        break;
    default:
        returnJson(400);
}

function getInfo() {
    if($_GET['identity'])
        $resid = getResId(verifyResToken());
    else {
        $empid = getEmpId(verifyEmpToken());
        $resid = getResIdByEmp($empid);
    }

    if (!$resid) returnJson(401);

    $resInfo = getResInfo($resid);

    //若头像为空，则设置为默认头像
    if ($resInfo['icon'] == '') $resInfo['icon'] = DEFAULT_AVATAR;

    returnJson(200, $resInfo);
}

function postInfo() {
    $resid = getResId(verifyResToken());
    if (!$resid) returnJson(401);

    if (!isset($_POST['name'])) returnJson(400);
    editResInfo($resid, $_POST['name']);
    returnJson(200);
}