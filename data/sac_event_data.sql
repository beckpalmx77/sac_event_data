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

 Date: 09/04/2026 11:02:14
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
  `use_room` tinyint(1) NULL DEFAULT 0,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 93 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of attendees
-- ----------------------------
INSERT INTO `attendees` VALUES (1, 1, 'นิว', 1, 1, 'ร้านยางกิจถาวร', 'shop', 'สมุทรปราการ', '', 2, 0, 1, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 600, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (2, 1, 'นิว', 2, 2, 'ร้านตั้งกิจเจริญ', 'shop', 'สมุทรปราการ', '', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (3, 1, 'นิว', 3, 3, 'บริษัท รุ่งเรืองยางยนต์ จำกัด', 'shop', 'สมุทรปราการ', 'สำรองห้อง', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (4, 1, 'นิว', 4, 4, 'เจริญรวมยาง', 'shop', 'สมุทรปราการ', 'สำรองห้อง', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (5, 1, 'นิว', 5, 5, 'เยาวลักษณ์การยาง', 'shop', 'สมุทรปราการ', 'สำรองห้อง', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (6, 1, 'อาร์ต', 1, 6, 'บจ.แกรนด์ไทร์', 'shop', 'กทม.', '', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (7, 1, 'อาร์ต', 2, 7, 'บจ.ธนายางพาณิชย์', 'shop', 'กทม.', '', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (8, 1, 'อาร์ต', 3, 8, 'บจ.ไจแอ้นทไทร์ สุวรรณภูมิ', 'shop', 'กทม.', '', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (9, 1, 'ออม', 1, 9, 'ร้าน ส . วิมลสิน (ร้านยาง)', 'shop', 'ปทุมธานี', '', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (10, 1, 'ออม', 2, 10, 'ร้านนพรัตน์การยาง (ร้านยาง)', 'shop', 'ปทุมธานี', '', 2, 0, 1, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (11, 1, 'ออม', 3, 11, 'ก.เจริญการยาง', 'shop', 'ปทุมธานี', 'สำรองห้อง', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (12, 1, 'ออม', 4, 12, 'ร้านสุพจการยาง', 'shop', 'ปทุมธานี', 'สำรองห้อง', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (13, 1, 'เม', 1, 13, 'บริษัท หลักเมืองถาวร 2004 จำกัด', 'shop', 'สุพรรณ', '', 2, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (14, 1, 'เม', 2, 14, 'บริษัท วาณิชไทร์ แอนด์ เซอร์วิส จำกัด', 'shop', 'สุพรรณ', '', 2, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (15, 1, 'เม', 3, 15, 'บริษัท คีน แอนด์ แฟลร์ จำกัด', 'shop', 'นครปฐม', '', 2, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (16, 1, 'เม', 4, 16, 'บริษัท  สายห้ากิจการยาง  จำกัด', 'shop', 'นครปฐม', '', 2, 0, 1, 0, 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (17, 1, 'เม', 5, 17, 'บัณฑิตยางยนต์ (นายบัณฑิต  จันทะเสน)', 'shop', 'นครปฐม', '', 2, 0, 1, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (18, 1, 'เม', 6, 18, 'บริษัท สมาร์ท ไพรด์ ลูบริแคนท์ส จำกัด', 'shop', 'นครปฐม', 'สำรองห้อง', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (19, 1, 'เม', 7, 19, 'บริษัท ที ไทร์ ออโต้พาร์ท จำกัด', 'shop', 'นครปฐม', '', 2, 0, 1, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (20, 1, 'เม', 8, 20, 'เอกชัยศูนย์ล้อ (สำรอง)', 'shop', 'สมุทรสาคร', '', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (21, 1, 'เม', 9, 21, 'อิ่มพันธ์ (สำรอง)', 'shop', 'กาญจนบุรี', 'สำรองห้อง', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (22, 1, 'หวาน', 1, 22, 'พ.พชรทรัพย์', 'shop', 'สระบุรี', 'สำรองห้อง', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (23, 1, 'หวาน', 2, 23, 'สระบุรีทรัค', 'shop', 'สระบุรี', '', 2, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (24, 1, 'หวาน', 3, 24, 'อ๊อดการยาง', 'shop', 'อยุธยา', '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (25, 1, 'หวาน', 4, 25, 'มาลัยการยาง', 'shop', 'สระบุรี', '', 2, 0, 1, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (26, 1, 'หวาน', 5, 26, 'เกียรติการยาง', 'shop', 'อยุธยา', '', 4, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (27, 1, 'หวาน', 6, 27, 'บิ้วตี้การยาง', 'shop', 'อยุธยา', '', 2, 0, 1, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (28, 1, 'นิว', 1, 1, 'บริษัท แอลเอช ทรานสปอร์ต จำกัด', 'user', 'สมุทรปราการ', '2', 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, 200, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (29, 1, 'นิว', 2, 2, 'บริษัท อาร์เจ โลจีสติกส์ จำกัด+เอ็นพี24', 'user', 'สมุทรปราการ', '2', 0, 0, 0, 120, 0, 0, 0, 0, 0, 600, 0, 720, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (30, 1, 'นิว', 3, 3, 'บริษัท ฟาอีฟ โลจีสติกส์ จำกัด', 'user', 'สมุทรปราการ', '2', 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, 120, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (31, 1, 'นิว', 4, 4, 'บริษัท เมืองน่าอยู่ จำกัด', 'user', 'สมุทรปราการ', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (32, 1, 'นิว', 5, 5, 'บริษัท ว.ลาภส่งผล โลจีสติกส์ จำกัด', 'user', 'สมุทรปราการ', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (33, 1, 'นิว', 6, 6, 'บริษัท เอเบิลทรานสปอร์ต', 'user', 'สมุทรปราการ', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (34, 1, 'นิว', 7, 7, 'บริษัท สปีดดี ทรานส์เซอร์วิส จำกัด', 'user', 'สมุทรสาคร', '1', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (35, 1, 'นิว', 8, 8, 'บริษัท ส.รัตนเทพขนส่ง จำกัด', 'user', 'สมุทรสาคร', '1', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (36, 1, 'นิว', 9, 9, 'บริษัท ไฮปั๊ม จำกัด', 'user', 'สมุทรสาคร', '1', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (37, 1, 'นิว', 10, 10, 'บริษัท วีเชน จำกัด แฟลช', 'user', 'สมุทรปราการ', '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (38, 1, 'นิว', 11, 11, 'บริษัท เอสวีสหัสวรรษแอนด์ เซอร์วิส', 'user', 'สมุทรปราการ', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (39, 1, 'นิว', 12, 12, 'หจก. จิรวัฒน์ขนส่ง', 'user', 'สมุทรสาคร', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (40, 1, 'อาท', 1, 13, 'บริษัท พีเอสเค 02 ทรานสปอร์ต จำกัด', 'user', 'กทม.', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (41, 1, 'อาท', 2, 14, 'บริษัท พัชรสินี 42 จำกัด', 'user', 'กทม.', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (42, 1, 'อาท', 3, 15, 'บริษัท เคเอ็นเอ็น ไดนามิคทรานสปอร์ต จำกัด', 'user', 'กทม.', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (43, 1, 'อาท', 4, 16, 'บจก.เมอร์เล็กซ์ทรานสปร์อต', 'user', 'กทม', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (44, 1, 'อาท', 5, 17, 'บจก.โซนิคออโตโลจิส', 'user', 'กทม', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (45, 1, 'อาท', 6, 18, 'บจก.บางพลีใหญ่ขนส่ง', 'user', 'สมุทรปราการ', '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (46, 1, 'ออม', 1, 18, 'บริษัท พาราสัมพันธ์ จำกัด', 'user', 'ปทุมธานี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (47, 1, 'ออม', 2, 19, 'ช.ทวีก่อสร้าง, ดอนเมือง', 'user', 'ปทุมธานี', '2', 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, 300, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (48, 1, 'ออม', 3, 20, 'หจก.พรแม่สาห์ จำกัด', 'user', 'ปทุมธานี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (49, 1, 'ออม', 4, 21, 'หจก.ส.ภัสรชัย จำกัด', 'user', 'ปทุมธานี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (50, 1, 'ออม', 5, 22, 'หจก. ช เทียนเจริญ', 'user', 'ปทุมธานี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (51, 1, 'ออม', 6, 23, 'หจก.บางไทร เคหะภัณฑ์', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (52, 1, 'ออม', 7, 24, 'บจก. เอส ที ดี ทรานสปอร์ต', 'user', 'ปทุมธานี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (53, 1, 'ออม', 8, 25, 'บริษัท สุภาณิฏฐ์ อินเตอร์ กรุ๊ป จำกัด', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (54, 1, 'ออม', 9, 26, 'บริษัท พ.สิทธโชค จำกัด', 'user', 'ปทุมธานี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (55, 1, 'ออม', 10, 27, 'บริษัท อินทร์จำนงค์ จำกัด', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (56, 1, 'ออม', 11, 28, 'บริษัท เฟื่องฟ้าชฎาวรรณ ขนส่ง จำกัด', 'user', 'ปทุมธานี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (57, 1, 'ออม', 12, 29, 'บริษัท ภูเก็ตศรีสุชาติขนส่ง จำกัด', 'user', 'ปทุมธานี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (58, 1, 'ออม', 13, 30, 'หจก.รักสุจริต', 'user', 'ปทุมธานี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (59, 1, 'ออม', 14, 31, 'บริษัท ธันย์แอนด์ธี ทรานสปอร์ต', 'user', 'ปทุมธานี', '2', 0, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, 300, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (60, 1, 'ออม', 15, 32, 'บริษัท ลูกอัดไทย  ลูกค้าใหม่', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (61, 1, 'ออม', 16, 33, 'บจก. สหศักดิ์  ลูกค้าใหม่', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (62, 1, 'ออม', 17, 34, 'บจก.ประยูรวิศ ลูกค้าใหม่', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (63, 1, 'ออม', 18, 35, 'บจก.เอ็กซ์เซอร์เร้น', 'user', 'สมุทรปราการ', '3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 600, 0, 600, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (64, 1, 'ออม', 19, 36, 'บริษัท เฉลิมภัทร ทรานสปอร์ต', 'user', 'ปทุมธานี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (65, 1, 'เม ผู้ใช้', 1, 37, 'บริษัท แสงสหทรัพย์ จำกัด', 'user', 'นครปฐม', '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 600, 0, 600, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (66, 1, 'เม ผู้ใช้', 2, 38, 'ห้างหุ้นส่วนจำกัด เจตนาเจริญขนส่ง', 'user', 'นครปฐม', '2', 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, 120, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (67, 1, 'เม ผู้ใช้', 3, 39, 'บริษัท ฉันทวิลาศ (2002) จำกัด', 'user', 'นครปฐม', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (68, 1, 'เม ผู้ใช้', 4, 40, 'ห้างหุ้นส่วนจำกัด ศรีพรกิจ ทรานสปอร์ต', 'user', 'นนทบุรี', '2', 0, 0, 0, 0, 0, 200, 0, 0, 0, 0, 0, 200, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (69, 1, 'เม ผู้ใช้', 5, 41, 'บริษัท เชียงใหม่ริมดอย จำกัด (มหาชน)', 'user', 'นนทบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (70, 1, 'เม ผู้ใช้', 6, 42, 'บริษัท ไทยรุ่งพัฒนาขนส่ง จำกัด', 'user', 'กทม', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (71, 1, 'เม ผู้ใช้', 7, 43, 'ห้างหุ้นส่วนจำกัด กรเอก ทรานสปอร์ต (สำรอง)', 'user', 'นครปฐม', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (72, 1, 'เม ผู้ใช้', 8, 44, 'บริษัท เหรียญเฮง จำกัด  (สำรอง)', 'user', 'นครปฐม', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (73, 1, 'เม ผู้ใช้', 9, 45, 'บริษัท  แสงชัยโชค  จำกัด  (สำรอง)', 'user', 'นครปฐม', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (74, 1, 'เม ผู้ใช้', 10, 46, 'บริษัท  อุดมพัฒนา (สำรอง)', 'user', 'สมุทรสาคร', '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (75, 1, 'เม ผู้ใช้', 11, 47, 'บริษัท  หงส์ดำ (สำรอง)', 'user', 'สมุทรสาคร', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (76, 1, 'เม ผู้ใช้', 12, 48, 'หจก.ก้าวสุวรรณ', 'user', 'สมุทรสาคร', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (77, 1, 'เม ผู้ใช้', 13, 49, 'หจก.วสุโลจิสติกส์', 'user', 'นครปฐม', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (78, 1, 'เม ผู้ใช้', 14, 50, 'หจก.วีระวิภา ทรานสปอร์ต', 'user', 'สมุทรสาคร', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (79, 1, 'เม ผู้ใช้', 15, 60, 'หจก.นารถรวี ขนส่ง', 'user', 'นครปฐม', '2', 40, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (80, 1, 'หวาน', 1, 61, 'บจก.ภาคภูมิโลจิสติกส์', 'user', 'อยุธยา', '2', 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 0, 120, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (81, 1, 'หวาน', 2, 62, 'บจก.ยุทธนาทรานสปอร์ต', 'user', 'อยุธยา', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (82, 1, 'หวาน', 3, 63, 'หจก.ภูพลบุตรทรานสปอร์ต', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (83, 1, 'หวาน', 4, 64, 'หจก.ชมแขขนส่ง', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (84, 1, 'หวาน', 5, 65, 'บจก.วินทีม', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (85, 1, 'หวาน', 6, 66, 'บจก.ตวงพรขนส่ง', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (86, 1, 'หวาน', 8, 67, 'บจก.สมยศ', 'user', 'สระบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (87, 1, 'หวาน', 9, 68, 'บจก.นิติยาขนส่ง', 'user', 'สระบุรี', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (88, 1, 'หวาน', 10, 69, 'บจก.ทีเอ็มทีสตีล(ลค.ใหม่)', 'user', 'อยุธยา', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (89, 1, 'หวาน', 11, 70, 'บจก.ธีรภัค ทรานสปอร์ต(ลค.ใหม่)', 'user', 'สระบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (90, 1, 'หวาน', 12, 71, 'หจก.ทองทรานทรานสปอร์ต', 'user', 'สระบุรี', '2', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (91, 1, 'หวาน', 13, 72, 'บจก.ทรงยศท่องเที่ยว', 'user', 'อยุธยา', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');
INSERT INTO `attendees` VALUES (92, 1, 'หวาน', 14, 73, 'หจก.นพมงคลเซอร์วิส', 'user', 'อยุธยา', '2', 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 80, 0, 1, 0, '2026-04-08 17:36:18');

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of events
-- ----------------------------
INSERT INTO `events` VALUES (1, 'งาน Event', '2026-04-08 17:36:18');

-- ----------------------------
-- Table structure for provinces
-- ----------------------------
DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of provinces
-- ----------------------------
INSERT INTO `provinces` VALUES (1, 'กรุงเทพมหานคร');
INSERT INTO `provinces` VALUES (2, 'สมุทรปราการ');
INSERT INTO `provinces` VALUES (3, 'นนทบุรี');
INSERT INTO `provinces` VALUES (4, 'ปทุมธานี');
INSERT INTO `provinces` VALUES (5, 'พระนครศรีอยุธยา');
INSERT INTO `provinces` VALUES (6, 'อ่างทอง');
INSERT INTO `provinces` VALUES (7, 'ลพบุรี');
INSERT INTO `provinces` VALUES (8, 'สิงห์บุรี');
INSERT INTO `provinces` VALUES (9, 'ชัยนาท');
INSERT INTO `provinces` VALUES (10, 'สระบุรี');
INSERT INTO `provinces` VALUES (11, 'ชลบุรี');
INSERT INTO `provinces` VALUES (12, 'ระยอง');
INSERT INTO `provinces` VALUES (13, 'จันทบุรี');
INSERT INTO `provinces` VALUES (14, 'ตราด');
INSERT INTO `provinces` VALUES (15, 'ฉะเชิงเทรา');
INSERT INTO `provinces` VALUES (16, 'ปราจีนบุรี');
INSERT INTO `provinces` VALUES (17, 'นครนายก');
INSERT INTO `provinces` VALUES (18, 'สระแก้ว');
INSERT INTO `provinces` VALUES (19, 'นครราชสีมา');
INSERT INTO `provinces` VALUES (20, 'บุรีรัมย์');
INSERT INTO `provinces` VALUES (21, 'สุรินทร์');
INSERT INTO `provinces` VALUES (22, 'ศรีสะเกษ');
INSERT INTO `provinces` VALUES (23, 'อุบลราชธานี');
INSERT INTO `provinces` VALUES (24, 'ยโสธร');
INSERT INTO `provinces` VALUES (25, 'ชัยภูมิ');
INSERT INTO `provinces` VALUES (26, 'อำนาจเจริญ');
INSERT INTO `provinces` VALUES (27, 'หนองบัวลำภู');
INSERT INTO `provinces` VALUES (28, 'อุดรธานี');
INSERT INTO `provinces` VALUES (29, 'เลย');
INSERT INTO `provinces` VALUES (30, 'หนองคาย');
INSERT INTO `provinces` VALUES (31, 'มหาสารคาม');
INSERT INTO `provinces` VALUES (32, 'ร้อยเอ็ด');
INSERT INTO `provinces` VALUES (33, 'กาฬสินธุ์');
INSERT INTO `provinces` VALUES (34, 'สกลนคร');
INSERT INTO `provinces` VALUES (35, 'นครพนม');
INSERT INTO `provinces` VALUES (36, 'มุกดาหาร');
INSERT INTO `provinces` VALUES (37, 'เชียงใหม่');
INSERT INTO `provinces` VALUES (38, 'ลำพูน');
INSERT INTO `provinces` VALUES (39, 'ลำปาง');
INSERT INTO `provinces` VALUES (40, 'อุตรดิตถ์');
INSERT INTO `provinces` VALUES (41, 'แพร่');
INSERT INTO `provinces` VALUES (42, 'น่าน');
INSERT INTO `provinces` VALUES (43, 'พะเยา');
INSERT INTO `provinces` VALUES (44, 'เชียงราย');
INSERT INTO `provinces` VALUES (45, 'แม่ฮ่องสอน');
INSERT INTO `provinces` VALUES (46, 'นครสวรรค์');
INSERT INTO `provinces` VALUES (47, 'อุทัยธานี');
INSERT INTO `provinces` VALUES (48, 'กำแพงเพชร');
INSERT INTO `provinces` VALUES (49, 'ตาก');
INSERT INTO `provinces` VALUES (50, 'สุโขทัย');
INSERT INTO `provinces` VALUES (51, 'พิษณุโลก');
INSERT INTO `provinces` VALUES (52, 'พิจิตร');
INSERT INTO `provinces` VALUES (53, 'เพชรบูรณ์');
INSERT INTO `provinces` VALUES (54, 'ราชบุรี');
INSERT INTO `provinces` VALUES (55, 'กาญจนบุรี');
INSERT INTO `provinces` VALUES (56, 'สุพรรณบุรี');
INSERT INTO `provinces` VALUES (57, 'นครปฐม');
INSERT INTO `provinces` VALUES (58, 'สมุทรสาคร');
INSERT INTO `provinces` VALUES (59, 'สมุทรสงคราม');
INSERT INTO `provinces` VALUES (60, 'เพชรบุรี');
INSERT INTO `provinces` VALUES (61, 'ประจวบคีรีขันธ์');
INSERT INTO `provinces` VALUES (62, 'นครศรีธรรมราช');
INSERT INTO `provinces` VALUES (63, 'กระบี่');
INSERT INTO `provinces` VALUES (64, 'พังงา');
INSERT INTO `provinces` VALUES (65, 'ภูเก็ต');
INSERT INTO `provinces` VALUES (66, 'สุราษฎร์ธานี');
INSERT INTO `provinces` VALUES (67, 'ระนอง');
INSERT INTO `provinces` VALUES (68, 'ชุมพร');
INSERT INTO `provinces` VALUES (69, 'สงขลา');
INSERT INTO `provinces` VALUES (70, 'สตูล');
INSERT INTO `provinces` VALUES (71, 'ตรัง');
INSERT INTO `provinces` VALUES (72, 'พัทลุง');
INSERT INTO `provinces` VALUES (73, 'ปัตตานี');
INSERT INTO `provinces` VALUES (74, 'ยะลา');
INSERT INTO `provinces` VALUES (75, 'นราธิวาส');
INSERT INTO `provinces` VALUES (76, 'บึงกาฬ');
INSERT INTO `provinces` VALUES (77, 'อำนาจเจริญ');

-- ----------------------------
-- Table structure for summary
-- ----------------------------
DROP TABLE IF EXISTS `summary`;
CREATE TABLE `summary`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `total_shops` int NULL DEFAULT 0,
  `total_participants_before` int NULL DEFAULT 0,
  `total_participants_after` int NULL DEFAULT 0,
  `total_use_room` int NULL DEFAULT 0,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = FIXED;

-- ----------------------------
-- Records of summary
-- ----------------------------
INSERT INTO `summary` VALUES (1, 1, 92, 1296, 0, 1706, 880, 0, 720, 0, 1320, 0, 2200, 0, 7400, 0, 662, 0, 13182, 0, '2026-04-09 10:57:05');

SET FOREIGN_KEY_CHECKS = 1;
