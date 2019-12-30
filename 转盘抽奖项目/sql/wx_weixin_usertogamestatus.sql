/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-10-29 10:08:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_weixin_usertogamestatus
-- ----------------------------
DROP TABLE IF EXISTS `wx_weixin_usertogamestatus`;
CREATE TABLE `wx_weixin_usertogamestatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL COMMENT '对应的活动id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态\r\n0 禁止参加游戏\r\n1 允许参加游戏',
  `times` int(5) DEFAULT '0' COMMENT '剩余参加次数(如果有次数的话)\r\n为空则无限次\r\n大于0 则为可参加次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14053 DEFAULT CHARSET=utf8;
