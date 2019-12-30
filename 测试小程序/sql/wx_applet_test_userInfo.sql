/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-18 17:15:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_applet_test_userInfo
-- ----------------------------
DROP TABLE IF EXISTS `wx_applet_test_userInfo`;
CREATE TABLE `wx_applet_test_userInfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) DEFAULT NULL,
  `gender` enum('2','1','0') DEFAULT NULL COMMENT '0 为未知  1为男 2为女',
  `avatarUrl` varchar(255) DEFAULT NULL COMMENT '头像链接',
  `country` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `openId` varchar(60) NOT NULL COMMENT '小程序openId',
  `unionId` varchar(60) DEFAULT NULL,
  `uniacid` tinyint(3) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `referrer` varchar(60) DEFAULT NULL COMMENT '推荐人',
  `level` smallint(5) DEFAULT NULL COMMENT '用户层级',
  `top` int(11) DEFAULT NULL COMMENT '顶层用户',
  `bd_country` varchar(100) DEFAULT NULL COMMENT '百度定位(国家)',
  `bd_province` varchar(100) DEFAULT NULL COMMENT '百度定位(省份)',
  `bd_city` varchar(100) DEFAULT NULL COMMENT '百度定位(城市)',
  `bd_district` varchar(255) DEFAULT NULL COMMENT '百度定位(街道)',
  `ip` varchar(16) DEFAULT NULL COMMENT 'ip地址',
  PRIMARY KEY (`id`),
  KEY `openId` (`openId`) USING BTREE,
  KEY `add_time` (`add_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
