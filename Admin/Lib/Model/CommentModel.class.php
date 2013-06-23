<?php
// +----------------------------------------------------------------------
// | 评论模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class CommentModel extends CommonModel {
	protected $_validate	 =	 array(
			array('pid','require','信息不全！'),
			array('content','require','请输入评论内容！'),
			array('module','require','信息不全！'),
		
		);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>