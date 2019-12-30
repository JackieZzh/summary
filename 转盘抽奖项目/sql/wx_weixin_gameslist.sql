/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-10-29 10:08:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_weixin_gameslist
-- ----------------------------
DROP TABLE IF EXISTS `wx_weixin_gameslist`;
CREATE TABLE `wx_weixin_gameslist` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `expirestime` int(11) NOT NULL COMMENT '有效时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '游戏状态\r\n0 为未开启\r\n1 为开启',
  `usertimes` tinyint(5) DEFAULT NULL COMMENT '玩家可玩次数\r\nnull为无限次\r\n大于0 则为游戏次数',
  `contact` varchar(225) DEFAULT NULL,
  `whichunit` varchar(225) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exptime` (`expirestime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
