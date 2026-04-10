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
    <title>Dashboard Graph - กราฟสรุปสถิติ</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .chart-card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .nav-link { cursor: pointer; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">🏠 หน้าหลัก</a>
        <a class="navbar-brand" href="dashboard.php">📊 Dashboard</a>
        <span class="navbar-brand text-white">📈 Dashboard Graph</span>
    </div>
</nav>

<div class="container-fluid py-4">
    <ul class="nav nav-pills mb-4" id="typeTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" id="shop-tab" data-bs-toggle="pill" data-bs-target="#shop">🏪 ร้านค้า</button></li>
        <li class="nav-item"><button class="nav-link" id="user-tab" data-bs-toggle="pill" data-bs-target="#user">👤 ผู้ใช้</button></li>
    </ul>

    <div class="tab-content" id="typeTabContent">
        <div class="tab-pane fade show active" id="shop" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">📊 เปรียบเทียบจำนวนผู้ร่วมงาน (ร้านค้า)</h5>
                        <canvas id="participantsChartShop"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">🛞 เปรียบเทียบจำนวนยางจอง (ร้านค้า)</h5>
                        <canvas id="tireChartShop"></canvas>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">📈 ยางจอง vs ยางที่มาจริง แยกตามขนาด (ร้านค้า)</h5>
                        <canvas id="tireDetailChartShop"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="user" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">📊 เปรียบเทียบจำนวนผู้ร่วมงาน (ผู้ใช้)</h5>
                        <canvas id="participantsChartUser"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">🛞 เปรียบเทียบจำนวนยางจอง (ผู้ใช้)</h5>
                        <canvas id="tireChartUser"></canvas>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="chart-card">
                        <h5 class="text-center mb-3">📈 ยางจอง vs ยางที่มาจริง แยกตามขนาด (ผู้ใช้)</h5>
                        <canvas id="tireDetailChartUser"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card mt-4">
        <h5 class="text-center mb-3">📊 เปรียบเทียบภาพรวมทั้งหมด</h5>
        <canvas id="overallChart"></canvas>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let eventId = 0;
    let shopData = null;
    let userData = null;

    document.addEventListener('DOMContentLoaded', function() {
        fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_event' })
            .then(res => res.json())
            .then(data => {
                eventId = data.id;
                Promise.all([
                    fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=shop' }).then(r => r.json()),
                    fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=user' }).then(r => r.json()),
                    fetch('api.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'action=get_dashboard_summary&event_id=' + eventId + '&type=all' }).then(r => r.json())
                ]).then(([shop, user, all]) => {
                    shopData = shop;
                    userData = user;
                    initCharts('shop', shop);
                    initCharts('user', user);
                    initOverallChart(all);
                });
            });
    });

    function initCharts(type, data) {
        const beforeColor = 'rgba(255, 193, 7, 0.8)';
        const afterColor = 'rgba(25, 135, 84, 0.8)';

        if (document.getElementById('participantsChart' + type)) {
            new Chart(document.getElementById('participantsChart' + type), {
                type: 'bar',
                data: {
                    labels: ['ลงทะเบียน', 'มาจริง'],
                    datasets: [{
                        label: 'จำนวนผู้ร่วมงาน',
                        data: [data.total_participants_before || 0, data.total_participants_after || 0],
                        backgroundColor: [beforeColor, afterColor]
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }

        if (document.getElementById('tireChart' + type)) {
            new Chart(document.getElementById('tireChart' + type), {
                type: 'bar',
                data: {
                    labels: ['ยางจอง', 'ยางที่มา'],
                    datasets: [{
                        label: 'จำนวนยาง',
                        data: [data.total_tire_before || 0, data.total_tire_after || 0],
                        backgroundColor: [beforeColor, afterColor]
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }

        const tireSizes = ['40', '80', '120', '200', '300', '600'];
        const tireLabels = tireSizes.map(s => 'ยาง ' + s);
        const tireBeforeData = tireSizes.map(s => data['total_tire_' + s + '_before'] || 0);
        const tireAfterData = tireSizes.map(s => data['total_tire_' + s + '_after'] || 0);

        if (document.getElementById('tireDetailChart' + type)) {
            new Chart(document.getElementById('tireDetailChart' + type), {
                type: 'bar',
                data: {
                    labels: tireLabels,
                    datasets: [
                        { label: 'ยางจอง', data: tireBeforeData, backgroundColor: beforeColor },
                        { label: 'ยางที่มา', data: tireAfterData, backgroundColor: afterColor }
                    ]
                },
                options: { responsive: true, scales: { x: { stacked: false }, y: { stacked: false } } }
            });
        }
    }

    function initOverallChart(allData) {
        new Chart(document.getElementById('overallChart'), {
            type: 'bar',
            data: {
                labels: ['ร้านค้า - ผู้ร่วมงาน', 'ร้านค้า - ยาง', 'ผู้ใช้ - ผู้ร่วมงาน', 'ผู้ใช้ - ยาง'],
                datasets: [
                    { label: 'ลงทะเบียน/จอง', data: [shopData.total_participants_before || 0, shopData.total_tire_before || 0, userData.total_participants_before || 0, userData.total_tire_before || 0], backgroundColor: 'rgba(255, 193, 7, 0.8)' },
                    { label: 'มาจริง', data: [shopData.total_participants_after || 0, shopData.total_tire_after || 0, userData.total_participants_after || 0, userData.total_tire_after || 0], backgroundColor: 'rgba(25, 135, 84, 0.8)' }
                ]
            },
            options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true } } }
        });
    }
</script>
</body>
</html>