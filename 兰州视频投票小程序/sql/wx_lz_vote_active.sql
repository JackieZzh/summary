/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-01 11:53:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_lz_vote_active
-- ----------------------------
DROP TABLE IF EXISTS `wx_lz_vote_active`;
CREATE TABLE `wx_lz_vote_active` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '活动名',
  `top_img` varchar(255) NOT NULL COMMENT '头部背景图',
  `visit_img` varchar(255) DEFAULT NULL COMMENT '访问信息背景图',
  `cd_logo` varchar(255) DEFAULT NULL COMMENT '倒计时logo',
  `vote_logo` varchar(255) DEFAULT NULL COMMENT '列表页投票logo',
  `video_vote_logo` varchar(255) DEFAULT NULL COMMENT '视频页投票logo',
  `video_rank_logo` varchar(255) DEFAULT NULL COMMENT '视频页排行logo',
  `video_share_logo` varchar(255) DEFAULT NULL COMMENT '视频页分享logo',
  `active_rule` text COMMENT '活动规则',
  `rank_logo_first` varchar(255) NOT NULL COMMENT '排行榜第一名徽章',
  `rank_logo_second` varchar(255) NOT NULL COMMENT '排行榜第二名徽章',
  `rank_logo_third` varchar(255) NOT NULL COMMENT '排行榜第三徽章',
  `begin_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `creater` varchar(50) NOT NULL COMMENT '创建者',
  `last_time` int(11) DEFAULT NULL COMMENT '最后一次修改时间',
  `last_modifier` varchar(50) DEFAULT NULL COMMENT '最后一位修改人员',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 为显示 2 为隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '软删除时间 1 为未删除 2 为删除',
  `share_logo` varchar(255) DEFAULT NULL COMMENT '转发图片',
  `voter_can` tinyint(3) DEFAULT NULL COMMENT '每人每天可投票次数',
  `leader_board_num` smallint(5) DEFAULT NULL COMMENT '排行榜默认显示人数 null 为不显示 ',
  `h_id` smallint(5) NOT NULL COMMENT '医院id',
  `forwards_can` tinyint(3) NOT NULL DEFAULT '0' COMMENT '增加投票数的转发次数 0 为没有',
  `forwards_num` tinyint(3) DEFAULT '1' COMMENT '每次转发可增加的投票次数',
  `rank_ttl` int(11) NOT NULL DEFAULT '1800' COMMENT '排行榜更新时间 默认半小时',
  `access_num` int(11) NOT NULL DEFAULT '0' COMMENT '访问量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
