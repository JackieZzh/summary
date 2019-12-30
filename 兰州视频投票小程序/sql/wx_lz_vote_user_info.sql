/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-01 11:58:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_lz_vote_user_info
-- ----------------------------
DROP TABLE IF EXISTS `wx_lz_vote_user_info`;
CREATE TABLE `wx_lz_vote_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(50) NOT NULL COMMENT,
  `nick_name` varchar(20) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `last_time` int(11) DEFAULT NULL,
  `create_ip` varchar(16) DEFAULT NULL,
  `last_ip` varchar(16) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `uniacid` tinyint(3) NOT NULL COMMENT '应用id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
