/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-11 16:17:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_assets
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_assets`;
CREATE TABLE `wx_prykforwarding_assets` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `remainingsum` decimal(10,2) DEFAULT NULL COMMENT '余额',
  PRIMARY KEY (`id`),
  KEY `accets` (`remainingsum`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
