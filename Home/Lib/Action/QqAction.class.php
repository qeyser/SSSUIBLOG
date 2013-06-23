<?php
// +----------------------------------------------------------------------
// | QQ登录
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

Vendor("QQ.qqConnectAPI");
class QqAction extends BaseAction{
	function login(){
		$qc = new QC();
		$qc->qq_login();
	}
	function callback(){
		$qc = new QC();
		$access_token= $qc->qq_callback();
		$openid=$qc->get_openid();
		$qc = new QC($access_token,$openid);
		$arr = $qc->get_user_info();
		if($ret['ret'] == 0){
			if ($arr["gender"]=='男'){
				$sex=1;
			}elseif ($arr["gender"]=='女'){
				$sex=2;
			}
			$info=array(
					'sex'=>$sex,
					'avatar'=>$arr['figureurl_2'],
					'nickname'=>$arr["nickname"],
					'authkey'=>$openid,
					'authtype'=>'qq',
			);
			R('Home/Public/bindaccount',array($info));
			
		}else{
		    $this->error("获取用户信息失败，请稍候重试");
		}
	}
	function add_t($info=array()){
		if (!$info['content']){
			return '内容不能为空!';
		}
		$info['type']=1;
		$qc = new QC();
		$info['img'] = urlencode($info['img']);
		$ret = $qc->add_t($info);
		if($ret['ret'] == 0){
		    return true;
		}else{
		    return false;
		}
	}
	function get_info(){
		$qc = new QC();
		$ret = $qc->get_info();
		if($ret['ret'] == 0){
			foreach($ret['data'] as $k => $v){
				echo '<li>'.$k.': &nbsp;'.$v.'</li>';
			}
		}else{
		    echo "获取失败，请开启调试查看原因";
		}

	}

}
?>