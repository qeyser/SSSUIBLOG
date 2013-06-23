<?php
	header("Content-type: text/html; charset=utf-8");
	ini_set('session.save_handler','files'); 
    define('THINK_PATH','./Core/');
	define('DATA_PATH',"./Data/");
    define('APP_NAME','Admin');
    define('APP_PATH','./Admin/');
    define('APP_DEBUG',true);
    require(THINK_PATH.'/Core.php');
?>