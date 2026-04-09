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
    <title>Dashboard สรุปสถิติ</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
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
            <h5 class="mb-3" id="salesTableTitle">📋 รายละเอียดตามเซลส์</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="salesTable" style="width:100%"></table>
            </div>
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
        let currentType = 'shop';

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        document.querySelectorAll('#dashTab button').forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                currentType = e.target.dataset.bsTarget.replace('#', '');
                initSalesTable(currentType === 'shop' ? allData.filter(d => d.type === 'shop') : allData.filter(d => d.type === 'user'));
            });
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
                renderStats(type.charAt(0).toUpperCase() + type.slice(1), data);
            });
        }

        function loadAttendees() {
            fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_attendees&event_id=' + eventId })
            .then(res => res.json())
            .then(data => {
                allData = data;
                initSalesTable(data.filter(d => d.type === currentType));
                loadSalesProvinceList('shop', data.filter(d => d.type === 'shop'));
                loadSalesProvinceList('user', data.filter(d => d.type === 'user'));
                loadTireBySizeTable('shop', data.filter(d => d.type === 'shop'));
                loadTireBySizeTable('user', data.filter(d => d.type === 'user'));
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
            const tireBefore = parseInt(data['total_tire_40_before']) + parseInt(data['total_tire_80_before']) + 
                              parseInt(data['total_tire_120_before']) + parseInt(data['total_tire_200_before']) + 
                              parseInt(data['total_tire_300_before']) + parseInt(data['total_tire_600_before']);
            const tireAfter = parseInt(data['total_tire_40_after']) + parseInt(data['total_tire_80_after']) + 
                              parseInt(data['total_tire_120_after']) + parseInt(data['total_tire_200_after']) + 
                              parseInt(data['total_tire_300_after']) + parseInt(data['total_tire_600_after']);

            const shopsCame = parseInt(data.shops_came) || 0;
            const shopsNotCame = totalShops - shopsCame;
            const partsNotCame = totalPartBefore - totalPartAfter;

            const summaryHTML = `
                <div class="col-12 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">📊 สรุป${type === 'Shop' ? 'ร้านค้า' : 'ผู้ใช้'}</h5>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>รายละเอียด</th>
                                    <th class="text-center">ลงทะเบียน</th>
                                    <th class="text-center">มาร่วมงาน</th>
                                    <th class="text-center">ไม่มาร่วมงาน</th>
                                    <th class="text-center">% มาร่วมงาน</th>
                                    <th class="text-center">% ไม่มาร่วมงาน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${type === 'Shop' ? 'ร้านค้า' : 'ผู้ใช้'}</td>
                                    <td class="text-center">${totalShops.toLocaleString()}</td>
                                    <td class="text-center">${shopsCame.toLocaleString()}</td>
                                    <td class="text-center">${shopsNotCame.toLocaleString()}</td>
                                    <td class="text-center">${totalShops > 0 ? Math.round(shopsCame/totalShops*100) : 0}%</td>
                                    <td class="text-center">${totalShops > 0 ? Math.round(shopsNotCame/totalShops*100) : 0}%</td>
                                </tr>
                                <tr>
                                    <td>จำนวนคน</td>
                                    <td class="text-center">${totalPartBefore.toLocaleString()}</td>
                                    <td class="text-center">${totalPartAfter.toLocaleString()}</td>
                                    <td class="text-center">${partsNotCame.toLocaleString()}</td>
                                    <td class="text-center">${totalPartBefore > 0 ? Math.round(totalPartAfter/totalPartBefore*100) : 0}%</td>
                                    <td class="text-center">${totalPartBefore > 0 ? Math.round(partsNotCame/totalPartBefore*100) : 0}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🛞 ข้อมูลยาง</h5>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>ขนาด</th>
                                    <th class="text-center">จองจอง</th>
                                    <th class="text-center">จองจริง</th>
                                    <th class="text-center">% จองจริง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>รวม</td><td class="text-center">${tireBefore.toLocaleString()}</td><td class="text-center">${tireAfter.toLocaleString()}</td><td class="text-center">${tireBefore > 0 ? Math.round(tireAfter/tireBefore*100) : 0}%</td></tr>
                                ${[40, 80, 120, 200, 300, 600].map(s => {
                                    const before = parseInt(data['total_tire_'+s+'_before']) || 0;
                                    const after = parseInt(data['total_tire_'+s+'_after']) || 0;
                                    return `<tr><td>${s}</td><td class="text-center">${before}</td><td class="text-center">${after}</td><td class="text-center">${before > 0 ? Math.round(after/before*100) : 0}%</td></tr>`;
                                }).join('')}
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
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🔢 การจองยาง</h5>
                        <table class="table table-bordered table-sm"><thead><tr><th>จำนวน</th><th>จองจอง</th><th>มาจริง</th><th>% จองจริง</th></tr></thead><tbody>${tireRows}</tbody></table>
                    </div>
                </div>
            `;
            const typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('tire' + typeCapitalized).innerHTML = tireHTML;
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
            const typeLabel = currentType === 'shop' ? '(ร้านค้า)' : '(ผู้ใช้)';
            document.getElementById('salesTableTitle').textContent = '📋 รายละเอียดตามเซลส์ ' + typeLabel;
            
            const tireSizes = [40, 80, 120, 200, 300, 600];
            const stats = {};
            data.forEach(i => {
                const s = i.sales_name || 'ไม่ระบุ';
                if (!stats[s]) {
                    stats[s] = { count: 0, came: 0, not_came: 0, participants: 0, participants_after: 0, reserveRoom: 0, usedRoom: 0, tire_before: 0, tire_after: 0 };
                    tireSizes.forEach(size => {
                        stats[s]['tire_'+size+'_before'] = 0;
                        stats[s]['tire_'+size+'_after'] = 0;
                    });
                }
                stats[s].count++;
                if ((parseInt(i.participants_after) || 0) > 0) {
                    stats[s].came++;
                } else {
                    stats[s].not_came++;
                }
                stats[s].participants += parseInt(i.participants_before) || 0;
                stats[s].participants_after += parseInt(i.participants_after) || 0;
                stats[s].reserveRoom += parseInt(i.reserve_room) || 0;
                stats[s].usedRoom += parseInt(i.used_room) || 0;
                stats[s].tire_before += (parseInt(i.tire_40_before)||0) + (parseInt(i.tire_80_before)||0) + (parseInt(i.tire_120_before)||0) + (parseInt(i.tire_200_before)||0) + (parseInt(i.tire_300_before)||0) + (parseInt(i.tire_600_before)||0);
                stats[s].tire_after += (parseInt(i.tire_40_after)||0) + (parseInt(i.tire_80_after)||0) + (parseInt(i.tire_120_after)||0) + (parseInt(i.tire_200_after)||0) + (parseInt(i.tire_300_after)||0) + (parseInt(i.tire_600_after)||0);
                tireSizes.forEach(size => {
                    stats[s]['tire_'+size+'_before'] += parseInt(i['tire_'+size+'_before']) || 0;
                    stats[s]['tire_'+size+'_after'] += parseInt(i['tire_'+size+'_after']) || 0;
                });
            });

            const totalCount = Object.values(stats).reduce((sum, s) => sum + s.count, 0);
            const totalCame = Object.values(stats).reduce((sum, s) => sum + s.came, 0);
            const totalNotCame = Object.values(stats).reduce((sum, s) => sum + s.not_came, 0);
            const totalParticipants = Object.values(stats).reduce((sum, s) => sum + s.participants, 0);
            const totalParticipantsAfter = Object.values(stats).reduce((sum, s) => sum + s.participants_after, 0);
            const totalReserveRoom = Object.values(stats).reduce((sum, s) => sum + s.reserveRoom, 0);
            const totalUsedRoom = Object.values(stats).reduce((sum, s) => sum + s.usedRoom, 0);
            const totalTireBefore = Object.values(stats).reduce((sum, s) => sum + s.tire_before, 0);
            const totalTireAfter = Object.values(stats).reduce((sum, s) => sum + s.tire_after, 0);

            const totals = {
                sales: '<strong>รวม</strong>',
                count: totalCount,
                came: totalCame,
                not_came: totalNotCame,
                participants: totalParticipants,
                participants_after: totalParticipantsAfter,
                reserveRoom: totalReserveRoom,
                usedRoom: totalUsedRoom,
                tire_before: totalTireBefore,
                tire_after: totalTireAfter
            };
            tireSizes.forEach(size => {
                totals['tire_'+size+'_before'] = Object.values(stats).reduce((sum, s) => sum + s['tire_'+size+'_before'], 0);
                totals['tire_'+size+'_after'] = Object.values(stats).reduce((sum, s) => sum + s['tire_'+size+'_after'], 0);
            });

            const tableData = Object.entries(stats).map(([name, s]) => ({ sales: name, ...s }));
            tableData.push(totals);

            const columns = [
                { title: 'เซลส์', data: 'sales' },
                { title: 'จำนวนร้านลงทะเบียน', data: 'count' },
                { title: 'ร้านมาร่วมงาน', data: 'came' },
                { title: 'ไม่มา', data: 'not_came' },
                { title: 'จำนวนคน(ลงทะเบียน)', data: 'participants' },
                { title: 'คน(มาร่วมงาน)', data: 'participants_after' },
                { title: 'จองห้อง', data: 'reserveRoom' },
                { title: 'เข้าพักจริง', data: 'usedRoom' },
                { title: 'ยาง(จอง)', data: 'tire_before' },
                { title: 'ยาง-จริง', data: 'tire_after' }
            ];
            
            tireSizes.forEach(size => {
                columns.push({ title: size+'จอง', data: 'tire_'+size+'_before' });
                columns.push({ title: size+'จริง', data: 'tire_'+size+'_after' });
            });

            if (salesTable) salesTable.destroy();
            salesTable = $('#salesTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
                lengthMenu: [[10, 20, 100, -1], [10, 20, 100, 'ทั้งหมด']],
                pageLength: 10,
                data: tableData,
                ordering: false,
                scrollX: true,
                autoWidth: false,
                columns: columns,
                rowCallback: function(row, data, index) {
                    if (data.sales.includes('รวม')) {
                        $(row).addClass('table-dark fw-bold');
                    }
                }
            });
        }
    </script>
</body>
</html>