<?php
// +----------------------------------------------------------------------
// | 前台菜单模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class MenuModel extends CommonModel {
	protected $_validate	=	array(
		array('name','require','标示符name必须填写'),
		array('title','require','请输入显示名称！'),
	);
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
	);
}
?>