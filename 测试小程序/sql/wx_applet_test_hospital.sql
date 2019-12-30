/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-18 17:33:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_hospital
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_hospital`;
CREATE TABLE `wx_applet_test_hospital` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned DEFAULT NULL COMMENT '栏目分类ID',
  `h_name` varchar(64) NOT NULL COMMENT '医院名',
  `h_telephone` varchar(32) DEFAULT '' COMMENT '联系电话',
  `h_yuyue` varchar(255) DEFAULT NULL COMMENT '预约链接',
  `h_link` varchar(255) DEFAULT NULL COMMENT '咨询链接',
  `h_email` varchar(64) DEFAULT '' COMMENT '邮件',
  `h_address` varchar(256) NOT NULL DEFAULT '' COMMENT '地址',
  `h_logo` varchar(256) NOT NULL DEFAULT '' COMMENT 'logo图片地址',
  `h_content` text NOT NULL COMMENT '医院详细介绍',
  `h_synopsis` varchar(500) DEFAULT '' COMMENT '医院简单描述250字以内',
  `h_keyword` varchar(255) DEFAULT '' COMMENT 'seo关键词',
  `h_description` varchar(255) DEFAULT '' COMMENT 'seo描述',
  `h_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `h_isshow` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示,0显示/1不显示',
  `h_addtime` int(11) NOT NULL,
  `h_isdel` tinyint(3) NOT NULL DEFAULT '0' COMMENT '后台删除标记',
  `h_updatetime` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `h_url` varchar(100) DEFAULT NULL,
  `h_qq` varchar(255) DEFAULT NULL COMMENT 'qq客服链接',
  `h_area` varchar(20) DEFAULT NULL,
  `h_wapurl` char(100) DEFAULT NULL COMMENT '手机站链接',
  `h_classify` text,
  `h_postcode` varchar(20) DEFAULT NULL COMMENT '邮编',
  `h_mobilePhone` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `h_route` varchar(300) DEFAULT NULL COMMENT '乘车路线',
  `city` varchar(50) DEFAULT NULL COMMENT '城市名/id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `h_isdel` (`h_isdel`) USING BTREE,
  KEY `catid` (`catid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
