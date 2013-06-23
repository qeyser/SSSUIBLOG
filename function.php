<?php
/*
*中文分词
@param $content 分词的目标内容
@param $type   分词类型,1001采用scws分词,1002是phpanalysis
*scws比较详细,可以返回词语的属性,是动词还是名词等,词语的权重,
//需要的组件可以到http://sssui.com下载
*/
function Zwfc($content,$type=1001)
{
	if ($type==1001){
		Vendor('Zwfc.pscws4',LIB_PATH.'Vendor/','.class.php');
		$pscws = new PSCWS4();
		$pscws->set_dict(LIB_PATH.'Vendor/Zwfc/scws/dict.utf8.xdb');
		$pscws->set_rule(LIB_PATH.'Vendor/Zwfc/scws/rules.rules.utf8.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($content);
		$words = $pscws->get_tops(50);//get_result
		$tags = array();
		foreach ($words as $val) {
			$tags[] = $val['word'];
		}
		$pscws->close();
		$tags=implode(',',$tags);
	}elseif ($type==1002){
		Vendor('Zwfc.phpanalysis',LIB_PATH.'Vendor/','.class.php');
		PhpAnalysis::$loadInit = false;
		$pa = new PhpAnalysis('utf-8', 'utf-8', false);
		$pa->LoadDict();
		$pa->SetSource($content);
		$pa->StartAnalysis( false );
		$tags = $pa->GetFinallyResult();
		return $tags;
	}
	return $tags;
}
function getCommentCount($pid=0){
	$Comment=M('Comment');
	return $Comment->where('pid='.$pid)->count('id');
}
function GUI($id=0,$f=null){
	$U=M('User');
	$u=$U->Where("id=".$id)->find();
	if (is_null($f)){
		return  $u;
	}else{
		return $u[$f];
	}
}
/*取得单个字段的值*/
function GTC($id,$table,$field="title"){
	$model=M($table);
	$tmp=$model->find($id);
	return $tmp[$field];	
}
function getsex($sex){
    switch ($sex) {
        case '1':
            return '男';
            break;
        case '2':
            return '女';
            break;
        default:
            return '未知';
            break;
    }
}
function P($arr,$die=false){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
	if ($die){
		die();
	}
}
/* 分类树 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
/* 时间格式化 */
function toDate($time,$format='Y-m-d H:i:s')
{
	if( empty($time))
    {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(($format),$time);
}
function getAd($id){
	$ad=M('Ad');
	$AD=$ad->where("status=1 and id=$id")->find();
	return "<a href=\"$AD[url]\" target='_blank'><img src=\"$AD[img]\" border='0'></a>";
}
//{$str|utf8Substr=0,30}
//截取utf8字符串 
function utf8Substr($str, $from, $len) 
{ 
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'. 
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', 
'$1',$str); 
}
function sendmail($mailto,$name,$title,$content){
	import('ORG.Net.PHPMailer.phpmailer');
	$mail = new PHPMailer();
	$mail->IsSMTP();					// 启用SMTP
	$mail->Host = "smtp.126.com";			//SMTP服务器
	$mail->SMTPAuth = true;					//开启SMTP认证
	$mail->Username = "llliqk";			// SMTP用户名
	$mail->Password = "lisiwei123";				// SMTP密码
	$mail->IsHTML(true);					// 是否HTML格式邮件
	$mail->CharSet = "utf-8";				// 这里指定字符集！
	$mail->Encoding = "base64"; 
	$mail->From = "llliqk@126.com";			//发件人地址
	$mail->FromName = "测试发件人";				//发件人
	$mail->AddAddress($mailto, $name);	//添加收件人
	//$mail->AddAddress("ellen@example.com");
	$mail->AddReplyTo("llliqk@126.com", "测试发件人");	//回复地址
	$mail->WordWrap = 50;					//设置每行字符长度
	/** 附件设置
	$mail->AddAttachment("/var/tmp/file.tar.gz");		// 添加附件
	$mail->AddAttachment("/tmp/image.jpg", "new.jpg");	// 添加附件,并指定名称
	*/
	$aa="<table width=\"660\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\" align=\"center\" style=\"font-family:宋体; font-size:13px; border:1px solid #993399\">$content</table>";
	$mail->Subject = $title;			//邮件主题
	$mail->Body    = $aa;		//邮件内容
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";	//邮件正文不支持HTML的备用显示
	if(!$mail->Send())
	{
	   return "Message could not be sent. <p>Mailer Error: " . $mail->ErrorInfo;
	}
	return true;
}
function getAbstract($c='',$sper='[......]'){
	$c=explode('_baidu_page_break_tag_', $c);
	return strip_tags($c[0]).$sper;
}
function getContent($c){
	$c=str_replace('_baidu_page_break_tag_', '',$c);
	return $c;
}
function parseemote($txt){
	$patter="/\[((l\d{1,3})|([\x4E00-\xFA29]{2,9}))\]/";
	if(preg_match_all ($patter,$txt,$arr)){
		foreach($arr[0] as $k =>$v){
			$tmp="<img src=\"Public/Images/emote/".str_replace('[','',str_replace(']','',$v)).'.gif" />';
			$txt=str_replace($v,$tmp,$txt);
		}
	}
	return $txt;
}
function filterbadword($msg){
	$badwords=explode(',',C('BadWords'));
	foreach($badwords as $v){
		$msg=str_replace($v,'**',$msg);
	}
	return $msg;
}
?>