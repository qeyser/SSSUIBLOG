<?php
// +----------------------------------------------------------------------
// | 后台首页
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class IndexAction extends CommonAction {
	// 首页
	public function index() {
		$this->display();
	}
	// 首页
	public function main() {
		// 统计数据
        $User=M("User");
        $lcheng=strtotime(date("Y-m-d")." 00:00:00");
        $info = array(
            '总会员数'=>$User->count("id")."个",
            '今日注册'=>$User->where("create_time>".$lcheng)->count("id")."个",
           
            '运行环境'=>PHP_OS ." ".$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
		$this->display();
	}
    //搜索
    public function search(){
        $q=$this->_POST('q');
        $Node=M('Node');
        $this->list=$Node->where("level=2 and is_show=1 and ((title like '%".$q."%') or (name like '%".$q."%'))")->select();
        //echo $Node->getlastsql();
        $this->display();
    }
}
?>