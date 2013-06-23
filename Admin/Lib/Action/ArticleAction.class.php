<?php
// +----------------------------------------------------------------------
// | 文章管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class ArticleAction extends CommonAction {
	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
	function add(){	
		$this->display('edit');
	}
    public function _tigger_insert($model)
    {
        $this->saveTag($model->tags, $model->id, 'Article');
        import("ORG.Util.Ping");
        $Ping=new Ping();
        $Ping->Ping($model->id,GTC($model->id,'Article','cid'));
    }
    public function _tigger_update($model)
    {
        $this->saveTag($model->tags, $model->id, 'Article');
        import("ORG.Util.Ping");
        $Ping=new Ping();
        $Ping->Ping($model->id,GTC($model->id,'Article','cid'));
    }
    function _Before_Add(){
        $c=M("Cate");
        $sql="SELECT * from z_cate as a where module='Article' and (select count(*) from z_cate where pid=a.id )=0";
        $list=$c->query($sql);
        foreach($list as $key=>$val){
            $l[$val['id']]=$val['title'];
        }
        $this->assign('cate',$l);
    }
    function _Before_edit(){
		$this->_Before_Add();
	}
    function _Before_index(){
		$this->_Before_Add();
	}
	public function _before_insert() {
        $this->upload();
    }
    public function _before_update() {
        $this->upload();
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
            $upload->savePath =  'Public/Uploads/Article/';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
				if (!empty($info['0']['savename'])){
					$_POST['img'] = '/Public/Uploads/Article/'.$info['0']['savename'];
				}
            }
        }
    }
    public function delete()
    {
        //删除指定记录
        $model        = M('Article');
        if(!empty($model)) {
            $id         = $_REQUEST["id"];
            if(isset($id)) {
                $condition = array("id"=>array('in',explode(',',$id)));
				$aaa=$model->where($condition)->select();
                if(false !== $model->where($condition)->delete()){
					foreach($aaa as $key=>$val){
						unlink($val['img']);
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
}

?>