<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 店员管理
 * 
 * 负责增，删，改店员的接口的实现
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
        putEmployee();
        break;
    case 'POST':
        postEmployee();
        break;
    case 'DELETE':
        deleteEmployee();
        break;
    default:
        returnJson(400);
}

function putEmployee() {
    global $resid;
    parse_str(file_get_contents('php://input'), $_PUT);
    if (!isset($_PUT['employeeid']) || !isset($_PUT['employeename']) || !isset($_PUT['employeejob']) || !isset($_PUT['employeewage'])) 
        returnJson(400);
    if(empty($_PUT['employeeid']))
        returnJson(400);
    if(joinRes($_PUT['employeeid'], $resid, $_PUT['employeename'], $_PUT['employeejob'], $_PUT['employeewage']))
        returnJson(200);
    returnJson(400);
}

function postEmployee() {
    global $resid;
    if (!isset($_POST['employeeid']) || !isset($_POST['employeename']) || !isset($_POST['employeejob']) || !isset($_POST['employeewage'])) returnJson(400);
    if (!findEmpById($_POST['employeeid'], $resid)) returnJson(400);
    editEmployee($_POST['employeeid'], $_POST['employeename'], $_POST['employeejob'], $_POST['employeewage']);
    returnJson(200);
}

function deleteEmployee() {
    global $resid;
    parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['employeeid']) || empty($data['employeeid'])) 
        returnJson(400);
    if(!findEmpById($data['employeeid'], $resid))
        returnJson(400);
	dismissEmp($data['employeeid'], $resid);
	returnJson(200);
}