/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-01 13:50:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_lz_vote_share_action
-- ----------------------------
DROP TABLE IF EXISTS `wx_lz_vote_share_action`;
CREATE TABLE `wx_lz_vote_share_action` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `aid` int(11) NOT NULL COMMENT '活动id',
  `time` int(11) NOT NULL COMMENT '分享时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
