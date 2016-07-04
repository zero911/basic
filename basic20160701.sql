-- MySQL dump 10.13  Distrib 5.6.28, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: yascmf
-- ------------------------------------------------------
-- Server version	5.6.28-0ubuntu0.15.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `yacmf_menus`
--

DROP TABLE IF EXISTS `yacmf_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yacmf_menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `title` varchar(64) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned DEFAULT '0',
  `parent` varchar(50) NOT NULL DEFAULT '',
  `forefather_ids` varchar(100) DEFAULT NULL,
  `forefathers` varchar(10240) DEFAULT NULL,
  `functionality_id` int(10) unsigned DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `controller` varchar(40) DEFAULT NULL,
  `action` varchar(40) DEFAULT NULL,
  `realm` tinyint(3) unsigned DEFAULT NULL,
  `params` varchar(100) DEFAULT NULL,
  `new_window` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '菜单是否启用（0 正常 1关闭）',
  `sequence` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_id` (`parent_id`,`title`),
  KEY `idx_validate` (`disabled`,`controller`,`action`),
  KEY `idx_search` (`disabled`),
  KEY `functionality_id` (`functionality_id`),
  KEY `created` (`created_at`),
  KEY `modified` (`updated_at`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`functionality_id`) REFERENCES `yascmf_functionalities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='基础权限';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yacmf_menus`
--

