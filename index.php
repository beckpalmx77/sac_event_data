<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อมูลงาน Event</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .summary-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px; margin-bottom: 15px; }
        .summary-item { text-align: center; padding: 8px; }
        .summary-item .number { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .summary-item .label { font-size: 12px; color: #6c757d; }
        .table-responsive { background: white; border-radius: 10px; padding: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .tire-input { width: 100%; min-width: 60px; }
        
        @media (max-width: 576px) {
            .container { padding: 10px; }
            h2 { font-size: 18px; }
            .btn-lg { font-size: 14px; padding: 8px 16px; }
            .summary-item .number { font-size: 18px; }
            .summary-item .label { font-size: 11px; }
            .modal-dialog { margin: 10px; }
        }
        
        @media (max-width: 768px) {
            .table th, .table td { font-size: 12px; padding: 5px; }
            .btn-sm { font-size: 10px; padding: 3px 6px; }
        }
        
        .table td, .table th { vertical-align: middle; }
        .action-btns .btn { margin: 2px; }
        .province-suggestion { padding: 8px 12px; cursor: pointer; }
        .province-suggestion:hover { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="container-fluid py-3 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-center">📋 บันทึกข้อมูลงาน Event</h2>
            <div>
                <span class="me-2 text-muted"><?= $_SESSION['full_name'] ?? '' ?></span>
                <a href="dashboard.php" target="_blank" class="btn btn-info">📊 Dashboard</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="manage_user.php" class="btn btn-warning">👥 จัดการผู้ใช้งานระบบ</a>
                <?php endif; ?>
                <a href="change_password.php" class="btn btn-outline-warning">🔑 เปลี่ยนรหัส</a>
                <a href="logout.php" class="btn btn-outline-danger">ออกจากระบบ</a>
            </div>
        </div>
        
        <div class="row summary-card" id="summarySection">
            <div class="col-12 mb-2"><strong>🏪 ร้านค้า</strong></div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalShopsShop">0</div>
                <div class="label">ร้านค้า</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalParticipantsBeforeShop">0</div>
                <div class="label">คน (ลงทะเบียน)</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalParticipantsAfterShop">0</div>
                <div class="label">คน (มาจริง)</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalNotCameShop">0</div>
                <div class="label">ยังไม่มา</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalPercentShop">0%</div>
                <div class="label">% มาจริง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalNotCamePercentShop">0%</div>
                <div class="label">% ยังไม่มา</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalUseRoom">0</div>
                <div class="label">จองห้องพัก</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalUsedRoom">0</div>
                <div class="label">ใช้ห้องจริง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTireShop">0</div>
                <div class="label">จองยาง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTireRealShop">0</div>
                <div class="label">จองยางจริง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTirePercentShop">0%</div>
                <div class="label">% จองยางจริง</div>
            </div>
        </div>
        <div class="row summary-card" id="summarySectionUser">
            <div class="col-12 mb-2"><strong>👤 ผู้ใช้</strong></div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalShopsUser">0</div>
                <div class="label">ผู้ใช้</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalParticipantsBeforeUser">0</div>
                <div class="label">คน (ลงทะเบียน)</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalParticipantsAfterUser">0</div>
                <div class="label">คน (มาจริง)</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalNotCameUser">0</div>
                <div class="label">ยังไม่มา</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalPercentUser">0%</div>
                <div class="label">% มาจริง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalNotCamePercentUser">0%</div>
                <div class="label">% ยังไม่มา</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTireUser">0</div>
                <div class="label">จองยาง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTireRealUser">0</div>
                <div class="label">จองยางจริง</div>
            </div>
            <div class="col-md-2 col-6 summary-item">
                <div class="number" id="totalTirePercentUser">0%</div>
                <div class="label">% จองยางจริง</div>
            </div>
        </div>

        <button class="btn btn-primary btn-lg mb-3" data-bs-toggle="modal" data-bs-target="#addModal" onclick="openModal()">
            + เพิ่มรายการ
        </button>
        <button class="btn btn-success btn-lg mb-3 ms-2" onclick="exportCSV('shop')">
            📥 Export ร้านค้า
        </button>
        <button class="btn btn-success btn-lg mb-3 ms-2" onclick="exportCSV('user')">
            📥 Export ผู้ใช้
        </button>
        <button class="btn btn-warning btn-lg mb-3 ms-2" onclick="window.open('export_excel.php?event_id=' + eventId, '_blank')">
            📊 Export Excel
        </button>

        <ul class="nav nav-tabs mb-3" id="dataTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="shop-tab" data-bs-toggle="tab" data-bs-target="#shop" type="button">🏪 ร้านค้า</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button">👤 ผู้ใช้</button>
            </li>
        </ul>

        <div class="tab-content" id="dataTabContent">
            <div class="tab-pane fade show active" id="shop" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableShop" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>ลำดับ</th>
                                <th>เซลส์</th>
                                <th>ลำดับในเซลส์</th>
                                <th>รายชื่อ</th>
                                <th>ประเภท</th>
                                <th>จังหวัด</th>
                                <th>หมายเหตุ</th>
<th>คน (ก่อน)</th>
                                <th>คน (จริง)</th>
                                <th>จองห้องพัก</th>
                                <th>ใช้ห้องจริง</th>
                                <th>จอง 40</th>
                                <th>จอง 80</th>
                                <th>จอง 120</th>
                                <th>จอง 200</th>
                                <th>จอง 300</th>
                                <th>จอง 600</th>
                                <th>จองจริง</th>
                                <th>ห้องพัก</th>
                                <th>ล่องเรือ</th>
                                <th>งานเลี้ยงเย็น</th>
                                <th>แก้ไข</th>
                                <th>ลบ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="user" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableUser" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>ลำดับ</th>
                                <th>เซลส์</th>
                                <th>ลำดับในเซลส์</th>
                                <th>รายชื่อ</th>
                                <th>ประเภท</th>
                                <th>จังหวัด</th>
                                <th>หมายเหตุ</th>
                                <th>คน (ก่อน)</th>
                                <th>คน (จริง)</th>
                                <th>จอง 40</th>
                                <th>จอง 80</th>
                                <th>จอง 120</th>
                                <th>จอง 200</th>
                                <th>จอง 300</th>
                                <th>จอง 600</th>
                                <th>จองจริง</th>
                                <th>ห้องพัก</th>
                                <th>ล่องเรือ</th>
                                <th>งานเลี้ยงเย็น</th>
                                <th>แก้ไข</th>
                                <th>ลบ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="table-responsive d-none">
            <table class="table table-bordered table-hover" id="dataTable" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>ลำดับ</th>
                        <th>เซลส์</th>
                        <th>ลำดับในเซลส์</th>
                        <th>รายชื่อ</th>
                        <th>ประเภท</th>
                        <th>จังหวัด</th>
                        <th>หมายเหตุ</th>
                                <th>คน (ก่อน)</th>
                                <th>คน (จริง)</th>
                                <th>จอง 40</th>
                        <th>จอง 80</th>
                        <th>จอง 120</th>
                        <th>จอง 200</th>
                        <th>จอง 300</th>
                        <th>จอง 600</th>
                        <th>จองจริง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">เซลส์</label>
                                <input type="text" class="form-control" id="sales_name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ลำดับในเซลส์</label>
                                <input type="number" class="form-control" id="order_no" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ลำดับรวม</label>
                                <input type="number" class="form-control" id="total_no" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รายชื่อ</label>
                            <input type="text" class="form-control" id="shop_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ประเภท</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_shop" value="shop" checked>
                                    <label class="form-check-label" for="type_shop">🏪 ร้านค้า</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_user" value="user">
                                    <label class="form-check-label" for="type_user">👤 ผู้ใช้</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label">จังหวัด</label>
                            <input type="text" class="form-control" id="province" oninput="showProvinceSuggestions(this)" onblur="setTimeout(()=>document.getElementById('provinceSuggestions').style.display='none',200)" required>
                            <div id="provinceSuggestions" class="position-absolute bg-white border rounded shadow-sm" style="display:none;z-index:1000;max-height:200px;overflow-y:auto;width:calc(100% - 12px);left:12px;top:100%;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <input type="text" class="form-control" id="note">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">จำนวนคน (สำรวจก่อน)</label>
                                <input type="number" class="form-control" id="participants_before" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">จำนวนคน (มาจริง)</label>
                                <input type="number" class="form-control" id="participants_after" value="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 d-flex align-items-center" id="useRoomContainer">
                                <div class="row">
                                <div class="col-6">
                                    <label class="form-label">จองห้องพัก</label>
                                    <input type="number" class="form-control" id="reserve_room" value="0" min="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">ใช้ห้องจริง</label>
                                    <input type="number" class="form-control" id="used_room" value="0" min="0">
                                </div>
                            </div>
                            </div>
                        </div>
                        <h6 class="mb-2">ข้อมูลกิจกรรม</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">จำนวนห้องพัก (room_att)</label>
                                <input type="number" class="form-control" id="room_att" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จำนวนล่องเรือ (ship_att)</label>
                                <input type="number" class="form-control" id="ship_att" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จำนวนงานเลี้ยงเย็น (night_attend)</label>
                                <input type="number" class="form-control" id="night_attend" value="0" min="0">
                            </div>
                        </div>
                        <h6 class="mb-2">จำนวนจองจอง (แพค) - สำรวจก่อน</h6>
                        <div class="row mb-3">
                            <div class="col">
                                <label>40</label>
                                <input type="number" class="form-control tire-input" id="tire_40_before" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>80</label>
                                <input type="number" class="form-control tire-input" id="tire_80_before" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>120</label>
                                <input type="number" class="form-control tire-input" id="tire_120_before" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>200</label>
                                <input type="number" class="form-control tire-input" id="tire_200_before" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>300</label>
                                <input type="number" class="form-control tire-input" id="tire_300_before" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>600</label>
                                <input type="number" class="form-control tire-input" id="tire_600_before" value="0" min="0">
                            </div>
                        </div>
                        <h6 class="mb-2">จำนวนจองจอง (แพค) - มาจริง</h6>
                        <div class="row">
                            <div class="col">
                                <label>40</label>
                                <input type="number" class="form-control tire-input" id="tire_40_after" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>80</label>
                                <input type="number" class="form-control tire-input" id="tire_80_after" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>120</label>
                                <input type="number" class="form-control tire-input" id="tire_120_after" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>200</label>
                                <input type="number" class="form-control tire-input" id="tire_200_after" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>300</label>
                                <input type="number" class="form-control tire-input" id="tire_300_after" value="0" min="0">
                            </div>
                            <div class="col">
                                <label>600</label>
                                <input type="number" class="form-control tire-input" id="tire_600_after" value="0" min="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveData()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let eventId = 0;
        let modal;
        let tableShop, tableUser;

        const shopColumns = [
            { title: 'ลำดับ', data: 'total_no' },
            { title: 'เซลส์', data: 'sales_name' },
            { title: 'ลำดับในเซลส์', data: 'order_no' },
            { title: 'รายชื่อ', data: 'shop_name' },
            { title: 'ประเภท', data: 'type', render: (data) => data === 'user' ? '👤 ผู้ใช้' : '🏪 ร้านค้า' },
            { title: 'จังหวัด', data: 'province' },
            { title: 'หมายเหตุ', data: 'note' },
            { title: 'คน (ก่อน)', data: 'participants_before' },
            { title: 'คน (จริง)', data: 'participants_after' },
            { title: 'จองห้องพัก', data: 'reserve_room', render: (data) => data > 0 ? data : '' },
            { title: 'ใช้ห้องจริง', data: 'used_room', render: (data) => data > 0 ? data : '' },
            { title: 'จอง 40', data: 'tire_40_before' },
            { title: 'จอง 80', data: 'tire_80_before' },
            { title: 'จอง 120', data: 'tire_120_before' },
            { title: 'จอง 200', data: 'tire_200_before' },
            { title: 'จอง 300', data: 'tire_300_before' },
            { title: 'จอง 600', data: 'tire_600_before' },
            { title: 'จองจริง', data: null, render: (data) => {
                return (parseInt(data.tire_40_after) || 0) + (parseInt(data.tire_80_after) || 0) + 
                       (parseInt(data.tire_120_after) || 0) + (parseInt(data.tire_200_after) || 0) + 
                       (parseInt(data.tire_300_after) || 0) + (parseInt(data.tire_600_after) || 0);
            }},
            { title: 'ห้องพัก', data: 'room_att', render: (data) => data > 0 ? data : '' },
            { title: 'ล่องเรือ', data: 'ship_att', render: (data) => data > 0 ? data : '' },
            { title: 'งานเลี้ยงเย็น', data: 'night_att', render: (data) => data > 0 ? data : '' },
            { title: 'แก้ไข', data: 'id', render: (data) => `<button class="btn btn-sm btn-warning" onclick="editItem(${data})">✏️</button>` },
            { title: 'ลบ', data: 'id', render: (data) => `<button class="btn btn-sm btn-danger" onclick="deleteItem(${data})">🗑️</button>` }
        ];

        const userColumns = [
            { title: 'ลำดับ', data: 'total_no' },
            { title: 'เซลส์', data: 'sales_name' },
            { title: 'ลำดับในเซลส์', data: 'order_no' },
            { title: 'รายชื่อ', data: 'shop_name' },
            { title: 'ประเภท', data: 'type', render: (data) => data === 'user' ? '👤 ผู้ใช้' : '🏪 ร้านค้า' },
            { title: 'จังหวัด', data: 'province' },
            { title: 'หมายเหตุ', data: 'note' },
            { title: 'คน (ลงทะเบียน)', data: 'participants_before' },
            { title: 'คน (มาจริง)', data: 'participants_after' },
            { title: 'จอง 40', data: 'tire_40_before' },
            { title: 'จอง 80', data: 'tire_80_before' },
            { title: 'จอง 120', data: 'tire_120_before' },
            { title: 'จอง 200', data: 'tire_200_before' },
            { title: 'จอง 300', data: 'tire_300_before' },
            { title: 'จอง 600', data: 'tire_600_before' },
            { title: 'จองจริง', data: null, render: (data) => {
                return (parseInt(data.tire_40_after) || 0) + (parseInt(data.tire_80_after) || 0) + 
                       (parseInt(data.tire_120_after) || 0) + (parseInt(data.tire_200_after) || 0) + 
                       (parseInt(data.tire_300_after) || 0) + (parseInt(data.tire_600_after) || 0);
            }},
            { title: 'ห้องพัก', data: 'room_att', render: (data) => data > 0 ? data : '' },
            { title: 'ล่องเรือ', data: 'ship_att', render: (data) => data > 0 ? data : '' },
            { title: 'งานเลี้ยงเย็น', data: 'night_att', render: (data) => data > 0 ? data : '' },
            { title: 'แก้ไข', data: 'id', render: (data) => `<button class="btn btn-sm btn-warning" onclick="editItem(${data})">✏️</button>` },
            { title: 'ลบ', data: 'id', render: (data) => `<button class="btn btn-sm btn-danger" onclick="deleteItem(${data})">🗑️</button>` }
        ];

        document.addEventListener('DOMContentLoaded', function() {
            modal = new bootstrap.Modal(document.getElementById('addModal'));
            initDataTables();
            loadEvent();
            loadProvinces();
            
            document.getElementById('type_shop').addEventListener('change', updateUseRoomVisibility);
            document.getElementById('type_user').addEventListener('change', updateUseRoomVisibility);
        });

        let allProvinces = [];
        
        function loadProvinces() {
            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_provinces'
            })
            .then(res => res.json())
            .then(data => {
                allProvinces = data;
            });
        }

        function showProvinceSuggestions(input) {
            const container = document.getElementById('provinceSuggestions');
            const value = input.value.toLowerCase().trim();
            
            if (value.length === 0) {
                container.style.display = 'none';
                return;
            }
            
            const matches = allProvinces.filter(p => p.toLowerCase().includes(value));
            
            if (matches.length === 0) {
                container.style.display = 'none';
                return;
            }
            
            container.innerHTML = matches.map(p => `<div class="province-suggestion" onclick="selectProvince('${p}')">${p}</div>`).join('');
            container.style.display = 'block';
        }

        function selectProvince(name) {
            document.getElementById('province').value = name;
            document.getElementById('provinceSuggestions').style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#province') && !e.target.closest('#provinceSuggestions')) {
                document.getElementById('provinceSuggestions').style.display = 'none';
            }
        });

        function initDataTables() {
            const commonOptions = {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                lengthMenu: [[5, 10, 20, 100, -1], [5, 10, 20, 100, 'ทั้งหมด']],
                pageLength: 10,
                ordering: true,
                searching: true,
                processing: false,
                serverSide: false,
                destroy: true
            };

            tableShop = $('#dataTableShop').DataTable({
                ...commonOptions,
                columns: shopColumns,
                order: [[0, 'asc']]
            });

            tableUser = $('#dataTableUser').DataTable({
                ...commonOptions,
                columns: userColumns,
                order: [[0, 'asc']]
            });
        }

        function loadEvent() {
            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_event'
            })
            .then(res => res.json())
            .then(data => {
                console.log('Event:', data);
                eventId = data.id;
                loadAttendees();
                loadSummary();
            })
            .catch(err => console.error('Error loadEvent:', err));
        }

        function loadLastOrder() {
            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_last_order&event_id=' + eventId
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('sales_name').value = data.sales_name || '';
                document.getElementById('order_no').value = data.order_no ? parseInt(data.order_no) + 1 : 1;
                document.getElementById('total_no').value = data.total_no ? parseInt(data.total_no) + 1 : 1;
            });
        }

        function openModal() {
            loadLastOrder();
            document.getElementById('addForm').reset();
            document.getElementById('order_no').value = 1;
            document.getElementById('total_no').value = 1;
            document.getElementById('participants_before').value = 0;
            document.getElementById('participants_after').value = 0;
            
            updateUseRoomVisibility();
        }

        function updateUseRoomVisibility() {
            const type = document.querySelector('input[name="type"]:checked').value;
            document.getElementById('useRoomContainer').style.display = type === 'shop' ? 'flex' : 'none';
            if (type === 'user') {
                document.getElementById('reserve_room').checked = false;
            }
        }

        function loadAttendees() {
            console.log('Loading attendees for event:', eventId);
            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_attendees&event_id=' + eventId
            })
            .then(res => res.json())
            .then(data => {
                console.log('Attendees data:', data);
                
                if (!data || data.length === 0) {
                    console.log('No data found');
                    return;
                }
                
                tableShop.clear().draw();
                tableUser.clear().draw();
                
                const shopData = data.filter(item => item.type === 'shop');
                const userData = data.filter(item => item.type === 'user');
                
                tableShop.rows.add(shopData);
                tableShop.draw();
                
                tableUser.rows.add(userData);
                tableUser.draw();
            });
        }

        function loadSummary() {
            Promise.all([
                fetch('api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=shop'
                }).then(res => res.json()),
                fetch('api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=user'
                }).then(res => res.json())
            ])
            .then(([shopData, userData]) => {
                document.getElementById('totalShopsShop').textContent = shopData.total_shops || 0;
                document.getElementById('totalShopsUser').textContent = userData.total_shops || 0;
                document.getElementById('totalParticipantsBeforeShop').textContent = shopData.total_participants_before || 0;
                document.getElementById('totalParticipantsAfterShop').textContent = shopData.total_participants_after || 0;
                document.getElementById('totalParticipantsBeforeUser').textContent = userData.total_participants_before || 0;
                document.getElementById('totalParticipantsAfterUser').textContent = userData.total_participants_after || 0;
                
                const shopNotCame = (parseInt(shopData.total_participants_before) || 0) - (parseInt(shopData.total_participants_after) || 0);
                const userNotCame = (parseInt(userData.total_participants_before) || 0) - (parseInt(userData.total_participants_after) || 0);
                const shopNotCamePct = shopData.total_participants_before > 0 
                    ? Math.round(shopNotCame / shopData.total_participants_before * 100) 
                    : 0;
                const userNotCamePct = userData.total_participants_before > 0 
                    ? Math.round(userNotCame / userData.total_participants_before * 100) 
                    : 0;
                document.getElementById('totalNotCameShop').textContent = shopNotCame > 0 ? shopNotCame : 0;
                document.getElementById('totalNotCamePercentShop').textContent = shopNotCamePct + '%';
                document.getElementById('totalNotCameUser').textContent = userNotCame > 0 ? userNotCame : 0;
                document.getElementById('totalNotCamePercentUser').textContent = userNotCamePct + '%';
                
                document.getElementById('totalUseRoom').textContent = shopData.total_reserve_room || 0;
                document.getElementById('totalUsedRoom').textContent = shopData.total_used_room || 0;
                
                const shopPct = shopData.total_participants_before > 0 
                    ? Math.round(shopData.total_participants_after / shopData.total_participants_before * 100) 
                    : 0;
                const userPct = userData.total_participants_before > 0 
                    ? Math.round(userData.total_participants_after / userData.total_participants_before * 100) 
                    : 0;
                document.getElementById('totalPercentShop').textContent = shopPct + '%';
                document.getElementById('totalPercentUser').textContent = userPct + '%';
                
                const shopTire = (parseInt(shopData['total_tire_40_before']) || 0) + (parseInt(shopData['total_tire_80_before']) || 0) + 
                               (parseInt(shopData['total_tire_120_before']) || 0) + (parseInt(shopData['total_tire_200_before']) || 0) + 
                               (parseInt(shopData['total_tire_300_before']) || 0) + (parseInt(shopData['total_tire_600_before']) || 0);
                const userTire = (parseInt(userData['total_tire_40_before']) || 0) + (parseInt(userData['total_tire_80_before']) || 0) + 
                               (parseInt(userData['total_tire_120_before']) || 0) + (parseInt(userData['total_tire_200_before']) || 0) + 
                               (parseInt(userData['total_tire_300_before']) || 0) + (parseInt(userData['total_tire_600_before']) || 0);
                document.getElementById('totalTireShop').textContent = shopTire;
                document.getElementById('totalTireUser').textContent = userTire;

                const shopTireReal = (parseInt(shopData['total_tire_40_after']) || 0) + (parseInt(shopData['total_tire_80_after']) || 0) + 
                                    (parseInt(shopData['total_tire_120_after']) || 0) + (parseInt(shopData['total_tire_200_after']) || 0) + 
                                    (parseInt(shopData['total_tire_300_after']) || 0) + (parseInt(shopData['total_tire_600_after']) || 0);
                const userTireReal = (parseInt(userData['total_tire_40_after']) || 0) + (parseInt(userData['total_tire_80_after']) || 0) + 
                                    (parseInt(userData['total_tire_120_after']) || 0) + (parseInt(userData['total_tire_200_after']) || 0) + 
                                    (parseInt(userData['total_tire_300_after']) || 0) + (parseInt(userData['total_tire_600_after']) || 0);
                document.getElementById('totalTireRealShop').textContent = shopTireReal;
                document.getElementById('totalTireRealUser').textContent = userTireReal;

                const shopTirePct = shopTire > 0 ? Math.round(shopTireReal / shopTire * 100) : 0;
                const userTirePct = userTire > 0 ? Math.round(userTireReal / userTire * 100) : 0;
                document.getElementById('totalTirePercentShop').textContent = shopTirePct + '%';
                document.getElementById('totalTirePercentUser').textContent = userTirePct + '%';
            });
        }

        let editingId = 0;

        function editItem(id) {
            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_attendees&event_id=' + eventId
            })
            .then(res => res.json())
            .then(data => {
                const item = data.find(i => i.id == id);
                if (item) {
                    editingId = id;
                    document.getElementById('sales_name').value = item.sales_name || '';
                    document.getElementById('order_no').value = item.order_no || 0;
                    document.getElementById('total_no').value = item.total_no || 0;
                    document.getElementById('shop_name').value = item.shop_name || '';
                    if (item.type === 'user') {
                        document.getElementById('type_user').checked = true;
                    } else {
                        document.getElementById('type_shop').checked = true;
                    }
                    updateUseRoomVisibility();
                    document.getElementById('province').value = item.province || '';
                    document.getElementById('note').value = item.note || '';
                    document.getElementById('participants_before').value = item.participants_before || 0;
                    document.getElementById('participants_after').value = item.participants_after || 0;
                    document.getElementById('reserve_room').value = item.reserve_room || 0;
                    document.getElementById('used_room').value = item.used_room || 0;
                    
                    document.getElementById('tire_40_before').value = item.tire_40_before || 0;
                    document.getElementById('tire_40_after').value = item.tire_40_after || 0;
                    document.getElementById('tire_80_before').value = item.tire_80_before || 0;
                    document.getElementById('tire_80_after').value = item.tire_80_after || 0;
                    document.getElementById('tire_120_before').value = item.tire_120_before || 0;
                    document.getElementById('tire_120_after').value = item.tire_120_after || 0;
                    document.getElementById('tire_200_before').value = item.tire_200_before || 0;
                    document.getElementById('tire_200_after').value = item.tire_200_after || 0;
                    document.getElementById('tire_300_before').value = item.tire_300_before || 0;
                    document.getElementById('tire_300_after').value = item.tire_300_after || 0;
                    document.getElementById('tire_600_before').value = item.tire_600_before || 0;
                    document.getElementById('tire_600_after').value = item.tire_600_after || 0;
                    document.getElementById('room_att').value = item.room_att || 0;
                    document.getElementById('ship_att').value = item.ship_att || 0;
                    document.getElementById('night_attend').value = item.night_attend || 0;
                    
                    document.querySelector('#addModal .modal-title').textContent = 'แก้ไขรายการ';
                    document.querySelector('#addModal .btn-primary').textContent = 'อัปเดต';
                    modal.show();
                }
            });
        }

        function saveData() {
            const formData = new FormData();
            formData.append('action', editingId ? 'update_attendee' : 'add_attendee');
            if (editingId) {
                formData.append('id', editingId);
            }
            formData.append('event_id', eventId);
            formData.append('sales_name', document.getElementById('sales_name').value);
            formData.append('order_no', document.getElementById('order_no').value);
            formData.append('total_no', document.getElementById('total_no').value);
            formData.append('shop_name', document.getElementById('shop_name').value);
            formData.append('type', document.querySelector('input[name="type"]:checked').value);
            formData.append('province', document.getElementById('province').value);
            formData.append('note', document.getElementById('note').value);
            formData.append('participants_before', document.getElementById('participants_before').value);
            formData.append('participants_after', document.getElementById('participants_after').value);
            formData.append('reserve_room', document.getElementById('reserve_room').value || 0);
            formData.append('used_room', document.getElementById('used_room').value || 0);
            formData.append('tire_40_before', document.getElementById('tire_40_before').value);
            formData.append('tire_40_after', document.getElementById('tire_40_after').value);
            formData.append('tire_80_before', document.getElementById('tire_80_before').value);
            formData.append('tire_80_after', document.getElementById('tire_80_after').value);
            formData.append('tire_120_before', document.getElementById('tire_120_before').value);
            formData.append('tire_120_after', document.getElementById('tire_120_after').value);
            formData.append('tire_200_before', document.getElementById('tire_200_before').value);
            formData.append('tire_200_after', document.getElementById('tire_200_after').value);
            formData.append('tire_300_before', document.getElementById('tire_300_before').value);
            formData.append('tire_300_after', document.getElementById('tire_300_after').value);
            formData.append('tire_600_before', document.getElementById('tire_600_before').value);
            formData.append('tire_600_after', document.getElementById('tire_600_after').value);
            formData.append('room_att', document.getElementById('room_att').value || 0);
            formData.append('ship_att', document.getElementById('ship_att').value || 0);
            formData.append('night_attend', document.getElementById('night_attend').value || 0);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    modal.hide();
                    editingId = 0;
                    loadAttendees();
                    loadSummary();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function deleteItem(id) {
            if (confirm('ยืนยันการลบ?')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=delete_attendee&id=' + id
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadAttendees();
                        loadSummary();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
            editingId = 0;
            document.querySelector('#addModal .modal-title').textContent = 'เพิ่มรายการใหม่';
            document.querySelector('#addModal .btn-primary').textContent = 'บันทึก';
        });

        function exportCSV(type) {
            window.open('export.php?type=' + type + '&event_id=' + eventId, '_blank');
        }
    </script>
</body>
</html>