<?php
// +----------------------------------------------------------------------
// | Cate模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class CateModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','分类名称必须！'),
		);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>