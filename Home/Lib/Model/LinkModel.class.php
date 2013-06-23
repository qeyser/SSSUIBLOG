<?php
// +----------------------------------------------------------------------
// | 友情链接模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class LinkModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
        array('url','url','链接格式错误'),
	);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_BOTH,'function'),
		);
}
?>