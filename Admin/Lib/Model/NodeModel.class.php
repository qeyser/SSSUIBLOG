<?php
// +----------------------------------------------------------------------
// | 权限节点模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class NodeModel extends CommonModel {
	protected $_validate	=	array(
		array('name','checkNode','节点已经存在',0,'callback'),
		);

	public function checkNode() {
        if(is_string($_POST['name'])) {
            $map['name']	 =	 $_POST['name'];
            $map['pid']	=	isset($_POST['pid'])?$_POST['pid']:0;
            $map['status'] = 1;
            if(!empty($_POST['id'])) {
                $map['id']	=	array('neq',$_POST['id']);
            }
            $result	=	$this->where($map)->field('id')->find();
            if($result) {
                return false;
            }else{
                return true;
            }
        }
        return true;
	}
}
?>