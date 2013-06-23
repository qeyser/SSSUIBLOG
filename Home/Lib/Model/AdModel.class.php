<?php
// +----------------------------------------------------------------------
// | 广告模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class AdModel extends CommonModel {
	protected $_validate = array(
		array('title','require','必须填写标题'),
		array('url','require','展示图片或者多媒体必须填写'),
		array('cid','require','必须选择分类'),
		array('format','require','必须指定文件格式'),
		);

	protected $_auto		=	array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'), 
		);
}
?>