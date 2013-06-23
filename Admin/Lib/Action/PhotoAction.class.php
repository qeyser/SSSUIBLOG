<?php
// +----------------------------------------------------------------------
// | 相册管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class PhotoAction extends CommonAction {
	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
	function _Before_Add(){
		$c=M("Cate");
		$sql="SELECT * from z_cate as a where module='Photo' and (select count(*) from z_cate where pid=a.id )=0";
		$list=$c->query($sql);
		foreach($list as $key=>$val){
			$l[$val['id']]=$val['title'];
		}
		$this->assign('cate',$l);
	}
	function add(){	
		$this->display('edit');
	}
    function _Before_edit(){
		$this->_Before_Add();
	}
    function _Before_index(){
		$this->_Before_Add();
	}
    public function upload() {
        $_POST['title'] = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
        if(empty($_FILES['picdata']['name'])){
            return false;
        }
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload->thumb      =true;
        $upload->thumbMaxWidth =100;
        $upload->thumbMaxHeight =100;  
        $upload->thumbMaxHeight =100;      
        $upload->maxSize= 10000000;
        $upload->allowExts  = array("gif", "png", "jpg", "jpeg", "bmp");
        $upload->savePath =  './Public/Uploads/Photo/';
        // 记录上传成功ID
        $uploadId =  array();
        $savename = array();
        //执行上传操作
        if(!$upload->upload()) {
            $state=$upload->getErrorMsg();
        }else {
            $uploadList         = $upload->getUploadFileInfo();
            $_POST['uid']       =$_SESSION[C('USER_AUTH_KEY')];
            //$_POST['uid']=$_POST['cid'];
            $_POST['img']       =$uploadList[0]['savepath'].$uploadList[0]['savename'];
            $_POST['thu_img']   =$uploadList[0]['savepath'].'thumb_'.$uploadList[0]['savename'];
            $_POST['format']    =$uploadList[0]['extension'];
            $_POST['title']     =empty($_POST['title']) ? $uploadList[0]['name'] : $_POST['title'];
            $Photo=M('Photo');
            if(false === $Photo->create()) {
                $state=$Photo->getError();
            }
            if($result = $Photo->add()) {
                $state='SUCCESS';
            }else {
                $state='操作失败';
            }
        }
        $str= "{'url':'" . $_POST["img"] . "','title':'" . $_POST['title'] . "','original':'" . $info["originalName"] . "','state':'" . $state . "'}";
        echo $str;
        file_put_contents('1.txt', $str);
    }
    public function delete()
    {
        //删除指定记录
        $model        = M('Photo');
        if(!empty($model)) {
            $id         = $_REQUEST["id"];
            if(isset($id)) {
                $condition = array("id"=>array('in',explode(',',$id)));
                $aaa=$model->where($condition)->select();
                if(false !== $model->where($condition)->delete()){
                    foreach($aaa as $key=>$val){
                        unlink($val['img']);
                        unlink($val['thu_img']);
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