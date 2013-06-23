<?php
// +----------------------------------------------------------------------
// | 广告管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class AdAction extends CommonAction {	
	
  /*  public function index(){
        if ($_GET['cid']){
            parent::index();
        }else{
            $Cate=M("Cate");
            $list=$this->_list($Cate,'status=1 and module="Ad"');
            $this->display();
        }
    }*/
	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
	public function _before_edit() {
		$c=M("Cate");
		$sql="SELECT * from z_cate as a where module='Ad' and (select count(id) from z_cate where pid=a.id )=0";
		$list=$c->query($sql);
		foreach($list as $key=>$val){
			$l[$val['id']]=$val['title'];
		}
		$this->assign('cate',$l);
		$zt=array(1=>'启用',0=>'禁用');
		$this->assign('zt',$zt);
		
		$this->assign('jumpurl',$_SERVER['HTTP_REFERER']);

		$format=array(0=>'图片',1=>'动画','文字/代码');
		$this->assign('format', $format);
    }
    Public function add(){
        $this->_before_edit();
        $this->display('edit');
    }

	public function _before_insert() {
        $this->upload();
    }
    public function _before_update() {
        $this->upload();
    }
	public function upload() {
        if(!empty($_FILES['url']['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,gif,png,jpeg,bmp,swf');
            //设置附件上传目录
            $upload->savePath =  './Public/Uploads/Ad/';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
                $info =  $info[0];
				if (!empty($info['savename'])){
					$_POST['url'] = $info['savepath'].$info['savename'];
				}
            }
        }
		//die($_FILES['url']['name']);
    }
	public function delete()
    {
        //删除指定记录
        $model        = M('Ad');
        if(!empty($model)) {
			$pk	=	"id";
            $id         = $_REQUEST[$pk];
            if(isset($id)) {
                $condition = array($pk=>array('in',explode(',',$id)));
				$aaa=$model->where($condition)->select();
                if(false !== $model->where($condition)->delete()){
					foreach($aaa as $key=>$val){
						unlink($val['url']);
					}
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    } 
	public function sort()
    {
		$node = D('Ad');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->where(array('condition'=>'status=1','order'=>'sort asc'))->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存文件
	public function cache() {
		$Ad		=	M("Ad");
		$Adcate		=	M("Adcate");
		$cate=$Adcate->select();
		$list=array();
		foreach($cate as $val){
			$key=$val['id'];
			$list[$key]=$Ad->where('cid='.$val['id'])->select();
            foreach ($list[$key] as $value) {
                $format=intval($value['format']);
                if($format==0){
                    $html='<a href="'.$value['link'].'" target="_blank"><img src="'.$value['url'].'"  alt="'.$value['title'].'"  title="'.$value['title'].'" border="0" /></a>';
                }elseif($format==1){
                    $html='';
                }elseif($format==2){
                    $html='<a href="'.$value['link'].'" target="_blank">'.$value['content'].'</a>';            
                }
                $content="document.write('".$html."');";
                file_put_contents(RUNTIME_PATH.'Ad/'.$value['id'].'.js', $content);
            }
		}
		//$list			=	$Ad->select();
		$savefile		=	DATA_PATH.'~ad.php';
		// 所有配置参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('配置缓存生成成功！');
		}else{
			$this->error('配置缓存失败！');
		}
	}
	/*public function saveSort()
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $P    = M("Ad");
            $col    =   explode(',',$seqNoList);
            //启动事务
            //$P->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                //$P->id	=	$val[0];
                //$P->sort	=	$val[1];
                $result =   $P->data(array("id"=>$val[0],"sort"=>$val[1]))->save();
                if(false === $result) {
					break;
                }
				//echo $result,"|".$P->getlastsql(),"<br>";
            }
            //提交事务
            $P->commit();
            if($result) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            }else {
                $this->error($P->getError());
            }
        }
    }*/
}
?>