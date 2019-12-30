/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-20 13:58:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_vote_active_hospital
-- ----------------------------
DROP TABLE IF EXISTS `wx_vote_active_hospital`;
CREATE TABLE `wx_vote_active_hospital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `add_time` int(11) DEFAULT NULL,
  `add_operator` varchar(50) DEFAULT NULL COMMENT '添加人员',
  `is_show` enum('2','1') NOT NULL DEFAULT '2' COMMENT '1 显示 2 隐藏',
  `is_del` enum('2','1') NOT NULL DEFAULT '1' COMMENT '1 未删除 2 已删除',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
