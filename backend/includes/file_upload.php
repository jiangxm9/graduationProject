<?php
/**
 * 文件上传类
 * 
 * 
 * @author  jiangxm9
 * @version 1.0
 */

require(__DIR__ . '/../vendor/autoload.php');

include(__DIR__ . '/../settings/settings.php');

use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 上传图片
 * 
 * @param string $path 图片路径(可为空)
 * @param string $filename 图片文件名(可为空)
 * @return string 存储的文件名
 */
function uploadImage($path = '', $filename = '') {
    if(file_exists("../../upload/" . $_FILES["file"]["name"]))
        return $_FILES["file"]["name"];
    else {
        $destination_path = getcwd().DIRECTORY_SEPARATOR;
        $target_path = $destination_path . '../../upload/'. basename( $_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_path);
        return $_FILES["file"]["name"];
    }
}
