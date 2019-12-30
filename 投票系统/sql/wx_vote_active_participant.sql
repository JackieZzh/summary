/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-20 14:43:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_vote_active_participant
-- ----------------------------
DROP TABLE IF EXISTS `wx_vote_active_participant`;
CREATE TABLE `wx_vote_active_participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(50) NOT NULL COMMENT '昵称',
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `age` tinyint(3) DEFAULT NULL COMMENT '年龄',
  `gender` enum('2','1','0') DEFAULT '0' COMMENT '0 为未知  1 为男  2 为女',
  `introduction` varchar(255) DEFAULT NULL COMMENT '描述',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT '头像链接',
  `video_url` varchar(255) DEFAULT NULL COMMENT '视频链接',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `add_operator` varchar(50) DEFAULT NULL COMMENT '添加人',
  `is_show` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 为显示 2 为隐藏',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 未删除 2 已删除',
  `active_id` smallint(5) NOT NULL COMMENT '活动id',
  `sort` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已获取票数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
