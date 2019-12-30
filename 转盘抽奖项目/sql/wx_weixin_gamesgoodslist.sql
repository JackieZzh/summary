/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-10-29 10:08:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_weixin_gamesgoodslist
-- ----------------------------
DROP TABLE IF EXISTS `wx_weixin_gamesgoodslist`;
CREATE TABLE `wx_weixin_gamesgoodslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `gid` int(11) NOT NULL,
  `picurl` varchar(225) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;
