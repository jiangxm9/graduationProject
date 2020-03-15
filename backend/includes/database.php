<?php
/**
 * 数据库管理类
 * 
 * 负责构建SQL语句，与数据库进行交互，并生成接口提供给上一层。
 * 
 * @author  jiangxm
 * @version 1.0
 */

include_once(__DIR__ . '/../settings/settings.php');

createTables();

/**
 * 初始化MySQL连接
 * 
 * @return mysqli MySQL连接对象
 */
function initConnection()
{
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($mysql, "utf8");
    mysqli_query($mysql, "set character set 'utf8'");
    mysqli_query($mysql, "set names 'utf8'");
    if ($mysql->connect_error) die($mysql->connect_error);
    return $mysql;
}

/**
 * 当数据库表不存在时，创建表
 * 
 * @return void
 */
function createTables()
{
    $mysql = initConnection();
    $mysql->query('CREATE TABLE IF NOT EXISTS restaurant (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(32) UNIQUE NOT NULL,
        passwd VARCHAR(255) NOT NULL,
        name TEXT DEFAULT NULL,
        icon VARCHAR(255) DEFAULT NULL
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS employee (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        restaurantid INTEGER DEFAULT 0,
        username VARCHAR(32) UNIQUE NOT NULL,
        passwd VARCHAR(255) NOT NULL,
        name TEXT DEFAULT NULL,
        job TEXT DEFAULT NULL,
        wage DECIMAL(8,2) DEFAULT 0
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS menu (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        restaurantid INTEGER NOT NULL,
        foodname VARCHAR(32) NOT NULL,
        price DECIMAL(8,2) NOT NULL,
        imgurl VARCHAR(255) DEFAULT NULL,
        CONSTRAINT m_r_fk FOREIGN KEY (restaurantid) REFERENCES restaurant (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS order_list (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        restaurantid INTEGER NOT NULL,
        ordertime Datetime NOT NULL,
        status Boolean NOT NULL DEFAULT false,
        info Text NOT NULL,
        remark VARCHAR(255) DEFAULT NULL,
        CONSTRAINT o_r_fk FOREIGN KEY (restaurantid) REFERENCES restaurant (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS task (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        restaurantid INTEGER NOT NULL,
        employeeid INTEGER NOT NULL,
        task Text NOT NULL,
        evaluation INTEGER DEFAULT 0,
        remark TEXT DEFAULT NULL,
        tasktime Datetime NOT NULL,
        CONSTRAINT t_e_fk1 FOREIGN KEY (employeeid) REFERENCES employee (id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT t_r_fk2 FOREIGN KEY (restaurantid) REFERENCES restaurant (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');

    //ENGINE = InnoDB 
    if ($mysql->error) die($mysql->error);
    $mysql->close();
}

/**
 * 获取对应饭店存储在数据库的哈希值，便于进行验证
 * 
 * @param string $user
 * @return string|bool 成功时返回Hash值，失败时返回false
 */
function getResPasswdHash($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT passwd FROM restaurant WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return false;

    $stmt->bind_result($passwd_hash);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return $passwd_hash;
}

/**
 * 获取对应饭店存储在数据库的哈希值，便于进行验证
 * 
 * @param string $user
 * @return string|bool 成功时返回Hash值，失败时返回false
 */
function getEmpPasswdHash($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT passwd FROM employee WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return false;

    $stmt->bind_result($passwd_hash);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return $passwd_hash;
}

/** 
 * 检查饭店是否存在（安全起见，不应直接调用此函数）
 * 
 * @param string $username 用户名
 * @return bool 是否存在
 */
function findRes($username)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM restaurant WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/** 
 * 添加饭店
 * 
 * @param string $username 用户名
 * @param string $passwd 密码Hash值
 * @return void
 */
function addRestaurant($username, $passwd)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO restaurant (username, passwd) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $passwd);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/** 
 * 检查店员是否存在（安全起见，不应直接调用此函数）
 * 
 * @param string $username 用户名
 * @return bool 是否存在
 */
function findEmp($username)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM employee WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/** 
 * 添加店员
 * 
 * @param string $username 用户名
 * @param string $passwd 密码Hash值
 * @return void
 */
function addEmployee($username, $passwd)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO employee (username, passwd) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $passwd);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 更改饭店头像
 * 
 * @param int $resid 饭店ID
 * @param string $image 头像链接
 * @return void
 */
function editResIconLink($resid, $image)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("UPDATE Restaurant SET icon = ? WHERE id = ?");
    $stmt->bind_param("si", $image, $resid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 获取饭店信息
 * 
 * @param int $resid 饭店ID
 * @return array 包含昵称($name)、图标($icon)的数组
 */
function getResInfo($resid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT name, icon FROM restaurant WHERE id = ?");
    $stmt->bind_param("i", $resid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($name,$icon);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return array('name' => $name, 'icon' => $icon);
}

/**
 * 获取饭店用户名对应的ID
 * 
 * @param string $username 用户名
 * @return int|bool 成功时返回用户ID，失败时返回false
 */
function getResIdByUsername($username)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id FROM restaurant WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows <= 0) return false;
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    $mysql->close();
    return $id;
}

/**
 * 获取店员用户名对应的ID
 * 
 * @param string $username 用户名
 * @return int|bool 成功时返回用户ID，失败时返回false
 */
function getEmpIdByUsername($username)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id FROM employee WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows <= 0) return false;
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    $mysql->close();
    return $id;
}

/**
 * 获取店员id对应的饭店id
 * 
 * @param int $empid 店员id
 * @return int 店员对应饭店的id
 */
function getResIdByEmpId($empid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT restaurantid FROM employee WHERE id = ?");
    $stmt->bind_param("i", $empid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows <= 0) return false;
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    $mysql->close();
    return $id;
}

/**
 * 修改饭店名称
 * 
 * @param int $resid 饭店ID
 * @param string $name 饭店昵称
 * @return void
 */
function editResInfo($resid, $name)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE restaurant SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $resid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/** 
 * 检查菜品是否存在
 * 
 * @param int $foodid 菜品ID
 * @return bool 是否存在
 */
function findFood($foodid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->bind_param("i", $foodid);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/**
* 添加菜品
*
* @param int $resid 商家id
* @param string $foodname 菜品名称
* @param double $price 菜品价格
* @param string $imgurl 菜品图片链接
* @return array 包含商品id($foodid)的数组
*/
function addFood($resid, $foodname, $price, $imgurl) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO menu (restaurantid, foodname, price, imgurl) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $resid, $foodname, $price, $imgurl);
    $stmt->execute();
    $stmt->store_result();
    $foodid = $stmt->insert_id;

    $stmt->close();
    $mysql->close();

    return array('id' => $foodid);
}

/**
* 修改菜品
*
* @param int $foodid 菜品id
* @param string $foodname 菜品名称
* @param double $price 菜品价格
* @param string $imgurl 菜品图片链接
* @return void
*/
function modifyFood($foodid, $foodname, $price, $imgurl) {
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE menu SET foodname = ?, price = ?, imgurl = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $foodname, $price, $imgurl, $foodid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
* 删除菜品
*
* @param int $foodid 菜品id
* @return void
*/
function deleteFood($foodid) {
    $mysql = initConnection();
    $stmt = $mysql->prepare( "DELETE FROM menu WHERE id = ?");
    $stmt->bind_param("i", $foodid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
* 获取菜单列表
*
* @param int $resid 商品ID
* @return array 包含多个数组，每个数组包含$id，$typeid，名称($foodname)，价格($price)，描述($description)，商品图片($imgurl)的数组
*/
function getFoodList($resid) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, foodname, price, imgurl FROM menu WHERE restaurantid = ?");
    $stmt->bind_param("i", $resid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $foodname, $price, $imgurl);
    $foodList = array();
    while($stmt->fetch()) {
        $foodList[] = array('id' => $id, 'name' => $foodname, 'price' => $price, 'icon' => $imgurl);
    }

    $stmt->close();
    $mysql->close();

    return $foodList;
}

/**
* 按时间倒序获取订单列表
*
* @param integer $resid 商家ID
* @param integer $count 获取订单数
* @return array 多个包含订单编号，订单时间($time)，订单状态($status)的数组构成的数组
*/
function getDESCList($resid, $count) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, ordertime, status FROM order_list WHERE restaurantid = ? ORDER BY ordertime DESC");
    $stmt->bind_param("i", $resid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $ordertime, $status);
    $orderList = array();
    while($stmt->fetch()) {
        switch($status)
        {
            case 1:
                $status = true;
                break;
            default:
                $status = false;  
        }
        $orderList[] = array('id' => $id, 'time' => $ordertime, 'status' => $status);
        if(count($orderList) >= $count) break;
    }

    $stmt->close();
    $mysql->close();

    return $orderList;
}

/** 
 * 检查订单是否存在（安全起见，不应直接调用此函数）
 *
 * @param integer $resid 饭店ID
 * @param integer $id 订单ID
 * @return bool 是否存在
 */
function findOrder($resid, $id)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM order_list WHERE restaurantid = ? AND id = ?");
    $stmt->bind_param("ii", $resid, $id);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/**
 * 增加订单
 * 
 * @param integer $resid 饭店id
 * @param array $data 订单内容($order(订单物品($content),备注($remark)))
 * @return array 包含订单编号($order_id), 下单时间($time)
 */
function addOrder($resid, $data)  {
    $temp = json_decode($data['order'], true);
    $tempJson = json_encode($temp['content']);
    date_default_timezone_set('PRC');
    $orderTime = date('Y-m-d H:i:s', time());
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO order_list (restaurantid, ordertime, info, remark) VALUES (?, ?,  ?, ?)");
    $stmt->bind_param("isss", $resid, $orderTime, $tempJson, $temp['remark']);
    $stmt->execute();
    $stmt->store_result();
    $order_id = $stmt->insert_id;

    $stmt->close();
    $mysql->close();

    return array('order_id' => $order_id, 'time' => $orderTime);
}

/**
 * 修改订单状态
 * 
 * @param integer $id 订单ID
 * @param integer $status 待修改订单状态

 * @return void
 */
function editOrderStatus($id, $status)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE order_list SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
* 获取订单详情
*
* @param integer $resid 商家ID
* @param integer $id 订单编号
* @return array 包含订单编号，订单状态($status),订单内容($content)和订单备注（$remark）的数组
*/
function getOrderItem($resid, $id) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT ordertime, status, info, remark FROM order_list WHERE restaurantid = ? AND id = ?");
    $stmt->bind_param("ii", $resid, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($ordertime, $status, $info, $remark);
    $stmt->fetch();

    $info = json_decode($info,TRUE);
    switch($status)
    {
        case 1:
            $status = true;
            break;
        default:
            $status = false;  
    }
    $content = array();

    for($i = 0; $i < count($info); $i ++) {
        $inside = $mysql->prepare("SELECT foodname, price, imgurl FROM menu WHERE id = ?");
        $inside->bind_param("i", $info[$i]['id']);
        $inside->execute();
        $inside->store_result();

        if ($inside->num_rows <= 0) {
        	$inside->close();
        	continue;
        }

        $inside->bind_result($foodname, $price, $imgurl);
        $inside->fetch();

        $content[] = array('id' => $info[$i]['id'], 'name' => $foodname, 'price' => $price, 'num' => $info[$i]['number'], 'icon' => $imgurl); 

        $inside->close();
    } 

    $item = array('id' => $id, 'status' => $status, 'content' => $content, 'remark' => $remark);

    $stmt->close();
    $mysql->close();

    return $item;
}

/**
 * 店主增加店员
 * 
 * @param $employeeid, $resid, $employeename, $employeejob, $employeewage
 * @return bool 是否增加成功
 */
function joinRes($employeeid, $resid, $employeename, $employeejob, $employeewage)  {
    $mysql = initConnection();
    $stmt = $mysql->prepare("UPDATE employee SET restaurantid = ?, name = ?, job = ?, wage = ? WHERE id = ?");
    $stmt->bind_param("issdi", $resid, $employeename, $employeejob, $employeewage, $employeeid);
    $stmt->execute();
    $result = $stmt->store_result();

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/** 
 * 检查店员是否存在且属于该店雇员
 *
 * @param integer $id 店员ID
 * @param integer $resid 店主ID
 * @return bool 是否存在
 */
function findEmpById($id, $resid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM employee WHERE id = ? AND restaurantid = ?");
    $stmt->bind_param("ii", $id, $resid);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/**
 * 修改店员信息
 * 
 * @param integer $id 店员ID
 * @param string $name 员工姓名
 * @param string $job 员工岗位
 * @param double $wage 员工薪资
 * @return void
 */
function editEmployee($id, $name, $job, $wage)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE employee SET name = ?, job = ?, wage = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $name, $job, $wage, $id);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 开除店员
 * 
 * @param integer $id 店员ID
 * @param integer $resid 店主ID
 * @return void
 */
function dismissEmp($id, $resid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE employee SET restaurantid = 0, name = null, job = null, wage = 0 WHERE id = ?  AND restaurantid = ?");
    $stmt->bind_param("ii", $id, $resid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
* 获取店员列表
*
* @param int $resid 饭店ID
* @return array 包含多个数组，每个数组包含$id，名称($name)，岗位($job)，薪资($wage)的数组
*/
function getEmployeeList($resid) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, name, job, wage FROM employee WHERE restaurantid = ?");
    $stmt->bind_param("i", $resid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $name, $job, $wage);
    $empList = array();
    while($stmt->fetch()) {
        $empList[] = array('id' => $id, 'name' => $name, 'job' => $job, 'wage' => $wage);
    }

    $stmt->close();
    $mysql->close();

    return $empList;
}

/**
* 获取员工任务列表
*
* @param int $resid 饭店ID
* @param int $employeeid 员工ID
* @return array 包含多个数组，每个数组包含$id，名称($employeename)，岗位($employeejob)，任务($task)，评价($evaluation)，备注($remark)的数组
*/
function getTaskListByEmpId($resid, $employeeid) {
    $mysql = initConnection();

    $stmt = $mysql->prepare("SELECT id, task, evaluation, remark, tasktime FROM task WHERE restaurantid = ? AND employeeid = ?");
    $stmt->bind_param("ii", $resid, $employeeid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $task, $evaluation, $remark, $tasktime);
    $taskList = array();
    while($stmt->fetch()) {
        $inside = $mysql->prepare("SELECT name, job FROM employee WHERE restaurantid = ? AND id = ?");
        $inside->bind_param("ii", $resid, $employeeid);
        $inside->execute();
        $inside->store_result();
        $inside->bind_result($employeename, $employeejob);
        while($inside -> fetch())
            $taskList[] = array('id' => $id, 'employeename' => $employeename, 'employeejob' => $employeejob, 'task' => $task, 'evaluation' => $evaluation, 'remark' => $remark, 'tasktime' => $tasktime);
        
        $inside->close();
    }

    $stmt->close();
    $mysql->close();

    return $taskList;
}

/**
* 获取饭店任务列表
*
* @param int $resid 饭店ID
* @return array 包含多个数组，每个数组包含$id，名称($employeename)，岗位($employeejob)，任务($task)，评价($evaluation)，备注($remark)的数组
*/
function getTaskListByResId($resid) {
    $mysql = initConnection();

    $stmt = $mysql->prepare("SELECT id, employeeid, task, evaluation, remark, tasktime FROM task WHERE restaurantid = ?");
    $stmt->bind_param("i", $resid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $employeeid, $task, $evaluation, $remark, $tasktime);
    $taskList = array();
    while($stmt->fetch()) {
        $inside = $mysql->prepare("SELECT name, job FROM employee WHERE restaurantid = ? AND id = ?");
        $inside->bind_param("ii", $resid, $employeeid);
        $inside->execute();
        $inside->store_result();
        $inside->bind_result($employeename, $employeejob);
        while($inside->fetch())
            $taskList[] = array('id' => $id, 'employeename' => $employeename, 'employeejob' => $employeejob, 'task' => $task, 'evaluation' => $evaluation, 'remark' => $remark, 'tasktime' => $tasktime);
        
        $inside->close();
    }

    $stmt->close();
    $mysql->close();

    return $taskList;
}

/**
 * 店主给店员指定任务
 * 
 * @param $resid, $employeeid, $task 店主id, 店员id, 任务
 * @return bool 是否增加成功
 */
function addTask($resid, $employeeid, $task)  {
    date_default_timezone_set('PRC');
    $taskTime = date('Y-m-d H:i:s', time());
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO task (restaurantid, employeeid, task, tasktime) VALUES (?, ?,  ?, ?)");
    $stmt->bind_param("iiss", $resid, $employeeid, $task, $taskTime);
    $stmt->execute();
    $stmt->store_result();

    $task_id = $stmt->insert_id;

    $stmt->close();
    $mysql->close();

    return array('task_id' => $task_id, 'time' => $taskTime);
}

/** 
 * 检查任务是否存在
 *
 * @param integer $id 任务ID
 * @param integer $resid 店主ID
 * @return bool 是否存在
 */
function findTaskById($id, $resid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM task WHERE id = ? AND restaurantid = ?");
    $stmt->bind_param("ii", $id, $resid);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/**
 * 修改/评价任务
 * 
 * @param integer $id 任务ID
 * @param string $task 任务
 * @param integer $evaluation 店主评价
 * @param string $remark 店主备注
 * @return void
 */
function editTask($id, $task, $evaluation, $remark)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE task SET task = ?, evaluation = ?, remark = ? WHERE id = ?");
    $stmt->bind_param("sisi", $task, $evaluation, $remark, $id);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 删除任务
 * 
 * @param integer $id 任务ID
 * @param integer $resid 店主ID
 * @return void
 */
function dismissTask($id, $resid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "DELETE FROM task WHERE id = ? AND restaurantid = ?");
    $stmt->bind_param("ii", $id, $resid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}
