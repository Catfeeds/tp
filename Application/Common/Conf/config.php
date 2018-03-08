<?php
/* 项目设定 */
return array(
    'APP_DEBUG' => TRUE, // 是否开启调试模式
    'APP_PLUGIN_ON' => false, // 是否开启插件机制
    'APP_GROUP_LIST' => FALSE, // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'APP_AUTOLOAD_REG' => false, // 是否开启SPL_AUTOLOAD_REGISTER
    'APP_AUTOLOAD_PATH' => 'Think.Util.', // __autoLoad 机制额外检测路径设置,注意搜索顺序
    /* 默认设定 */
    'DEFAULT_APP' => '@', // 默认项目名称，@表示当前项目
    'MODULE_ALLOW_LIST' => array('Home'),
    'DEFAULT_MODULE' => 'Home',
  //  'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
//      'DEFAULT_AJAX_RETURN'   => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
//      'DEFAULT_THEME'         => 'default', // 默认模板主题名称
    'DEFAULT_LANG' => 'zh-cn', // 默认语言

    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'zlt',
    'DB_USER' => 'zltuser', // 用户名
    'DB_PWD' => 'm75WezYsQQ33FdXP',
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_SUFFIX' => '', // 数据库表后缀
    'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    /* 错误设置 */
 //   'TMPL_EXCEPTION_FILE' => '/tp/index.php/home/error/index',
    'ERROR_MESSAGE' => '您浏览的页面暂时发生了错误！请稍后再试～', //错误显示信息,非调试模式有效
//        'TMPL_EXCEPTION_FILE' => APP_PATH.'/Public/lib/404.php',
    /* 语言设置 */
    'LANG_SWITCH_ON' => false, // 默认关闭多语言包功能
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    /* 日志设置 */
    'LOG_RECORD' => false, // 默认不记录日志
    'LOG_FILE_SIZE' => 2097152, // 日志文件大小限制
    'LOG_RECORD_LEVEL' => array('EMERG', 'ALERT', 'CRIT', 'ERR'), // 允许记录的日志级别

    /* 分页设置 */
    'PAGE_ROLLPAGE' => 5, // 分页显示页数
    'PAGE_LISTROWS' => 20, // 分页每页显示记录数
    /* SESSION设置 */
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    /* 运行时间设置 */
    'SHOW_RUN_TIME' => false, // 运行时间显示
    'SHOW_ADV_TIME' => false, // 显示详细的运行时间
    'SHOW_DB_TIMES' => false, // 显示数据库查询和写入次数
    'SHOW_CACHE_TIMES' => false, // 显示缓存操作次数
    'SHOW_USE_MEM' => false, // 显示内存开销
    'SHOW_PAGE_TRACE' => false, // 显示页面Trace信息 由Trace文件定义和Action操作赋值
    'SHOW_ERROR_MSG' => true, // 显示错误信息

    /* SAM_MQTT服务器配置 */
    'MQTT_HOST' => '127.0.0.1',
    'MQTT_PORT' => '1883',
    'MQTT_USER' => 'dog-devsrv',
    'MQTT_PASS' => '484848',
    /* 短信验证地址 */
    'PHONE_CODE_URL' => 'https://app.cloopen.com:8883',
    'sid' => '8a48b5514c2fd22f014c418a95750b4a',
    'auth_token' => 'c0ac74ce45b74965a7ce32a8f00aa219',
    'TEMPLATEID' => '159345',
    /* 极光推送设置 */
    'APP_KEY' => '8708d22a2777844638baeb96',
    'MASTER_SECRET' => '3210845bf60d52e2171e7718',
    /* 设备状态 */
    'DEVICE_HOST' => '127.0.0.1',
    'DEVICE_HOST_PORT' => '4001',
    'DOMAIN' => 'https://app.imerit.cn/',
    /* 高德 */
    "AMAP_KEY" => '8668f6085cb8575d7c340bd77040ab0b',
    'AMAP_CONVERT_URL' => 'http://restapi.amap.com/v3/assistant/coordinate/convert'
        )
?>
