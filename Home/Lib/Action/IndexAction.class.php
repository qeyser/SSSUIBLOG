<?php
// +----------------------------------------------------------------------
// | 默认首页
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

import("ORG.Util.Session");
class IndexAction extends BaseAction {
    public function index(){
        $this->slide();
        $Article=M('Article');
		$this->setseoinfo('首页','','');
		$this->_list('Article','status=1','id desc',10,'ArtList','ArtPage');
        $this->display('Article:list');
    }
	public function tag($tag=null){
		$this->slide();
		$tag=is_null($tag) ? $_GET['tag'] : $tag;
        $name=trim($tag);
		$this->setseoinfo($name,$name,$name);
        $module= 'Article';
		$Tag = M("Tag");
        $Stat  = $Tag->where('module="'.$module.'" and name="'.$name.'"')->field("id,count")->find();
        $tagId  =  $Stat['id'];
        $count   =  $Stat['count'];
        $Model = M('Article');
        $Tagged = M("Tagged");
        $recordIds  =  $Tagged->where("module='".$module."' and tag_id=".$tagId)->getField('id,record_id');
        if($recordIds) {
            $map['id']   = array('IN',$recordIds);
            $this->_list($module,$map,'id desc',20,'ArtList','ArtPage');
			$this->display('Article:list');
        }else{
            $this->error('标签没有对应的文章！');
        }
	}
	protected function slide(){
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