<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 订单处理
 * 
 * 负责获取订单详情,订单上传,更改状态的接口的实现
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

parse_str(file_get_contents('php://input'), $temp);
if($temp['identity'])
    $resid = getResId(verifyResToken());
else {
    $empid = getEmpId(verifyEmpToken());
    $resid = getResIdByEmp($empid);
}
if (!$resid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getOrderDetail();
        break;
    case 'PUT':
        putOrder();
        break;
    case 'POST':
        postOrder();
        break;
    default:
        returnJson(400);
}

function getOrderDetail() {
    global $resid;
    if (!isset($_GET['id']) || $_GET['id'] == '') returnJson(400);
    if (!findOrder($resid, $_GET['id'])) returnJson(400);

    $iteminfo = getOrderItem($resid, $_GET['id']);

    if(empty($iteminfo)) returnJson(200, null);

    returnJson(200, $iteminfo);
}


function putOrder() {
    global $resid;
    parse_str(file_get_contents('php://input'), $_PUT);
	if (!isset($_PUT['order'])) 
        returnJson(400);
    $data = json_decode($_PUT['order'], true);
    if ($data && (is_object($data)) || (is_array($data) && !empty($data))) {
        $returnArray = addOrder($resid, $_PUT);
		returnJson(200, $returnArray);
    }
    else{
    	returnJson(400);
    }
	
}

function postOrder() {
    global $resid;
    if (!isset($_POST['id']) || !isset($_POST['status'])) returnJson(400);
    if ($_POST['id'] == '' || $_POST['status'] == '') returnJson(400);
    if (!findOrder($resid, $_POST['id'])) returnJson(400);

    editOrderStatus($_POST['id'], $_POST['status']);

    returnJson(200);
}