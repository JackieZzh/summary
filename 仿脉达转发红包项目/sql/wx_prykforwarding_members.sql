/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-11 10:43:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_members
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_members`;
CREATE TABLE `wx_prykforwarding_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `faceimg` varchar(100) DEFAULT NULL COMMENT '头像',
  `qqemail` varchar(20) DEFAULT NULL COMMENT 'qq邮箱',
  `qqnum` int(20) DEFAULT NULL COMMENT 'qq号',
  `wechatnum` varchar(50) DEFAULT NULL COMMENT '微信号',
  `tel` varchar(11) NOT NULL COMMENT '手机号',
  `companyname` varchar(100) DEFAULT NULL COMMENT '公司名',
  `overduetime` int(11) unsigned DEFAULT NULL COMMENT '过期时间',
  `identity` enum('2','1') NOT NULL DEFAULT '1' COMMENT '1 为普通会员  2 为超级会员',
  `status` enum('2','1') NOT NULL DEFAULT '1' COMMENT '1 为启用  2 为禁用',
  `memberpassword` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tel` (`tel`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
