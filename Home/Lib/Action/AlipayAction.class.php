<?php
// +----------------------------------------------------------------------
// | 支付宝即时到帐接口,版本：3.2
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
require_once(LIB_PATH."Vendor/Alipay/alipay_core.function.php");
require_once(LIB_PATH."Vendor/Alipay/alipay_submit.class.php");
class AlipayAction extends BaseAction{
    public $aliapy_config=array(
            'partner'       =>  '2088801384404756',
            'key'           =>  'vnzl00144jpeutagtdraykznuieqgrxa',
            'seller_email'  =>  'jishanwang@qq.com',
            'return_url'    =>  C('WEB_URL').'/Alipay_returnurl.html',
            'notify_url'    =>  C('WEB_URL').'/Alipay_notifyurl.html',
            'sign_type'     =>  'MD5',
            'input_charset' =>  'utf-8',
            'transport'     =>  'http'
    );
    public function index(){
        require_once(LIB_PATH."Vendor/Alipay/alipay_service.class.php");
        $Chongzhi=M('Chongzhi');
        if (!$_REQUEST['ordernum']){
            $data=array(
                    'uid'=>$_SESSION['uid'],
                    'num'=>intval($_REQUEST['jine']),
                    'create_time'=>time(),
                    'paystatus'=>0,
                    'opstatus'=>0,
                    'status'=>1
                    );
            if ($data['uid']==0 ||$data['uid']==3 ){
                $this->error('请先登录!');
            }
            if ($data['num']<=0){
                $this->error('金额不能小于零!');
            }
            $ordernum=$Chongzhi->data($data)->add();
            if (false===$ordernum){
                $this->error('订单失败,请刷新后重试!');
            }
        }else{
           if(($_SESSION['uid']!=0)&&($_SESSION['uid']!="")){
            $ordernum=$_REQUEST['ordernum'];
           }
        }
        $vo=$Chongzhi->where("id=$ordernum and status=1 and paystatus=0")->find();
        if (count($vo)==0){
            $this->error('订单不存在或已支付,请查证!');
        }
        //唯一订单号
        $out_trade_no = $vo['id'];
        $subject      = C("WEB_NAME")."(".C('WEB_URL').")充值";
        $body         = C("WEB_NAME")."(".C('WEB_URL').")充值";
        $total_fee    = $vo['num'];
        $paymethod    = '';
        $defaultbank  = '';
        $anti_phishing_key  = '';
        //获取客户端的IP地址，建议：编写获取客户端IP地址的程序
        $exter_invoke_ip = get_client_ip();
        $show_url           = C('WEB_URL');

        //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
        $extra_common_param = '';

        //扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
        $royalty_type       = "";
        $royalty_parameters = "";
        //构造要请求的参数数组
        $parameter = array(
                "service"           => "create_direct_pay_by_user",
                "payment_type"      => "1",
                
                "partner"           => trim($this->aliapy_config['partner']),
                "_input_charset"    => trim(strtolower($this->aliapy_config['input_charset'])),
                "seller_email"      => trim($this->aliapy_config['seller_email']),
                "return_url"        => trim($this->aliapy_config['return_url']),
                "notify_url"        => trim($this->aliapy_config['notify_url']),
                
                "out_trade_no"      => $out_trade_no,
                "subject"           => $subject,
                "body"              => $body,
                "total_fee"         => $total_fee,
                
                "paymethod"         => $paymethod,
                "defaultbank"       => $defaultbank,
                
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip"   => $exter_invoke_ip,
                
                "show_url"          => $show_url,
                "extra_common_param"=> $extra_common_param,
                
                "royalty_type"      => $royalty_type,
                "royalty_parameters"=> $royalty_parameters
        );

        //构造即时到帐接口
        $alipayService = new AlipayService();
        $alipayService->aliapy_config=$this->aliapy_config;
        $html_text = $alipayService->create_direct_pay_by_user($parameter);
        echo "正在跳转,如果无法自动跳转，请点击确认按钮......";
        echo "<pre>$html_text</pre>";
        //$this->assign('msg',$html_text);
        //$this->display();
    }

