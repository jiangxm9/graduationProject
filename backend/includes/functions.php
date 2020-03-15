<?php
/**
 * 杂项函数类
 * 
 * 此处放置一些无法明确分类的函数。
 * 
 * @author  jiangxm9
 * @version 1.1
 */
include_once(__DIR__ . '/../settings/settings.php');

//临时增加跨域问题处理
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:Content-Type, Authorization, Accept, Origin, X-Requested-With');
header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE');

//DEBUG
ini_set("display_errors","On");
error_reporting(E_ALL);

//状态码列表
const STATUS_CODE = array(
    200 => 'OK',
    201 => 'Created',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    429 => 'Too Many Requests',
    500 => 'Internal Server Error',
    502 => 'Bad Gateway',
    504 => 'Gateway Timeout',
    1001 => 'Username Length Too Short(<4)',
    1002 => 'Password Length Too Short(<8)',
    1003 => 'Username Length Too Long(>=20)',
    1004 => 'Password Length Too Long(>=20)',
    1005 => 'Invaild Username',
    1006 => 'Invaild Password',
    1010 => 'Invaild Token',
    1011 => 'Token Already Expired'
);

/**
 * 为JSON对象附加状态码，返回并中止此次处理
 * 
 * @param int $httpCode HTTP状态码
 * @param array $jsonObj 输入的JSON对象
 */
function returnJson($httpCode, $jsonObj = array())
{
    //如果状态码不存在于列表中，则返回500
    if (!array_key_exists($httpCode, STATUS_CODE)) {
        $httpCode = 500;
    }
    $outputs = array();
    $outputs['status'] = $httpCode;
    $outputs['msg'] = STATUS_CODE[$httpCode];
    if ($jsonObj === null || count($jsonObj) > 0) $outputs['data'] = $jsonObj;
    die(json_encode($outputs, JSON_UNESCAPED_UNICODE));
}

/**
 * 生成图片链接(由于是在本地测试未考虑真正部署在远程服务器上的情况，如要部署需要进一步修改)
 * 
 * @param string $filename 存储的文件名
 * @return string 生成的链接
 */
function getImageLink($filename) {
    return 'http' . '://' . HOST_NAME . '/backend/upload/' . $filename;
}


