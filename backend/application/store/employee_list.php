<?php
/**
 * 店员列表获取类
 * 
 * 负责店员列表信息的获取
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
        getEmpList();
        break;
    default:
        returnJson(400);
}

function getEmpList() {
	global $resid;

    $EmpList = getEmployeeList($resid);
    if(empty($EmpList)){
        returnJson(200, null);
    }

    returnJson(200, $EmpList);
}