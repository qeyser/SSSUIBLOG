<?php
// +----------------------------------------------------------------------
// | Access模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class AccessModel extends CommonModel {
	protected $_validate	 =	 array(	
			array('role_id','require','信息不全！'),
			array('node_id','require','信息不全！'),
			array('status','require','信息不全！'),
		
		);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		);
}
?>