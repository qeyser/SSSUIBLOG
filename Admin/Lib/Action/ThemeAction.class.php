<?php
class ThemeAction extends CommonAction {
	public function index(){
		$handle = opendir('./Home/Tpl/');
	    /* 这是正确地遍历目录方法 */
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && $file != "Widget") {
		       $list[$file]='./Home/Tpl/'.$file;
		    }
	    }
		$global=include('config.php');
		$this->assign('curr_theme',$global['DEFAULT_THEME'] ? $global['DEFAULT_THEME'] : 'default');
		$this->assign('list',$list);
		$this->display();
	}
	public function setTheme(){
		$global=include('config.php');
        $theme=$_REQUEST['theme'];
		$global['DEFAULT_THEME']	=	$theme;
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($global,CASE_UPPER),true).";\n?>";
        if(!file_put_contents('config.php',$content)){
            $this->error('配置缓存失败！');
        }
        cookie('think_template',$theme);
        $this->success('操作成功!');
	}
	public function edit(){
		$theme=$_GET['theme'] ? $_GET['theme'] : 'default';
		$path="./Home/Tpl/".$theme;
		$filelist=$this->scandir($path);
		$this->assign('filelist',$filelist);
		$this->assign('theme',$theme);
		$this->assign('themepath',$path.$theme);
		$file=base64_decode($_GET['file']);
		$this->assign('extarr',array(
			'img'=>array('jpg','png','bmp','gif','jpeg')
		));
		if ($file){
			$this->assign('file',$file);
			$this->assign('fileinfo',pathinfo($file));
		}
		$this->display();
	}
	public function update(){
		$file=$_POST['file'];
		//echo $_POST['filecontent'];
		//die($file);
		if (file_put_contents($file,stripcslashes($_POST['filecontent']))){
			redirect('?s=Theme/edit/theme/'.$_POST['theme'].'/file/'.base64_encode($file));
		}else{
			$this->error('文件存储失败!');
		}
	}
	function scandir($path){
		$tree=array();
		foreach(glob($path.'/*') as $v){
			if(is_dir($v)){
				$tree=array_merge($tree,$this->scandir($v));
			}else{
				$v1=pathinfo($v);
				$v1['rpath']=$v;
				$v1['epath']=base64_encode($v);
				$tree[]=$v1;
			}
		}
		return$tree;
	}
}
?>