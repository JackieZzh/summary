/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-20 14:52:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_vote_active_action
-- ----------------------------
DROP TABLE IF EXISTS `wx_vote_active_action`;
CREATE TABLE `wx_vote_active_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid` int(11) NOT NULL COMMENT '投票人id',
  `aid` int(11) NOT NULL COMMENT '活动id',
  `pid` int(11) NOT NULL COMMENT '参与者id',
  `time` int(11) NOT NULL COMMENT '投票时间',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `time` (`time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
