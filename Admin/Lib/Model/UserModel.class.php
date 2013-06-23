<?php
// +----------------------------------------------------------------------
// | 用户模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class UserModel extends CommonModel {
	protected $_validate	=	array(
		array('account','require','请填写帐号'),
		array('password','require','请输入密码'),
		array('nickname','require','请输入昵称'),
		array('account','','帐号已经存在',0,'unique',1,self::MODEL_INSERT),
		);

	protected $_auto		=	array(
		array('avatar','Public/Images/noavatar.jpg',self::MODEL_INSERT,'string'),
		array('sex','2',self::MODEL_INSERT,'string'),
		array('fansnum','0',self::MODEL_INSERT,'string'),
		array('renqi','0',self::MODEL_INSERT,'string'),
		array('jifen','0',self::MODEL_INSERT,'string'),
		array('money','0',self::MODEL_INSERT,'string'),
		array('email_auth','0',self::MODEL_INSERT,'string'),
		array('login_count','0',self::MODEL_INSERT,'string'),
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);

    public function checkBindAccount() {
        $map['id']   = array('neq',$_POST['id']);
        $map['bind_account']    = $_POST['bind_account'];
        if($this->where($map)->find()) {
            return false;
        }
        return true;
    }

}
?>