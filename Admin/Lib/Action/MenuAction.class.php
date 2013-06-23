<?php
// +----------------------------------------------------------------------
// | 前台菜单管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class MenuAction extends CommonAction {

	public function _filter(&$map)
	{
		if($map['title']!=""){
			$map['title']=array('like',"%".$map['title']."%");
		}
		if(!isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		$_SESSION['currentMenuId']	=	$map['pid'];
		//获取上级节点
		$Menu  = D("Menu");
		if($Menu->getById($map['pid'])) {
			$this->assign('level',$Menu->level+1);
			$this->assign('columnName',$Menu->name);
		}else {
			$this->assign('level',1);
		}
	}

	public function add()
	{
		$Menu	=	D("Menu");
		$Menu->getById($_SESSION['currentMenuId']);
        $this->assign('pid',$Menu->id);
		$this->assign('level',$Menu->level+1);
		$this->display();
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
		$node = M('Menu');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentMenuId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
}
?>