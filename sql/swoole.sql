-- phpMyAdmin SQL Dump
-- version 4.0.10
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2015-09-16 11:02:17
-- 服务器版本: 5.5.44-0ubuntu0.14.04.1
-- PHP 版本: 5.6.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `www4swoole`
--

-- --------------------------------------------------------

--
-- 表的结构 `ask_category`
--

CREATE TABLE IF NOT EXISTS `ask_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `ask_content`
--

CREATE TABLE IF NOT EXISTS `ask_content` (
  `aid` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `ask_reply`
--

CREATE TABLE IF NOT EXISTS `ask_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `vote` int(11) NOT NULL,
  `best` tinyint(1) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`,`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- 表的结构 `ask_subject`
--

CREATE TABLE IF NOT EXISTS `ask_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `cname` varchar(32) CHARACTER SET utf8 NOT NULL,
  `cid2` int(11) NOT NULL,
  `c2name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `gold` smallint(6) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mstatus` tinyint(4) NOT NULL,
  `uid` int(11) NOT NULL,
  `qcount` smallint(6) NOT NULL,
  `lcount` smallint(6) NOT NULL,
  `expire` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`cid2`,`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=487 ;

-- --------------------------------------------------------

--
-- 表的结构 `ask_vote`
--

CREATE TABLE IF NOT EXISTS `ask_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_admin`
--

CREATE TABLE IF NOT EXISTS `st_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `ugroup` varchar(32) NOT NULL,
  `realname` varchar(32) NOT NULL,
  `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_apps`
--

CREATE TABLE IF NOT EXISTS `st_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `app` varchar(32) NOT NULL,
  `own_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_attachment`
--

CREATE TABLE IF NOT EXISTS `st_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `app` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `filetype` varchar(16) NOT NULL,
  `filesize` varchar(16) NOT NULL,
  `url` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_branch`
--

CREATE TABLE IF NOT EXISTS `st_branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(48) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `pagename` varchar(32) DEFAULT NULL,
  `intro` text,
  `content` mediumtext,
  `pic` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `source` varchar(64) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `digest` tinyint(4) DEFAULT '0',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_catelog`
--

CREATE TABLE IF NOT EXISTS `st_catelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `pagename` varchar(32) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `intro` text NOT NULL,
  `fid` int(11) NOT NULL,
  `app` varchar(16) NOT NULL,
  `acl` varchar(255) NOT NULL,
  `tplname` varchar(64) NOT NULL,
  `tpl_detail` varchar(64) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_download`
--

CREATE TABLE IF NOT EXISTS `st_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(48) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `pagename` varchar(32) DEFAULT NULL,
  `intro` text,
  `content` mediumtext,
  `file` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `source` varchar(64) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `digest` tinyint(4) DEFAULT '0',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_guestbook`
--

CREATE TABLE IF NOT EXISTS `st_guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `num` int(11) NOT NULL,
  `product` varchar(40) NOT NULL,
  `realname` varchar(40) NOT NULL,
  `age` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  `ctime` varchar(255) NOT NULL,
  `conn` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `mobile` varchar(32) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `tel` varchar(32) DEFAULT NULL,
  `reply` text,
  `source` varchar(40) NOT NULL,
  `stype` varchar(32) DEFAULT NULL,
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_news`
--

CREATE TABLE IF NOT EXISTS `st_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(48) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `pagename` varchar(32) DEFAULT NULL,
  `intro` text,
  `content` mediumtext,
  `click_num` int(8) NOT NULL,
  `pic` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `is_ori` tinyint(4) NOT NULL,
  `copyfrom` varchar(128) NOT NULL,
  `source` varchar(64) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `digest` tinyint(4) DEFAULT '0',
  `tagid` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=286 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_page`
--

CREATE TABLE IF NOT EXISTS `st_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(48) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `pagename` varchar(32) DEFAULT NULL,
  `intro` text,
  `content` mediumtext,
  `pic` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `source` varchar(64) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `digest` tinyint(4) DEFAULT '0',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_product`
--

CREATE TABLE IF NOT EXISTS `st_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(40) NOT NULL,
  `cid` int(11) DEFAULT '0',
  `cname` varchar(40) NOT NULL,
  `tagid` int(11) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `market_price` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `brand` varchar(40) NOT NULL,
  `PN` varchar(128) NOT NULL,
  `quality` varchar(40) NOT NULL,
  `fineness` varchar(40) NOT NULL,
  `title` text,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `image` varchar(64) NOT NULL,
  `pic` varchar(128) DEFAULT NULL,
  `intro` text,
  `keywords` varchar(128) NOT NULL,
  `content` mediumtext NOT NULL,
  `work_common` text,
  `work_special` text,
  `work_time` varchar(128) DEFAULT NULL,
  `otherinfo` text,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_tag`
