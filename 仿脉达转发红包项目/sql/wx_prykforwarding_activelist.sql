/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-11 10:48:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_activelist
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_activelist`;
CREATE TABLE `wx_prykforwarding_activelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `title` varchar(255) NOT NULL COMMENT '页面标题',
  `htmlText` mediumtext,
  `activeUrl` varchar(255) DEFAULT NULL COMMENT '活动链接',
  `createTime` int(11) NOT NULL,
  `beginTime` int(11) NOT NULL,
  `overTime` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL COMMENT '活动描述',
  `faceImg` varchar(255) DEFAULT NULL COMMENT '转发封面图',
  `accountName` varchar(60) DEFAULT NULL COMMENT '公众号名称',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `historyArticle` varchar(255) DEFAULT NULL COMMENT '公众号历史文章',
  `style` enum('2','1') NOT NULL DEFAULT '1' COMMENT '1 为转发成功后获得红包  2 为转发后其他人阅读之后获得红包',
  `status` enum('3','2','1') NOT NULL DEFAULT '3' COMMENT '1 为开启  2 为停止 3 为待审',
  `userparticipate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户可参与次数',
  `infobox` enum('2','1') NOT NULL DEFAULT '1' COMMENT '1 为禁用  2 为启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
