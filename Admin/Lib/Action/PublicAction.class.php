<?php
//  ----------------------------------------------------------------------
// | 后台用户认证入口
//  ----------------------------------------------------------------------
// | @该文件不需要通过RBAC认证
//  ----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
//  ----------------------------------------------------------------------

class PublicAction extends CommonAction {
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION['uid'])) {
			$this->assign('jumpUrl','?s=Public/login');
			$this->error('没有登录');
		}
	}
	// 顶部页面
	public function top() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	M("Group");
		$list	=	$model->Field('id,title')->where('status=1')->select();
		$this->assign('nodeGroupList',$list);
		$this->display();
	}
	// 用户登录页面
	public function login() {
		if(!isset($_SESSION['uid'])) {
			//$this->display();
			redirect("Public_login.html");
		}else{
			$this->redirect('?s=Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect('?s=Index/index');
	}
	// 用户登出
    public function logout(){
        if(isset($_SESSION['uid'])) {
			unset($_SESSION['uid']);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",'/');
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }

	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION['uid']);
		$this->assign('vo',$vo);
		$this->display();
	}

	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}

    // 更换密码
    public function changePwd()
    {
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= md5($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION['authId'])) {
            $map['id']		=	$_SESSION['authId'];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	md5($_POST['password']);
			$User->save();
			$this->assign('jumpUrl','?s=Public/main');
			$this->success('密码修改成功！');
         }
    }

    //菜单页面
    public function menu() {
        //显示菜单项
        $menu  = array();
        if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])) {
            //如果已经缓存，直接读取缓存
            $menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
        }else {
            //读取数据库模块列表生成菜单项
            $node    =   M("Node");
            $id =   $node->where(' level=1 and name="'.APP_NAME.'"')->getField("id");
            
            $list   =   $node->where(array('_query'=>'level=2&is_show=1&status=1&pid='.$id))->field('id,name,module,group_id,title')->order('sort asc')->select();
            //echo $node->getlastsql();
            $accessList = $_SESSION['_ACCESS_LIST'];
            foreach($list as $key=>$module) {
                 if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || !empty($_SESSION['administrator'])) {
                    //设置模块访问权限
                    $module['access'] =   1;
                    if(!empty($module['module'])) {
                        $module['name']   = $module['name'];//$module['module'].'/'.$module['name'];
                    }
                    $menu[$key]  = $module;
                }
            }
            //缓存菜单访问
            $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]] =   $menu;
        }
        $group   = !empty($_GET['tag'])?$_GET['tag']:0;
        $this->assign('menuTag',$group);
        $this->assign('menu',$menu);
        C('SHOW_RUN_TIME',false);           // 运行时间显示
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
	// 验证码显示
    public function verify()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify(4);
    }

}
?>