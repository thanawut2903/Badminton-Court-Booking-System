<?php
session_start();
include '../php/admin_navbar.php';
include '../php/dbconnect.php';

// Query to fetch cancellation reasons, usernames, canceler names, and roles from booking and account tables
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');  // ถ้าไม่มีให้ใช้ปีปัจจุบัน
$month = isset($_GET['month']) ? $_GET['month'] : date('m');  // ถ้าไม่มีให้ใช้เดือนปัจจุบัน


$query = "SELECT b.CancelReason, 
                 CONCAT(a.FirstName, ' ', a.LastName) AS UserName, 
                 COUNT(*) as CancelCount,
                 c.FirstName AS CancelByName,
                 c.Role AS CancelByRole
          FROM booking b
          JOIN account a ON b.AccountID = a.AccountID
          LEFT JOIN account c ON b.CancelBy = c.AccountID
          WHERE b.Status = 'ยกเลิก' 
          AND a.Role != 'A'
          AND YEAR(b.CancelDT) = '$year'";

if ($month !== '') {
    $query .= " AND MONTH(b.CancelDT) = '$month'";
}

$query .= " GROUP BY b.CancelReason, b.AccountID, b.CancelBy, c.Role 
            ORDER BY CancelCount DESC";

$result = mysqli_query($conn, $query);


$cancellations = [
    'ไม่ชำระในเวลาที่กำหนด' => [],
    'ไม่มาใช้บริการในเวลาที่จอง' => [],
    'ยกเลิกโดยผู้ใช้' => [],
    'เหตุผลอื่นๆ' => []
];

