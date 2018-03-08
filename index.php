<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',TRUE);
// 定义应用目录
define('APP_PATH','./Application/');
//加入公共函数  原项目名称utility.php
define('PUB_PATH','./Application/Public/');
//引入公共文件
require PUB_PATH.'lib/public.php';
//引入SQTT文件
//require(PUB_PATH.'SAM/php_sam.php');;
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';