--

CREATE TABLE IF NOT EXISTS `st_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `ttype` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `st_video`
--

CREATE TABLE IF NOT EXISTS `st_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `cid` int(11) NOT NULL,
  `cname` varchar(48) NOT NULL,
  `flvtime` varchar(32) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(40) NOT NULL,
  `name` varchar(128) NOT NULL,
  `pagename` varchar(32) DEFAULT NULL,
  `intro` text,
  `content` mediumtext,
  `file` varchar(128) NOT NULL,
  `pic` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `source` varchar(64) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `digest` tinyint(4) DEFAULT '0',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_blog`
--

CREATE TABLE IF NOT EXISTS `user_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `look_count` int(8) NOT NULL,
  `reply_count` int(8) NOT NULL,
  `uid` int(11) NOT NULL,
  `dir` tinyint(4) NOT NULL,
  `c_id` int(11) NOT NULL COMMENT '分类ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_blogcate`
--

CREATE TABLE IF NOT EXISTS `user_blogcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL COMMENT '类别名称',
  `uid` int(11) NOT NULL,
  `num` smallint(6) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_comment`
--

CREATE TABLE IF NOT EXISTS `user_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `app` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `reply` text NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(32) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_feed`
--

CREATE TABLE IF NOT EXISTS `user_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `tid` int(11) NOT NULL,
  `eventid` int(11) NOT NULL,
  `ftype` char(8) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_friend`
--

CREATE TABLE IF NOT EXISTS `user_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `frid` int(11) NOT NULL,
  `fgroup` tinyint(4) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_link`
--

CREATE TABLE IF NOT EXISTS `user_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_login`
--

CREATE TABLE IF NOT EXISTS `user_login` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(32) CHARACTER SET ascii NOT NULL,
  `password` char(40) CHARACTER SET ascii NOT NULL,
  `usertype` tinyint(4) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `realname` varchar(32) NOT NULL,
  `intro` varchar(160) NOT NULL,
  `sex` set('1','2') NOT NULL,
  `email` varchar(48) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `php_level` tinyint(4) NOT NULL,
  `skill` varchar(255) NOT NULL,
  `company` varchar(128) NOT NULL,
  `blog` varchar(128) NOT NULL,
  `work_year` tinyint(4) NOT NULL,
  `avatar` varchar(128) NOT NULL,
  `education` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `province` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `active_days` int(4) unsigned NOT NULL,
  `vip` tinyint(4) NOT NULL,
  `gold` int(11) NOT NULL,
  `login_times` int(10) unsigned NOT NULL,
  `reg_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reg_ip` char(16) NOT NULL,
  `lastlogin` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=232 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_mail`
--

CREATE TABLE IF NOT EXISTS `user_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ctype` tinyint(4) NOT NULL COMMENT '邮件类型',
  `fid` int(11) NOT NULL,
  `mstatus` tinyint(3) NOT NULL,
  `tid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_microblog`
--

CREATE TABLE IF NOT EXISTS `user_microblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `pic_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `category` int(11) NOT NULL,
  `theme` varchar(128) NOT NULL,
  `reply_count` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_note`
--

CREATE TABLE IF NOT EXISTS `user_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `mstatus` tinyint(4) NOT NULL,
  `attach` varchar(255) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_picture`
--

CREATE TABLE IF NOT EXISTS `user_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `imagep` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `title` varchar(128) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ischeck` tinyint(1) NOT NULL DEFAULT '1' COMMENT '照片审核，1：未通过，2：已通过',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_skill`
--

CREATE TABLE IF NOT EXISTS `user_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `uid` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- 表的结构 `wiki_content`
--

CREATE TABLE IF NOT EXISTS `wiki_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `close_comment` tinyint(1) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `uptime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `wiki_history`
--

CREATE TABLE IF NOT EXISTS `wiki_history` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `close_comment` tinyint(1) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `wiki_image`
--

CREATE TABLE IF NOT EXISTS `wiki_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- 表的结构 `wiki_project`
--

CREATE TABLE IF NOT EXISTS `wiki_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `home_id` int(11) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `links` varchar(255) NOT NULL COMMENT '链接其他项目',
  `close_comment` tinyint(1) NOT NULL COMMENT '是否关闭评论',
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- 表的结构 `wiki_tree`
--

CREATE TABLE IF NOT EXISTS `wiki_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `order_by_time` tinyint(1) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=445 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
