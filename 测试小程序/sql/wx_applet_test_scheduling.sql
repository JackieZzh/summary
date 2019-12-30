/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-19 09:03:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_scheduling
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_scheduling`;
CREATE TABLE `wx_applet_test_scheduling` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL COMMENT '医生id',
  `class_id` int(11) NOT NULL COMMENT '科室id',
  `date` int(11) NOT NULL COMMENT '排班时间',
  `reserve` tinyint(3) NOT NULL DEFAULT '1' COMMENT '可预约人数',
  `reserved` tinyint(3) NOT NULL DEFAULT '0' COMMENT '已预约人数',
  PRIMARY KEY (`id`),
  KEY `doc` (`doc_id`) USING BTREE,
  KEY `class` (`class_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
