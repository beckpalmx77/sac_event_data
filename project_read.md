# SAC Event Data Management System

## ภาพรวมระบบ (System Overview)

ระบบบริหารจัดการข้อมูลงาน Event สำหรับธุรกิจจำหน่ายยางรถยนต์ โดยเก็บข้อมูลร้านค้าและผู้เข้าร่วมงาน พร้อมสรุปสถิติผ่าน Dashboard

---

## โครงสร้างฐานข้อมูล (Database Structure)

### ตาราง `events` - ข้อมูลงาน Event

| ฟิลด์ | ประเภท | คำอธิบาย |
|--------|---------|---------|
| id | INT | Primary Key |
| event_name | VARCHAR(100) | ชื่องาน |
| event_date | DATE | วันที่จัดงาน |
| created_at | TIMESTAMP | วันที่สร้าง |

### ตาราง `attendees` - ข้อมูลผู้เข้าร่วม/ร้านค้า

| ฟิลด์ | ประเภท | คำอธิบาย |
|--------|---------|---------|
| id | INT | Primary Key |
| event_id | INT | Foreign Key → events |
| sales_name | VARCHAR(100) | ชื่อเซลส์ |
| order_no | INT | ลำดับในเซลส์ |
| total_no | INT | ลำดับรวม |
| shop_name | VARCHAR(255) | ชื่อร้าน/ผู้เข้าร่วม |
| type | ENUM('shop','user') | ประเภท: ร้านค้า/ผู้ใช้ |
| province | VARCHAR(100) | จังหวัด |
| note | TEXT | หมายเหตุ |
| participants_before | INT | จำนวนคน (ลงทะเบียน) |
| participants_after | INT | จำนวนคน (มาจริง) |
| reserve_room | INT | จองห้องพัก |
| used_room | INT | ใช้ห้องจริง |
| tire_40_before ~ tire_600_before | INT | จองยาง (ลงทะเบียน) |
| tire_40_after ~ tire_600_after | INT | จองยาง (มาจริง) |
| room_att | INT | ห้องพัก (ลงทะเบียน) |
| ship_att | INT | ล่องเรือ (ลงทะเบียน) |
| night_att | INT | งานเลี้ยง (ลงทะเบียน) |
| room_att_after | INT | ห้องพัก (มาจริง) |
| ship_att_after | INT | ล่องเรือ (มาจริง) |
| night_att_after | INT | งานเลี้ยง (มาจริง) |
| created_at | TIMESTAMP | วันที่สร้าง |

### ตาราง `summary` - สรุปข้อมูล (Real-time)

| ฟิลด์ | ประเภท | คำอธิบาย |
|--------|---------|---------|
| id | INT | Primary Key |
| event_id | INT | Foreign Key → events |
| total_shops | INT | จำนวนร้าน/ผู้เข้าร่วม |
| total_participants_before | INT | คน (ลงทะเบียน) |
| total_participants_after | INT | คน (มาจริง) |
| total_reserve_room | INT | จองห้องพัก |
| total_used_room | INT | ใช้ห้องจริง |
| total_tire_40_before ~ tire_600_before | INT | จองยางแต่ละขนาด |
| total_tire_40_after ~ tire_600_after | INT | จองยางจริงแต่ละขนาด |
| total_room_att | INT | ห้องพัก (ลงทะเบียน) |
| total_ship_att | INT | ล่องเรือ (ลงทะเบียน) |
| total_night_att | INT | งานเลี้ยง (ลงทะเบียน) |
| total_room_att_after | INT | ห้องพัก (มาจริง) |
| total_ship_att_after | INT | ล่องเรือ (มาจริง) |
| total_night_att_after | INT | งานเลี้ยง (มาจริง) |
| updated_at | TIMESTAMP | อัปเดตล่าสุด |

### ตาราง `provinces` - ข้อมูลจังหวัดไทย

---

## โครงสร้างไฟล์ (File Structure)

| ไฟล์ | คำอธิบาย |
|------|---------|
| login.php | หน้าเข้าสู่ระบบ |
| select_event.php | เลือกงาน Event |
| index.php | หน้าหลัก - บันทึกข้อมูลผู้เข้าร่วม |
| dashboard.php | Dashboard - สรุปสถิติ |
| manage_event.php | จัดการงาน Event (Admin) |
| manage_user.php | จัดการผู้ใช้ (Admin) |
| change_password.php | เปลี่ยนรหัสผ่าน |
| api.php | API หลักสำหรับเพิ่ม/แก้ไข/ลบ/ดึงข้อมูล |
| config/connect_db.php | เชื่อมต่อฐานข้อมูล |
| database.sql | SQL สร้างฐานข้อมูล |

