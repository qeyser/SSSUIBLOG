<?php
// +----------------------------------------------------------------------
// | 单页内容
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class SinglepageAction extends CommonAction {
	public function add(){
		$this->display('edit');
	}

	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
	function delimg($id=0){
		if ($id=0){
			$id=intval($_GET['id']);
		}
		$Img=M('Img');
		$vo=$Img->find($id);
		@unlink($vo['thu_img']);
		@unlink($vo['img']);
		$Img->where('id='.$id)->delete();
		return true;
	}
	public function imgupload() {
        if(!empty($_FILES['img']['name'])) {
            import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->thumb		=true;
			$upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
        	$upload->allowExts  = explode(',',"gif,png,jpg,jpeg,bmp");
        	$upload->savePath =  './Public/Uploads/Article/';
        	//$upload->thumbPath =  './Public/Uploads/Photo/';
			$upload->thumbMaxWidth =60;
			$upload->thumbMaxHeight =40;
			// 记录上传成功ID
			$uploadId =  array();
			$savename = array();
			//执行上传操作
			if(!$upload->upload()) {
				if($this->isAjax() && isset($_POST['_uploadFileResult'])) {
					$uploadSuccess =  false;
					$ajaxMsg  =  $upload->getErrorMsg();
				}else {
					//捕获上传异常
					$this->error($upload->getErrorMsg());
				}
			}else {
				 //取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				$Img=M('Img');
				foreach($uploadList as  $val){
					$data=array(
						'pid'		=>$_POST['id'],
						'module'	=>$_POST['module'],
						'img'		=>$val['savepath'].$val['savename'],	
						'thu_img'	=>$val['savepath'].'thumb_'.$val['savename'],
						'status'	=>1,
						'create_time'=>time()
					);
					$Img->data($data)->add();
					$this->assign('jumpUrl','?s=/Singlepage/img/id/'.$data['pid']);
					$this->success('操作成功!');
					//redirect('Singlepage/img/id/'.$data['pid']);
				}
			}
        }
    }
    public function sort()
    {
        $Manual = M('Singlepage');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
        }else{
            $map['status'] = 1;
        }
        $sortList   =   $Manual->where($map)->order('sort asc')->select();
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
}

?>