/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50137
Source Host           : localhost:3306
Source Database       : zmxy3

Target Server Type    : MYSQL
Target Server Version : 50137
File Encoding         : 65001

Date: 2013-09-24 16:49:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `jh_candidate`
-- ----------------------------
DROP TABLE IF EXISTS `jh_candidate`;
CREATE TABLE `jh_candidate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `description` text,
  `avatar` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jh_candidate
-- ----------------------------
INSERT INTO `jh_candidate`(`name`,position,description,avatar,photo,display_order) VALUES ('0', '小明', '我是小明', '1.jpg', '1.jpg', '1');

-- ----------------------------
-- Table structure for `jh_votes`
-- ----------------------------
DROP TABLE IF EXISTS `jh_votes`;
CREATE TABLE `jh_votes` (
  `ip` varchar(15) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `add_time` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jh_votes
-- ----------------------------
