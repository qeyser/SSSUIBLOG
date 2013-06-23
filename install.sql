/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50128
Source Host           : localhost:3306
Source Database       : Blog

Target Server Type    : MYSQL
Target Server Version : 50128
File Encoding         : 65001

Date: 2013-06-16 14:39:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `z_access`
-- ----------------------------
DROP TABLE IF EXISTS `z_access`;
CREATE TABLE `z_access` (
  `role_id` smallint(6) NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_access
-- ----------------------------
INSERT INTO `z_access` VALUES ('38', '1', '1', '0', '', '1');
INSERT INTO `z_access` VALUES ('38', '2', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '3', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '5', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '6', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '7', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '26', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '30', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '40', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '55', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '57', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '141', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '73', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '132', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '136', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '137', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '139', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '166', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '180', '2', '1', '', '1');
INSERT INTO `z_access` VALUES ('38', '31', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '38', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '39', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '41', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '42', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '43', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '44', '3', '30', '', '1');
INSERT INTO `z_access` VALUES ('38', '49', '3', '30', '', '1');

-- ----------------------------
-- Table structure for `z_ad`
-- ----------------------------
DROP TABLE IF EXISTS `z_ad`;
CREATE TABLE `z_ad` (
  `id` mediumint(15) NOT NULL AUTO_INCREMENT,
  `cid` int(15) DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `format` varchar(10) DEFAULT NULL,
  `content` text,
  `sort` int(15) NOT NULL DEFAULT '0',
  `create_time` int(15) NOT NULL DEFAULT '0',
  `update_time` int(15) DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_ad
-- ----------------------------

-- ----------------------------
-- Table structure for `z_cate`
-- ----------------------------
DROP TABLE IF EXISTS `z_cate`;
CREATE TABLE `z_cate` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `pid` int(15) NOT NULL DEFAULT '0',
  `uid` int(15) DEFAULT '0',
  `img` varchar(255) DEFAULT './Public/nopic.jpg',
  `title` varchar(50) NOT NULL,
  `level` tinyint(3) DEFAULT '1',
  `module` varchar(100) NOT NULL,
  `create_time` int(15) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8 COMMENT='广告分类';

-- ----------------------------
-- Table structure for `z_comment`
-- ----------------------------
DROP TABLE IF EXISTS `z_comment`;
CREATE TABLE `z_comment` (
  `id` mediumint(30) NOT NULL AUTO_INCREMENT,
  `pid` int(20) DEFAULT '0',
  `uid` int(15) NOT NULL DEFAULT '-2',
  `url` varchar(255) DEFAULT NULL,
  `nickname` varchar(32) NOT NULL DEFAULT '0',
  `content` text,
  `module` varchar(30) DEFAULT '',
  `create_time` varchar(500) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46213 DEFAULT CHARSET=utf8;



-- ----------------------------
-- Table structure for `z_config`
-- ----------------------------
DROP TABLE IF EXISTS `z_config`;
CREATE TABLE `z_config` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `name` varchar(30) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `extra` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_config
-- ----------------------------
INSERT INTO `z_config` VALUES ('1', '网站名称', '{web_name}', '1205899396', '', 'WEB_NAME', '0', '', '1', '1');
INSERT INTO `z_config` VALUES ('2', '网站标题', '{web_name}', '1205917701', '', 'WEB_TITLE', '0', '', '1', '2');
INSERT INTO `z_config` VALUES ('4', '验证码长度', '3,5', '1217511424', '数字表示固定的长度，用 4,6 表示一定范围的长度', 'VERIFY_CODE_LENGTH', '1', '', '1', '4');
INSERT INTO `z_config` VALUES ('5', '主题附件上传后缀', 'png,gif,jpg,jpeg,7z,mp3,flv,doc,rar,zip,txt,swf,pdf,ppt,chm,tar.gz,gz', '1217511611', '', 'TOPIC_UPLOAD_FILE_EXT', '3', '', '1', '5');
INSERT INTO `z_config` VALUES ('11', '文件上传的最大限制', '14485760', '1229499278', '使用字节定义', 'UPLOAD_MAX_SIZE', '1', '', '1', '11');
INSERT INTO `z_config` VALUES ('12', 'smtp服务器', 'smtp.admin.com', '1229499278', '', 'MAIL_SMTP', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('13', '管理员邮箱', '{admin_email}', '1229499278', '', 'WEB_Mail', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('14', '发送邮件的邮件地址', 'admin', '1229499278', '', 'MAIL_ACCOUNT', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('15', '发送邮件用户名', 'admin', '1229499278', '', 'MAIL_SENDER', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('16', '发送邮件的密码', '123456', '1229499278', '', 'MAIL_PW', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('18', '脏话过滤', '你妈,操,日', '1229499278', '', 'BadWords', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('20', '网站描述', '本站关注互联网,专注互联网,侧重互联网', '1229499278', '', 'description', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('21', '关键字', '{web_name},PHP,程序员,设计师,CSS,AJAX,C#,AS3,javascript,互联网,seo', '1229499278', '', 'Keywords', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('32', '网站备案号', '粤ICP备09098401号-6', '1229499278', '', 'WEB_BEIANHAO', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('31', '网站域名', '{web_url}', '1229499278', '', 'Web_URL', '0', '', '1', '0');
INSERT INTO `z_config` VALUES ('27', '分页大小', '20', '1229499278', '', 'page_size', '1', '', '1', '0');
INSERT INTO `z_config` VALUES ('35', '初始用户组', '36', '1229499278', '', 'INIT_ROLE', '0', '', '0', '0');
INSERT INTO `z_config` VALUES ('38', '编辑器设置', '\"source\",\"fontfamily\",\"fontsize\",\"bold\",\"italic\",\"underline\",\"forecolor\",\"backcolor\",\"justifyleft\",\"justifycenter\",\"justifyright\",\"insertunorderedlist\",\"insertorderedlist\",\"link\",\"insertimage\",\"insertvideo\",\"music\",\"highlightcode\",\"pagebreak\"', '1368863256', '', 'editorconfig', '0', '', '0', '0');

-- ----------------------------
-- Table structure for `z_feedback`
-- ----------------------------
DROP TABLE IF EXISTS `z_feedback`;
CREATE TABLE `z_feedback` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `uid` int(15) DEFAULT NULL,
  `pid` int(15) DEFAULT NULL,
  `nickname` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `tel` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `module` varchar(30) DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `reply` text CHARACTER SET utf8,
  `create_time` int(15) DEFAULT '0',
  `reply_time` int(15) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2288 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of z_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for `z_group`
-- ----------------------------
DROP TABLE IF EXISTS `z_group`;
CREATE TABLE `z_group` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `title` varchar(50) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_group
-- ----------------------------
INSERT INTO `z_group` VALUES ('3', 'System', '系统中心', '1222841285', '0', '1', '3', '1');
INSERT INTO `z_group` VALUES ('7', 'Content', '内容管理', '0', '0', '1', '5', '0');

-- ----------------------------
-- Table structure for `z_link`
-- ----------------------------
DROP TABLE IF EXISTS `z_link`;
CREATE TABLE `z_link` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort` mediumint(5) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='友情链接表';


-- ----------------------------
-- Table structure for `z_menu`
-- ----------------------------
DROP TABLE IF EXISTS `z_menu`;
CREATE TABLE `z_menu` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort` mediumint(5) unsigned DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `pid` mediumint(5) unsigned DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT NULL,
  `target` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

INSERT INTO `z_menu` VALUES ('32', '相册', 'Photo', 'Photo.html', '9', '1', '0', '1', '');
INSERT INTO `z_menu` VALUES ('33', '关于我', 'About', 'Singlepage_1.html', '11', '1', '0', '1', '');
INSERT INTO `z_menu` VALUES ('34', '给我留言', 'FeedBack', 'feedback.html', '13', '1', '0', '1', '');

-- ----------------------------
-- Table structure for `z_new`
-- ----------------------------
DROP TABLE IF EXISTS `z_new`;
CREATE TABLE `z_new` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `cid` int(15) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '0',
  `img` varchar(255) NOT NULL DEFAULT '/Public/nopic.jpg',
  `content` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `create_time` int(15) NOT NULL DEFAULT '0',
  `update_time` int(15) NOT NULL DEFAULT '0',
  `hit` int(11) NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT 'Admin',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=692 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `z_node`
-- ----------------------------
DROP TABLE IF EXISTS `z_node`;
CREATE TABLE `z_node` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned DEFAULT '0',
  `module` varchar(25) NOT NULL,
  `is_show` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_node
-- ----------------------------
INSERT INTO `z_node` VALUES ('1', 'Admin', '后台管理', '1', '', '0', '0', '1', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('2', 'Node', '节点管理', '1', '', '30', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('3', 'Group', '分组管理', '1', '', '29', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('5', 'Config', '配置管理', '1', '', '25', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('6', 'Role', '角色管理', '1', '', '31', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('7', 'User', '用户管理', '1', '', '32', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('26', 'Link', '友情链接', '1', '', '28', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('30', 'Public', '公共模块', '1', '', '24', '1', '2', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('31', 'add', '新增', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('32', 'insert', '写入', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('33', 'edit', '编辑', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('34', 'update', '更新', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('35', 'delete', '删除', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('36', 'forbid', '禁用', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('37', 'resume', '恢复', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('38', 'sort', '排序', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('39', 'index', '列表', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('40', 'Index', '默认模块', '1', '', '23', '1', '2', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('41', 'select', '选择', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('42', 'treeSelect', '树形选择', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('43', 'tree', '树形列表', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('44', 'upload', '上传', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('45', 'delAttach', '删除附件', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('47', 'recommend', '推荐', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('48', 'top', '置顶', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('49', 'read', '查看', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('50', 'main', '空白首页', '1', '', '0', '40', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('53', 'saveSort', '排序保存', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('55', 'Menu', '菜单管理', '1', '', '22', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('57', 'Article', '文章管理', '1', '', '2', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('66', 'Home', '前台默认模块', '1', '', '0', '0', '1', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('141', 'Adcate', '广告分类', '1', '', '0', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('73', 'Articlecate', '文章分类', '1', '', '1', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('132', 'Ad', '广告管理', '1', '', '0', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('136', 'Cache', '全站缓存', '1', '', '0', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('137', 'Feedback', '会员反馈', '1', '', '0', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('139', 'Singlepage', '单页内容', '1', '', '0', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('166', 'Seo', '优化管理', '1', '', '0', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('180', 'Theme', '主题管理', '1', '', '0', '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('181', 'Photo', '相册管理', '1', '', '0', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('182', 'Photocate', '相册分类', '1', '', '0', '30', '3', '0', '0', '', '0');
INSERT INTO `z_node` VALUES ('183', 'Photocate', '相册分类', '1', '', '0', '1', '2', '0', '7', 'Admin', '1');
INSERT INTO `z_node` VALUES ('184', 'Backup', '网站备份', '1', '', null, '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('186', 'Update', '检查更新', '1', '', null, '1', '2', '0', '3', 'Admin', '1');
INSERT INTO `z_node` VALUES ('187', 'Systemset', '系统设置', '1', '', null, '1', '2', '0', '3', 'Admin', '1');

-- ----------------------------
-- Table structure for `z_photo`
-- ----------------------------
DROP TABLE IF EXISTS `z_photo`;
CREATE TABLE `z_photo` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `uid` int(13) NOT NULL DEFAULT '0',
  `cid` int(15) NOT NULL DEFAULT '-1',
  `title` varchar(30) NOT NULL,
  `content` text,
  `thu_img` varchar(100) NOT NULL,
  `img` varchar(100) NOT NULL,
  `size` varchar(20) NOT NULL DEFAULT '0',
  `format` varchar(10) NOT NULL DEFAULT 'jpg',
  `hit` int(13) NOT NULL DEFAULT '0',
  `create_time` int(13) NOT NULL DEFAULT '0',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11742 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `z_role`
-- ----------------------------
DROP TABLE IF EXISTS `z_role`;
CREATE TABLE `z_role` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `ename` varchar(5) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `sort` int(15) DEFAULT '0' COMMENT '排序,级别越高数字越小',
  `needsource` mediumint(15) DEFAULT '0' COMMENT '升到本级的分数',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `ename` (`ename`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_role
-- ----------------------------
INSERT INTO `z_role` VALUES ('1', '一般管理员', '2', '1', '', '', '1208784792', '1215496350', '999998', '0');
INSERT INTO `z_role` VALUES ('2', '后台公共用户组', '0', '1', '', '', '1215496283', '1222872471', '999999', '0');
INSERT INTO `z_role` VALUES ('3', '网站编辑', '2', '1', '', '', '1229319925', '1215496350', '999997', '0');
INSERT INTO `z_role` VALUES ('5', '前台会员', '0', '1', '', '', '1215496283', '1215496350', '999999', '0');
INSERT INTO `z_role` VALUES ('36', '注册会员', '5', '1', '', '', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `z_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `z_role_user`;
CREATE TABLE `z_role_user` (
  `role_id` mediumint(9) DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_role_user
-- ----------------------------
INSERT INTO `z_role_user` VALUES ('0', '-1');
INSERT INTO `z_role_user` VALUES ('0', '1');

-- ----------------------------
-- Table structure for `z_singlepage`
-- ----------------------------
DROP TABLE IF EXISTS `z_singlepage`;
CREATE TABLE `z_singlepage` (
  `id` mediumint(15) NOT NULL AUTO_INCREMENT,
  `cid` mediumint(15) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL DEFAULT '0',
  `img` varchar(0) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `author` varchar(50) DEFAULT 'Admin',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_singlepage
-- ----------------------------
INSERT INTO `z_singlepage` VALUES ('1', '0', '关于我们', '', '<p> &nbsp; &nbsp;QQ:261001642</p><p> &nbsp; &nbsp;电话：13825286467<br /></p>', '', '3', '1355111323', '1369929203', 'Admin', '1');
-- ----------------------------
-- Table structure for `z_tag`
-- ----------------------------
DROP TABLE IF EXISTS `z_tag`;
CREATE TABLE `z_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `count` mediumint(6) unsigned NOT NULL,
  `module` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_tag
-- ----------------------------
INSERT INTO `z_tag` VALUES ('4', '发布会', '1', 'New');
INSERT INTO `z_tag` VALUES ('5', '开发者', '1', 'New');
INSERT INTO `z_tag` VALUES ('6', '开源', '1', 'New');
INSERT INTO `z_tag` VALUES ('7', '搜索引擎', '1', 'New');
INSERT INTO `z_tag` VALUES ('8', 'HTML5', '2', 'New');
INSERT INTO `z_tag` VALUES ('9', '草案', '2', 'New');
INSERT INTO `z_tag` VALUES ('10', 'PHP', '1', 'New');
INSERT INTO `z_tag` VALUES ('11', 'json_encode', '1', 'New');
INSERT INTO `z_tag` VALUES ('12', '编码', '1', 'New');
INSERT INTO `z_tag` VALUES ('13', '中文', '1', 'New');
INSERT INTO `z_tag` VALUES ('14', 'ping', '1', 'New');
INSERT INTO `z_tag` VALUES ('15', 'google', '1', 'New');
INSERT INTO `z_tag` VALUES ('16', '百度', '1', 'New');
INSERT INTO `z_tag` VALUES ('17', '趣文', '5', 'New');
INSERT INTO `z_tag` VALUES ('18', '编程语言', '5', 'New');
INSERT INTO `z_tag` VALUES ('19', '简史', '5', 'New');
INSERT INTO `z_tag` VALUES ('20', '扁平化', '1', 'New');
INSERT INTO `z_tag` VALUES ('21', '设计', '1', 'New');
INSERT INTO `z_tag` VALUES ('22', '原则', '1', 'New');

-- ----------------------------
-- Table structure for `z_tagged`
-- ----------------------------
DROP TABLE IF EXISTS `z_tagged`;
CREATE TABLE `z_tagged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `record_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `module` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;



-- ----------------------------
-- Table structure for `z_user`
-- ----------------------------
DROP TABLE IF EXISTS `z_user`;
CREATE TABLE `z_user` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `account` varchar(64) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `password` char(32) NOT NULL,
  `avatar` varchar(100) DEFAULT '/Public/Images/noavatar.jpg' COMMENT '头像',
  `email` varchar(50) DEFAULT NULL,
  `sex` int(11) DEFAULT '0',
  `birthday` int(11) DEFAULT NULL,
  `cardnum` varchar(20) DEFAULT NULL,
  `mobi` varchar(20) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `ap` varchar(50) DEFAULT NULL,
  `ac` varchar(50) DEFAULT NULL,
  `aa` varchar(50) DEFAULT NULL,
  `addr` varchar(50) DEFAULT NULL,
  `aboutme` text,
  `question` varchar(50) DEFAULT NULL,
  `answer` varchar(50) DEFAULT NULL,
  `last_login_time` int(11) unsigned DEFAULT '0',
  `last_login_ip` varchar(40) DEFAULT NULL,
  `login_count` mediumint(8) unsigned DEFAULT '0',
  `remark` varchar(255) DEFAULT '',
  `sinaauthkey` varchar(64) DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `qqauthkey` varchar(64) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM AUTO_INCREMENT=76469 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of z_user
-- ----------------------------
INSERT INTO `z_user` VALUES ('-1', '{admin_account}', '{admin_account}', '{admin_password}', '', '{admin_email}', '1', '0', '', ' ', ' ', '', '', '', '', ' ', '', '', '', '{time_now}', '127.0.0.1', '83', '', '', '{time_now}', '1', ' ', '{time_now}');
INSERT INTO `z_user` VALUES ('-2', '游客', ' 游客', '游客', '', '123@163.com', '1', '0', '', ' ', ' ', ' ', '', '', '', ' ', '', '', '', '{time_now}', '127.0.0.1', '0', '', '', '0', '1', ' ', '{time_now}');
