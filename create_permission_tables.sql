-- ====================================
-- Table: permissions
-- รายการสิทธิ์ทั้งหมด (หน้าจอต่างๆ ในระบบ)
-- ====================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `permission_key` VARCHAR(50) NOT NULL,
  `permission_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_system` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE=MyISAM;

-- ====================================
-- Table: user_permissions
-- สิทธิ์ที่กำหนดให้แต่ละ user
-- ====================================
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permission` (`user_id`, `permission_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM;

-- ====================================
-- Seed: permissions
-- ====================================
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

-- ====================================
-- Grant all permissions to existing admins
-- ====================================
INSERT IGNORE INTO `user_permissions` (`user_id`, `permission_id`)
SELECT u.id, p.id
FROM ims_user u
CROSS JOIN permissions p
WHERE u.role = 'admin';
