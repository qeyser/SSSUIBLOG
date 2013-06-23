<?php
// +----------------------------------------------------------------------
// | 用户反馈模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class FeedbackModel extends CommonModel {
	protected $_validate	 =	 array(
		array('nickname','require','请问您怎么称呼？'),
		array('email','require','请请输入您的邮箱，以便我们回访'),
		array('content','require','请输入反馈内容！'),
		);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
        array('reply_time','time',self::MODEL_UPDATE,'function'),
		);

}
?>