<?php
header("Access-Control-Allow-Origin:http://localhost:8080/"); 
/**
 * 员工任务列表获取类
 * 
 * 负责员工任务列表获取接口的实现
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

if($_GET['identity']){
    $resid = getResId(verifyResToken());
    $empid = $_GET['employeeid'];
}
else {
    $empid = getEmpId(verifyEmpToken());
    $resid = getResIdByEmp($empid);
}
if (!$resid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getTaList();
        break;
    default:
        returnJson(400);
}

function getTaList() {
    global $resid;
    global $empid;

    $taskList = getTaskListByEmpId($resid, $empid);
    if(empty($taskList)){
        returnJson(200, null);
    }

    returnJson(200, $taskList);
}