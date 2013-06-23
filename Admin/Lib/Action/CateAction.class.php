<?php
// +----------------------------------------------------------------------
// | 分类管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class CateAction extends CommonAction {
	public $pagetitle=array(
		'Ad'		=>'广告',
		'Article'	=>'文章',
		'Photo'		=>'相册'
	);
	function gmn(){
		//getmodulename
		return str_replace('cate','',$this->getActionName());
	}
	function index(){
		$name=$this->gmn();
		$Cate=M("Cate");
		$where.='module="'.$name.'" ';
		if($_REQUEST['title']!=''){
			$where.=' AND title LIKE "%'.$_REQUEST['title'].'%"';
		}
		$count=$Cate->where($where)->count();
		if($count>0){
			import("ORG.Util.Page");
			$pagesize=10;
			$currpage=intval($_GET['p']);
			if ($currpage<1){
				$currpage=1;
			}
			$_GET['p']=$currpage;
			$p=new Page($count,$pagesize);
			$p->parameter="&where=".$where;
			$pages=$p->show();
			$list=$Cate->where($where)->page($currpage,$pagesize)->order('id desc')->select();
			$this->assign("list",$list);
			$this->assign("page",$pages);
		}
		$this->assign('pagetitle',$this->pagetitle[$name]);
		$this->display('Cate:index');
	}
    function add(){
		$name=$this->gmn();
		$Cate=M("Cate");
		$list=$Cate->field('title,id')->where('status=1 and module="'.$name.'"' .$where)->select();
		$l[0]='无上级类';
		foreach($list as $key=>$val){
			$l[$val['id']]=$val['title'];
		}
		$this->assign('cate',$l);
		$zt=array(0=>'禁用',1=>'启用');
		$this->assign('zt',$zt);
		$this->assign('name',$name);
		$this->assign('pagetitle',$this->pagetitle[$name]);
		$this->display("Cate:edit");
	}
    function edit(){
		$id=intval($_REQUEST['id']);
		$name=$this->gmn();
		$Cate=M("Cate");
		if ($name=='Product'){
			$where=" and pid=0";
		}
		$list=$Cate->field('title,id')->where('status=1 and module="'.$name.'"' .$where)->select();
		$l[0]='无上级类';
		foreach($list as $key=>$val){
			$l[$val['id']]=$val['title'];
		}
		$this->assign('cate',$l);
		$zt=array(0=>'禁用',1=>'启用');
		$this->assign('zt',$zt);
		$this->assign('name',$name);
		$this->assign('pagetitle',$this->pagetitle[$name]);
		$this->assign('vo',$Cate->find($id));
		$this->display("Cate:edit");
	}


	function insert()
    {
		$this->upload();
		$model	=	D("Cate");
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
        //保存当前数据对象
        if($result = $model->add()) { //保存成功		
			if ($_REQUEST['jumpurl']){
				$url=$_REQUEST['jumpurl'];
			}else{
				$url=U(MODULE_NAME.'/Index');
			}
            $this->assign('jumpUrl',$url);
            $this->success(L('新增成功'));
        }else {
            //失败提示
            $this->error(L('新增失败'));
        }
    }
	
	function update() {
		$this->upload();
		$model	=	D("Cate");
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
		// 更新数据
		if(false !== $model->save()) {
            //成功提示
			if ($_REQUEST['jumpurl']){
				$url=$_REQUEST['jumpurl'];
			}else{
				$url=U(MODULE_NAME.'/Index');
			}
			//die($model->getlastsql());
            $this->assign('jumpUrl',$url);
            $this->success(L('更新成功'));
        }else {
            //错误提示
            $this->error(L('更新失败'));
        }
	}
	public function delete()
    {
		//删除指定记录
        $model        = M("Cate");
        if(!empty($model)) {
			$pk	=	$model->getPk();
            $id         = $_REQUEST[$pk];
            if(isset($id)) {
                $condition = array($pk=>array('in',explode(',',$id)));
                if(false !== $model->where($condition)->delete()){
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    }

	function _before_Delete(){
		$ids= explode(',',$_REQUEST["id"]);
		$ac1=M($this->gmn());
		$ac=M('Cate');
		foreach($ids as $id){
			$tmp=$ac->where("pid=$id")->count('id');
			if ($tmp>0){
				$this->error('该类下面有内容未被删除或移动!');
			}
			$tmp=$ac1->where("cid=$id")->count('id');
			if ($tmp>0){
				$this->error('该类下面有内容未被删除或移动!');
			}
		}
	}

    public function sort()
    {
        $Manual = M('Cate');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
        }else{
            $map['status'] = 1;
            $map['module'] =$action;
        }
        $sortList   =   $Manual->where($map)->order('sort asc')->select();
        $this->assign("sortList",$sortList);
        $this->display("Cate:sort");
        return ;
    }
	public function upload() {
        if(!empty($_FILES['img']['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,gif,png,jpeg,bmp');
            //设置附件上传目录
            $upload->savePath =  'Public/Uploads/Cate/';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
				if (!empty($info['0']['savename'])){
					$_POST['img'] = '/Public/Uploads/Cate/'.$info['0']['savename'];
				}
            }
        }
    }
}
?>