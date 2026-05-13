-- =============================================
-- Database: sac_event_data
-- Complete schema for SAC Event Data system
-- =============================================

CREATE DATABASE IF NOT EXISTS sac_event_data;
USE sac_event_data;

-- Drop existing tables
DROP TABLE IF EXISTS `user_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `attendees`;
DROP TABLE IF EXISTS `summary`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `provinces`;
DROP TABLE IF EXISTS `ims_user`;

-- =============================================
-- Table: ims_user (ผู้ใช้ระบบ)
-- =============================================
CREATE TABLE `ims_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM;

-- =============================================
-- Table: permissions (รายการสิทธิ์)
-- =============================================
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(50) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE=MyISAM;

-- =============================================
-- Table: user_permissions (สิทธิ์ผู้ใช้)
-- =============================================
CREATE TABLE `user_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permission` (`user_id`, `permission_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM;

-- =============================================
-- Table: events (ข้อมูลงาน Event)
-- =============================================
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- =============================================
-- Table: attendees (ผู้เข้าร่วม/ร้านค้า)
-- =============================================
CREATE TABLE `attendees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `sales_name` varchar(100) DEFAULT NULL,
  `order_no` int DEFAULT NULL,
  `total_no` int DEFAULT NULL,
  `shop_name` varchar(255) NOT NULL,
  `type` enum('shop','user') DEFAULT 'shop',
  `province` varchar(100) DEFAULT NULL,
  `note` text,
  `participants_before` int DEFAULT '0',
  `participants_after` int DEFAULT '0',
  `reserve_room` tinyint(1) DEFAULT '0',
  `used_room` tinyint(1) DEFAULT '0',
  `tire_40_before` int DEFAULT '0',
  `tire_40_after` int DEFAULT '0',
  `tire_80_before` int DEFAULT '0',
  `tire_80_after` int DEFAULT '0',
  `tire_120_before` int DEFAULT '0',
  `tire_120_after` int DEFAULT '0',
  `tire_200_before` int DEFAULT '0',
  `tire_200_after` int DEFAULT '0',
  `tire_300_before` int DEFAULT '0',
  `tire_300_after` int DEFAULT '0',
  `tire_600_before` int DEFAULT '0',
  `tire_600_after` int DEFAULT '0',
  `room_att` int DEFAULT '0',
  `room_att_after` int DEFAULT '0',
  `ship_att` int DEFAULT NULL,
  `ship_att_after` int DEFAULT '0',
  `night_att` int DEFAULT '0',
  `night_att_after` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM;

-- =============================================
-- Table: summary (สรุปข้อมูล - 1 row ต่อ event)
-- =============================================
CREATE TABLE `summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `total_shops` int DEFAULT '0',
  `shops_came` int DEFAULT '0',
  `total_participants_before` int DEFAULT '0',
  `total_participants_after` int DEFAULT '0',
  `total_reserve_room` int DEFAULT '0',
  `total_used_room` int DEFAULT '0',
  `total_tire_40_before` int DEFAULT '0',
  `total_tire_40_after` int DEFAULT '0',
  `total_tire_80_before` int DEFAULT '0',
  `total_tire_80_after` int DEFAULT '0',
  `total_tire_120_before` int DEFAULT '0',
  `total_tire_120_after` int DEFAULT '0',
  `total_tire_200_before` int DEFAULT '0',
  `total_tire_200_after` int DEFAULT '0',
  `total_tire_300_before` int DEFAULT '0',
  `total_tire_300_after` int DEFAULT '0',
  `total_tire_600_before` int DEFAULT '0',
  `total_tire_600_after` int DEFAULT '0',
  `total_tire_before` int DEFAULT '0',
  `total_tire_after` int DEFAULT '0',
  `total_room_att` int DEFAULT '0',
  `total_room_att_after` int DEFAULT '0',
  `total_ship_att` int DEFAULT '0',
  `total_ship_att_after` int DEFAULT '0',
  `total_night_att` int DEFAULT '0',
  `total_night_att_after` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM;

-- =============================================
-- Table: provinces (จังหวัดไทย)
-- =============================================
CREATE TABLE `provinces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `province_name` (`province_name`)
) ENGINE=InnoDB;

