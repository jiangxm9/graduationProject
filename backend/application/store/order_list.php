<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 获取订单列表
 * 
 * 负责订单列表获取接口的实现
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

if($_GET['identity'])
    $resid = getResId(verifyResToken());
else {
    $empid = getEmpId(verifyEmpToken());
    $resid = getResIdByEmp($empid);
}
if (!$resid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getOrderList();
        break;
    default:
        returnJson(400);
}

function getOrderList() {
    global $resid;
    
    if (!isset($_GET['count'])) returnJson(400);
    if (!empty($_GET['count']))
        $orderlist = getDESCList($resid, $_GET['count']);
    if(empty($orderlist)) returnJson(200, null);

    returnJson(200, $orderlist);
}