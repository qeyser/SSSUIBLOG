<?php
// +----------------------------------------------------------------------
// | 友情链接管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class LinkAction extends CommonAction {
	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function sort()
    {
		$this->assign('pagetitle',$this->titlearr[MODULE_NAME]);
		$Link = M('Link');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
        }else{
            $map['status'] = 1;
        }
        $map['module'] = MODULE_NAME;
        $sortList   =   $Link->where($map)->order('sort asc')->select();
        $this->assign("sortList",$sortList);
        $this->display("Yqlj:sort");
        return ;
    }
}
?>