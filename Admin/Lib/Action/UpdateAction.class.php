<?php 
class UpdateAction extends CommonAction{
	function index(){
		//$remoteversion=file_get_contents('http://127.0.0.1/version.php?action=version');
		//die($remoteversion);	
		$remoteversion=json_decode(file_get_contents('http://127.0.0.1/version.php?action=version'),true);
		$localversion=file_get_contents('version.txt');
		$this->assign('remoteversion',$remoteversion);
		$this->assign('localversion',$localversion);
		$this->display();
	}
	function updatefromserver(){
		// for ($i=0; $i < 10; $i++) { 
		// 	$localversion=file_put_contents($i.'.txt','cansnow');
		// }
		// die();
		$remoteversion=json_decode(file_get_contents('http://127.0.0.1/version.php?action=version'),true);
		foreach ($remoteversion['file'] as $key => $value) {
			switch ($value) {
				case 'd':
					echo "正在删除{$key}<Br>"; 
					sleep(1);
					ob_flush(); 
					flush(); 
					if (!unlink($key)){$err[$key]=$value;}else{$ok[$key]=$value;}
					break;
				default:
					echo "正在更新{$key}<Br>"; 
					sleep(1);
					ob_flush(); 
					flush(); 
					if(!file_put_contents($key, file_get_contents('http://127.0.0.1/version.php?action=getfile&file='.base64_encode($key)))){$err[$key]=$value;}else{$ok[$key]=$value;}
					break;
			}
		}
		if (empty($err)){
			$this->success('更新成功!');
			$localversion=file_put_contents('version.txt',$remoteversion['version']);
		}else{
			echo "<script>document.body.innerHTML='';</script>";
			$this->assign('ok',$ok);
			$this->assign('err',$err);
			$this->display();
		}
	}
	protected function scandir($path){
		$tree=array();
		foreach(glob($path.'/*') as $v){
			if(!is_dir($v)){
				$tree[]=$v;
			}
		}
		return $tree;
	}
}
?>