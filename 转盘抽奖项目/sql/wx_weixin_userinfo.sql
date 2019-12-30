/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-10-29 10:08:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_weixin_userinfo
-- ----------------------------
DROP TABLE IF EXISTS `wx_weixin_userinfo`;
CREATE TABLE `wx_weixin_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unionid` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nickname` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `province` varchar(225) COLLATE utf8_unicode_ci DEFAULT '',
  `city` varchar(225) COLLATE utf8_unicode_ci DEFAULT '',
  `country` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headimgurl` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `subscribe_scene` enum('ADD_SCENE_PROFILE_CARD','ADD_SCENE_ACCOUNT_MIGRATION','ADD_SCENE_OTHERS','ADD_SCENE_PAID','ADD_SCENE_PROFILE_ITEM','ADD_SCENEPROFILE','ADD_SCENE_QR_CODE','ADD_SCENE_SEARCH') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ADD_SCENE_SEARCH' COMMENT 'ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他',
  `tel` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `realname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `realcity` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isshortsightedness` enum('3','2','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '3' COMMENT '1 为近视  2 为非近视 3 为未选择',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`) USING BTREE,
  KEY `nickname` (`nickname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15160 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='同步于微信公众号用户信息';
