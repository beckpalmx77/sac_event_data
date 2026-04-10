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
    <title>Dashboard สรุปสถิติ (Auto Refresh)</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .chart-container { background: white; border-radius: 15px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 15px; position: relative; }
        .province-list { max-height: 300px; overflow-y: auto; }

        #refresh-status { font-size: 12px; color: #198754; font-weight: 600; }
        .dot { height: 10px; width: 10px; background-color: #198754; border-radius: 50%; display: inline-block; margin-right: 5px; animation: blink 1.5s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        @media (max-width: 576px) {
            h2 { font-size: 18px; }
        }
    </style>
</head>
<body>
<div class="container-fluid py-3 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Dashboard สรุปสถิติ</h2>
        <div id="refresh-status">
            <span class="dot"></span> อัปเดตล่าสุด: <span id="last-update">-</span>
        </div>
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
            <div class="row mb-4" id="detailShop"></div>
        </div>
        <div class="tab-pane fade" id="user" role="tabpanel">
            <div class="row mb-4" id="summaryUser"></div>
            <div class="row mb-4" id="detailUser"></div>
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
    let refreshTimer;

    document.addEventListener('DOMContentLoaded', function() {
        loadData();
        startAutoRefresh(30000); // รีเฟรชทุก 30 วินาที
    });

    function startAutoRefresh(ms) {
        if (refreshTimer) clearInterval(refreshTimer);
        refreshTimer = setInterval(loadData, ms);
    }

    function loadData() {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_event'
        })
            .then(res => res.json())
            .then(data => {
                eventId = data.id;
                loadAttendees();
                loadDashboardSummary('shop');
                loadDashboardSummary('user');
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
            })
            .catch(err => console.error('Fetch Error:', err));
    }

    document.querySelectorAll('#dashTab button').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            currentType = e.target.dataset.bsTarget.replace('#', '');
            initSalesTable(allData.filter(d => d.type === currentType));
        });
    });

    function renderStats(type, data) {
        const totalShops = parseInt(data.total_shops) || 0;
        const totalPartBefore = parseInt(data.total_participants_before) || 0;
        const totalPartAfter = parseInt(data.total_participants_after) || 0;

        const sizes = [40, 80, 120, 200, 300, 600];
        let tireBefore = 0, tireAfter = 0;
        sizes.forEach(s => {
            tireBefore += parseInt(data['total_tire_'+s+'_before']) || 0;
            tireAfter += parseInt(data['total_tire_'+s+'_after']) || 0;
        });

        const shopsCame = parseInt(data.shops_came) || 0;
        const summaryHTML = `
                <div class="col-12 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">📊 สรุป${type === 'Shop' ? 'ร้านค้า' : 'ผู้ใช้'}</h5>
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>รายละเอียด</th>
                                    <th>ลงทะเบียน</th>
                                    <th>มาร่วมงาน</th>
                                    <th>ไม่มาร่วมงาน</th>
                                    <th>% มา</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td class="text-start">${type === 'Shop' ? 'ร้านค้า' : 'ผู้ใช้'} (แห่ง)</td>
                                    <td>${totalShops.toLocaleString()}</td>
                                    <td>${shopsCame.toLocaleString()}</td>
                                    <td>${(totalShops - shopsCame).toLocaleString()}</td>
                                    <td>${totalShops > 0 ? Math.round(shopsCame/totalShops*100) : 0}%</td>
                                </tr>
                                <tr>
                                    <td class="text-start">จำนวนคน (ท่าน)</td>
                                    <td>${totalPartBefore.toLocaleString()}</td>
                                    <td>${totalPartAfter.toLocaleString()}</td>
                                    <td>${(totalPartBefore - totalPartAfter).toLocaleString()}</td>
                                    <td>${totalPartBefore > 0 ? Math.round(totalPartAfter/totalPartBefore*100) : 0}%</td>
                                </tr>
                                <tr class="table-info fw-bold">
                                    <td class="text-start">ยอดจองยาง (เส้น)</td>
                                    <td>${tireBefore.toLocaleString()}</td>
                                    <td>${tireAfter.toLocaleString()}</td>
                                    <td>${(tireBefore - tireAfter).toLocaleString()}</td>
                                    <td>${tireBefore > 0 ? Math.round(tireAfter/tireBefore*100) : 0}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        document.getElementById('summary' + type).innerHTML = summaryHTML;
    }

    function loadAttendees() {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_attendees&event_id=' + eventId
        })
            .then(res => res.json())
            .then(data => {
                allData = data;
                initSalesTable(data.filter(d => d.type === currentType));

                // โหลดส่วน จังหวัด และ เซลส์
                renderLocationAndSales('shop', data.filter(d => d.type === 'shop'));
                renderLocationAndSales('user', data.filter(d => d.type === 'user'));
            });
    }

    function renderLocationAndSales(type, data) {
        const salesStats = {};
        data.forEach(i => { const s = i.sales_name || 'ไม่ระบุ'; salesStats[s] = (salesStats[s] || 0) + 1; });
        const salesHTML = Object.entries(salesStats).sort((a,b)=>b[1]-a[1]).map(([n, c]) =>
            `<div class="d-flex justify-content-between p-2 border-bottom"><span>${n}</span><span class="badge bg-primary">${c}</span></div>`
        ).join('');

        const provStats = {};
        data.forEach(i => { const p = i.province || 'ไม่ระบุ'; provStats[p] = (provStats[p] || 0) + 1; });
        const provHTML = Object.entries(provStats).sort((a, b) => b[1] - a[1]).map(([n, c]) =>
            `<div class="d-flex justify-content-between p-2 border-bottom"><span>${n}</span><span class="badge bg-success">${c}</span></div>`
        ).join('');

        const html = `
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🏪 แยกตามเซลส์</h5>
                        <div class="province-list">${salesHTML}</div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">🗺️ แยกตามจังหวัด</h5>
                        <div class="province-list">${provHTML}</div>
                    </div>
                </div>
            `;
        const typeCap = type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('detail' + typeCap).innerHTML = html;
    }

    function loadDashboardSummary(type) {
        fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=' + type
        })
            .then(res => res.json())
            .then(data => renderStats(type.charAt(0).toUpperCase() + type.slice(1), data));
    }

    function initSalesTable(data) {
        const typeLabel = currentType === 'shop' ? '(ร้านค้า)' : '(ผู้ใช้)';
        document.getElementById('salesTableTitle').textContent = '📋 รายละเอียดตามเซลส์ ' + typeLabel;

        const tireSizes = [40, 80, 120, 200, 300, 600];
        const stats = {};
        data.forEach(i => {
            const s = i.sales_name || 'ไม่ระบุ';
            if (!stats[s]) {
                stats[s] = { sales: s, count: 0, came: 0, not_came: 0, participants: 0, participants_after: 0, reserveRoom: 0, usedRoom: 0, tire_before: 0, tire_after: 0 };
                tireSizes.forEach(sz => { stats[s]['tire_'+sz+'_before'] = 0; stats[s]['tire_'+sz+'_after'] = 0; });
            }
            stats[s].count++;
            if ((parseInt(i.participants_after) || 0) > 0) stats[s].came++; else stats[s].not_came++;
            stats[s].participants += parseInt(i.participants_before) || 0;
            stats[s].participants_after += parseInt(i.participants_after) || 0;
            stats[s].reserveRoom += parseInt(i.reserve_room) || 0;
            stats[s].usedRoom += parseInt(i.used_room) || 0;

            tireSizes.forEach(sz => {
                const b = parseInt(i['tire_'+sz+'_before']) || 0;
                const a = parseInt(i['tire_'+sz+'_after']) || 0;
                stats[s]['tire_'+sz+'_before'] += b;
                stats[s]['tire_'+sz+'_after'] += a;
                stats[s].tire_before += b;
                stats[s].tire_after += a;
            });
        });

        const tableData = Object.values(stats);
        if (tableData.length > 0) {
            const totalRow = { sales: '<strong>รวมทั้งหมด</strong>', count: 0, came: 0, not_came: 0, participants: 0, participants_after: 0, reserveRoom: 0, usedRoom: 0, tire_before: 0, tire_after: 0 };
            tireSizes.forEach(sz => { totalRow['tire_'+sz+'_before'] = 0; totalRow['tire_'+sz+'_after'] = 0; });
            tableData.forEach(row => {
                totalRow.count += row.count; totalRow.came += row.came; totalRow.not_came += row.not_came;
                totalRow.participants += row.participants; totalRow.participants_after += row.participants_after;
                totalRow.reserveRoom += row.reserveRoom; totalRow.usedRoom += row.usedRoom;
                totalRow.tire_before += row.tire_before; totalRow.tire_after += row.tire_after;
                tireSizes.forEach(sz => { totalRow['tire_'+sz+'_before'] += row['tire_'+sz+'_before']; totalRow['tire_'+sz+'_after'] += row['tire_'+sz+'_after']; });
            });
            tableData.push(totalRow);
        }

        const columns = [
            { title: 'เซลส์', data: 'sales' },
            { title: 'ลงทะเบียน', data: 'count' },
            { title: 'มางาน', data: 'came' },
            { title: 'ไม่มา', data: 'not_came' },
            { title: 'คน(จอง)', data: 'participants' },
            { title: 'คน(มา)', data: 'participants_after' },
            { title: 'ยาง(จอง)', data: 'tire_before' },
            { title: 'ยาง(มา)', data: 'tire_after' }
        ];
        tireSizes.forEach(sz => {
            columns.push({ title: sz+'(จอง)', data: 'tire_'+sz+'_before' });
            columns.push({ title: sz+'(จริง)', data: 'tire_'+sz+'_after' });
        });

        if ($.fn.DataTable.isDataTable('#salesTable')) {
            salesTable.clear().rows.add(tableData).draw(false);
        } else {
            salesTable = $('#salesTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
                data: tableData,
                columns: columns,
                ordering: false,
                scrollX: true,
                pageLength: 10,
                stateSave: true,
                rowCallback: function(row, data) {
                    if (data.sales.includes('รวม')) $(row).addClass('table-dark fw-bold');
                }
            });
        }
    }
</script>
</body>
</html>