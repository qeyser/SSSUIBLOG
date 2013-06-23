<?php
// +----------------------------------------------------------------------
// | 用户相册模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class PhotoModel extends CommonModel {
	protected $_validate	 =	 array(
		array('cid','require','请选择相册分类！'),
		array('title','require','请输入相片标题！'),
		array('img','require','请选择照片！'),
	
	);
	protected $_auto	 =	 array(
		array('hit','0',self::MODEL_INSERT,'string'),
		array('is_recommend','0',self::MODEL_INSERT,'string'),
		array('status','1',self::MODEL_INSERT,'string'),
		array('uid','getMemberId',self::MODEL_INSERT,'callback'),
		array('create_time','time',self::MODEL_INSERT,'function'),
	);
}
?>