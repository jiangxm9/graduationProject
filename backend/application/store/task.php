<?php
/**
 * 任务处理
 * 
 * 负责获取饭店任务列表,任务的增删改的接口的实现
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
    case 'GET':
        getTaskList();
        break;
    case 'PUT':
        putTask();
        break;
    case 'POST':
        postTask();
        break;
    case 'DELETE':
        deleteTask();
        break;
    default:
        returnJson(400);
}

function getTaskList() {
    global $resid;

    $taskList = getTaskListByResId($resid);
    if(empty($taskList)){
        returnJson(200, null);
    }

    returnJson(200, $taskList);
}


function putTask() {
    global $resid;
    parse_str(file_get_contents('php://input'), $_PUT);
	if (!isset($_PUT['employeeid']) || !isset($_PUT['task'])) 
        returnJson(400);
    if (empty($_PUT['employeeid']) || empty($_PUT['task'])) 
        returnJson(400);
    $returnArray = addTask($resid, $_PUT['employeeid'], $_PUT['task']);
    returnJson(200, $returnArray);
}

function postTask() {
    global $resid;
    if (!isset($_POST['taskid']) || !isset($_POST['task']) || !isset($_POST['evaluation']) || !isset($_POST['remark'])) returnJson(400);
    if (!findTaskById($_POST['taskid'], $resid)) returnJson(400);
    editTask($_POST['taskid'], $_POST['task'], $_POST['evaluation'], $_POST['remark']);
    returnJson(200);
}

function deleteTask() {
    global $resid;
    parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['taskid']) || empty($data['taskid'])) 
        returnJson(400);
    if(!findTaskById($data['taskid'], $resid))
        returnJson(400);
	dismissTask($data['taskid'], $resid);
	returnJson(200);
}