<?php
// +----------------------------------------------------------------------
// | 文章
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class ArticleAction extends BaseAction {
    function _initialize(){
        parent::_initialize();
        $Article=M('Article');
        $this->assign("NearList",$Article->where('status=1')->Order('hit asc')->limit(10)->select());
        
        $Comment=M('Comment');
        $this->assign("NearComment",$Comment->where('status=1')->Order('id asc')->limit(10)->select());
        
        $Cate=M('Cate');
        $this->assign("CateList",$Cate->where('status=1')->Order('id asc')->select());
        
        $Link=M('Link');
        $this->assign("LinkList",$Link->where('status=1')->Order('id asc')->limit(20)->select());
    }
    public function index(){
    	$this->category();
    }
    public function category($id=0){
    	if ($id==0){
    		$id=intval($_REQUEST['id']);
    	}
    	$where='status=1';
    	if ($id!=0){
    		$where.=' and cid='.$id;
    	}
		$pagetitle=GTC($cid,'Cate');
    	$pagetitle=empty($pagetitle) ? '新闻列表' : $pagetitle;
    	$this->_list('Article',$where,'id desc',20,'ArtList','ArtPage');

        $this->setseoinfo($pagetitle,$pagetitle,$pagetitle);
        $this->display('Article:list');
    }
    public function read($id=0){
        $id= $id ? $id : intval($_REQUEST['id']);
        $Article=M('Article');
        $vo=$Article->find($id);
        if (!$vo){
            $this->error('该文章不存在或已被删除!');
        }
        $this->assign("vo",$vo);
        $this->setseoinfo($vo['title'],$vo['title'],strip_tags(getAbstract($vo['content'],'')));
        $this->_list('Comment','module="Article" and pid='.$id,'id desc',20,'CommentList','CommentPage');
        $this->assign('pre',$Article->where("id<".$id)->order("id desc")->find());
        $this->assign('next',$Article->where("id>".$id)->order("id asc")->find());
        $this->display('Article:read');
        $Article->query('update z_article set hit=hit + 1 where id='.$id);
    }
    public function search($kw=''){
        $kw= empty($kw) ? $this->_request('kw') : $kw;
        $kws=explode(' ',$kw);
        $where='status=1';
        if (count($kws)>0){
            foreach ($kws as $k => $v) {
                $where.=" and title like '%".$v."%'";
            }
        }
        $this->setseoinfo('搜索结果:'.$kw,$kw,$kw);
        $this->_list('Article',$where,'id desc',20,'ArtList');
        $this->display('Article:list');
    } 
}
?>