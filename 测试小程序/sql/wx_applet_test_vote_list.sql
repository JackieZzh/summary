/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-19 09:37:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_vote_list
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_vote_list`;
CREATE TABLE `wx_applet_test_vote_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `can` tinyint(3) NOT NULL DEFAULT '0' COMMENT '可点赞次数',
  `forwards` tinyint(3) NOT NULL DEFAULT '0' COMMENT '可转发次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
