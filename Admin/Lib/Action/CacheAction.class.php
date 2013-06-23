<?php
// +----------------------------------------------------------------------
// | 缓存管理
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class CacheAction extends Action{
	function index(){
			$f = $this->delpath("./Admin/Runtime/"); //调用删除
			$f = $this->delpath("./Home/Runtime/"); //调用删除
			$f = $this->delpath("./Data/"); //调用删除
			$this->assign('jumpUrl','admin.php?s=index/main');
			$this->success("缓存生成成功!");

	}
	//删除目录
	function delpath($del_path){
		if(!file_exists($del_path)){ //目标目录不存在则建立
			echo "目录未找到."; return false;
		}
		$hand = @opendir($del_path);
		$i = 0;
		while($file = @readdir($hand)){
			$i++;
			if($file!="." && $file!=".."){//目录
				if(is_dir($del_path."/".$file)){
					$del_s_path = $del_path."/".$file;
					$this -> delpath($del_s_path);
				}else{
					$del_file = $del_path."/".$file;
					$this -> file($del_file);
				}
			}
		}
		@closedir($hand);
		$this->path($del_path);
		return true;
	}
	//删除文件
	function file($del_file){ 
		@unlink($del_file);
	}
	//删除目录
	function path($del_path){ 
		@rmdir($del_path); 
	}
}
?>