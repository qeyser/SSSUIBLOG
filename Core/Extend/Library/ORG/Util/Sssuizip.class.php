<?php
class Sssuizip{
	public function __construct(){
	}
	public function Jieya($savename='',$extractpath=''){
		$extractpath= empty($extractpath) ? './'.date('YmdHis') : $extractpath;
		if (!file_exists($extractpath)){
			if (!mkdir($extractpath)){
				return '文件夹创建失败,请先手动创建存放目录!';
			}
		}
		if (!file_exists($savename)){
			return '要解压缩的文件不存在!';
		}
		$zip = new ZipArchive;
		if ($zip->open($savename) === TRUE) {
		    $zip->extractTo($extractpath);
		    $zip->close();
		    return true;
		} else {
		    return 'zip文件不存在或者目标不是zip文件';
		}

	}
	protected function scandir($path){
		$tree=array();
		foreach(glob($path.'/*') as $v){
			if(is_dir($v)){
				$tree=array_merge($tree,Sssuizip::scandir($v));
			}else{
				$tree[]=$v;
			}
		}
		return $tree;
	}
	public function Yasuo($file='',$savename=''){
		if (is_array($file)){
			$filearr=$file;
		}elseif (is_dir($file)){
			$filearr=Sssuizip::scandir($file);
		}elseif(is_file($file)){
			$filearr=array($file);			
		}else{
			return '第一个参数不合法,正确情况下应该是一个文件,一个文件夹,一个一维文件数组的任意一种!';
		}
		$savename= empty($savename) ? './'.date('YmdHis').'zip' : $savename;
		$tmp=pathinfo($savename);
		if (!file_exists($tmp['dirname'])){
			if (!mkdir($tmp['dirname'])){
				return '文件夹创建失败,请先手动创建存放目录!';
			}
		}
		Sssuizip::tozip($filearr,$savename);
		return true;
	}
    /**
    * 压缩文件(zip格式)
    */
    protected function tozip($filearr,$savename){ 
        $zip=new ZipArchive();
        $zip->open($savename,ZipArchive::CREATE);
        for ($i=0;$i<count($filearr);$i++){
        	$tmp=pathinfo($filearr[$i]);
            $zip->addFile($filearr[$i],$tmp['basename']);  //true || false 
        }
        $zip->close();
    }
 }
?>