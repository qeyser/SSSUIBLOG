<?php
// +----------------------------------------------------------------------
// | 标签视图模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
import('ViewModel');
class TagViewModel extends ViewModel {
    protected $viewFields =  array(
        'Tag'=>array('name','module'),
        'Tagged'=>array('user_id','tag_id','record_id'),
        'Topic'=>array('title','id','create_time','read_count','comment_count','status')
        );

    protected $viewCondition = array(
        'Tagged.tag_id'    =>array('eqf','Tag.id'),
        'Topic.id'=>array('eqf','Tagged.record_id'),
        'Tagged.module'=>'Topic',
    );
}
?>