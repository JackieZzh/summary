/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-19 08:51:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_classify
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_classify`;
CREATE TABLE `wx_applet_test_classify` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '科室id',
  `c_name` varchar(100) NOT NULL COMMENT '分类名称',
  `c_name_en` varchar(100) DEFAULT NULL,
  `c_pid` tinyint(3) unsigned NOT NULL COMMENT '父级id',
  `c_sort` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `c_keyword` varchar(255) DEFAULT '' COMMENT 'seo关键词',
  `c_description` varchar(255) DEFAULT '' COMMENT 'seo描述',
  `c_title` varchar(100) DEFAULT '' COMMENT 'seo标题',
  `c_img` varchar(255) DEFAULT '' COMMENT '原始图',
  `c_hid` tinyint(3) DEFAULT '0' COMMENT '医院id  0 为公用',
  `c_hidden` enum('1','0') NOT NULL DEFAULT '1' COMMENT '0 为显示  1 为隐藏',
  `c_isdel` enum('1','0') NOT NULL DEFAULT '0' COMMENT '是否删除 0 未删除 1 删除',
  `c_addtime` int(11) NOT NULL,
  `c_hospitals` tinyint(5) DEFAULT NULL COMMENT '医院id集',
  PRIMARY KEY (`id`),
  KEY `c_parentid` (`c_pid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