while ($row = mysqli_fetch_assoc($result)) {
    $reason = in_array($row['CancelReason'], ['ไม่ชำระในเวลาที่กำหนด', 'ไม่มาใช้บริการในเวลาที่จอง','ยกเลิกโดยผู้ใช้']) ? $row['CancelReason'] : 'เหตุผลอื่นๆ';
    $cancellations[$reason][] = [
        'UserName' => $row['UserName'],
        'CancelReason' => $row['CancelReason'],
        'CancelCount' => $row['CancelCount'],
        'CancelByName' => $row['CancelByName'],
        'CancelByRole' => ($row['CancelByRole'] == 'A') ? 'ผู้ดูแล' : (($row['CancelByRole'] == 'M') ? 'สมาชิก' : 'ไม่ระบุ')
    ];
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติการยกเลิกการจอง</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="Cancelstatistics.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../php/admin_menu.php' ?>



<div class="container mt-4">
        <!-- กล่องข้อความ -->
        <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>สถิติการยกเลิกการจอง</h1>
      </div>
      <div class="chart-container text-center">
    <label for="year-input">กรอกปี:</label>
    <input type="number" id="year-input" class="form-control w-auto d-inline-block mb-3" placeholder="กรอกปี เช่น 2025" value="<?php echo $year; ?>">  <!-- ตั้งค่าปีจาก PHP -->
  
    <select id="month-selector" class="form-select w-auto d-inline-block mb-3">
        <option value="">-- ทุกเดือน --</option>
        <option value="01" <?php echo ($month == '01') ? 'selected' : ''; ?>>มกราคม</option>
        <option value="02" <?php echo ($month == '02') ? 'selected' : ''; ?>>กุมภาพันธ์</option>
        <option value="03" <?php echo ($month == '03') ? 'selected' : ''; ?>>มีนาคม</option>
        <option value="04" <?php echo ($month == '04') ? 'selected' : ''; ?>>เมษายน</option>
        <option value="05" <?php echo ($month == '05') ? 'selected' : ''; ?>>พฤษภาคม</option>
        <option value="06" <?php echo ($month == '06') ? 'selected' : ''; ?>>มิถุนายน</option>
        <option value="07" <?php echo ($month == '07') ? 'selected' : ''; ?>>กรกฎาคม</option>
        <option value="08" <?php echo ($month == '08') ? 'selected' : ''; ?>>สิงหาคม</option>
        <option value="09" <?php echo ($month == '09') ? 'selected' : ''; ?>>กันยายน</option>
        <option value="10" <?php echo ($month == '10') ? 'selected' : ''; ?>>ตุลาคม</option>
        <option value="11" <?php echo ($month == '11') ? 'selected' : ''; ?>>พฤศจิกายน</option>
        <option value="12" <?php echo ($month == '12') ? 'selected' : ''; ?>>ธันวาคม</option>
    </select>
</div>
    </div>
    <div class="chart-container1">
        <canvas id="cancellationChart"></canvas>
    </div>

    <?php
    $colors = ['#00A9FF', '#FF9800','#FF0000', '#4CAF50']; // สีสำหรับแท่งกราฟ
    $colorIndex = 0;
    ?>

    <?php foreach ($cancellations as $reason => $users): ?>
        <div class="container-table mt-4">
            <h2 class="table-header" style="background-color: <?php echo $colors[$colorIndex]; ?>; color: black; padding: 10px; border-radius: 8px;">
                <?php echo htmlspecialchars($reason); ?>
            </h2>
            <table class="booking-table">
                <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อผู้ยกเลิก</th>
                    <?php if ($reason === 'เหตุผลอื่นๆ'): ?>
                        <th>เหตุผล</th>
                    <?php endif; ?>
                    <th>จำนวนครั้งที่ยกเลิก</th>
                    <th>ยกเลิกโดย</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">ไม่มีข้อมูล</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['UserName']); ?></td>
                            <?php if ($reason === 'เหตุผลอื่นๆ'): ?>
                                <td><?php echo htmlspecialchars($user['CancelReason']); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($user['CancelCount']) . ' ครั้ง'; ?></td>
                            <td><?php echo htmlspecialchars($user['CancelByName']) . ' (' . htmlspecialchars($user['CancelByRole']) . ')'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $colorIndex++; ?>
    <?php endforeach; ?>


<script>
    const ctx = document.getElementById('cancellationChart').getContext('2d');
    const chartData = <?php
$chartData = [];
foreach ($cancellations as $reason => $users) {
    $chartData[] = [
        'label' => $reason,
        'count' => array_reduce($users, function($carry, $user) {
            return $carry + $user['CancelCount'];
        }, 0)
    ];
}
echo json_encode($chartData);

    ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(data => data.label),
        datasets: [
            {
                label: 'จำนวนครั้ง',
                data: chartData.map(data => data.count),
                backgroundColor: ['#00A9FF', '#FF9800', '#FF0000', '#4CAF50'],
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: '' },
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'จำนวนครั้ง' },
                ticks: {
                    stepSize: 1, // 🔹 เพิ่มช่วงแกน Y ทีละ 10
                    callback: function(value) {
                        return Number.isInteger(value) ? value : null; // 🔹 แสดงเฉพาะจำนวนเต็ม
                    }
                }
            }
        }
    }
});


// ฟังก์ชันที่จะปรับปรุง URL
function updateURL() {
    const year = document.getElementById('year-input').value;
    const month = document.getElementById('month-selector').value;

    // กำหนด URL ที่จะใช้
    let url = new URL(window.location.href);

    // ปรับ URL โดยการเพิ่ม query parameters สำหรับ year และ month
    url.searchParams.set('year', year);
    url.searchParams.set('month', month);

    // โหลดหน้าใหม่โดยใช้ URL ที่อัปเดต
    window.location.href = url.toString();
}

// ฟังก์ชันที่จะตั้งค่าปีและเดือนใน input จาก URL
function setInputValuesFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year');
    const month = urlParams.get('month');

    // ตั้งค่า input ปี และ เดือน
    if (year) {
        document.getElementById('year-input').value = year;
    }
    if (month) {
        document.getElementById('month-selector').value = month;
    }
}

// ตั้งค่า input ตาม URL เมื่อหน้าเว็บโหลด
window.addEventListener('DOMContentLoaded', function() {
    setInputValuesFromURL();

    // เมื่อมีการเปลี่ยนแปลงใน input หรือ select
    document.getElementById('year-input').addEventListener('change', updateURL);
    document.getElementById('month-selector').addEventListener('change', updateURL);
});


</script>

<script src="cancelstatistics.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>