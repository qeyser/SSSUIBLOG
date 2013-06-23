<?php
// +----------------------------------------------------------------------
// | Home项目基类
// +----------------------------------------------------------------------
// | Home所有Action都继承此类
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
import("ORG.Util.Cookie");
class BaseAction extends Action{
    function _initialize() {
        if (!$_SESSION['uid']){
            $loginfo=Cookie::get('loginfo');
            if(!empty($loginfo)){
                $loginfo=explode(',',$loginfo);
                $_SESSION["uid"]            =   $loginfo[0];
                $_SESSION['nickname']       =   $loginfo[1];
                $_SESSION['account']        =   $loginfo[2];
                if($loginfo[0]=='-1') {
                    $_SESSION['administrator']      =   true;
                }
            }else{
                $_SESSION['uid']=-2;
                $_SESSION['nickname']='游客';
                $_SESSION['account']='游客';
            }
        }/*else{
            if(!Cookie::is_set('loginfo')){
                Cookie::set('loginfo', $_SESSION['uid'].','.$_SESSION['nickname'].','.$_SESSION['account']);
            }
        }*/
        $this->navigation();
        $this->readconfig();
    }
    protected function readconfig(){
        // 读取系统配置参数
        if (!file_exists(DATA_PATH . '~config.php')) {
            $config = M("Config");
            $list = $config->getField('name,value');
            $savefile = DATA_PATH . '~config.php';
            // 所有配置参数统一为大写
            $content = "<?php\nreturn " . var_export(array_change_key_case($list, CASE_UPPER), true) . ";\n?>";
            if (!file_put_contents($savefile, $content)) {
                $this->error('配置缓存失败！');
            }
        }
        C(include_once DATA_PATH . '~config.php');
    }
    protected function navigation(){
        if (!file_exists(DATA_PATH . '~menu.php')) {
            $list = include DATA_PATH . '~menu.php';
            $Menu = M('Menu');
            $list = $Menu->field('name,link,title,target')->where('status=1 and level=1')->order('sort asc')->select();
            $savefile = DATA_PATH . '~menu.php';
            // 所有配置参数统一为大写           
            $content = "<?php\nreturn " . var_export(array_change_key_case($list, CASE_UPPER), true) . ";\n?>";
            if (!file_put_contents($savefile, $content)) {
                $this->error('配置缓存失败！');
            }
        } 
        $list = include DATA_PATH . '~menu.php';
        $this->assign('navigation', $list);
    }
    // 404 错误定向
    protected function _404($message = '', $jumpUrl = '', $waitSecond = 3) {
        $this->assign('message', '访问的页面不存在！');
        if (!empty($jumpUrl)) {
            $this->assign('jumpUrl', $jumpUrl);
            $this->assign('waitSecond', $waitSecond);
        }
        $this->display(C('TMPL_ACTION_ERROR'));
    }
    /**读取列表*/
    protected function _list($model = '', $map = '', $sortBy = '',$pagesize=0,$listword='list',$pageword='page',$varPage='p') {
        $model = empty($model) ? $this->getActionName() : $model;
        $model = M($model);
        //排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order =  $sortBy ;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            //$sort = 'desc';
        }
        if ($pagesize==0){
            $pagesize=C('PAGE_SIZE');
        }
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            //分页显示
            import("ORG.Util.Page");
            $p = new Page($count, $pagesize,'',$varPage);
            $page = $p->show();
            $currpage=empty($_GET[$varPage]) ? 1 : $_GET[$varPage];
            $list = $model->order($order . ' ' . $sort)->where($map)->page($currpage,$pagesize)->select();
            //echo "|".$model->getlastsql()."|";
            $this->assign($pageword, $page);
            $this->assign($listword, $list);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
    public function _empty($method) {
        $this->assign('message', '非法操作！');
        $this->display(C('TMPL_ACTION_ERROR'));
    }
    function insert($model=false,$data=null)
    {
        if (!$model){
            $model=$this->getActionName();
        }
        $model  =   D($model);
        if (!is_null($data)){
            $tmp=$_POST;
            $_POST=$data;
        }
        if(false === $model->create()) {
            $this->error($model->getError());
        }
        if (!is_null($data)){
            $_POST=$tmp;
        }
        $result = $model->add();
        if($result) {
            return $result;
        }else {            
            return false;
        }
    }
    function update($model=false,$data=null) {
        if (!$model){
            $model=$this->getActionName();
        }
        $model  =   D($model);
        if (!is_null($data)){
            $tmp=$_POST;
            $_POST=$data;
        }
        if(false === $model->create()) {
            $this->error($model->getError());
        }
        $n=$model->save();
        if (!is_null($data)){
            $_POST=$tmp;
        }
        if(false !== $n) {  
            return true;
        }else {
            return false;
        }
    }
    function postcomment(){
        $Comment=D('Comment');
        if(false === $Comment->create()) {
            $this->error($Comment->getError());
        }
        if($result = $Comment->add()) {
            $this->assign('jumpUrl',$_POST['jumpurl']);
            $this->success('评论成功');
        }else {
            $this->error('评论失败');
        }
    }
    function getcomment(){
        $pid=$_REQUEST['pid'];
        $currpage=$_REQUEST['p'];
        $module=$_REQUEST['module'];
        $model=M('Comment');
        $map="status=1 and module='".$module."' and pid=".$pid;
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            $list = $model->where($map)->order('id desc ')->page($currpage,20)->select();
            foreach ($list as $key => $vo) {
                $blogurl=U('Blog/').$vo['uid'];
                $cui=GUI($vo['uid']);
                $html.='<li><a href="'.$blogurl.'"><img src="'.$cui['avatar'].'" alt="'.$cui['nickname'].'" /></a><a href="'.$blogurl.'" class="green">'.$cui['nickname'].'</a><span class="colorL">'.toDate($vo['create_time'],'Y-m-d H:i:s').'</span><div class="colorD">'.parseemote($vo['content']).'</div></li>';
            }
            $this->success($html);
        }else{
            $this->error('木有内容');
        }
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
    function saveTag($tags, $id, $module = MODULE_NAME) {
        if (!empty($tags) && !empty($id)) {
            $Tag = M("Tag");
            $Tagged = M("Tagged");            // 记录已经存在的标签
            $exists_tags = $Tagged->where("module='{$module}' and record_id={$id}")->getField("id,tag_id");
            $Tagged->where("module='{$module}' and record_id={$id}")->delete();
            $tags = explode(' ', $tags);
            foreach ($tags as $key => $val) {
                $val = trim($val);
                if (!empty($val)) {
                    $tag = $Tag->where("module='{$module}' and name='{$val}'")->find();
                    if ($tag) {                        // 标签已经存在  
                        if (!in_array($tag['id'], $exists_tags)) {
                            $Tag->where('id=' . $tag['id'])->setInc('count');
                        }
                    } else {                        // 不存在则添加
                        $tag = array();
                        $tag['name'] = $val;
                        $tag['count'] = 1;
                        $tag['module'] = $module;
                        $result = $Tag->add($tag);
                        $tag['id'] = $result;
                    }                    // 记录tag关联信息      
                    $t = array();
                    $t['user_id'] = Session::get(C('USER_AUTH_KEY'));
                    $t['module'] = $module;
                    $t['record_id'] = $id;
                    $t['create_time'] = time();
                    $t['tag_id'] = $tag['id'];
                    $Tagged->add($t);
                }
            }
        }
    }
    public function delete($model=false)
    {
        if (!$model){
            $model=$this->getActionName();
        }
        $model  =   D($model);
        if(!empty($model)) {
            $pk =   $model->getPk();
            $id         = $_REQUEST[$pk];
            if(isset($id)) {
                $condition = array($pk=>array('in',explode(',',$id)));
                if(false !== $model->where($condition)->delete()){
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    }
    function checkuser(){
        if (!$_SESSION['uid'] || $_SESSION['uid']==-2){
            $this->redirect('Public/login');
        }
    }
	function setseoinfo($title='',$keyword='',$description='',$addr=array()){
		$app 				=	empty($addr[0]) ? strtolower(APP_NAME) : strtolower($addr[0]);
		$group 				=	empty($addr[1]) ? strtolower('Home') : strtolower($addr[1]);
		$module 			=	empty($addr[2]) ? strtolower(MODULE_NAME) : strtolower($addr[2]);
		$action 			=	empty($addr[3]) ? strtolower(ACTION_NAME) : strtolower($addr[3]);
		$keyword 			=	require('keyword.php');
		$seoinfo	=	$keyword[$app][$group][$module][$action];
		$seoinfo['title']=str_replace(array('{页面标题}','{全局标题}'), array($title,C('WEB_TITLE')), $seoinfo['title']);
		$seoinfo['keyword']=str_replace(array('{页面关键字}','{全局关键字}'), array($keyword,C('KEYWORDS')), $seoinfo['keyword']);
		$seoinfo['description']=str_replace(array('{页面描述}','{全局描述}'), array($description,C('DESCRIPTION')), $seoinfo['description']);
		$this->assign('seoinfo', '<title>'.$seoinfo['title'].'</title>		<meta name="keywords" content="'.$seoinfo['keyword'].'" />		<meta name="description" content="'.$seoinfo['description'].'" />');
		//return $content;
	}
}
?>