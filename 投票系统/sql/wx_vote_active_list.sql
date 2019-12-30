/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-20 14:32:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_vote_active_list
-- ----------------------------
DROP TABLE IF EXISTS `wx_vote_active_list`;
CREATE TABLE `wx_vote_active_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '活动标题',
  `description` varchar(255) DEFAULT NULL COMMENT '活动描述',
  `timer_img` varchar(255) DEFAULT NULL COMMENT '倒计时背景图',
  `background_img` varchar(255) DEFAULT NULL COMMENT '活动页面背景图',
  `goods_url` varchar(255) DEFAULT NULL COMMENT '奖品页面链接',
  `rote_url` varchar(255) DEFAULT NULL COMMENT '规则页面链接',
  `background_color` varchar(7) DEFAULT NULL COMMENT '页面背景色',
  `begin` int(11) NOT NULL COMMENT '开始时间',
  `end` int(11) DEFAULT NULL COMMENT '结束时间',
  `add_operator` varchar(50) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 为显示 2 为隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 未删除 2 已删除',
  `h_id` smallint(5) NOT NULL COMMENT '医院id',
  `sort` int(11) NOT NULL DEFAULT '0',
  `voter_can` tinyint(3) NOT NULL DEFAULT '1' COMMENT '每个用户每天可投票次数',
  `background_color_rote` varchar(255) DEFAULT NULL,
  `leader_board_num` int(11) NOT NULL DEFAULT '0' COMMENT '0 则全部展示 10 则展示前10  一次类推',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
