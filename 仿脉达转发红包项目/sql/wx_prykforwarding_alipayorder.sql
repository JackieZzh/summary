/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-12 09:05:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_alipayorder
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_alipayorder`;
CREATE TABLE `wx_prykforwarding_alipayorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `out_trade_no` varchar(60) NOT NULL COMMENT '支付宝商户订单号',
  `total_amount` decimal(10,2) NOT NULL COMMENT '订单金额',
  `status` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '0 为未处理订单 1 为付款成功 2 为付款失败',
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
