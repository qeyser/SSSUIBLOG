<?php
// +----------------------------------------------------------------------
// | 分组管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class GroupAction extends CommonAction {
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
		$node = D('Group');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->where(array(
                'condition'=>'status=1',
                'order'=>'sort asc')
                )->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
	public function _filter(&$map){
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
	}
}
?>