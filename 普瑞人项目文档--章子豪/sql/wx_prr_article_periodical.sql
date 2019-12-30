/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-05-09 10:56:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prr_article_periodical
-- ----------------------------
DROP TABLE IF EXISTS `wx_prr_article_periodical`;
CREATE TABLE `wx_prr_article_periodical` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '期刊id',
  `pid` int(11) DEFAULT NULL COMMENT '父级ID',
  `uniacid` int(10) NOT NULL COMMENT '应用id',
  `title` varchar(255) NOT NULL COMMENT '期刊标题',
  `intr` varchar(255) DEFAULT NULL COMMENT '期刊简介',
  `image` varchar(255) NOT NULL COMMENT '期刊图片',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1删除0未删除',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='期刊表';
