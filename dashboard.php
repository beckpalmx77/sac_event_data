<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard สรุปสถิติ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .stat-card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px; text-align: center; margin-bottom: 15px; }
        .stat-card.primary { border-left: 5px solid #0d6efd; }
        .stat-card.success { border-left: 5px solid #198754; }
        .stat-card.warning { border-left: 5px solid #ffc107; }
        .stat-card.info { border-left: 5px solid #0dcaf0; }
        .stat-number { font-size: 32px; font-weight: bold; color: #212529; }
        .stat-label { font-size: 14px; color: #6c757d; margin-top: 8px; }
        .chart-container { background: white; border-radius: 15px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .province-list { max-height: 300px; overflow-y: auto; }
        
        @media (max-width: 576px) {
            .container { padding: 10px; }
            h2 { font-size: 18px; }
            .stat-number { font-size: 24px; }
            .stat-label { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-3 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📊 Dashboard สรุปสถิติ</h2>
            <a href="index.php" class="btn btn-outline-primary">← กลับหน้าบันทึก</a>
        </div>

        <ul class="nav nav-tabs mb-3" id="dashTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="shop-tab" data-bs-toggle="tab" data-bs-target="#shop" type="button">🏪 ร้านค้า</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button">👤 ผู้ใช้</button>
            </li>
        </ul>

        <div class="tab-content" id="dashTabContent">
            <div class="tab-pane fade show active" id="shop" role="tabpanel">
                <div class="row mb-4" id="summaryShop"></div>
                <div class="row mb-4" id="tireShop"></div>
            </div>
            <div class="tab-pane fade" id="user" role="tabpanel">
                <div class="row mb-4" id="summaryUser"></div>
                <div class="row mb-4" id="tireUser"></div>
            </div>
        </div>

        <div class="chart-container">
            <h5 class="mb-3">📋 รายละเอียดตามเซลส์</h5>
            <table class="table table-bordered table-hover" id="salesTable" style="width:100%"></table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let eventId = 0;
        let allData = [];
        let salesTable;

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        function loadData() {
            fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_event' })
            .then(res => res.json())
            .then(data => {
                eventId = data.id;
                loadAttendees();
                loadDashboardSummary('shop');
                loadDashboardSummary('user');
            });
        }

        function loadDashboardSummary(type) {
            fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=' + type })
            .then(res => res.json())
            .then(data => {
                renderStats(type, data);
            });
        }

        function loadAttendees() {
            fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_attendees&event_id=' + eventId })
            .then(res => res.json())
            .then(data => {
                allData = data;
                initSalesTable(data);
                loadSalesProvinceList('Shop', data.filter(d => d.type === 'shop'));
                loadSalesProvinceList('User', data.filter(d => d.type === 'user'));
                loadTireBySizeTable('Shop', data.filter(d => d.type === 'shop'));
                loadTireBySizeTable('User', data.filter(d => d.type === 'user'));
            });
        }

        function p(count, total) {
            const pct = total > 0 ? Math.round(count / total * 100) : 0;
            return { count, pct };
        }

        function renderStats(type, data) {
            const totalShops = parseInt(data.total_shops) || 0;
            const totalPartBefore = parseInt(data.total_participants_before) || 0;
            const totalPartAfter = parseInt(data.total_participants_after) || 0;
            const tireBefore = parseInt(data.total_tire_40_before) + parseInt(data.total_tire_80_before) + 
                              parseInt(data.total_tire_120_before) + parseInt(data.total_tire_200_before) + 
                              parseInt(data.total_tire_300_before) + parseInt(data.total_tire_600_before);
            const tireAfter = parseInt(data.total_tire_40_after) + parseInt(data.total_tire_80_after) + 
                              parseInt(data.total_tire_120_after) + parseInt(data.total_tire_200_after) + 
                              parseInt(data.total_tire_300_after) + parseInt(data.total_tire_600_after);

            const shopsCame = parseInt(data.shops_came) || 0;
            const shopsNotCame = parseInt(data.shops_not_came) || 0;
            const shopsBookedTire = parseInt(data.shops_booked_tire) || 0;
            const shopsBookedTireCame = parseInt(data.shops_booked_tire_came) || 0;
            const shopsBookedTireNotCame = shopsBookedTire - shopsBookedTireCame;
            const shopsNoTireCame = parseInt(data.shops_no_tire_came) || 0;

            const partsNotCame = totalPartBefore - totalPartAfter;

            const summaryHTML = `
                <div class="col-12">
                    <div class="chart-container">
                        <h5 class="mb-3">📊 สรุป${type === 'Shop' ? 'ร้านค้า' : 'ผู้ใช้'}</h5>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>รายละเอียด</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-center">ร้อยละ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-secondary"><td colspan="3"><strong>📋 ข้อมูลลงทะเบียน</strong></td></tr>
                                <tr><td>ร้านค้าที่ลงทะเบียน</td><td class="text-center">${totalShops.toLocaleString()}</td><td class="text-center">100%</td></tr>
                                <tr><td>จำนวนคนที่ลงทะเบียน</td><td class="text-center">${totalPartBefore.toLocaleString()}</td><td class="text-center">100%</td></tr>
                                <tr class="table-success"><td colspan="3"><strong>✅ มาร่วมงาน</strong></td></tr>
                                <tr><td>ร้านค้าที่มาร่วมงานจริง</td><td class="text-center">${shopsCame.toLocaleString()}</td><td class="text-center">${totalShops > 0 ? Math.round(shopsCame/totalShops*100) : 0}%</td></tr>
                                <tr><td>จำนวนคนที่มาร่วมงานจริง</td><td class="text-center">${totalPartAfter.toLocaleString()}</td><td class="text-center">${totalPartBefore > 0 ? Math.round(totalPartAfter/totalPartBefore*100) : 0}%</td></tr>
                                <tr class="table-danger"><td colspan="3"><strong>❌ ไม่มาร่วมงาน</strong></td></tr>
                                <tr><td>ร้านค้าที่ไม่มาร่วมงาน</td><td class="text-center">${shopsNotCame.toLocaleString()}</td><td class="text-center">${totalShops > 0 ? Math.round(shopsNotCame/totalShops*100) : 0}%</td></tr>
                                <tr><td>จำนวนคนที่ไม่ร่วมงาน</td><td class="text-center">${partsNotCame.toLocaleString()}</td><td class="text-center">${totalPartBefore > 0 ? Math.round(partsNotCame/totalPartBefore*100) : 0}%</td></tr>
                                <tr class="table-warning"><td colspan="3"><strong>🛞 ข้อมูลยาง</strong></td></tr>
                                <tr><td>ยางที่จองก่อน</td><td class="text-center">${tireBefore.toLocaleString()}</td><td class="text-center">100%</td></tr>
                                <tr><td>ยางที่มาจริง</td><td class="text-center">${tireAfter.toLocaleString()}</td><td class="text-center">${tireBefore > 0 ? Math.round(tireAfter/tireBefore*100) : 0}%</td></tr>
                                <tr class="table-info"><td colspan="3"><strong>🏪 ข้อมูลการจองยาง</strong></td></tr>
                                <tr><td>ร้านค้าที่จองยางแล้วมาร่วมงาน</td><td class="text-center">${shopsBookedTireCame.toLocaleString()}</td><td class="text-center">${shopsBookedTire > 0 ? Math.round(shopsBookedTireCame/shopsBookedTire*100) : 0}%</td></tr>
                                <tr><td>ร้านค้าที่จองยางแล้วไม่ร่วมงาน</td><td class="text-center">${shopsBookedTireNotCame.toLocaleString()}</td><td class="text-center">${shopsBookedTire > 0 ? Math.round(shopsBookedTireNotCame/shopsBookedTire*100) : 0}%</td></tr>
                                <tr><td>ร้านค้าที่ไม่จองยางแล้วมาร่วมงาน</td><td class="text-center">${shopsNoTireCame.toLocaleString()}</td><td class="text-center">100%</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            document.getElementById('summary' + type).innerHTML = summaryHTML;
        }

        function loadTireBySizeTable(type, data) {
            const tireSizes = [40, 80, 120, 200, 300, 600];
            const tireBefore = data.reduce((s, i) => s + 
                (parseInt(i.tire_40_before)||0) + (parseInt(i.tire_80_before)||0) + 
                (parseInt(i.tire_120_before)||0) + (parseInt(i.tire_200_before)||0) + 
                (parseInt(i.tire_300_before)||0) + (parseInt(i.tire_600_before)||0), 0);
            const tireAfter = data.reduce((s, i) => s + 
                (parseInt(i.tire_40_after)||0) + (parseInt(i.tire_80_after)||0) + 
                (parseInt(i.tire_120_after)||0) + (parseInt(i.tire_200_after)||0) + 
                (parseInt(i.tire_300_after)||0) + (parseInt(i.tire_600_after)||0), 0);
            const tirePct = tireBefore > 0 ? Math.round(tireAfter/tireBefore*100) : 0;

            let tireRows = tireSizes.map(s => {
                const b = data.reduce((sum, i) => sum + (parseInt(i['tire_'+s+'_before'])||0), 0);
                const a = data.reduce((sum, i) => sum + (parseInt(i['tire_'+s+'_after'])||0), 0);
                return `<tr><td>${s}</td><td>${b}</td><td>${a}</td><td>${b > 0 ? Math.round(a/b*100) : 0}%</td></tr>`;
            }).join('');
            tireRows += `<tr class="table-dark fw-bold"><td>รวม</td><td>${tireBefore}</td><td>${tireAfter}</td><td>${tirePct}%</td></tr>`;

            const tireHTML = `
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🔢 การจองยางแต่ละขนาด</h5>
                        <table class="table table-bordered table-sm"><thead><tr><th>ขนาด</th><th>จองก่อน</th><th>มาจริง</th><th>%</th></tr></thead><tbody>${tireRows}</tbody></table>
                    </div>
                </div>
            `;
            document.getElementById('tire' + type).innerHTML = tireHTML;
        }

        function loadSalesProvinceList(type, data) {
            const stats = {};
            data.forEach(i => { const s = i.sales_name || 'ไม่ระบุ'; stats[s] = (stats[s] || 0) + 1; });
            const salesHTML = Object.entries(stats).map(([n, c]) => 
                `<div class="d-flex justify-content-between p-2 border-bottom"><span>${n}</span><span class="badge bg-primary">${c}</span></div>`
            ).join('');

            const provinceStats = {};
            data.forEach(i => { const p = i.province || 'ไม่ระบุ'; provinceStats[p] = (provinceStats[p] || 0) + 1; });
            const provinceHTML = Object.entries(provinceStats).sort((a, b) => b[1] - a[1]).map(([n, c]) => 
                `<div class="d-flex justify-content-between p-2 border-bottom"><span>${n}</span><span class="badge bg-success">${c}</span></div>`
            ).join('');

            const html = `
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🏪 ร้านค้าต่อเซลส์</h5>
                        <div class="province-list">${salesHTML}</div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🗺️ จังหวัด</h5>
                        <div class="province-list">${provinceHTML}</div>
                    </div>
                </div>
            `;
            document.getElementById('tire' + type).innerHTML += html;
        }

        function initSalesTable(data) {
            const stats = {};
            data.forEach(i => {
                const s = i.sales_name || 'ไม่ระบุ';
                if (!stats[s]) stats[s] = { count: 0, participants: 0, useRoom: 0, tire: 0 };
                stats[s].count++;
                stats[s].participants += parseInt(i.participants_after) || 0;
                if (i.type === 'shop') {
                    stats[s].useRoom += i.use_room == 1 ? 1 : 0;
                }
                stats[s].tire += (parseInt(i.tire_40_after)||0) + (parseInt(i.tire_80_after)||0) + (parseInt(i.tire_120_after)||0) + (parseInt(i.tire_200_after)||0) + (parseInt(i.tire_300_after)||0) + (parseInt(i.tire_600_after)||0);
            });

            if (salesTable) salesTable.destroy();
            salesTable = $('#salesTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
                lengthMenu: [[5, 10, 20, 100, -1], [5, 10, 20, 100, 'ทั้งหมด']],
                pageLength: 5,
                data: Object.entries(stats).map(([name, s]) => ({ sales: name, ...s })),
                columns: [
                    { title: 'เซลส์', data: 'sales' },
                    { title: 'จำนวนร้าน', data: 'count' },
                    { title: 'จำนวนคน', data: 'participants' },
                    { title: 'ใช้ห้อง', data: 'useRoom' },
                    { title: 'รวมยาง (แพค)', data: 'tire' }
                ]
            });
        }
    </script>
</body>
</html>