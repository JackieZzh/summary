/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-05-27 16:20:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prr_banner
-- ----------------------------
DROP TABLE IF EXISTS `wx_prr_banner`;
CREATE TABLE `wx_prr_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL COMMENT '链接',
  `hidden` enum('1','0') NOT NULL DEFAULT '0' COMMENT '0 为显示 以为隐藏',
  `is_del` enum('1','0') NOT NULL DEFAULT '0' COMMENT '0 为未删除 1 为已删除',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `uniacid` tinyint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
