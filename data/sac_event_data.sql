/*
 Navicat Premium Dump SQL

 Source Server         : Mysql-192.168.88.7
 Source Server Type    : MySQL
 Source Server Version : 80300 (8.3.0)
 Source Host           : 171.100.56.194:3307
 Source Schema         : sac_event_data

 Target Server Type    : MySQL
 Target Server Version : 80300 (8.3.0)
 File Encoding         : 65001

 Date: 13/05/2026 17:48:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for attendees
-- ----------------------------
DROP TABLE IF EXISTS `attendees`;
CREATE TABLE `attendees`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `sales_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `order_no` int NULL DEFAULT NULL,
  `total_no` int NULL DEFAULT NULL,
  `shop_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` enum('shop','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'shop',
  `province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `participants_before` int NULL DEFAULT 0,
  `participants_after` int NULL DEFAULT 0,
  `reserve_room` tinyint(1) NULL DEFAULT 0,
  `used_room` tinyint(1) NULL DEFAULT 0,
  `tire_40_before` int NULL DEFAULT 0,
  `tire_40_after` int NULL DEFAULT 0,
  `tire_80_before` int NULL DEFAULT 0,
  `tire_80_after` int NULL DEFAULT 0,
  `tire_120_before` int NULL DEFAULT 0,
  `tire_120_after` int NULL DEFAULT 0,
  `tire_200_before` int NULL DEFAULT 0,
  `tire_200_after` int NULL DEFAULT 0,
  `tire_300_before` int NULL DEFAULT 0,
  `tire_300_after` int NULL DEFAULT 0,
  `tire_600_before` int NULL DEFAULT 0,
  `tire_600_after` int NULL DEFAULT 0,
  `room_att` int NULL DEFAULT 0,
  `room_att_after` int NULL DEFAULT 0,
  `ship_att` int NULL DEFAULT NULL,
  `ship_att_after` int NULL DEFAULT 0,
  `night_att` int NULL DEFAULT 0,
  `night_att_after` int NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `event_date` date NULL DEFAULT NULL,
  `event_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ims_user
-- ----------------------------
DROP TABLE IF EXISTS `ims_user`;
CREATE TABLE `ims_user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(50) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for provinces
-- ----------------------------
DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `province_name`(`province_name` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for summary
-- ----------------------------
DROP TABLE IF EXISTS `summary`;
CREATE TABLE `summary`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `total_shops` int NULL DEFAULT 0,
  `shops_came` int NULL DEFAULT 0,
  `total_participants_before` int NULL DEFAULT 0,
  `total_participants_after` int NULL DEFAULT 0,
  `total_reserve_room` int NULL DEFAULT 0,
  `total_used_room` int NULL DEFAULT 0,
  `total_tire_40_before` int NULL DEFAULT 0,
  `total_tire_40_after` int NULL DEFAULT 0,
  `total_tire_80_before` int NULL DEFAULT 0,
  `total_tire_80_after` int NULL DEFAULT 0,
  `total_tire_120_before` int NULL DEFAULT 0,
  `total_tire_120_after` int NULL DEFAULT 0,
  `total_tire_200_before` int NULL DEFAULT 0,
  `total_tire_200_after` int NULL DEFAULT 0,
  `total_tire_300_before` int NULL DEFAULT 0,
  `total_tire_300_after` int NULL DEFAULT 0,
  `total_tire_600_before` int NULL DEFAULT 0,
  `total_tire_600_after` int NULL DEFAULT 0,
  `total_tire_before` int NULL DEFAULT 0,
  `total_tire_after` int NULL DEFAULT 0,
  `total_room_att` int NULL DEFAULT 0,
  `total_room_att_after` int NULL DEFAULT 0,
  `total_ship_att` int NULL DEFAULT 0,
  `total_ship_att_after` int NULL DEFAULT 0,
  `total_night_att` int NULL DEFAULT 0,
  `total_night_att_after` int NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = FIXED;

-- ----------------------------
-- Table structure for user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `user_permission` (`user_id`, `permission_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;
