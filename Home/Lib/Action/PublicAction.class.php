<?php
// +----------------------------------------------------------------------
// | 公开操作
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class PublicAction extends BaseAction{	
        public function index(){
             $this->redirect("Index/Index");
        }
        protected function addRole($userId) {
            //新增用户自动加入相应权限组
            $RoleUser = D("RoleUser");
            $RoleUser->user_id	=	$userId;
            $RoleUser->role_id	=	C('INIT_ROLE');
            $RoleUser->add();
        }

        /*Login Start*/
        // 登录检测
        public function checkLogin($account='',$password='',$jumpUrl='',$bindlogin=false) {
                if (empty($account) && empty($password)){
                    $account=$_POST['account'];
                        $password=$_POST['password'];
                }
                if(empty($account)) {
                        $this->error('帐号必须！');
                }elseif (empty($password)){
                        $this->error('密码必须！');
                }
                $map="account='{$account}' and status=1";
                import('ORG.Util.RBAC');
                $authInfo = RBAC::authenticate($map);
                //使用用户名、密码和状态的方式进行认证
                if(false === $authInfo) {
                        $this->error('帐号不存在或已禁用！');
                }else {
                        if (!$bindlogin){
                                $password=md5($password);
                        }
                        if($authInfo['password']!=$password) {
                                $this->error('密码错误！');
                        }
                        $_SESSION["uid"]	=	$authInfo['id'];
                        $_SESSION['account']		=	$authInfo['account'];
                        $_SESSION['nickname']		=	$authInfo['nickname'];
                        if($authInfo['id']=='-1') {
                            $_SESSION['administrator']      =   true;
                        }
                        Cookie::set('loginfo', $_SESSION['uid'].','.$_SESSION['nickname'].','.$_SESSION['account']);
                        $User = M('User');
                        $data = array();
                        $data['id']	=	$authInfo['id'];
                        $data['last_login_time']=	time();
                        $data['login_count']	=	array('exp','(login_count+1)');
                        $data['last_login_ip']	=	get_client_ip();
                        $User->save($data);
                        // 缓存访问权限		
                        RBAC::saveAccessList();
                        if (empty($jumpUrl)){
                                if ($_REQUEST['Referer']!=""){
                                        $jumpUrl=$_REQUEST['Referer'];
                                }else{
                                        $jumpUrl=U("Home/Index/index");
                                }
                        }
                        if ($this->isAjax()){
                                $this->success('登录成功！');
                        }else{
                                redirect($jumpUrl);
                        }
                }
        }
        public function logout(){
			$this->setseoinfo('退出登录','退出登录','退出登录');
            $jumpUrl='login.html';
            if(isset($_SESSION["uid"])) {
                    unset($_SESSION["uid"]);
                    unset($_SESSION["nickname"]);
                    unset($_SESSION['account']);
                    unset($_SESSION);
                    session_destroy();
                    Cookie::set('loginfo','');
                    $this->success('登出成功！',$jumpUrl);
            }else {
                    $this->success('已经登出！',$jumpUrl);
            }
        }

        // 用户登录页面
        public function login() {
			if(isset($_SESSION["uid"]) && $_SESSION["uid"]>0) {
					$this->redirect("Index/index");
			}else{
					$this->setseoinfo('用户登录','用户登录','用户登录');
					$this->display();
			}
        }
        /*Login End*/
        public function register(){
                //如果通过认证跳转到首页
                $this->setseoinfo('用户注册','用户注册','用户注册');
                $this->display();
        }
        // 插入数据
        public function doregister() {
            $_POST['account']=$_POST['email'];
            if($_SESSION['registerverify'] != md5($_POST['vcode'])) {
                    $this->error('验证码错误！');
            }
            if($_POST['password'] != $_POST['repassword']) {
                    $this->error('两次密码输入不一致！');
            }
            $password=$_POST['password'];
            $_POST['password']=md5($_POST['password']);
            //$_POST['create_time']=time();
            $User	 =	 D("User");
            if(!$User->create()) {
                $this->error($User->getError());
            }else{
                // 写入帐号数据
                if($result	 =	 $User->add()) {
                    $this->addRole($result);
                    //sendmsg(0,$result,'你好,'.$data['nickname'].',欢迎来到'.C("WEB_NAME")."!");
                    $this->checkLogin($_POST['account'],$password,U("User/"));
                    //$this->success('注册成功！');
                }else{
                    $this->assign('jumpUrl','javascript:history.back();');
                    $this->success('注册失败！');
                }
            }
        }
        /*登陆验证码*/
        public function loginverify(){
			import("ORG.Util.Image");
			Image::buildImageVerify(4, 1, 'png', 60, 34, 'loginverify');
        }
        /*注册验证码*/
        public function registerverify(){
			import("ORG.Util.Image");
			Image::buildImageVerify(4, 1, 'png', 60, 34, 'registerverify');
        }
        public function verify(){
			import("ORG.Util.Image");
			Image::buildImageVerify(4);
        }
        // 检查帐号
        public function checkAccount($account='') {
			$User = D("User");
			if (empty($account)){
					$name  =  $_REQUEST['account'];
			}
			$result  =  $User->getByAccount($name);
			if($result) {
					$this->error('邮箱已被注册');
			}else {
					die('y');
			}
        }
        //检查帐号
        public function checkAccount1(){
            $account  =  $_REQUEST['account'];
            $User = D("User");
            $result  =  $User->getByAccount($account);
            if($result) {
                    die('y');
            }else {
                    die('该账号不存在!');
            }
        }
        public function checkemail() {
                $email  =  $_REQUEST['email'];
                $User = D("User");
                $result1  =  $User->getByAccount($email);
                $result  =  $User->getByEmail($email);
                if($result || $result1) {
                        die('邮箱已被注册!');
                }else {
                        die('y');
                }
        }
        public function checkloginverifycode() {
                $code=$_REQUEST["verify"];
                if ($_SESSION['loginverify'] == md5($code)){
                        echo "y";
                }else{
                        echo "验证码输入有误";
                }
        }
        public function checkregisterverifycode() {
                $vcode=$_REQUEST["vcode"];
                if ($_SESSION['registerverify'] == md5($vcode)){
                        echo "y";
                }else{
                        echo "验证码输入有误";
                }
        }
        public function feedback(){
			$this->setseoinfo('用户反馈','用户反馈','用户反馈');
			if ($this->_request('action')=="save"){
				$url=$this->_post('url');
				if ($this->insert('Feedback')) {
					$this->success(L('操作成功'),$url);
				} else { 
					$this->error(L('操作失败'),U("Home/Public/feedback"));
				}
			}else{
				$this->url=$_SERVER['HTTP_REFERER'];
				$this->module=$this->_get('module');
				$this->pid= intval($this->_get('id'));;
				$this->display();
			}
        }
        public function active(){
            $this->setseoinfo('验证邮箱','验证邮箱','验证邮箱');
			//如果时间不过期，即不超过一天
			if((time()-fo_decode($_REQUEST['time']))<=86400){
				$id=fo_decode($_REQUEST['id']);
				$User=M('User');
				$res=$User->where('id='.$id)->setField('email_auth',1);
				if($res){
						$account=$_REQUEST['account'];
						$password=fo_decode($_REQUEST['password']);
						$url=fo_decode($_REQUEST['url']);
						$this->checkLogin($account,$password,$url);
				}else{
						$this->error('激活失败','Index');
				}
			}else{
				$this->error('该邮件超出有效期！');
			}
        }

        public function verifyemail(){
                $this->setseoinfo('验证邮箱','验证邮箱','验证邮箱');
                $this->assign("jumpUrl",U('Home/Index/index'));
                if($_REQUEST['email']){
                        $User=M("User");
                        $vo=$User->where("email='".$_REQUEST['email']."'")->find();
                        //echo $User->getlastsql();
                        //print_r($vo);
                        $uid=$vo['id'];
                        if (intval($vo['email_auth'])==0){
                                $vc=intval(fo_decode($_REQUEST['veriycode']));
                                $vc=intval(time()-$vc);
                                if ($vc<=86400){
                                        $User->data(array('email_auth'=>1))->where("id=$uid")->save();
                                        //die($User->getlastsql());
                                        //setcookie('veriycode' $uid,'',time()-86400);
                                        $this->success('操作成功!',"Index");
                                }else{
                                        $this->error('过期或者验证码不正确!');
                                }
                        }else{
                                $this->error('非法操作!');  
                        }
                }else{
                        $this->error('非法操作!');
                }
        }
        function dobind($info,$uid){
                //防止用户资料被意外更改
                unset($_POST);
                $this->assign('jumpUrl',U('Home/'));
                $User=M('User');
                $vo=$User->find($uid);
                if($vo){
                        $field=$info['authtype'].'authkey';
                        $User->$field=$info[$info['authtype'].'authkey'];
                        $User->save();
                        $this->success('绑定成功!');
                }else{
                        $this->error('账号不存在!');
                }
        }
        function bindaccount($info=null){
            if ($info!=null && $info['authkey']!='' && $info['authtype']!=''){
                    $User=M("User");
                    $vo=$User->field('account,password')->where($info['authtype']."authkey='".$info['authkey']."'")->find();
                    //P($info);
                    //die($User->getlastsql());
                    if (count($vo)<1){
                        
                        //此OPENID未绑定账户
                        if (intval($_SESSION['uid'])>1){
                                //当前用户已经登录,直接绑定此OPENID到当前用户
                                $this->dobind($info,$_SESSION['uid']);
                        }else{
                                //没登录也没绑定,提交到本页面,让用户登录或完善资料,这样做是防止本页刷新导致需要QQ,sina等重新登录
                                echo '<html><head><meta content="text/html; charset=UTF-8" http-equiv="Content-Type"><title>页面跳转中</title><body onload="form1.submit();">
                                <form action="'.U('Home/Public/bindaccount').'" name="form1" method="POST">
                                <input type="hidden" name="sex" value="'.$info['sex'].'">
                                <input type="hidden" name="nickname" value="'.$info['nickname'].'">
                                <input type="hidden" name="avatar" value="'.$info['avatar'].'">
                                <input type="hidden" name="authkey" value="'.$info['authkey'].'">
                                <input type="hidden" name="authtype" value="'.$info['authtype'].'">
                                </form></body></html>';
                        }
                    }else{
                        //此OPENID已经绑定账户了
                        $this->checkLogin($vo['account'],$vo['password'],U('Home/'),true);
                    }
            }else{
                if ($_POST['type']=='save'){
                    //再次判断是否登录,避免搁置时间过长,二次登录
                    if (intval($_SESSION['uid'])>1){
                        $this->dobind($_POST,$_SESSION['uid']);
                    }else{
                        //重新注册用户,并绑定此OPENID
                        $_POST['password']=md5("7922538788");
                        $_POST['account']=$_POST['email'];
                        //$authkeyfield=$_POST['authtype'].'authkey';
                        //$_POST[$authkeyfield]=$_POST['authkey'];
                        $User	 =	 M("User");
                        if(!$User->create()) {
                            $this->error($User->getError());
                        }else{
                            $result	 =	 $User->add();
                            //die($User->getlastsql());
                            if($result) {
                                $this->addRole($result);
                                $this->checkLogin($_POST['account'],'7922538788',U('Home/'));
                            }else{
                                $this->error('注册失败,请重试!');
                            }
                        }
                    }
                }else{
                    $this->display('Public:bindaccount');
                }
            }
        }
        function singlepage(){
			$New=M('New');
			$this->assign("NearList",$New->where('status=1')->Order('hit asc')->limit(10)->select());
	
			$Comment=M('Comment');
			$this->assign("NearComment",$Comment->where('status=1')->Order('id asc')->limit(10)->select());
	
			$Cate=M('Cate');
			$this->assign("CateList",$Cate->where('status=1')->Order('id asc')->select());
	
			$Link=M('Link');
			$this->assign("LinkList",$Link->where('status=1')->Order('id asc')->limit(20)->select());
			$Singlepage=M('Singlepage');
			$vo=$Singlepage->find(intval($this->_get('id')));
			$this->assign('vo',$vo);
			$this->setseoinfo($vo['title'],$vo['title'],$vo['title']);
			$this->display();
        }    
}
?>