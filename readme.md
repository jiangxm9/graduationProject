# graduationProject

## 选题
饭店管理应用

## 背景
在信息化时代，大多数饭店或者餐馆仍旧缺乏系统的管理饭店的方式或者方法。对于饭店员工的绩效考核，支出收入汇总，菜单管理等方面其实可以通过一个饭店管理应用辅助解决，减少时间成本又能提高效率。

## 目的
- 设计饭店管理应用辅助饭店老板管理餐馆员工，提升服务品质以及帮助饭店员工明确当日任务，主要功能有：
    - 任务分配和绩效考核，用于帮助饭店老板进行每日任务分配和考核员工绩效；
    - 电子菜单管理及点餐，用于管理餐馆的菜单（对于老板）以及点餐后将菜单传回后厨（顾客点餐）；
    - 支出收入汇总，用于统计餐馆的每日支出、收入并在每月进行汇总；
    - 进货管理，用于辅助每日采购原材料，也防止可能的贪污现象；

## 思路和方法
- 实现主要分为三大部分：面向老板的管理员端，面向员工与顾客的接收端（这里默认使用手机作为电子菜单）以及后端；还有三个功能模块，分配是：
- 注册登录模块：分别针对于老板注册登录饭店以及员工注册登录，加入对应的饭店。
- 任务分配和绩效考核模块：任务分配由老板（管理端）对每一个员工（接收端）分配（可设置多个默认分配方案）并上传服务器，员工（接收端）收到任务并完成后在接收端通知老板（管理员端）进行绩效考核，老板还可对员工每日表现进行评定。每个月汇总员工的绩效方便老板进行奖惩。
- 电子菜单模块：老板上传餐馆菜单到后台，接收端从后台同步菜单方便顾客通过电子菜单点餐，顾客点餐后点餐信息（已点菜品信息，桌号和总价）传回后台，后台同步至后厨的接收端通知后厨准备菜品。

## 相关支持条件：
无

## 进度安排：
- 2019.11：开题
- 2019.12：完成初步的界面设计并考虑各个功能模块的具体实现
- 2020.01：大致实现五个功能模块
- 2020.02：调试出现的bug并进一步完善功能
- 2020.03：撰写论文初稿
- 2020.04：毕业论文定稿
