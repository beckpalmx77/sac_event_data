-- =============================================
-- Database: sac_event_data
-- =============================================

CREATE DATABASE IF NOT EXISTS sac_event_data;
USE sac_event_data;

-- Drop existing tables
DROP TABLE IF EXISTS attendees;
DROP TABLE IF EXISTS summary;
DROP TABLE IF EXISTS events;

-- =============================================
-- Table: events (ข้อมูลงาน/เซลส์)
-- =============================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Table: attendees (ข้อมูลผู้เข้าร่วม/ร้านค้า)
-- =============================================
CREATE TABLE attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    sales_name VARCHAR(100),
    order_no INT,
    total_no INT,
    shop_name VARCHAR(255) NOT NULL,
    type ENUM('shop', 'user') DEFAULT 'shop',
    province VARCHAR(100),
    note TEXT,
    participants_before INT DEFAULT 0,
    participants_after INT DEFAULT 0,
    reserve_room TINYINT(1) DEFAULT 0,
    used_room TINYINT(1) DEFAULT 0,
    tire_40_before INT DEFAULT 0,
    tire_40_after INT DEFAULT 0,
    tire_80_before INT DEFAULT 0,
    tire_80_after INT DEFAULT 0,
    tire_120_before INT DEFAULT 0,
    tire_120_after INT DEFAULT 0,
    tire_200_before INT DEFAULT 0,
    tire_200_after INT DEFAULT 0,
    tire_300_before INT DEFAULT 0,
    tire_300_after INT DEFAULT 0,
    tire_600_before INT DEFAULT 0,
    tire_600_after INT DEFAULT 0,
    room_att INT DEFAULT 0,
    ship_att INT DEFAULT 0,
    night_att INT DEFAULT 0,
    room_att_after INT DEFAULT 0,
    ship_att_after INT DEFAULT 0,
    night_att_after INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- =============================================
-- Table: summary (สรุปข้อมูล - Real-time)
-- =============================================
CREATE TABLE summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    total_shops INT DEFAULT 0,
    total_participants_before INT DEFAULT 0,
    total_participants_after INT DEFAULT 0,
    total_reserve_room INT DEFAULT 0,
    total_used_room INT DEFAULT 0,
    total_tire_40_before INT DEFAULT 0,
    total_tire_40_after INT DEFAULT 0,
    total_tire_80_before INT DEFAULT 0,
    total_tire_80_after INT DEFAULT 0,
    total_tire_120_before INT DEFAULT 0,
    total_tire_120_after INT DEFAULT 0,
    total_tire_200_before INT DEFAULT 0,
    total_tire_200_after INT DEFAULT 0,
    total_tire_300_before INT DEFAULT 0,
    total_tire_300_after INT DEFAULT 0,
    total_tire_600_before INT DEFAULT 0,
    total_tire_600_after INT DEFAULT 0,
    total_tire_before INT DEFAULT 0,
    total_tire_after INT DEFAULT 0,
    total_room_att INT DEFAULT 0,
    total_ship_att INT DEFAULT 0,
    total_night_att INT DEFAULT 0,
    total_room_att_after INT DEFAULT 0,
    total_ship_att_after INT DEFAULT 0,
    total_night_att_after INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- =============================================
-- Table: provinces (จังหวัด)
-- =============================================
CREATE TABLE provinces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_th VARCHAR(100) NOT NULL,
    name_en VARCHAR(100)
);

