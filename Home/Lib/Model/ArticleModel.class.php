<?php
// +----------------------------------------------------------------------
// | 新闻模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class ArticleModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','标题必须填写！'),
		array('cid','require','分类必须选择！'),
		array('content','require','内容必须填写！'),
	);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_UPDATE,'function'),
	);

}
?>