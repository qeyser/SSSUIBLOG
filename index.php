<?php
	ini_set('session.save_handler','files'); 
	header("Content-type: text/html; charset=utf-8");
    define('THINK_PATH','./Core/');
	define('DATA_PATH',"./Data/");
    define('APP_NAME','Home');
    define('APP_PATH','./Home/');
    define('APP_DEBUG',true);
    require(THINK_PATH.'Core.php');
?>