    public function notifyurl(){
        require_once(LIB_PATH."Vendor/Alipay/alipay_notify.class.php");
        $alipayNotify = new AlipayNotify($this->aliapy_config);
        $verify_result = $alipayNotify->verifyNotify();
        $info="fail";
        if($verify_result) {
            $out_trade_no   = $_POST['out_trade_no'];    //获取订单号
            $trade_no       = $_POST['trade_no'];        //获取支付宝交易号
            $total_fee      = $_POST['total_fee'];       //获取总价格
            if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                $Chongzhi=M('Chongzhi');
                $vo=$Chongzhi->where("id=$out_trade_no and status=1")->select();
                if (count($vo)>0){
                    $vo=$vo[0];
                    if ($vo['paystatus'] ==1 || $vo['opstatus']==1){
                        //$this->error('该订单已被处理过!');
                        $info="success";
                    }else{
                        if ($total_fee == $vo['num']){
                            $addresult		=true;	
                            if($vo['status']==1){
                                $addresult=$this->add($vo['uid'],$vo['num']*C("MONEY"),$feild='money');
                            }
                            if($addresult){
                                $Chongzhi->data(array('paystatus'=>1,'opstatus'=>1))->where("id=$out_trade_no and status=1")->save();
                            }else{
                                $Chongzhi->data(array('paystatus'=>1))->where("id=$out_trade_no and status=1")->save();
                                ///$this->assign("jumpUrl",U("User/repeatpayment",'id='.$out_trade_no));
                                //$this->error('支付成功，但是银两未兑换成功。你可以在会员中心的充值记录里面重新兑换。如有问题请联系管理员！');
                            }
                            $info="success";
                        }
                    }
                }
            }
        }
        echo $info;
    }
    public function returnurl(){
        unset($_GET['_URL_']);
        $_GET['notify_id']= rawurldecode($_GET['notify_id']);
        require_once(LIB_PATH."Vendor/Alipay/alipay_notify.class.php");
        $alipayNotify = new AlipayNotify($this->aliapy_config);
        $verify_result = $alipayNotify->verifyReturn();
        $this->assign("jumpUrl",U("User/Chongzhi/index"));
        if($verify_result) {
            //验证成功
            $out_trade_no   = $_GET['out_trade_no'];    //获取订单号
            $trade_no       = $_GET['trade_no'];        //获取支付宝交易号
            $total_fee      = $_GET['total_fee'];       //获取总价格
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $Chongzhi=M('Chongzhi');
                $vo=$Chongzhi->where("id=$out_trade_no and status=1")->select();
                //echo $Chongzhi->getlastsql(),'<br>';
                if (count($vo)==0){
                    $this->error('订单不存在或已支付,请查证!');
                }else{
                    $vo=$vo[0];
                    if($vo['status']==1){
                        if ($vo['paystatus'] ==1 || $vo['opstatus']==1){
                            //$this->error('该订单已被处理过!');
                        }else{
                            if ($total_fee == $vo['num']){
                                if($this->add($vo['uid'],$vo['num']*C("MONEY"),$feild='money')){
                                    $Chongzhi->data(array('paystatus'=>1,'opstatus'=>1))->where("id=$out_trade_no and status=1")->save();
                                    $this->success("支付成功，".C('HUOBI')."已兑换成功！");
                                }else{
                                    $Chongzhi->data(array('paystatus'=>1))->where("id=$out_trade_no and status=1")->save();
                                    $this->error('支付成功，但是'.C('HUOBI').'未兑换成功。你可以在会员中心的充值记录里面重新兑换。如有问题请联系管理员！');
                                }
                            }else{
                                $this->error('订单交易额不一致,请查证,或联系管理员!');
                            }
                        }
                    }else{
                        $this->error('该订单已被管理员禁止操作!');
                    }
                }
            }else {
                $this->error("支付失败！");
            }
        }else {
            $this->error("支付失败！");
        }
    }
    /*增加用户参数*/
    function add($uid,$num,$feild='money'){
        $User=M("User");        
        $status=$User->data(array($feild=>array('exp', "($feild+$num)")))->where("id=$uid")->save();
        if ($status){
            return true;
        }else{
            return false;
        }
    }

    function repeatpayment(){
        $id=$this->_get('id');
        $Chongzhi=M('Chongzhi');
        $vo=$Chongzhi->where("id=$id and status=1 and paystatus=1")->find();
        if (count($vo)==0){
            $this->error('订单不存在或已成功,请查证!');
        }else{
            if($vo['status']==1){
                if ($vo['opstatus']==1){
                    $this->error('该订单已被处理过!');
                }else{
                    $addstatus=$this->add($vo['uid'],$vo['num']*C("MONEY"),$feild='money');
                    if($addstatus){
                        $Chongzhi->data(array('opstatus'=>1))->where("id=$id and status=1")->save();
                        $this->success(C('HUOBI').'增加成功！',U('User/Chongzhi/index'));
                    }else{
                        $this->error('增加'.C('HUOBI').'未成功！',U('User/Chongzhi/index'));
                    }
                }
            }else{
                $this->error('该订单已被管理员禁止操作!',U('User/Chongzhi/index'));
            }
        }
    }
}

?>