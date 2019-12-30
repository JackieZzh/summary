/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-19 08:55:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_doctor
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_doctor`;
CREATE TABLE `wx_applet_test_doctor` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned DEFAULT NULL,
  `doc_name` varchar(12) NOT NULL COMMENT '医生名字',
  `job_id` mediumint(8) unsigned DEFAULT '0' COMMENT '职务id',
  `doc_img` varchar(255) DEFAULT '' COMMENT '医生大图',
  `doc_smallimg` varchar(255) DEFAULT '' COMMENT '医生缩略图',
  `doc_blog` varchar(255) DEFAULT '' COMMENT '医生博客',
  `doc_sort` smallint(5) unsigned DEFAULT '0' COMMENT '排序,序列越大越后面',
  `doc_content` text COMMENT '医生描述',
  `doc_keyword` varchar(255) DEFAULT '' COMMENT 'seo关键词',
  `doc_description` varchar(255) DEFAULT '' COMMENT 'seo描述',
  `doc_title` varchar(100) DEFAULT '' COMMENT 'seo标题',
  `doc_synopsis` varchar(250) DEFAULT '' COMMENT '医生简单描述250字以内',
  `doc_addtime` int(11) DEFAULT '0' COMMENT '发布时间',
  `doc_updatetime` int(11) DEFAULT '0' COMMENT '更新时间',
  `doc_good` varchar(200) DEFAULT '' COMMENT '擅长',
  `job_title_id` mediumint(8) unsigned DEFAULT '0' COMMENT '职称id',
  `doc_content_title` varchar(220) DEFAULT '' COMMENT '医生文章标题',
  `is_del` tinyint(3) unsigned DEFAULT '0' COMMENT '是否删除',
  `is_show` enum('1','0') DEFAULT '1' COMMENT '0 显示 1 隐藏',
  `d_hid` tinyint(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `catid` (`catid`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
