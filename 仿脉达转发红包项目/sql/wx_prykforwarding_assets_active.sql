/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-11 16:22:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_assets_active
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_assets_active`;
CREATE TABLE `wx_prykforwarding_assets_active` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `out_trade_no` varchar(60) NOT NULL COMMENT '订单编号',
  `desc` varchar(100) NOT NULL COMMENT '订单描述',
  `activeid` int(11) DEFAULT NULL COMMENT '活动id',
  `payway` enum('2','3','1') NOT NULL COMMENT '1 为支付宝  2 为微信  3 为账户余额',
  `total_amount` decimal(10,2) NOT NULL COMMENT '充值金额',
  `status` enum('3','2','1','0') NOT NULL DEFAULT '0' COMMENT '0 未操作订单 1 为支付成功 2 为支付失败 3 为支付宝异步回调验证失败',
  `realmoney` decimal(10,2) NOT NULL COMMENT '实际充值金额',
  `createtime` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
