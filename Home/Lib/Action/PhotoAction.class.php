<?php
// +----------------------------------------------------------------------
// | 用户相册
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class PhotoAction extends BaseAction {
    public function index(){
		$this->slide();
        $Where="status=1";
        $cid=$this->_get('cid');
        if ($cid){
            $Where.=" and cid=".$cid;
        }
		$this->_list('Photo',$where,'id desc',10,'PhotoList','PhotoPage');
        $this->display('list');
    }
	protected function slide(){
        $Cate=M('Cate');
        $this->assign('PhotoCateList',$Cate->where('status=1 and module="Photo"')->order('id asc')->select());
        $Article=M('Article');
        $this->assign("NearList",$Article->where('status=1')->Order('hit desc')->limit(10)->select());
        
        $Comment=M('Comment');
        $this->assign("NearComment",$Comment->where('status=1')->Order('id asc')->limit(10)->select());
        
        $Cate=M('Cate');
        $this->assign("CateList",$Cate->where('status=1')->Order('id asc')->select());
        
        $Link=M('Link');
        $this->assign("LinkList",$Link->where('status=1')->Order('id asc')->limit(20)->select());	
	}
}

?>