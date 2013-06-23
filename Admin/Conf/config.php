<?php
$db_config=include('db_config.php');
$config=include('config.php');
$private= array(
	//'APP_STATUS'				=> 'debug',
	'TMPL_STRIP_SPACE'      	=> 	false,
	//'APP_DEBUG'					=>	1,
    'LANG_AUTO_DETECT'			=>	false,
    'LANG_SWITCH_ON'			=>	true,
    'LANG_DEFAULT'				=>	'cn',
	'DEFAULT_THEME'				=>	'default',

    'URL_MODEL'             	=> 3,
    
	/*RBAC START*/
	'USER_AUTH_ON'				=>true,
	'USER_AUTH_TYPE'			=>1,
	'USER_AUTH_KEY'				=>'uid',
    'ADMIN_AUTH_KEY'			=>'administrator',
	'USER_AUTH_MODEL'			=>'User',
	'AUTH_PWD_ENCODER'			=>'md5',
	'USER_AUTH_GATEWAY'			=>'Public/login',
	'NOT_AUTH_MODULE'			=>'Public',
	'REQUIRE_AUTH_MODULE'		=>'',
	'NOT_AUTH_ACTION'			=>'',
	'REQUIRE_AUTH_ACTION'		=>'',
    'GUEST_AUTH_ON'          	=> false,
	'TAGLIB_PRE_LOAD'		=> 'html',

    /*RBAC END*/
	//'SHOW_PAGE_TRACE'			=>	true,
    //'SHOW_ERROR_MSG'			=>true,
);
return array_merge($config,$db_config,$private);
?>