LOCK TABLES `yacmf_menus` WRITE;
/*!40000 ALTER TABLE `yacmf_menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `yacmf_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yacmf_role_user`
--

DROP TABLE IF EXISTS `yacmf_role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yacmf_role_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `rights` varchar(10240) DEFAULT NULL,
  `role_name` varchar(40) DEFAULT NULL,
  `username` varchar(16) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`),
  KEY `admin_user_id` (`user_id`),
  KEY `admin_role_id` (`role_id`),
  CONSTRAINT `admin_role_admin_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `yascmf_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `admin_role_admin_user_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `yascmf_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色用户关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yacmf_role_user`
--

LOCK TABLES `yacmf_role_user` WRITE;
/*!40000 ALTER TABLE `yacmf_role_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `yacmf_role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_contents`
--

DROP TABLE IF EXISTS `yascmf_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `flag` varchar(15) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '推荐位',
  `title` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT '文章/单页/碎片/备忘标题',
  `thumb` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '文章/单页缩略图',
  `content` text COLLATE utf8_unicode_ci NOT NULL COMMENT '文章/单页/碎片/备忘正文',
  `slug` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '网址缩略名，文章、单页与碎片模型有缩略名，其它模型暂无',
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'article' COMMENT '内容类型：article文章模型 page单页模型 fragment碎片模型 memo备忘模型',
  `user_id` int(12) NOT NULL DEFAULT '0' COMMENT '文章编辑用户id',
  `is_top` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否置顶：1置顶，0不置顶',
  `owner_id` int(12) unsigned DEFAULT '0' COMMENT '归属用户id:一般备忘有归属用户id，0表示无任何归属',
  `outer_link` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '外链地址',
  `category_id` int(10) NOT NULL COMMENT '文章分类id',
  `deleted_at` datetime DEFAULT NULL COMMENT '被软删除时间',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_slug_unique` (`slug`),
  KEY `content_title_index` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='内容数据（文章/单页/碎片/备忘）表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_contents`
--

LOCK TABLES `yascmf_contents` WRITE;
/*!40000 ALTER TABLE `yascmf_contents` DISABLE KEYS */;
INSERT INTO `yascmf_contents` VALUES (2,'','关于','','&lt;blockquote&gt;\n&lt;p&gt;即使再渺小，也要不顾一切地成长&lt;/p&gt;\n&lt;/blockquote&gt;\n\n&lt;p&gt;笔者目前从事互联网相关编程工作，掌握多种前后端技能，正在努力成长为全栈型码农。&lt;/p&gt;\n\n&lt;p&gt;完整关于介绍，请查阅下面链接：&lt;/p&gt;\n\n&lt;p&gt;&lt;a href=&quot;http://douyasi.com/about.html&quot;&gt;http://douyasi.com/about.html&lt;/a&gt;&lt;/p&gt;\n\n&lt;p&gt;如需反馈问题、提出意见或建议，请通过以下方式联系作者。&lt;/p&gt;\n\n&lt;p&gt;Email：admin#yun-apps&amp;com 或 837454876#qq&amp;com (请将#换成@，&amp;换成.)&lt;/p&gt;\n\n&lt;p&gt;Github：https://github.com/douyasi&lt;/p&gt;\n\n&lt;p&gt;官方QQ群： 260655062&lt;/p&gt;\n\n&lt;p&gt;&nbsp;&lt;/p&gt;\n','about','page',1,0,0,NULL,0,NULL,'2015-02-09 15:48:58','2015-02-10 14:16:00'),(3,'','YASCMF','','&lt;h1&gt;何为内容管理框架？&lt;/h1&gt;\n\n&lt;p&gt;首先，我们要理解何为框架？ `框架` 就是被构建过的一套基础的环境，比如`Laravel` 框架，就实现 `PHP` `MVC` 三层架构，支持 `PHP` 最新特性，可以很方便地使用 `composer` 来进行包依赖管理等。那内容管理框架也是如此，我们可以拿 **在土地上建房子** 这件事情来作为比喻： **操作系统** ( `OS` ) 与 **特定语言环境** ( `PHP` ) 就是其中的 **土地** ，`Laravel` 框架就是其中的 **地基** 、 **水泥** 与 **砖块** ，而 **内容管理框架** 就是 **房屋设计图** 、 **门窗** 与 **支柱** ，建成的 **房子** 就是最后完成的 **业务系统** 。&lt;/p&gt;\n\n&lt;p&gt;项目开发的目的是为了完成某项特定的业务与需求，项目一旦开展起来，您就会发现，构建一套合理、美观的后台是您要优先考虑的事情，也是很重要的事情。后台页面布局、导航的设计与实现（这里可能更多地涉及到前端知识）很花费时间，在大型的研发公司里面，这些都是由专门的设计与前端团队来负责。而对于一些小公司与不懂前端的后端程序员来说，这些基础构建工作很棘手与繁琐。内容管理框架（ `CMF` ）应运而生，它给您提供一套基础的内容管理后台，以便让您在此基础上快捷（二次）开发，从而更专心地投入到后端编码中。&lt;/p&gt;\n\n&lt;h1&gt;芽丝内容管理框架简介&lt;/h1&gt;\n\n&lt;p&gt;芽丝内容管理框架( 英文简称 `YASCMF` / `yascmf`，后文称 `yascmf` )， 基于 `Laravel 4` 开发而成，它比较适合拿来做一些小众项目开发。目前框架实现了一个简单的内容管理系统（ `CMS` ），支持多种内容模型，文章、单页、分类、碎片与标签，您现在完全可以拿 `yascmf` 来完成一个简单的博客网站。待 `yascmf` 正式上线发布时，官方会给出一个演示博客网站。&lt;/p&gt;\n','yascmf','page',1,0,0,'',0,NULL,'2015-02-09 15:50:45','2015-03-11 23:48:38'),(12,'','test','test','&lt;p&gt;test&lt;/p&gt;',NULL,'article',0,0,0,'',1,NULL,'2016-04-14 17:46:20','2016-04-14 17:46:20'),(13,'Array','test123fadsfasdf','test123adfasdfas','<p>test123</p>','12','article',0,0,0,'',1,NULL,'2016-04-14 17:47:09','2016-04-14 20:35:43'),(6,'h,','芽丝博客视图文件目录结构','','&lt;blockquote&gt;\r\n&lt;p&gt;Laravel 5 版视图文件位于&nbsp;resources\\views 目录下，本套内容管理框架视图目录结构大致如下。&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;pre&gt;\r\n&lt;code class=&quot;language-markdown&quot;&gt;|_authority 登录视图文件夹\r\n    |_login.blade.php 登录页\r\n    \r\n|_back 后台视图文件夹\r\n    |_article 管理文章\r\n        |_index.blade.php\r\n        |_edit.blade.php\r\n        |_show.blade.php\r\n    |_business\r\n    |_...\r\n    \r\n|_emails    邮件模版文件夹\r\n    |_password.blade.php 重置密码邮件模版\r\n       \r\n|_front    前台视图文件夹\r\n    |_index.blade.php 前台首页\r\n    |_page\r\n        |_yascmf.blade.php 单页模版\r\n    |_...\r\n\r\n|_layout    布局layout文件夹\r\n    |_base.blade.php 供继承所用的基础layout\r\n    |_backend.blade.php 后台layout\r\n    |_frontend.blade.php 前台layout\r\n    |_layer.blade.php Layer弹窗layout\r\n\r\n|_scripts    javascript相关代码碎片文件夹（供嵌入使用）\r\n    |_endChosen.blade.php 使用chosen插件所使用到javascript代码\r\n    |_...\r\n\r\n|_widgets    通用外挂型组件文件夹\r\n    |_leftSidebar.blade.php 后台左侧导航通用外挂组件\r\n    |_topHeadNav.blade.php 后台顶部导航通用外挂组件\r\n    |_...&lt;/code&gt;&lt;/pre&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;`back` 文件夹&lt;/strong&gt;主要放置所有后台视图文件&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;`front` 文件夹&lt;/strong&gt;主要放置所有前台视图文件&lt;/p&gt;\r\n\r\n&lt;p&gt;&nbsp;&lt;/p&gt;','6','article',1,0,0,'',3,NULL,'2015-02-10 13:58:51','2016-04-09 16:02:09'),(7,'','芽丝内容管理框架简介','','&lt;blockquote&gt;\n&lt;p&gt;芽丝内容管理框架， 基于 `Laravel 5` 开发而成，它比较适合拿来做一些小众项目开发。目前框架实现了一个简单的内容管理系统（ `CMS` ），支持多种内容模型，文章、单页、分类、碎片与标签，您现在完全可以拿它 来完成一个简单的博客网站，&ldquo;芽丝博客&rdquo;就是它所驱动的博客示例网站。&lt;/p&gt;\n\n&lt;p&gt;芽丝内容管理框架提供了一套基础的内容管理后台，可以很方便地在此基础上进行快捷（二次）开发，从而让您更专心地投入到后端编码中。&lt;/p&gt;\n&lt;/blockquote&gt;\n\n&lt;p&gt;&nbsp;&lt;/p&gt;\n\n&lt;h2&gt;YASCMF主要特性&lt;/h2&gt;\n\n&lt;ol&gt;\n	&lt;li&gt;使用MIT开源协议，代码托管在 GitHub&lt;/li&gt;\n	&lt;li&gt;基于Laravel 5 开发，支持 PHP5 最新特性，注定灵活快捷，拥有丰富的组件&lt;/li&gt;\n	&lt;li&gt;遵循 RESTFUL 规范，后台数据以 AJAX 方式提交，能比较好地满足用户体验&lt;/li&gt;\n	&lt;li&gt;运用 Entrust&nbsp;包来实现用户角色与权限控制&lt;/li&gt;\n	&lt;li&gt;三级导航，根据当前路由地址自动化高亮，无需从后端传入索引值&lt;/li&gt;\n	&lt;li&gt;选用最新版的&nbsp;CKEditor&nbsp;网页编辑器，适合普通用户使用，未来会开发 markdown 编辑器以供高级用户使用&lt;/li&gt;\n	&lt;li&gt;代码注释完整，结合全中文化的文档、QQ群与社区支持，让你二次开发无忧&lt;/li&gt;\n	&lt;li&gt;官方自带博客演示，让你轻轻松松建立自己博客网站，后期会有更多示例网站&lt;/li&gt;\n&lt;/ol&gt;\n\n&lt;p&gt;更多...请入群交流，官方QQ交流群：&lt;a href=&quot;http://shang.qq.com/wpa/qunwpa?idkey=c43a551e4bc0ff5c5051ec8f6d901ab21c1e89e3001d6cf0b0b4a28c0fa4d4f8&quot;&gt;260655062&lt;/a&gt;&lt;/p&gt;\n\n&lt;p&gt;项目 GitHub 地址：&lt;a href=&quot;https://github.com/douyasi/yascmf&quot;&gt;https://github.com/douyasi/yascmf&lt;/a&gt;&nbsp;。&lt;/p&gt;\n\n&lt;p&gt;&nbsp;&lt;/p&gt;\n','7','article',1,0,0,'',2,NULL,'2015-02-10 14:02:22','2015-03-12 00:06:16'),(8,'','Laravel 5 中文文档','','&lt;p&gt;Laravel 5已经正式发布，不久前其中文化文档也翻译完毕。&lt;/p&gt;\n\n&lt;p&gt;传送地址： &lt;a href=&quot;http://laravel-china.org/docs/5.0&quot;&gt;http://laravel-china.org/docs/5.0&lt;/a&gt;&lt;/p&gt;\n','8','article',1,0,0,'',4,NULL,'2015-02-10 14:05:26','2015-03-11 23:37:10'),(9,'','Javascript获取当前URL相关参数','','&lt;pre&gt;\n&lt;code class=&quot;language-javascript&quot;&gt;	var search = window.location.search; //获取url中&quot;?&quot;符后的字串\n	var hash = window.location.hash; //获取url中&quot;#&quot;锚点符\n\n	var parser = document.createElement(&#039;a&#039;);\n	//var parser = {};\n	parser.href = &quot;http://example.com:3000/pathname/?search=test#hash&quot;;\n	parser.protocol; // =&gt; &quot;http:&quot;\n	parser.hostname; // =&gt; &quot;example.com&quot;\n	parser.port;     // =&gt; &quot;3000&quot;\n	parser.pathname; // =&gt; &quot;/pathname/&quot;\n	parser.search;   // =&gt; &quot;?search=test&quot;\n	parser.hash;     // =&gt; &quot;#hash&quot;\n	parser.host;     // =&gt; &quot;example.com:3000&quot;\n	/*\n	hash	 从井号 (#) 开始的 URL（锚）\n	host	 主机名和当前 URL 的端口号\n	hostname	 当前 URL 的主机名\n	href	 完整的 URL\n	pathname	 当前 URL 的路径部分\n	port	 当前 URL 的端口号\n	protocol	 当前 URL 的协议\n	search	 从问号 (?) 开始的 URL（查询部分）\n	*/\n	console.log(search);\n	console.log(hash);&lt;/code&gt;&lt;/pre&gt;\n\n&lt;p&gt;相关解析URL的JS类库：&lt;/p&gt;\n\n&lt;p&gt;jquery.url.js&nbsp;&lt;a href=&quot;https://github.com/allmarkedup/purl&quot;&gt;https://github.com/allmarkedup/purl&lt;/a&gt;&nbsp;（不再更新维护）&lt;/p&gt;\n\n&lt;p&gt;URI.js&nbsp;&lt;a href=&quot;https://github.com/medialize/URI.js&quot;&gt;https://github.com/medialize/URI.js&lt;/a&gt;&lt;/p&gt;\n','9','article',1,0,0,'',1,NULL,'2015-02-10 14:08:19','2015-03-11 23:03:06'),(10,'','芽丝博客上线','','&lt;p&gt;芽丝博客已上线，欢迎浏览！&lt;/p&gt;\n\n&lt;p&gt;当前本博客运行的版本为 Laravel 5 适配版本，Github 下载地址：&lt;a href=&quot;https://github.com/douyasi/yascmf&quot;&gt;https://github.com/douyasi/yascmf&lt;/a&gt;&nbsp;。欢迎下载安装，有任何问题可以加群或在 GitHub 发出 Issue 。&lt;/p&gt;\n','10','article',1,0,0,'',1,NULL,'2015-02-10 14:20:37','2015-03-11 23:40:35');
/*!40000 ALTER TABLE `yascmf_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_flags`
--

DROP TABLE IF EXISTS `yascmf_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_flags` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `attr` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT '属性名',
  `attr_full_name` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT '属性全称名',
  `display_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '展示名',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_flags`
--

LOCK TABLES `yascmf_flags` WRITE;
/*!40000 ALTER TABLE `yascmf_flags` DISABLE KEYS */;
INSERT INTO `yascmf_flags` VALUES (1,'l','link','链接','可用于友情链接'),(2,'f','flash','幻灯','可用于页面幻灯片模型'),(3,'s','scrolling','滚动','可用于页面滚动效果的文章'),(4,'h','hot','热门','可用于页面推荐性文章');
/*!40000 ALTER TABLE `yascmf_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_functionalities`
--

DROP TABLE IF EXISTS `yascmf_functionalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_functionalities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `title` varchar(64) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT '0',
  `parent` varchar(50) NOT NULL DEFAULT '',
  `forefather_ids` varchar(100) NOT NULL,
  `forefathers` varchar(10240) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `controller` varchar(40) NOT NULL DEFAULT '',
  `action` varchar(40) NOT NULL DEFAULT '',
  `button_type` tinyint(3) NOT NULL DEFAULT '1',
  `popup_id` int(10) unsigned DEFAULT NULL,
  `popup_title` varchar(64) DEFAULT NULL,
  `button_onclick` varchar(64) DEFAULT NULL,
  `confirm_msg_key` varchar(200) DEFAULT NULL,
  `refresh_cycle` smallint(5) unsigned DEFAULT NULL,
  `menu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否菜单项',
  `need_curd` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要CURD权限',
  `need_search` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要搜索表单',
  `search_config_id` int(10) unsigned DEFAULT NULL,
  `realm` tinyint(3) unsigned DEFAULT NULL COMMENT '领域:1为管理，2为用户，3为全部',
  `need_log` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '菜单是否启用（0 正常 1关闭）',
  `sequence` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_id` (`parent_id`,`title`),
  KEY `idx_validate` (`disabled`,`controller`,`action`),
  KEY `idx_search` (`disabled`,`menu`),
  KEY `sequence` (`sequence`),
  KEY `created` (`created_at`),
  KEY `modified` (`updated_at`),
  KEY `only_admin` (`realm`),
  KEY `popup_id` (`popup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='基础权限';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_functionalities`
--

LOCK TABLES `yascmf_functionalities` WRITE;
/*!40000 ALTER TABLE `yascmf_functionalities` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_functionalities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_functionality_params`
--

DROP TABLE IF EXISTS `yascmf_functionality_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_functionality_params` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `functionality_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'string' COMMENT '数据类型',
  `default_value` varchar(200) DEFAULT '',
  `limit_when_null` tinyint(1) NOT NULL DEFAULT '0' COMMENT '为空时是否设定',
  `sequence` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `functionality_id` (`functionality_id`),
  KEY `sequence` (`sequence`),
  CONSTRAINT `functionality_params_ibfk_1` FOREIGN KEY (`functionality_id`) REFERENCES `yascmf_functionalities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_functionality_params`
--

LOCK TABLES `yascmf_functionality_params` WRITE;
/*!40000 ALTER TABLE `yascmf_functionality_params` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_functionality_params` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_functionality_relations`
--

DROP TABLE IF EXISTS `yascmf_functionality_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_functionality_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `functionality_id` int(10) unsigned NOT NULL DEFAULT '0',
  `r_functionality_id` int(10) unsigned NOT NULL DEFAULT '0',
  `realm` tinyint(3) unsigned DEFAULT NULL,
  `position` tinyint(3) unsigned DEFAULT NULL,
  `button_onclick` varchar(64) DEFAULT NULL,
  `confirm_msg_key` varchar(64) DEFAULT NULL,
  `for_page` tinyint(1) NOT NULL DEFAULT '0',
  `for_page_batch` tinyint(1) NOT NULL DEFAULT '0',
  `for_item` tinyint(1) NOT NULL DEFAULT '0',
  `label` varchar(50) DEFAULT NULL,
  `precondition` varchar(200) NOT NULL DEFAULT '',
  `params` varchar(200) NOT NULL DEFAULT '',
  `new_window` tinyint(1) NOT NULL DEFAULT '0',
  `use_redirector` tinyint(1) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `functionality_id` (`functionality_id`),
  KEY `sub_functionality_id` (`r_functionality_id`),
  KEY `sequence` (`sequence`),
  KEY `created` (`created_at`),
  KEY `modified` (`updated_at`),
  KEY `for_item` (`for_item`),
  KEY `for_page` (`for_page`),
  KEY `position` (`position`),
  CONSTRAINT `functionality_relations_ibfk_1` FOREIGN KEY (`functionality_id`) REFERENCES `yascmf_functionalities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `functionality_relations_ibfk_2` FOREIGN KEY (`r_functionality_id`) REFERENCES `yascmf_functionalities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_functionality_relations`
--

LOCK TABLES `yascmf_functionality_relations` WRITE;
/*!40000 ALTER TABLE `yascmf_functionality_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_functionality_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_metas`
--

DROP TABLE IF EXISTS `yascmf_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_metas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'meta名',
  `thumb` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'meta缩略图',
  `slug` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'meta缩略名',
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'category' COMMENT 'meta类型: [category]-分类，[tag]-标签',
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'meta描述',
  `count` int(10) unsigned DEFAULT '0' COMMENT 'meta被使用的次数',
  `sort` int(6) unsigned DEFAULT '0' COMMENT 'meta排序，数字越大排序越靠前',
  PRIMARY KEY (`id`),
  KEY `name_index` (`name`),
  KEY `slug_index` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='META元数据（分类|标签） 表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_metas`
--

LOCK TABLES `yascmf_metas` WRITE;
/*!40000 ALTER TABLE `yascmf_metas` DISABLE KEYS */;
INSERT INTO `yascmf_metas` VALUES (1,'默认',NULL,'default','category','默认',0,0),(2,'软件',NULL,'soft','category','收录个人开发的软件或脚本',0,0),(3,'文档',NULL,'docs','category','这里收录自己开发过程中所撰写的文档',0,0),(4,'Laravel',NULL,'laravel','category','分享一些Laravel相关文章',0,0),(5,'Javascript',NULL,'javascript','category','前端Javascript相关知识',0,0),(6,'测试',NULL,NULL,'category','测试内容',0,0);
/*!40000 ALTER TABLE `yascmf_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_method`
--

DROP TABLE IF EXISTS `yascmf_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method_code` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '模块代码',
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '模块名称',
  `url` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_actived` int(2) NOT NULL DEFAULT '1',
  `pid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `method_code` (`method_code`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='后台模块表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_method`
--

LOCK TABLES `yascmf_method` WRITE;
/*!40000 ALTER TABLE `yascmf_method` DISABLE KEYS */;
INSERT INTO `yascmf_method` VALUES (2,'manage_contents','内容管理','','2016-03-31 11:46:32','2016-03-31 11:46:32',1,0),(3,'manage_users','用户管理','','2016-03-31 12:01:19','2016-03-31 12:01:19',1,0),(4,'manage_system','系统管理','','2016-03-31 12:01:41','2016-03-31 12:01:41',1,0),(5,'manage_contents_articles','内容管理/文章','article.index,article.create,article.edit,article.destroy','2016-03-31 12:10:06','2016-04-09 03:53:52',1,2),(6,'manage_contents_categorys','内容管理/分类','category.index,category.destroy,category.edit,category.create','2016-03-31 12:10:46','2016-04-09 04:10:37',1,2),(7,'manage_contents_pages','内容管理/单页','page.index,page.view,page.edit,page.create,page.destroy','2016-03-31 12:15:08','2016-04-09 04:11:50',1,2),(8,'manage_contents_fragment','内容管理/碎片','fragment.index,fragment.view,fragment.destroy,fragment.edit,fragment.create','2016-03-31 12:15:59','2016-04-09 04:12:33',1,2),(9,'manage_users_admin','用户管理/管理员','manager.index,manager.view,manager.destroy,manager.edit,manager.create','2016-03-31 12:17:51','2016-04-09 04:40:13',1,3),(10,'manage_users_roles','用户管理/角色','role.index,role.view,role.destroy,role.edit,role.create','2016-03-31 12:18:34','2016-04-09 04:42:39',1,3),(11,'manage_users_permissions','用户管理/权限','permission.index,permission.view,permission.destroy,permission.edit,permission.create','2016-03-31 12:18:59','2016-04-09 04:42:46',1,3),(12,'manage_users_mthods','系统管理/系统模块','method.index,method.view,method.destroy,method.edit,method.create,method.setPermission,method.viewPermission','2016-03-31 12:19:33','2016-04-09 05:00:19',1,4),(13,'manage_system_system_options','系统管理/系统配置','syscfg.index,syscfg.edit','2016-03-31 12:21:03','2016-04-09 04:44:21',1,4),(14,'manage_system_setting_types','系统管理/动态设置分组','setting_type.index,setting_type.destroy,setting_type.edit,setting_type.create','2016-03-31 12:21:49','2016-04-09 04:45:15',1,4),(15,'manage_system_settings','系统管理/动态设置','setting.index,setting.destroy,setting.edit,setting.create','2016-03-31 12:22:17','2016-04-09 04:45:54',1,4),(16,'manage_system_logs','系统管理/系统日志','log.index,log.view,log.exportExcel,log.importExcel','2016-03-31 12:26:09','2016-05-18 06:11:56',1,4);
/*!40000 ALTER TABLE `yascmf_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_password_resets`
--

DROP TABLE IF EXISTS `yascmf_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_password_resets` (
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `token` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '会话token',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_password_resets`
--

LOCK TABLES `yascmf_password_resets` WRITE;
/*!40000 ALTER TABLE `yascmf_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_relationships`
--

DROP TABLE IF EXISTS `yascmf_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_relationships` (
  `cid` int(10) unsigned NOT NULL COMMENT '内容数据id',
  `mid` int(10) unsigned NOT NULL COMMENT 'meta元数据id',
  PRIMARY KEY (`cid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='内容与元数据关系联系表[考虑查询复杂度，目前只存储文章单页跟标签的关系]';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_relationships`
--

LOCK TABLES `yascmf_relationships` WRITE;
/*!40000 ALTER TABLE `yascmf_relationships` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_role_user`
--

DROP TABLE IF EXISTS `yascmf_role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id_foreign` (`role_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_role_user`
--

LOCK TABLES `yascmf_role_user` WRITE;
/*!40000 ALTER TABLE `yascmf_role_user` DISABLE KEYS */;
INSERT INTO `yascmf_role_user` VALUES (1,2);
/*!40000 ALTER TABLE `yascmf_role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_roles`
--

DROP TABLE IF EXISTS `yascmf_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `name` varchar(40) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `rights` varchar(10240) DEFAULT NULL,
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `is_system` tinyint(1) NOT NULL DEFAULT '1' COMMENT '系统角色',
  `right_settable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可设置权限',
  `user_settable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可设置用户',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `sequence` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uni` (`name`),
  KEY `idx_search` (`id`,`disabled`),
  KEY `isdisabled` (`disabled`),
  KEY `created` (`created_at`),
  KEY `modified` (`updated_at`),
  KEY `sequence` (`sequence`),
  KEY `system_role` (`is_system`),
  KEY `right_settable` (`right_settable`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_roles`
--

LOCK TABLES `yascmf_roles` WRITE;
/*!40000 ALTER TABLE `yascmf_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_search_configs`
--

DROP TABLE IF EXISTS `yascmf_search_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_search_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `form_name` varchar(64) NOT NULL DEFAULT '',
  `row_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行尺寸',
  `realm` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='搜索表单配置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_search_configs`
--

LOCK TABLES `yascmf_search_configs` WRITE;
/*!40000 ALTER TABLE `yascmf_search_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_search_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_search_items`
--

DROP TABLE IF EXISTS `yascmf_search_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_search_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_config_id` int(10) unsigned DEFAULT '0',
  `model` varchar(40) NOT NULL DEFAULT '',
  `field` varchar(40) NOT NULL DEFAULT '',
  `label` varchar(40) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `default_value` varchar(250) DEFAULT NULL,
  `source` text NOT NULL COMMENT '数据源',
  `div` tinyint(1) NOT NULL DEFAULT '0',
  `empty` tinyint(1) NOT NULL DEFAULT '0',
  `empty_text` varchar(50) NOT NULL DEFAULT '',
  `match_rule` mediumtext COMMENT '匹配规则',
  `sequence` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model`),
  KEY `sequence` (`sequence`),
  KEY `search_config_id` (`search_config_id`),
  CONSTRAINT `search_items_ibfk_2` FOREIGN KEY (`search_config_id`) REFERENCES `yascmf_search_configs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='搜索表单控件配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_search_items`
--

LOCK TABLES `yascmf_search_items` WRITE;
/*!40000 ALTER TABLE `yascmf_search_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_search_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_setting_type`
--

DROP TABLE IF EXISTS `yascmf_setting_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_setting_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '设置类型项名',
  `value` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '设置类型项值',
  `sort` int(6) unsigned DEFAULT '0' COMMENT '设置类型排序，数字越大排序越靠前',
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_type_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='动态设置类型表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_setting_type`
--

LOCK TABLES `yascmf_setting_type` WRITE;
/*!40000 ALTER TABLE `yascmf_setting_type` DISABLE KEYS */;
INSERT INTO `yascmf_setting_type` VALUES (1,'default','默认',0),(2,'system_operation','系统操作类型',0),(3,'content_type','内容类型',0),(4,'role_type','角色类型',0),(5,'testtest','testtest',0);
/*!40000 ALTER TABLE `yascmf_setting_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_settings`
--

DROP TABLE IF EXISTS `yascmf_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '设置项名',
  `value` text COLLATE utf8_unicode_ci COMMENT '设置项值',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 0未启用 1启用 其它数字表示异常',
  `type_id` int(12) DEFAULT '0' COMMENT '设置所属类型id 0表示无任何归属类型',
  `sort` int(6) unsigned DEFAULT '0' COMMENT '设置排序，数字越大排序越靠前',
  PRIMARY KEY (`id`),
  KEY `setting_name_index` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='动态设置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_settings`
--

LOCK TABLES `yascmf_settings` WRITE;
/*!40000 ALTER TABLE `yascmf_settings` DISABLE KEYS */;
INSERT INTO `yascmf_settings` VALUES (1,'default_setting','默认设置',1,1,999),(2,'system','系统',1,2,0),(3,'manager','管理员',1,2,0),(4,'content','内容',1,2,0),(5,'upload','上传',1,2,0),(6,'article','文章',1,3,0),(7,'page','单页',1,3,0),(8,'fragment','碎片',1,3,0),(9,'memo','备忘',1,3,0),(10,'Founder','创始人',1,4,0),(11,'Admin','超级管理员',1,4,0),(12,'Editor','编辑',1,4,0),(13,'Demo','演示',1,4,0),(14,'fasdfasdf','asdfasdfa',1,2,0);
/*!40000 ALTER TABLE `yascmf_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_sys_model_columns`
--

DROP TABLE IF EXISTS `yascmf_sys_model_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_sys_model_columns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `sys_model_id` int(10) unsigned DEFAULT '0',
  `sys_model_name` varchar(64) DEFAULT NULL,
  `db` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `ordinal_position` bigint(21) unsigned NOT NULL DEFAULT '0' COMMENT '原始顺序',
  `column_default` longtext COMMENT '默认值',
  `is_nullable` varchar(3) NOT NULL DEFAULT '',
  `data_type` varchar(64) NOT NULL DEFAULT '',
  `max_length` bigint(21) unsigned DEFAULT '0',
  `charset_name` varchar(32) DEFAULT '',
  `column_type` longtext NOT NULL COMMENT '字段类型',
  `column_comment` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `TABLE_SCHEMA` (`db`,`table_name`,`name`),
  KEY `model_id` (`sys_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_sys_model_columns`
--

LOCK TABLES `yascmf_sys_model_columns` WRITE;
/*!40000 ALTER TABLE `yascmf_sys_model_columns` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_sys_model_columns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_sys_models`
--

DROP TABLE IF EXISTS `yascmf_sys_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_sys_models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(1024) DEFAULT NULL,
  `file_exists` tinyint(1) NOT NULL DEFAULT '0' COMMENT '定义是否存在',
  `table_exists` tinyint(1) NOT NULL DEFAULT '0' COMMENT '表是否存在',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name` (`table_name`),
  UNIQUE KEY `name` (`name`),
  KEY `file_exists` (`file_exists`),
  KEY `table_exists` (`table_exists`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_sys_models`
--

LOCK TABLES `yascmf_sys_models` WRITE;
/*!40000 ALTER TABLE `yascmf_sys_models` DISABLE KEYS */;
/*!40000 ALTER TABLE `yascmf_sys_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_system_log`
--

DROP TABLE IF EXISTS `yascmf_system_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_system_log` (
  `id` int(12) NOT NULL AUTO_INCREMENT COMMENT '系统日志id',
  `user_id` int(12) DEFAULT '0' COMMENT '用户id（为0表示系统级操作，其它一般为管理型用户操作）',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'system' COMMENT '操作类型',
  `url` varchar(200) COLLATE utf8_unicode_ci DEFAULT '-' COMMENT '操作发起的url',
  `content` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作内容',
  `operator_ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者ip',
  `deleted_at` datetime DEFAULT NULL COMMENT '被软删除时间',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='系统日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_system_log`
--

LOCK TABLES `yascmf_system_log` WRITE;
/*!40000 ALTER TABLE `yascmf_system_log` DISABLE KEYS */;
INSERT INTO `yascmf_system_log` VALUES (1,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-21 17:25:04','2016-03-21 17:25:04'),(2,1,'manager','http://g.yascmf.cn/auth/logout','管理员：Admin[admin@example.com] 登出系统。','127.0.0.1',NULL,'2016-03-21 19:09:32','2016-03-21 19:09:32'),(3,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-21 19:20:01','2016-03-21 19:20:01'),(4,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-22 14:40:44','2016-03-22 14:40:44'),(5,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-22 19:04:09','2016-03-22 19:04:09'),(6,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-23 11:16:06','2016-03-23 11:16:06'),(7,1,'manager','http://g.yascmf.cn/auth/logout','管理员：Admin[admin@example.com] 登出系统。','127.0.0.1',NULL,'2016-03-23 12:56:16','2016-03-23 12:56:16'),(8,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-23 15:49:59','2016-03-23 15:49:59'),(9,1,'manager','http://g.yascmf.cn/auth/logout','管理员：Admin[admin@example.com] 登出系统。','127.0.0.1',NULL,'2016-03-23 15:50:06','2016-03-23 15:50:06'),(10,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-23 17:01:52','2016-03-23 17:01:52'),(11,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-26 15:02:22','2016-03-26 15:02:22'),(12,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-28 13:00:04','2016-03-28 13:00:04'),(13,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-28 13:44:59','2016-03-28 13:44:59'),(14,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-28 19:03:43','2016-03-28 19:03:43'),(15,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-29 12:43:21','2016-03-29 12:43:21'),(16,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-30 12:21:58','2016-03-30 12:21:58'),(17,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-30 16:21:59','2016-03-30 16:21:59'),(18,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-31 12:41:45','2016-03-31 12:41:45'),(19,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-03-31 17:26:57','2016-03-31 17:26:57'),(20,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-04-09 13:54:23','2016-04-09 13:54:23'),(21,1,'manager','http://g.z.mcms.cn/auth/logout','系统管理员_admin_AuthorityController@logout_登出系统','127.0.0.1',NULL,'2016-04-09 15:52:58','2016-04-09 15:52:58'),(22,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-09 16:01:37','2016-04-09 16:01:37'),(23,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-04-11 17:16:09','2016-04-11 17:16:09'),(24,1,'manager','http://g.yascmf.cn/auth/logout','管理员：Admin[admin@example.com] 登出系统。','127.0.0.1',NULL,'2016-04-11 17:36:26','2016-04-11 17:36:26'),(25,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-11 17:45:43','2016-04-11 17:45:43'),(26,1,'manager','http://g.z.mcms.cn/method/16/edit','系统管理员_admin_MethodController@edit_编辑模块:16','127.0.0.1',NULL,'2016-04-11 17:48:43','2016-04-11 17:48:43'),(27,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-04-11 18:01:15','2016-04-11 18:01:15'),(28,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-11 20:48:57','2016-04-11 20:48:57'),(29,1,'manager','http://g.yascmf.cn/auth/login','管理员：Admin[admin@example.com] 登录系统。','127.0.0.1',NULL,'2016-04-11 21:01:59','2016-04-11 21:01:59'),(30,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-14 17:18:28','2016-04-14 17:18:28'),(31,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-14 20:05:19','2016-04-14 20:05:19'),(32,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-04-15 14:17:47','2016-04-15 14:17:47'),(33,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-05-13 20:12:07','2016-05-13 20:12:07'),(34,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-05-14 13:44:59','2016-05-14 13:44:59'),(35,1,'manager','http://g.z.mcms.cn/auth/login','系统管理员_admin_AuthorityController@login_登陆系统','127.0.0.1',NULL,'2016-05-18 13:13:21','2016-05-18 13:13:21'),(36,1,'manager','http://g.z.mcms.cn/method/16/edit','系统管理员_admin_MethodController@edit_编辑Method:16','127.0.0.1',NULL,'2016-05-18 14:11:56','2016-05-18 14:11:56');
/*!40000 ALTER TABLE `yascmf_system_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_system_options`
--

DROP TABLE IF EXISTS `yascmf_system_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_system_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '配置选项名',
  `value` text COLLATE utf8_unicode_ci COMMENT '配置选项值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_option_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='系统配置选项表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_system_options`
--

LOCK TABLES `yascmf_system_options` WRITE;
/*!40000 ALTER TABLE `yascmf_system_options` DISABLE KEYS */;
INSERT INTO `yascmf_system_options` VALUES (1,'website_keywords','芽丝博客,芽丝,CMF,内容管理框架'),(2,'company_address',''),(3,'website_title','芽丝博客'),(4,'company_telephone','800-168-8888'),(5,'company_full_name','芽丝内容管理框架'),(6,'website_icp',''),(7,'system_version','yascmf_alpha_1.0'),(8,'page_size','10'),(9,'system_logo','http://cmf.yas.so/assets/img/yas_logo.png'),(10,'picture_watermark','http://cmf.yas.so/assets/img/yas_logo.png'),(11,'company_short_name','芽丝博客'),(12,'system_author','豆芽丝'),(13,'system_author_website','http://douyasi.com'),(14,'is_watermark','0');
/*!40000 ALTER TABLE `yascmf_system_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yascmf_users`
--

DROP TABLE IF EXISTS `yascmf_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yascmf_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(200) DEFAULT '',
  `password` varchar(60) NOT NULL,
  `language` varchar(10) DEFAULT NULL,
  `menu_link` tinyint(1) NOT NULL DEFAULT '1' COMMENT '记录级链接',
  `menu_context` tinyint(1) NOT NULL DEFAULT '1' COMMENT '记录级右键菜单',
  `rights` varchar(10240) DEFAULT NULL,
  `actived` tinyint(1) NOT NULL DEFAULT '1' COMMENT '激活',
  `remember_token` varchar(200) DEFAULT NULL,
  `signin_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_2` (`username`),
  UNIQUE KEY `username` (`username`,`password`),
  KEY `created` (`created_at`),
  KEY `modified` (`updated_at`),
  KEY `email` (`email`),
  KEY `signin_at` (`signin_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yascmf_users`
--

LOCK TABLES `yascmf_users` WRITE;
/*!40000 ALTER TABLE `yascmf_users` DISABLE KEYS */;
INSERT INTO `yascmf_users` VALUES (1,'system','system','system@qq.com','$2y$10$J7LHukU1OvdKS0HjHyP67OckaKXwci9vV6iqOCpN65x8X7MDgMNlu','',1,1,NULL,1,NULL,'0000-00-00 00:00:00',NULL,NULL);
/*!40000 ALTER TABLE `yascmf_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-04 21:22:33
