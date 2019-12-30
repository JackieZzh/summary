/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-01 11:55:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_lz_vote_participant
-- ----------------------------
DROP TABLE IF EXISTS `wx_lz_vote_participant`;
CREATE TABLE `wx_lz_vote_participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '姓名',
  `come_from` varchar(50) DEFAULT NULL COMMENT '来自',
  `video_url` varchar(255) NOT NULL COMMENT '视频链接',
  `face_url` varchar(255) NOT NULL COMMENT '封面图',
  `sort` int(11) NOT NULL DEFAULT '0',
  `is_show` tinyint(1) NOT NULL DEFAULT '1',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 为未删除 2 为已删除',
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 为未知  1为男  2为女',
  `create_time` int(11) DEFAULT NULL,
  `creater` varchar(50) DEFAULT NULL,
  `votes` tinyint(3) NOT NULL DEFAULT '0',
  `active_id` int(11) NOT NULL,
  `topic` varchar(20) NOT NULL COMMENT '话题',
  PRIMARY KEY (`id`),
  KEY `aid` (`active_id`) USING BTREE,
  KEY `topic` (`topic`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
