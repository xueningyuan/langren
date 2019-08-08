/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : localhost:3306
 Source Schema         : lang

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 25/07/2019 11:14:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` char(9) CHARACTER SET utf8 COLLATE utf8_croatian_ci NULL DEFAULT NULL COMMENT '角色',
  `num` int(3) NULL DEFAULT NULL COMMENT '几人局',
  `room_id` char(3) CHARACTER SET utf8 COLLATE utf8_croatian_ci NULL DEFAULT NULL COMMENT '房间id',
  `user_id` char(3) CHARACTER SET utf8 COLLATE utf8_croatian_ci NULL DEFAULT NULL COMMENT '用户',
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_croatian_ci NULL DEFAULT NULL,
  `seat` char(3) CHARACTER SET utf8 COLLATE utf8_croatian_ci NULL DEFAULT NULL COMMENT '座位号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_croatian_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for room
-- ----------------------------
DROP TABLE IF EXISTS `room`;
CREATE TABLE `room`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_croatian_ci NOT NULL COMMENT '房主名',
  `nums` tinyint(3) NOT NULL DEFAULT 0 COMMENT '人数',
  `limits` int(11) NOT NULL DEFAULT 9 COMMENT '人数上限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_croatian_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for room_user
-- ----------------------------
DROP TABLE IF EXISTS `room_user`;
CREATE TABLE `room_user`  (
  `room_id` int(11) NOT NULL COMMENT '房间id',
  `user_name` char(10) CHARACTER SET utf8 COLLATE utf8_croatian_ci NOT NULL COMMENT '用户名字'
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_croatian_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_croatian_ci NOT NULL COMMENT '用户名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_croatian_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, '咯');

SET FOREIGN_KEY_CHECKS = 1;