-- Insert all 77 provinces of Thailand
INSERT INTO provinces (name_th, name_en) VALUES
('กรุงเทพมหานคร', 'Bangkok'),
('เชียงใหม่', 'Chiang Mai'),
('เชียงราย', 'Chiang Rai'),
('น่าน', 'Nan'),
('พะเยา', 'Phayao'),
('แพร่', 'Phrae'),
('ลำปาง', 'Lampang'),
('ลำพูน', 'Lamphun'),
('อุตรดิตถ์', 'Uttaradit'),
('ตาก', 'Tak'),
('สุโขทัย', 'Sukhothai'),
('พิษณุโลก', 'Phitsanulok'),
('พระนครศรีอยุธยา', 'Phra Nakhon Si Ayutthaya'),
('อ่างทอง', 'Ang Thong'),
('ลพบุรี', 'Lop Buri'),
('สิงห์บุรี', 'Sing Buri'),
('ชัยนาท', 'Chai Nat'),
('สระบุรี', 'Sara Buri'),
('ชลบุรี', 'Chon Buri'),
('ระยอง', 'Rayong'),
('จันทบุรี', 'Chanthaburi'),
('ตราด', 'Trat'),
('ฉะเชิงเทรา', 'Chachoengsao'),
('ปราจีนบุรี', 'Prachin Buri'),
('นครนายก', 'Nakhon Nayok'),
('สระแก้ว', 'Sa Kaeo'),
('บุรีรัมย์', 'Buri Ram'),
('อุบลราชธานี', 'Ubon Ratchathani'),
('ศรีสะเกษ', 'Si Sa Ket'),
('ยโสธร', 'Yasothon'),
('อำนาจเจริญ', 'Amnat Charoei'),
('หนองบัวลำภู', 'Nong Bua Lam Phu'),
('ขอนแก่น', 'Khon Kaen'),
('หนองคาย', 'Nong Khai'),
('มหาสารคาม', 'Maha Sarakham'),
('ร้อยเอ็ด', 'Roi Et'),
('กาฬสินธุ์', 'Kalasin'),
('สกลนคร', 'Sakon Nakhon'),
('นครพนม', 'Nakhon Phanom'),
('มุกดาหาร', 'Mukdahan'),
('เคลื่อน', 'Kalasin'),
('อุดรธานี', 'Udon Thani'),
('เลย', 'Loei'),
('หนองคาย', 'Nong Khai'),
('สว่างแดนดิน', 'Sawang Daen Din'),
('นครศรีธรรมราช', 'Nakhon Si Thammarat'),
('พัทลุง', 'Phatthalung'),
('สตูล', 'Satun'),
('ตรัง', 'Trang'),
('ภูเก็ต', 'Phuket'),
('กระบี่', 'Krabi'),
('นครศรีธรรมราช', 'Nakhon Si Thammarat'),
('ประจวบคีรีขันธ์', 'Prachuap Khiri Khan'),
('ชุมพร', 'Chumphon'),
('สุราษฎร์ธานี', 'Surat Thani'),
('ระนอง', 'Ranong'),
('พระนครศรีอยุธยา', 'Phra Nakhon Si Ayutthaya'),
('ปราจีนบุรี', 'Prachin Buri'),
('สระแก้ว', 'Sa Kaeo'),
('นครนายก', 'Nakhon Nayok'),
('ลพบุรี', 'Lop Buri'),
('สิงห์บุรี', 'Sing Buri'),
('อ่างทอง', 'Ang Thong'),
('พิจิตร', 'Phichit'),
('นครสวรรค์', 'Nakhon Sawan'),
('อุทัยธานี', 'Uthai Thani'),
('กำแพงเพชร', 'Kamphaeng Phet'),
('เพชรบูรณ์', 'Phetchabun'),
('ลำปาง', 'Lampang'),
('แม่ฮ่องสอน', 'Mae Hong Son'),
('นครศรีธรรมราช', 'Nakhon Si Thammarat'),
('ประจวบคีรีขันธ์', 'Prachuap Khiri Khan'),
('เพชรบุรี', 'Phetchaburi'),
('ราชบุรี', 'Ratchaburi'),
('กาญจนบุรี', 'Kanchanaburi'),
('สุพรรณบุรี', 'Suphan Buri'),
('นครปฐม', 'Nakhon Pathom'),
('สมุทรสาคร', 'Samut Sakhon'),
('สมุทรปรากร', 'Samut Prakan'),
('ปทุมธานี', 'Pathum Thani'),
('เทียบ', 'Nonthaburi');

-- Insert default event
INSERT INTO events (event_name) VALUES ('งาน Event');
INSERT INTO summary (event_id) VALUES (1);