-- =============================================
-- Seed: permissions (10 system permissions)
-- =============================================
INSERT IGNORE INTO `permissions` (`permission_key`, `permission_name`, `description`, `is_system`) VALUES
('main', 'หน้าหลักบันทึกข้อมูล', 'หน้า Record/แก้ไข/ลบ ข้อมูลผู้เข้าร่วมงาน', 1),
('dashboard', 'Dashboard สรุปสถิติ', 'ดู Dashboard สรุปยอดรวม สถิติต่างๆ', 0),
('dashboard_graph', 'Dashboard กราฟ', 'ดู Dashboard แบบกราฟเปรียบเทียบ', 0),
('dashboard_by_sales', 'Dashboard แยกตามเซลส์', 'ดู Dashboard รายละเอียดแยกตามเซลส์', 0),
('manage_event', 'จัดการ Event', 'สร้าง/แก้ไข/ลบ งาน Event', 0),
('manage_user', 'จัดการผู้ใช้', 'เพิ่ม/ลบ ผู้ใช้ระบบ', 0),
('manage_permission', 'จัดการสิทธิ์', 'กำหนดสิทธิ์การเข้าถึงให้ผู้ใช้', 0),
('export_excel', 'Export Excel (.xls)', 'Export ข้อมูลเป็นไฟล์ Excel', 0),
('export_csv', 'Export CSV', 'Export ข้อมูลเป็นไฟล์ CSV', 0),
('import_csv', 'Import CSV', 'Import ข้อมูลจากไฟล์ CSV', 0);

-- =============================================
-- Seed: provinces (77 provinces of Thailand)
-- =============================================
INSERT INTO `provinces` (`province_name`) VALUES
('กรุงเทพมหานคร'), ('สมุทรปราการ'), ('นนทบุรี'), ('ปทุมธานี'),
('พระนครศรีอยุธยา'), ('อ่างทอง'), ('ลพบุรี'), ('สิงห์บุรี'),
('ชัยนาท'), ('สระบุรี'), ('ชลบุรี'), ('ระยอง'),
('จันทบุรี'), ('ตราด'), ('ฉะเชิงเทรา'), ('ปราจีนบุรี'),
('นครนายก'), ('สระแก้ว'), ('นครราชสีมา'), ('บุรีรัมย์'),
('สุรินทร์'), ('ศรีสะเกษ'), ('อุบลราชธานี'), ('ยโสธร'),
('ชัยภูมิ'), ('อำนาจเจริญ'), ('หนองบัวลำภู'), ('อุดรธานี'),
('เลย'), ('หนองคาย'), ('มหาสารคาม'), ('ร้อยเอ็ด'),
('กาฬสินธุ์'), ('สกลนคร'), ('นครพนม'), ('มุกดาหาร'),
('เชียงใหม่'), ('ลำพูน'), ('ลำปาง'), ('อุตรดิตถ์'),
('แพร่'), ('น่าน'), ('พะเยา'), ('เชียงราย'),
('แม่ฮ่องสอน'), ('นครสวรรค์'), ('อุทัยธานี'), ('กำแพงเพชร'),
('ตาก'), ('สุโขทัย'), ('พิษณุโลก'), ('พิจิตร'),
('เพชรบูรณ์'), ('ราชบุรี'), ('กาญจนบุรี'), ('สุพรรณบุรี'),
('นครปฐม'), ('สมุทรสาคร'), ('สมุทรสงคราม'), ('เพชรบุรี'),
('ประจวบคีรีขันธ์'), ('นครศรีธรรมราช'), ('กระบี่'), ('พังงา'),
('ภูเก็ต'), ('สุราษฎร์ธานี'), ('ระนอง'), ('ชุมพร'),
('สงขลา'), ('สตูล'), ('ตรัง'), ('พัทลุง'),
('ปัตตานี'), ('ยะลา'), ('นราธิวาส'), ('บึงกาฬ');

-- =============================================
-- Seed: default event
-- =============================================
INSERT INTO `events` (`event_name`) VALUES ('งาน Event');
INSERT INTO `summary` (`event_id`) VALUES (1);
