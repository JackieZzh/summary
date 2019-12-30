/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-19 08:59:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_doctor_relation
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_doctor_relation`;
CREATE TABLE `wx_applet_test_doctor_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `d_id` int(10) NOT NULL COMMENT '医生id',
  `c_id` int(10) NOT NULL COMMENT '科室id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
