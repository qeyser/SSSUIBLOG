<?php
// +----------------------------------------------------------------------
// | 后台公用模块，所有类都继承此类
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
import("ORG.Util.Cookie");
class CommonAction extends Action {
    public function _initialize()
    {
		//die($_SESSION[C('USER_AUTH_KEY')]);
        // 用户权限检查
        if(C('USER_AUTH_ON') && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if(!RBAC::AccessDecision()) {
                if(!$_SESSION[C('USER_AUTH_KEY')] || $_SESSION[C('USER_AUTH_KEY')]==-2) {
                    redirect(PHP_FILE.C('USER_AUTH_GATEWAY'));
                }
                if(C('RBAC_ERROR_PAGE')) {
                    redirect(C('RBAC_ERROR_PAGE'));
                }else{
                    if(C('GUEST_AUTH_ON')){
                        $this->assign('jumpUrl',PHP_FILE.C('USER_AUTH_GATEWAY'));
                    }
                    $this->error(L('_VALID_ACCESS_').'123');
                }
            }
        }
		// 读取系统配置参数
        if(!file_exists(DATA_PATH.'~config.php')) {
            $config		=	M("Config");
            $list			=	$config->getField('name,value');
            $savefile		=	DATA_PATH.'~config.php';
            // 所有配置参数统一为大写
            $content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
            if(!file_put_contents($savefile,$content)){
                $this->error('配置缓存失败！');
            }
        }
		C(include_once DATA_PATH.'~config.php');
    }
    // 检查用户是否登录
    protected function checkUser() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->assign('jumpUrl',U('Public/login'));
            $this->error('没有登录');
        }
    }
    
	// 缓存文件
	public function cache($name='',$fields='') {
		$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M($name);
		$list		=	$Model->where('status=1')->select();
		$data		=	array();
		foreach ($list as $key=>$val){
			if(empty($fields)) {
				$data[$val[$Model->getPk()]]	=	$val;
			}else{
				// 获取需要的字段
				if(is_string($fields)) {
					$fields	=	explode(',',$fields);
				}
				if(count($fields)==1) {
					$data[$val[$Model->getPk()]]	 =	 $val[$fields[0]];
				}else{
					foreach ($fields as $field){
						$data[$val[$Model->getPk()]][]	=	$val[$field];
					}
				}
			}
		}
		$savefile		=	$this->getCacheFilename($name);
		// 所有参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($data,CASE_UPPER),true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}

	protected function getCacheFilename($name='') {
		$name	=	$name?	$name	:	$this->getActionName();
		return	 DATA_PATH.'~'.strtolower($name).'.php';
	}

	public function index($tmpl='')
    {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if(method_exists($this,'_filter')) {
            $this->_filter($map);
        }
		$model        = M($this->getActionName());
        if(!empty($model)) {
        	$this->_list($model,$map);
        }
		$this->display($tmpl);
        return;
    }

    /**
     +----------------------------------------------------------
     * 验证码显示
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function verify()
    {
        import("ORG.Util.Image");
       	Image::buildImageVerify();
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 数据对象名称
     +----------------------------------------------------------
     * @return HashMap
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _search($name='')
    {
        //生成查询条件
		if(empty($name)) {
			$name	=	$this->getActionName();
		}
		$model	=	M($name);
		$map	=	array();
		//dump($_REQUEST);
        foreach($model->getDbFields() as $key=>$val) {
            if(substr($key,0,1)=='_') continue;
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
                $map[$val]	=	$_REQUEST[$val];
            }
        }
        return $map;
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _list($model,$map=array(),$sortBy='',$asc=false)
    {
        //P($map);
        //排序字段 默认为主键名
        if(isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        }else {
            $order = !empty($sortBy)? $sortBy: $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if(isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort']?'asc':'desc';
        }else {
            $sort = $asc?'asc':'desc';
        }
        //取得满足条件的记录数
        $count      = $model->where($map)->count('id');
		//echo $model->getlastsql();
        import("ORG.Util.Page");
        //创建分页对象
        if(!empty($_REQUEST['listRows'])) {
            $listRows  =  $_REQUEST['listRows'];
        }else {
            $listRows  =  '';
        }
        $p          = new Page($count,$listRows);
        //分页查询数据
        $list     = $model->where($map)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->select();
        //echo $model->getlastsql();
		//分页跳转的时候保证查询条件
        foreach($map as $key=>$val) {
            if(!is_array($val)) {
            $p->parameter.="&$key=".urlencode($val);
            }else{
			$p->parameter.="&$key=".urlencode($val[1]);
			}
        }

        //分页显示
        $page       = $p->show();
        //列表排序显示
        $sortImg    = $sort ;                                   //排序图标
        $sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
        $sort       = $sort == 'desc'? 1:0;                     //排序方式
        //模板赋值显示
		//echo "<pre>";
		//print_r($list);
        $this->assign('list',       $list);
        $this->assign('sort',       $sort);
        $this->assign('order',      $order);
        $this->assign('sortImg',    $sortImg);
        $this->assign('sortType',   $sortAlt);
        $this->assign("page",       $page);
        Cookie::set('_currentUrl_',__SELF__);
        //echo $model->getlastsql();
        return ;
    }

    function insert($m=false)
    {
        if(!$m){
		  $m=$this->getActionName();
        }
        $model =   D($m);
        //P($model);
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
        //保存当前数据对象
        if($result = $model->add()) { //保存成功
            // 回调接口
            if(method_exists($this,'_tigger_insert')) {
                $model->id =  $result;
				$model->tags=$_POST['tags'];
                $this->_tigger_insert($model);
            }
            //成功提示
			
			if ($_REQUEST['jumpurl']){
				$url=$_REQUEST['jumpurl'];
			}else{
				$url=$this->getReturnUrl();
			}
            $this->assign('jumpUrl',$url);
            $this->success(L('新增成功'));
        }else {
            //失败提示
            $this->error(L('新增失败').'<pre>'.$model->getlastsql().'</pre>');
        }
    }

	public function add() {
		$this->display('edit');
	}

	function read() {
		$this->edit();
	}

	function edit($tmpl='') {
		$model	=	M($this->getActionName());
		$id     = $_REQUEST[$model->getPk()];
		$vo	=	$model->find($id);
		$this->assign('vo',$vo);
		$this->display($tmpl);
	}

	function update($m=false) {
        if(!$m){
          $m=$this->getActionName();
        }
        $model =   D($m);
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
		// 更新数据
		if(false !== $model->save()) {
			$model->tags=$_POST['tags'];
			$model->id=$_POST['id'];
			if(method_exists($this,'_tigger_update')) {
                $this->_tigger_update($model);
            }
            //成功提示
			if ($_REQUEST['jumpurl']){
				$url=$_REQUEST['jumpurl'];
			}else{
				$url=$this->getReturnUrl();
			}
            $this->assign('jumpUrl',$url);
            $this->success(L('更新成功'));
        }else {
            //错误提示
            $this->error(L('更新失败'));
        }
	}

    /**
     +----------------------------------------------------------
     * 默认列表选择操作
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    protected function select($fields='id,name',$title='')
    {
        $map = $this->_search();
        //创建数据对象
        $Model = M($this->getActionName());
        //查找满足条件的列表数据
        $list     = $Model->where($map)->getField($fields);
		$this->assign('selectName',$title);
        $this->assign('list',$list);
        $this->display();
        return;
    }
    public function delete($module=false)
    {
		//删除指定记录
        if(!$module){
          $module=$this->getActionName();
        }
        $model =   M($module);
        if(!empty($model)) {
			$pk	=	$model->getPk();
            $id         = $_REQUEST[$pk];
            if(isset($id)) {
                $condition = array($pk=>array('in',explode(',',$id)));
                if(false !== $model->where($condition)->delete()){
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败').$model->getlastsql());
                }
            }else {
                $this->error('非法操作');
            }
        }
    }
	function _before_del(){
		$ids= explode(',',$_REQUEST["id"]);
		$action=$this->getActionName();
		$ac=$$action=M($action);
		$ac1=str_replace('cate','',$action);
		$ac1=$$ac1=M($ac1);
		foreach($ids as $id){
			$tmp=$ac->where("up=$id")->count('id');
			if ($tmp>0){
				$this->error('该类下面有内容未被删除或移动!');
			}
			$tmp=$ac1->where("cid=$id")->count('id');
			if ($tmp>0){
				$this->error('该类下面有内容未被删除或移动!');
			}
		}
	
	}
    /**
     +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getReturnUrl()
    {
        return 'admin.php?s=/'.MODULE_NAME.'/'.C('DEFAULT_ACTION');
		//return U(MODULE_NAME);
        //return __URL__;
		//return $_SERVER['HTTP_REFERER'];
    }

	/**
	* 站点公用上传方法
	* @name  ,表单里所有file的name值,作检测是否需要上传
	* @path,保存路径
	* @thumb,是否缩略图
	* @turnpost,是否自动转换到POST数组,为假就直接返回数组,自己处理,为真就直接当普通字段处理,只不过文件的内容已经换成服务器保存路径了
	* @maxsize,文件最大大小
	* @ext,文件扩展名限制
	*/
	protected function _upload($name,$path='./Public/Uploads/',$thumb=false,$turnpost=true,$maxSize=0,$ext='gif,png,jpg,jpeg'){
		if (is_array($name)){
			$tf=false;
			foreach ($name as $k => $v) {
				if (!empty($_FILES[$v]['name'])){
					$tf=true;
					break;
				}
			}
			if (!$tf){
				return false;
			}
		}elseif(empty($_FILES[$name]['name'])){
			return false;
		}
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		if ($thumb!==false){
			$upload->thumb      =true;
			$size=explode(',', $thumb);
			$upload->thumbMaxWidth =$size[0];
			$upload->thumbMaxHeight =$size[1];
		}
		$upload->maxSize= ($maxSize ==0) ? C('UPLOAD_MAX_SIZE') : $maxSize;
		$upload->allowExts  = explode(',',$ext);
		$upload->savePath =  $path;
		// 记录上传成功ID
		$uploadId =  array();
		$savename = array();
		//执行上传操作
		if(!$upload->upload()) {
			if($this->isAjax() && isset($_POST['_uploadFileResult'])) {
				$uploadSuccess =  false;
				$ajaxMsg  =  $upload->getErrorMsg();
			}else {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}
		}else {
			 //取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			if ($turnpost){
				foreach ($uploadList as $key => $value) {
					if ($value['array']){
						$_POST[$value['title']][]=$value['savepath'].$value['savename'];
					}else{
						$_POST[$value['title']]=$value['savepath'].$value['savename'];;
					}   
				}
			} else{
				return $uploadList;
			}
		}
	}

    /**
     +----------------------------------------------------------
     * 默认上传操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	public function upload() {
		if(!empty($_FILES)) {//如果有文件上传
			// 上传附件并保存信息到数据库
			$this->_upload(MODULE_NAME);
		}
	}

    function saveTag($tags,$id,$module=MODULE_NAME)
    {
        if(!empty($tags) && !empty($id)) {
            $Tag = M("Tag");
            $Tagged   = M("Tagged");
            // 记录已经存在的标签
            $exists_tags  = $Tagged->where("module='{$module}' and record_id={$id}")->getField("id,tag_id");
            $Tagged->where("module='{$module}' and record_id={$id}")->delete();
            $tags = explode(',',$tags);
            foreach($tags as $key=>$val) {
                $val  = trim($val);
                if(!empty($val)) {
                    $tag =  $Tag->where("module='{$module}' and name='{$val}'")->find();
                    if($tag) {
                        // 标签已经存在
                        if(!in_array($tag['id'],$exists_tags)) {
							$Tag->where('id='.$tag['id'])->setInc('count');
                        }
                    }else {
                        // 不存在则添加
						$tag = array();
                        $tag['name'] =  $val;
                        $tag['count']  =  1;
                        $tag['module']   =  $module;
                        $result  = $Tag->add($tag);
                        $tag['id']   =  $result;
                    }
                    // 记录tag关联信息
                    $t = array();
                    $t['user_id'] =$_SESSION[C('USER_AUTH_KEY')];
                    $t['module']   = $module;
                    $t['record_id'] =  $id;
                    $t['create_time']  = time();
                    $t['tag_id']  = $tag['id'];
                    $Tagged->add($t);
                }
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 生成树型列表XML文件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	public function tree() {
		$Model	=	M($this->getActionName());
		$title		=	$_REQUEST['title']?		$_REQUEST['title']		:'选择';
		$caption	=	$_REQUEST['caption']?	$_REQUEST['caption']	:'name';
		$list   =  $Model->where('status=1')->order('sort')->findAll();
		$tree		=	toTree($list);
		header("Content-Type:text/xml; charset=utf-8");
		$xml	 =  '<?xml version="1.0" encoding="utf-8" ?>'."\n";
		$xml	.=  '<tree caption="'.$title.'" >'."\n";
		$xml  .= $this->_toTree($tree,$caption);
		$xml	.= '</tree>';
		exit($xml);
	}

    /**
     +----------------------------------------------------------
     * 把树型列表数据转换为XML节点
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	private function _toTree($list,$caption) {
		foreach ($list as $key=>$val){
			$tab	=	str_repeat("\t",$val['level']);
			if(isset($val['_child'])) {
				// 有子节点
				$xml	.= $tab.'<level'.$val['level'].' id="'.$val['id'].'" level="'.$val['level'].'" parentId="'.$val['pid'].'" caption="'.$val[$caption].'" >'."\n";
				$xml  .= $this->_toTree($val['_child'],$caption);
				$xml  .= $tab.'</level'.$val['level'].'>'."\n";
			}else{
				$xml	.= $tab.'<level'.$val['level'].' id="'.$val['id'].'" level="'.$val['level'].'" parentId="'.$val['pid'].'" caption="'.$val[$caption].'" />'."\n";
			}
		}
		return $xml;
	}

    public function saveSort()
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $model    = M($this->getActionName());
            $col    =   explode(',',$seqNoList);
            //启动事务
            $model->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $model->id	=	$val[0];
                $model->sort	=	$val[1];
                $result =   $model->save();
                if(false === $result) {
					break;
                }
                //echo $model->getlastsql();
            }
            //提交事务
            $model->commit();
            if(false !== $result) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            }else {
                $this->error($model->getError());
            }
        }
    }

    // 查看某个模块的标签相关的记录
    public function tag()
    {
        $Tag = M("Tag");
        $name=trim($_GET['tag']);
        $Stat  = $Tag->where('module="'.$this->getActionName().'" and name="'.$name.'"')->field("id,count")->find();
        $tagId  =  $Stat['id'];
        $count   =  $Stat['count'];
        $Model = M($this->getActionName());
        $Tagged = M("Tagged");
        $recordIds  =  $Tagged->where("module='".$this->getActionName()."' and tag_id=".$tagId)->getField('id,record_id');
        if($recordIds) {
            $map['id']   = array('IN',$recordIds);
            $this->_list($Model,$map);
            $this->display('index');
        }else{
            $this->error('标签没有对应的文章！');
        }
    }
	function push(){
        //id/34/field/status/model//value/
		$id=$_GET['id'];
        $field=empty($_GET['field']) ? "status":$_GET['field'];
        $value=empty($_GET['value']) ? array('exp','-('.$field.'-1)'):$_GET['value'];
        $model=empty($_GET['model']) ? $this->getActionName() :$_GET['model'];
		$model = M($model);
		$condition = array('id'=>array('in',$id));
		if($model->data(array($field=>$value))->where($condition)->save()){
            $this->assign("jumpUrl",$_SERVER['HTTP_REFERER']);
			$this->success('操作成功！');
		}else {
			$this->error('操作失败！'.$model->getlastsql());
		}
	}

    function imageUp(){
        /**
        * Created by JetBrains PhpStorm.
        * User: taoqili
        * Date: 12-7-18
        * Time: 上午10:42
        */
        header("Content-Type: text/html; charset=utf-8");
        error_reporting(E_ERROR | E_WARNING);
        import("ORG.Net.Uploader");
        //上传图片框中的描述表单名称，
        $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
        $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);

        //上传配置
        $config = array(
            "savePath" => ($path == "1" ? "./Public/Uploads/" : "./Public/Uploads/"),
            "maxSize" => 1000, //单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")
        );

        //生成上传实例对象并完成上传
        $up = new Uploader("upfile", $config);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "originalName" => "",   //原始文件名
         *     "name" => "",           //新文件名
         *     "url" => "",            //返回的地址
         *     "size" => "",           //文件大小
         *     "type" => "" ,          //文件类型
         *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
         * )
         */
        $info = $up->getFileInfo();

        /**
         * 向浏览器返回数据json数据
         * {
         *   'url'      :'a.jpg',   //保存后的文件路径
         *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
         *   'original' :'b.jpg',   //原始文件名
         *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
         * }
         */
        echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";
    }
    function imageManager(){
        /**
        * Created by JetBrains PhpStorm.
        * User: taoqili
        * Date: 12-1-16
        * Time: 上午11:44
        * To change this template use File | Settings | File Templates.
        */
        header("Content-Type: text/html; charset=utf-8");
        error_reporting( E_ERROR | E_WARNING );

        //需要遍历的目录列表，最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
        $paths = array('./Public/Uploads','./Public/Uploads');

        $action = htmlspecialchars( $_POST[ "action" ] );
        if ( $action == "get" ) {
            $files = array();
            foreach ( $paths as $path){
                $tmp = $this->getfiles( $path );
                if($tmp){
                    $files = array_merge($files,$tmp);
                }
            }
            if ( !count($files) ) return;
            rsort($files,SORT_STRING);
            $str = "";
            foreach ( $files as $file ) {
                $str .= $file . "ue_separate_ue";
            }
            echo str_replace('Public/ueditor/dialogs/image/','',$str);
        }
    }
    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    function getfiles( $path , &$files = array() )
    {
        if ( !is_dir( $path ) ) return null;
        $handle = opendir( $path );
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                $path2 = $path . '/' . $file;
                if ( is_dir( $path2 ) ) {
                    $this->getfiles( $path2 , $files );
                } else {
                    if ( preg_match( "/\.(gif|jpeg|jpg|png|bmp)$/i" , $file ) ) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }
    function getRemoteImage(){
        /**
        * Created by JetBrains PhpStorm.
        * User: taoqili
        * Date: 11-12-28
        * Time: 上午9:54
        * To change this template use File | Settings | File Templates.
        */
        header("Content-Type: text/html; charset=utf-8");
        error_reporting(E_ERROR|E_WARNING);
        //远程抓取图片配置
        $config = array(
            "savePath" => "./Public/Uploads/" ,            //保存路径
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" ) , //文件允许格式
            "maxSize" => 3000                    //文件大小限制，单位KB
        );
        $uri = htmlspecialchars( $_POST[ 'upfile' ] );
        $uri = str_replace( "&amp;" , "&" , $uri );
        $this->getRemoteImage1( $uri,$config );
    }
    /**
     * 远程抓取
     * @param $uri
     * @param $config
     */
    function getRemoteImage1( $uri,$config)
    {
        //忽略抓取时间限制
        set_time_limit( 0 );
        //ue_separate_ue  ue用于传递数据分割符号
        $imgUrls = explode( "ue_separate_ue" , $uri );
        $tmpNames = array();
        foreach ( $imgUrls as $imgUrl ) {
            //http开头验证
            if(strpos($imgUrl,"http")!==0){
                array_push( $tmpNames , "error" );
                continue;
            }
            //获取请求头
            $heads = get_headers( $imgUrl );
            //死链检测
            if ( !( stristr( $heads[ 0 ] , "200" ) && stristr( $heads[ 0 ] , "OK" ) ) ) {
                array_push( $tmpNames , "error" );
                continue;
            }

            //格式验证(扩展名验证和Content-Type验证)
            $fileType = strtolower( strrchr( $imgUrl , '.' ) );
            if ( !in_array( $fileType , $config[ 'allowFiles' ] ) || stristr( $heads[ 'Content-Type' ] , "image" ) ) {
                array_push( $tmpNames , "error" );
                continue;
            }

            //打开输出缓冲区并获取远程图片
            ob_start();
            $context = stream_context_create(
                array (
                    'http' => array (
                        'follow_location' => false // don't follow redirects
                    )
                )
            );
            //请确保php.ini中的fopen wrappers已经激活
            readfile( $imgUrl,false,$context);
            $img = ob_get_contents();
            ob_end_clean();

            //大小验证
            $uriSize = strlen( $img ); //得到图片大小
            $allowSize = 1024 * $config[ 'maxSize' ];
            if ( $uriSize > $allowSize ) {
                array_push( $tmpNames , "error" );
                continue;
            }
            //创建保存位置
            $savePath = $config[ 'savePath' ];
            if ( !file_exists( $savePath ) ) {
                mkdir( "$savePath" , 0777 );
            }
            //写入文件
            $tmpName = $savePath . rand( 1 , 10000 ) . time() . strrchr( $imgUrl , '.' );
            try {
                $fp2 = @fopen( $tmpName , "a" );
                fwrite( $fp2 , $img );
                fclose( $fp2 );
                array_push( $tmpNames ,  $tmpName );
            } catch ( Exception $e ) {
                array_push( $tmpNames , "error" );
            }
        }
        /**
         * 返回数据格式
         * {
         *   'url'   : '新地址一ue_separate_ue新地址二ue_separate_ue新地址三',
         *   'srcUrl': '原始地址一ue_separate_ue原始地址二ue_separate_ue原始地址三'，
         *   'tip'   : '状态提示'
         * }
         */
        echo "{'url':'" . implode( "ue_separate_ue" , $tmpNames ) . "','tip':'远程图片抓取成功！','srcUrl':'" . $uri . "'}";
    }
    function getMovie(){
        error_reporting(E_ERROR|E_WARNING);
        $key =htmlspecialchars($_POST["searchKey"]);
        $type = htmlspecialchars($_POST["videoType"]);
        $html = file_get_contents('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw='.$key.'&pageNo=1&pageSize=20&channelId='.$type.'&inDays=7&media=v&sort=s');
        echo $html;
    }
    function getContent(){
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"/>
        <script src=\"../uparse.js\" type=\"text/javascript\"></script>
        <script>
          uParse('.content',{
              'highlightJsUrl':'../third-party/SyntaxHighlighter/shCore.js',
              'highlightCssUrl':'../third-party/SyntaxHighlighter/shCoreDefault.css'
          })
        </script>";
        //获取数据
        error_reporting(E_ERROR|E_WARNING);
        $content =  htmlspecialchars(stripslashes($_POST['myEditor']));
        $content1 =  htmlspecialchars(stripslashes($_POST['myEditor1']));

        //存入数据库或者其他操作

        //显示
        echo "第1个编辑器的值";
        echo  "<div class='content'>".htmlspecialchars_decode($content)."</div>";
        echo "<br/>第2个编辑器的值<br/>";
        echo "<textarea class='content' style='width:500px;height:300px;'>".htmlspecialchars_decode($content1)."</textarea><br/>";
    }
    function fileUp(){
        header("Content-Type: text/html; charset=utf-8");
        error_reporting( E_ERROR | E_WARNING );
        import("ORG.Net.Uploader");
        //上传配置
        $config = array(
            "savePath" => "./Public/Uploads/" , //保存路径
            "allowFiles" => array( ".rar" , ".doc" , ".docx" , ".zip" , ".pdf" , ".txt" , ".swf" , ".wmv" ) , //文件允许格式
            "maxSize" => 100000 //文件大小限制，单位KB
        );
        //生成上传实例对象并完成上传
        $up = new Uploader( "upfile" , $config );

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "originalName" => "",   //原始文件名
         *     "name" => "",           //新文件名
         *     "url" => "",            //返回的地址
         *     "size" => "",           //文件大小
         *     "type" => "" ,          //文件类型
         *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
         * )
         */
        $info = $up->getFileInfo();

        /**
         * 向浏览器返回数据json数据
         * {
         *   'url'      :'a.rar',        //保存后的文件路径
         *   'fileType' :'.rar',         //文件描述，对图片来说在前端会添加到title属性上
         *   'original' :'编辑器.jpg',   //原始文件名
         *   'state'    :'SUCCESS'       //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
         * }
         */
        echo '{"url":"' .$info[ "url" ] . '","fileType":"' . $info[ "type" ] . '","original":"' . $info[ "originalName" ] . '","state":"' . $info["state"] . '"}';
    }
    function scrawlUp(){
        header("Content-Type:text/html;charset=utf-8");
        error_reporting( E_ERROR | E_WARNING );
        import("ORG.Net.Uploader");
        //上传配置
        $config = array(
            "savePath" => "./Public/Uploads/" ,             //存储文件夹
            "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
        );
        //临时文件目录
        $tmpPath = "tmp/";

        //获取当前上传的类型
        $action = htmlspecialchars( $_GET[ "action" ] );
        if ( $action == "tmpImg" ) { // 背景上传
            //背景保存在临时目录中
            $config[ "savePath" ] = $tmpPath;
            $up = new Uploader( "upfile" , $config );
            $info = $up->getFileInfo();
            /**
             * 返回数据，调用父页面的ue_callback回调
             */
            echo "<script>parent.ue_callback('" . $info[ "url" ] . "','" . $info[ "state" ] . "')</script>";
        } else {
            //涂鸦上传，上传方式采用了base64编码模式，所以第三个参数设置为true
            $up = new Uploader( "content" , $config , true );
            //上传成功后删除临时目录
            if(file_exists($tmpPath)){
                $this->delDir($tmpPath);
            }
            $info = $up->getFileInfo();
            echo "{'url':'" . $info[ "url" ] . "',state:'" . $info[ "state" ] . "'}";
        }
    }
    /**
     * 删除整个目录
     * @param $dir
     * @return bool
     */
    function delDir( $dir )
    {
        //先删除目录下的所有文件：
        $dh = opendir( $dir );
        while ( $file = readdir( $dh ) ) {
            if ( $file != "." && $file != ".." ) {
                $fullpath = $dir . "/" . $file;
                if ( !is_dir( $fullpath ) ) {
                    unlink( $fullpath );
                } else {
                    delDir( $fullpath );
                }
            }
        }
        closedir( $dh );
        //删除当前文件夹：
        return rmdir( $dir );
    }
}
?>