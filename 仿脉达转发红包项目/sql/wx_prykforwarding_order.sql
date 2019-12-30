/*
Navicat MySQL Data Transfer

Source Server         : pryktest
Source Server Version : 50720
Source Host           : 106.15.94.97:3306
Source Database       : weixinprykweb

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-11-12 08:43:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_prykforwarding_order
-- ----------------------------
DROP TABLE IF EXISTS `wx_prykforwarding_order`;
CREATE TABLE `wx_prykforwarding_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(32) NOT NULL COMMENT '订单号',
  `activeid` int(11) NOT NULL,
  `memberid` int(11) NOT NULL COMMENT '付款人id',
  `desc` varchar(50) NOT NULL COMMENT '订单描述',
  `paytheobject` varchar(100) NOT NULL COMMENT '收款人',
  `paythemoney` decimal(10,2) NOT NULL COMMENT '金额',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `orderstatus` enum('3','2','1') NOT NULL DEFAULT '1' COMMENT '1 为创建订单但未支付 2 支付完成 3 为创建订单但支付失败 ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
