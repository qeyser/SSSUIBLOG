<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>SSSUI Blog 安装程序</title>
<style type="text/css">
	body{background: url(Home/Tpl/red/Public/images/bg.jpg);}
	table{margin:auto;width:600px;border:1px solid #C7222F;background:white;border: 1px solid;border-color: #CCC #CCC #B3A795 #CCC;padding: 0px;}
	.title{background:#C7222F;color: white; }
	/*padding: 20px;*/
</style>
</head>
<body>
<table>
	<?php
	if (file_exists('install.lock')){
		echo '程序已经安装,请删除install.lock';
	}
	$step= intval($_GET['step']);
	if ($step==0){
	?>
	<form method="POST" name="form1" id="form1" action="?step=1">
	<tr>
		<td class="title" colspan="3">SSSUI Blog 安装程序</td>
	</tr>
	<tr>
		<td>数据库主机：</td>
		<td><input type="text" name="dbhost" id="dbhost" value="127.0.0.1"></td>
		<td></td>
	</tr>
	<tr>
		<td>数据库端口：</td>
		<td><input type="text" name="dbport" id="dbport" value="3306"></td>
		<td></td>
	</tr>
	<tr>
		<td>数据库名称：</td>
		<td><input type="text" name="dbname" id="dbname" value="sssuiblog"></td>
		<td></td>
	</tr>
	<tr>
		<td>数据库账户：</td>
		<td><input type="text" name="dbuser" id="dbuser" value="root"></td>
		<td></td>
	</tr>
	<tr>
		<td>数据库密码：</td>
		<td><input type="text" name="dbpwd" id="dbpwd" value="123456"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td>博客名称：</td>
		<td><input type="text" name="blogname" id="blogname" value="又是一个无名博客"></td>
		<td></td>
	</tr>
	<tr>
		<td>博客地址：</td>
		<td><input type="text" name="blogurl" id="blogurl" value="<?php echo 'http://',$_SERVER['HTTP_HOST'],str_replace('install.php','',$_SERVER['REQUEST_URI'] );?>"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td>管理用户名：</td>
		<td><input type="text" name="adminaccount" id="adminaccount" value="admin"></td>
		<td></td>
	</tr>
	<tr>
		<td>管理员面密码：</td>
		<td><input type="text" name="adminpassword" id="adminpassword" value="123456"></td>
		<td></td>
	</tr>
	<tr>
		<td>管理员邮箱：</td>
		<td><input type="text" name="adminemail" id="adminemail" value="admin@admin.com"></td>
		<td></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="开始安装"></td>
		<td>&nbsp;</td>
	</tr>
	</form>
	<?php }elseif ($step==1){
		$domain=$_SERVER['HTTP_HOST'];
		if (substr($domain,0,4)=='www.'){
			$domain=substr($domain,4);
		}
		$db_config=array(
			'DB_TYPE'					=>	'mysql',
			'DB_HOST'					=>	$_POST['dbhost'],
			'DB_NAME'					=>	$_POST['dbname'],
			'DB_USER'					=>	$_POST['dbuser'],
			'DB_PWD'					=>	$_POST['dbpwd'],
			'DB_PORT'					=>	$_POST['dbport'],
			'DB_PREFIX'					=>	'z_',
			'DB_CHARSET'				=>	'utf8',  
		    'COOKIE_DOMAIN'        		=> '.'.$domain,
		    'DOMAIN'                	=>	$domain,  
		);
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($db_config,CASE_UPPER),true).";\n?>";
		file_put_contents('db_config.php', $content);
		$sql=file_get_contents('install.sql');
		$sql=str_replace(array('{admin_account}','{admin_password}','{admin_email}','{time_now}','{web_name}','{web_url}'),array($_POST['adminaccount'],md5($_POST['adminpassword']),$_POST['adminemail'],time(),$_POST['blogname'],$_POST['blogurl']), $sql);
		file_put_contents('install.sql', $sql);

		if(!$conn=@mysql_connect($db_config['DB_HOST'],$db_config['DB_USER'],$db_config['DB_PWD'])){  
		       echo "连接数据库失败！请返回上一页检查连接参数 <a href=\"javascript:history.go(-1)\"><font color=\"#ff0000\">返回修改</font></a>";  
		       exit();  
		}else{  
		  mysql_query("set names utf8");
		   if(!mysql_select_db($db_config['DB_NAME'],$conn)){   //如果数据库不存在，我们就进行创建。  
		         if(!mysql_query("CREATE DATABASE `".$db_config['DB_NAME']."`")){  
		           echo "创建数据库失败，请确认是否有足够的权限！<a href=\"javascript:history.go(-1)\"><font color=\"#ff0000\">返回修改</font></a>";  
		           exit();  
		          }  
		   }
			require_once("./Core/Extend/Library/ORG/Util/Dbmanage.class.php");
			$db = new DBManage ($db_config['DB_HOST'], $db_config['DB_USER'], $db_config['DB_PWD'], $db_config['DB_NAME'],$db_config['DB_CHARSET']);
			$db->restore ('install.sql');
			rename('install.php','install.lock');
			rename('install.sql','#install.sql');
			echo '<script>window.location="admin.php";</script>';
		}
	?>
	<?php }elseif ($step==2){?>
	<?php }elseif ($step==3){?>
	<?php }?>
</table>
</body>
</html>