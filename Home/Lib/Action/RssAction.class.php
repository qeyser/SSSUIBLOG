<?php
// +----------------------------------------------------------------------
// | RSS订阅
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

class RssAction extends BaseAction {
    public function index(){
        //include LIB_PATH."ORG/Feedcreator.class.php";
        import("ORG.Util.Feedcreator");
        //define channel
        $feed = new UniversalFeedCreator();
        $feed->useCached();
        $feed->title=C('WEB_NAME');
        $feed->description=C('DESCRIPTION');
        $feed->link=C('WEB_URL');
        $feed->syndicationURL="http://sssui.com/rss.html";
        $cid=$this->_get('cid');
        $cid= empty($cid) ? '0' : $cid;
        $cate=($cid==0) ? C('WEB_NAME') : GTC($cid,'Cate');
        $feed->category= $cate;
        $feed->copyright = C('WEB_NAME')." (c) ".date('Y');

        //Valid parameters are RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
        // MBOX, OPML, ATOM, ATOM1.0, ATOM0.3, HTML, JS

        $feed=$this->addItem($feed,$cid);

        if ($_GET['type'] == 'atom') {
            $feed->outputFeed("ATOM1.0"); 
            //$feed->saveFeed("ATOM1.0", "news/feed.xml"); 
        } else if ($_GET['type'] == 'atom0'){
            $feed->outputFeed("ATOM0.3"); 
        } else if ($_GET['type'] == 'rss'){
            $feed->outputFeed("RSS"); 
        }  else  {
            $feed->outputFeed("RSS2.0"); 
        }
    }
    protected function addItem($feed,$cid){
        $New=M('New');
        $where='status=1';
        $where= $cid ? $where.' and cid='.$cid : $where;
        $list=$New->where($where)->order('update_time desc,create_time desc')->limit(0,10)->select();
        //添加每条文章
        foreach ($list as $key => $v) {
            $item = new FeedItem();
            $item->title = $v['title'];
            $item->link = "http://sssui.com/".$v['id'].".html";
            $item->guid = "urn:feeds-archive-org:validator:".$v['id'];
            $item->description = getContent($v['content']);
            $item->source = "http://sssui.com/";
            $item->author = C('WEB_NAME');
            $item->authorEmail = C('MAIL_EMAIL');
            $item->authorURL = "http://sssui.com/Singlepage_1.html";
            $item->descriptionHtmlSyndicated = true;
            $item->category=GTC($v['cid'],'Cate');
            //可选附件支持
            /*$item->enclosure = new EnclosureItem();
            $item->enclosure->url='';
            $item->enclosure->length="65905";
            $item->enclosure->type='image/jpeg';*/
            $feed->addItem($item);
        }
        return $feed;
    }
}

?>