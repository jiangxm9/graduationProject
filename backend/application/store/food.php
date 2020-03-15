<?php
/**
 * 商品管理类
 * 
 * 负责商品的添加、修改和删除
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

$resid = getResId(verifyResToken());
if (!$resid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
        putFood();
        break;
    case 'POST':
        postFood();
        break;
    case 'DELETE':
    	removeFood();
        break;
    default:
        returnJson(400);
}

function putFood() {
    global $resid;
	parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['foodname']) || !isset($data['price']) || !isset($data['imgurl'])) 
        returnJson(400);
    if(empty($data['foodname']) || empty($data['price']))
        returnJson(400);
	$returnArray = addFood($resid, $data['foodname'], $data['price'], $data['imgurl']);
    if($returnArray['id'] == 0 || empty($returnArray))
        returnJson(400);
    returnJson(200, $returnArray);
}

function postFood() {
    global $resid;
    if (!isset($_POST['foodid']) || !isset($_POST['foodname']) || !isset($_POST['price']) || !isset($_POST['imgurl'])) 
        returnJson(400);
    if(empty($_POST['foodid']) || empty($_POST['foodname']) || empty($_POST['price']))
        returnJson(400);
    if(!findFood($_POST['foodid']))
        returnJson(400);
	modifyFood($_POST['foodid'], $_POST['foodname'], $_POST['price'], $_POST['imgurl']);
	returnJson(200);
}

function removeFood() {
    global $resid;
	parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['foodid']) || empty($data['foodid'])) 
        returnJson(400);
    if(!findFood($data['foodid']))
        returnJson(400);
	deleteFood($data['foodid']);
	returnJson(200);
}