---

## การทำงานของระบบ (Workflow)

### 1. การเข้าสู่ระบบ
```
login.php
  ↓ ตรวจสอบ username/password
  ↓ สร้าง Session
  ↓ redirect → select_event.php
```

### 2. การเลือก/สร้างงาน
```
select_event.php / manage_event.php
  - เลือก Event ที่มีอยู่
  - สร้าง Event ใหม่ (มี event_date)
  - แก้ไข/ลบ Event (Admin)
  ↓ redirect → index.php
```

### 3. การบันทึกข้อมูล
```
index.php (Frontend)
  ↓ JavaScript: saveData()
api.php → addAttendee()
  ↓ INSERT ลง attendees
  ↓ updateSummary(event_id) → UPDATE summary
  ↓ ส่ง response กลับ
```

### 4. การแก้ไข/ลบข้อมูล
```
index.php → editItem() / deleteItem()
api.php → updateAttendee() / deleteAttendee()
  ↓ UPDATE/DELETE ลง attendees
  ↓ updateSummary(event_id) → UPDATE summary
```

### 5. Dashboard - สรุปสถิติ
```
dashboard.php
  ↓ loadDashboardSummary()
api.php → get_dashboard_summary()
  ↓ SELECT SUM() FROM attendees (คำนวณใหม่ทุกครั้ง)
  ↓ saveSummaryToDB() → UPDATE summary
  ↓ return JSON → แสดงผล
```

---

## การคำนวณ Summary

### จังหวะที่คำนวณและบันทึก

| จังหวะ | ฟังก์ชัน | สถานะ |
|--------|----------|-------|
| เพิ่มผู้เข้าร่วม | `addAttendee()` → `updateSummary()` | ✅ บันทึก |
| แก้ไขผู้เข้าร่วม | `updateAttendee()` → `updateSummary()` | ✅ บันทึก |
| ลบผู้เข้าร่วม | `deleteAttendee()` → `updateSummary()` | ✅ บันทึก |
| เปิด Dashboard | `get_dashboard_summary()` → `saveSummaryToDB()` | ✅ บันทึก |

### ฟิลด์ที่ถูกคำนวณ

```sql
-- คำนวณจาก attendees
SELECT 
  COUNT(*) as total_shops,
  SUM(participants_before) as total_participants_before,
  SUM(participants_after) as total_participants_after,
  SUM(reserve_room) as total_reserve_room,
  SUM(tire_40_before) + ... + SUM(tire_600_before) as total_tire_before,
  SUM(room_att) as total_room_att,
  SUM(ship_att) as total_ship_att,
  SUM(night_att) as total_night_att,
  ...
FROM attendees WHERE event_id = ?
```

---

## สิทธิ์ผู้ใช้ (Role)

| Role | สิทธิ์ |
|------|--------|
| admin | ทั้งหมด + จัดการ User/Event |
| user | เพิ่ม/แก้ไข/ลบผู้เข้าร่วม, ดู Dashboard |

---

## UI/UX

- ใช้ Bootstrap 5
- ฟอนต์ Kanit
- Card Layout เต็มหน้าจอ
- Logo: `img/logo/logo text-01.png`
- DataTables สำหรับตาราง
- Responsive Design

---

## การติดตั้ง (Installation)

1. สร้าง Database:
```sql
mysql -u root -p < database.sql
```

2. รัน SQL เพิ่มคอลัมน์:
```sql
ALTER TABLE events ADD COLUMN event_date DATE;
ALTER TABLE attendees ADD COLUMN room_att_after INT DEFAULT 0;
ALTER TABLE attendees ADD COLUMN ship_att_after INT DEFAULT 0;
ALTER TABLE attendees ADD COLUMN night_att_after INT DEFAULT 0;
```

3. ตั้งค่า Database ใน `config/connect_db.php`

---

## หมายเหตุ

- Summary จะถูกคำนวณและบันทึกทุกครั้งที่มีการเปลี่ยนแปลงข้อมูล
- Dashboard จะคำนวณใหม่ทุกครั้งที่เปิด และบันทึกลง DB
- `updated_at` ในตาราง summary จะบันทึกเวลาล่าสุดที่อัปเดต