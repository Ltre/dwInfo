/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : duowaninfo

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-11-10 22:03:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dwi_passport`
-- ----------------------------
DROP TABLE IF EXISTS `dwi_passport`;
CREATE TABLE `dwi_passport` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `imid` bigint(20) DEFAULT NULL COMMENT 'YY号',
  `jifen` int(11) DEFAULT NULL COMMENT 'YY积分',
  `nick` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'YY昵称',
  `passport` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '多玩通行证',
  `register_time` datetime DEFAULT NULL COMMENT 'YY注册时间',
  `sign` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'YY签名',
  `yyuid` bigint(20) DEFAULT NULL COMMENT 'YY用户表的UID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `yyuid` (`yyuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of dwi_passport
-- ----------------------------
