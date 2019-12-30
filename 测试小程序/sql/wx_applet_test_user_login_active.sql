/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-18 17:23:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_user_login_active
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_user_login_active`;
CREATE TABLE `wx_applet_test_user_login_active` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL COMMENT '手机型号',
  `scene` smallint(5) DEFAULT NULL COMMENT '微信场景值',
  PRIMARY KEY (`id`),
  KEY `time` (`time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
