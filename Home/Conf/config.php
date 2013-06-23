<?php
$db_config=include('db_config.php');
$global=include('config.php');
$private=array(
    'FIRE_SHOW_PAGE_TRACE'	=>	true,
    //'SHOW_PAGE_TRACE'	    =>	true,
	'TMPL_DETECT_THEME'		=> true,
    /* 数据库设置 */
    'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        => 0,           // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    /*URL Start*/
    'URL_MODEL'             => 1,
    'URL_CASE_INSENSITIVE'  => true,        // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_PATHINFO_DEPR'     => '_',         // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       => '.html',     // URL伪静态后缀设置
    /*URL End*/

    'USER_AUTH_GATEWAY'     =>'Public/login',
    'NOT_AUTH_MODULE'       =>'Public',

    'COOKIE_DOMAIN'         => '.sssui.net',
    'DOMAIN'                =>'sssui.net',
    'SESSION_EXPIRE'        =>864000,
    'COOKIE_EXPIRE'         =>864000,

    'USER_AUTH_ON'          =>false,
    'USER_AUTH_KEY'         =>'uid',
	/*URL */
	//开启路由
	'URL_ROUTER_ON'   => true, 
	//定义路由规则
	'URL_ROUTE_RULES' => array( 
		':id\d'				=>	'Article/read',
		'category/:id'		=>	'Article/category',
		'tag/:tag'			=>	'Index/tag',
		'search'			=>	'Article/search',
		'login'				=>	'Public/Login',
		'register'			=>	'Public/register',
		'singlepage/:id'	=>	'Public/singlepage',
		'feedback'			=>	'Public/feedback',
	),
	'APP_SUB_DOMAIN_DEPLOY'=>1,
	'APP_SUB_DOMAIN_RULES'=>array(
		'www'=>array('Home/'),
	),
    /* 日志设置 */
    'LOG_RECORD'            => false,   // 默认不记录日志
    'LOG_TYPE'              => 3, // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST'              => '', // 日志记录目标
    'LOG_EXTRA'             => '', // 日志记录额外信息
    'LOG_LEVEL'             => 'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
    'LOG_FILE_SIZE'         => 2097152,	// 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  => true,    // 是否记录异常信息日志
	'MIN_MERGE_JS'		=>true,
	'MIN_MERGE_CSS'	=>true,
);
return array_merge($private,$db_config,$global);
?>