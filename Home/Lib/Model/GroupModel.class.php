<?php
// +----------------------------------------------------------------------
// | 权限分组模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class GroupModel extends CommonModel {
	protected $_validate = array(
		array('name','require','标示符必须填写'),
		array('title','require','请输入显示名称！'),
		);

	protected $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>