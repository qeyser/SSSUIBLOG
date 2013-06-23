<?php
//SEO关键字设置
class SeoAction extends CommonAction {
	public function index(){
		$arr=require('keyword.php');
		$this->assign('list',$arr);
		$this->display();
	}
	public function update(){
		//P($_POST['cansnow']);
		$list=require('keyword.php');
		$curr = explode('|',$_POST['curr']);
		$App=$curr[0];
		$Group=$curr[1];
		$Module=$curr[2];
		//echo $App,$Group,$Module;
		$list[$App][$Group][$Module]=$_POST['cansnow'];
		///P($list);
		//die();
		$savefile = 'keyword.php';
		// 所有配置参数统一为大写        
		$content = "<?php\nreturn " . var_export($list, true) . ";\n?>";
		if (!file_put_contents($savefile, $content)) {
			$this->error('配置缓存失败！');
		}else{
			$this->success('操作成功！');
		}
        
	}
}
?>