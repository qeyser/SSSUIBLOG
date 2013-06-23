<?php 
class BackupAction extends CommonAction{
	function index(){
		$model=new Model();
		$tmp=$model->query('show tables');
		foreach ($tmp as $key => $value) {
			$list[]=$value['Tables_in_'.C('DB_NAME')];
		}
		$this->assign('dblist',$list);
		//P($list);
		$this->display();
	}
	function reserved(){

		import("ORG.Util.Dbmanage");
		$db = new DBManage ( C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_NAME'),C('DB_CHARSET'));
		$dir='./BACKUP/'.date('Y-m-d H_i_s');
		foreach ($_POST['tobackup'] as $value) {
			//echo $value;
			$db->backup ($value,$dir.'/');
		}

		if(isset($_POST['is_email']) || isset($_POST['is_download'])){
			$filename=$dir.'.zip';
			//import('ORG.Util.Sssuizip');
			//Sssuizip::Yasuo($dir.'/',$filename);

			import('ORG.Util.PclZip');
			$zip = new PclZip($filename);
			$zip->create($dir);
			//$zip->extract(PCLZIP_OPT_PATH,'./data');
		}
		if(isset($_POST['is_email']) && $_POST['email']!=''){
			import('ORG.Net.PHPMailer.phpmailer');
			$mail = new PHPMailer();
			$mail->IsSMTP();					// 启用SMTP
			$mail->Host = C('MAIL_SMTP');			//SMTP服务器
			$mail->SMTPAuth = true;					//开启SMTP认证
			$mail->Username = C('MAIL_ACCOUNT');			// SMTP用户名
			$mail->Password = C('MAIL_PW');				// SMTP密码
			$mail->IsHTML(true);					// 是否HTML格式邮件
			$mail->CharSet = "utf-8";				// 这里指定字符集！
			$mail->Encoding = "base64"; 
			$mail->From = C('MAIL_SENDER');			//发件人地址
			$mail->FromName = C("WEB_NAME");				//发件人
			$mail->AddAddress('261001642@qq.com', 'Dear');	//添加收件人
			//$mail->AddAddress("ellen@example.com");
			$mail->AddReplyTo(C('MAIL_SENDER'), C("WEB_NAME"));	//回复地址
			$mail->WordWrap = 50;					//设置每行字符长度
			// 附件设置
			$mail->AddAttachment($filename);		// 添加附件
			$content="这是".date('Y年m月d日H时i分')."的数据库备份";
			$aa="<table width=\"660\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\" align=\"center\" style=\"font-family:宋体; font-size:13px; border:1px solid #993399\">$content</table>";
			$mail->Subject = date('Y年m月d日H时i分').'数据库备份';			//邮件主题
			$mail->Body    = $aa;		//邮件内容
			$mail->AltBody = $content;	//邮件正文不支持HTML的备用显示
			if(!$mail->Send())
			{
			   return "Message could not be sent. <p>Mailer Error: " . $mail->ErrorInfo;
			}
		}
		if(isset($_POST['is_download'])){
			header("Cache-Control: public");   
			header("Content-Description: File Transfer");   
			header('Content-disposition: attachment; filename='.basename($filename)); //文件名  
			header("Content-Type: application/zip"); //zip格式的  
			header("Content-Transfer-Encoding: binary");    //告诉浏览器，这是二进制文件   
			header('Content-Length: '. filesize($filename));    //告诉浏览器，文件大小  
			@readfile($filename);  
		}else{
			$this->success('备份成功!');
		}
	}
	protected function scandir($path){
		$tree=array();
		foreach(glob($path.'/*') as $v){
			if(!is_dir($v)){
				$tree[]=$v;
			}
		}
		return $tree;
	}
	function restore(){
		$sqlfilearr=$this->scandir($_POST['torestore']);
		import("ORG.Util.Dbmanage");
		$db = new DBManage (C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_NAME'),C('DB_CHARSET'));
		foreach ($sqlfilearr as $value) {
			$db->restore ($value);
		}
		P($sqlfilearr);
		$this->success('还原完成!');
	}
}
?>