/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-20 14:46:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_vote_active_voter
-- ----------------------------
DROP TABLE IF EXISTS `wx_vote_active_voter`;
CREATE TABLE `wx_vote_active_voter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(60) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `sex` enum('2','1','0') DEFAULT NULL COMMENT '0 未知 1 男 2 女',
  `province` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `accessIp` varchar(16) DEFAULT NULL COMMENT '登陆ip',
  `accessTime` int(11) DEFAULT NULL COMMENT '登陆时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
