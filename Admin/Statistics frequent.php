<?php
session_start();
include '../php/admin_navbar.php';
include '../php/dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>สถิติช่วงเวลาที่ใช้บริการบ่อย</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="Statistics frequent.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body>

<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- Header -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>สถิติช่วงเวลาที่ใช้บริการบ่อย</h1>
      </div>
    </div>

    <!-- Filters -->
    <div class="filter-container">
        <label for="court-select">ชื่อสนาม :</label>
        <select id="court-select">
            <option value="all">ทั้งหมด</option>
            <option value="1">สนาม 1</option>
            <option value="2">สนาม 2</option>
            <option value="3">สนาม 3</option>
            <option value="4">สนาม 4</option>
            <option value="5">สนาม 5</option>
        </select>

        <label for="start-date">วันที่มาใช้บริการ:</label>
        <input type="date" id="start-date" value="2025-01-01">

        <label for="end-date">ถึง</label>
        <input type="date" id="end-date">
<script>
  // ดึงวันที่ปัจจุบัน
  const today = new Date();
  
  // ฟอร์แมตวันที่เป็น YYYY-MM-DD
  const year = today.getFullYear();
  const month = ("0" + (today.getMonth() + 1)).slice(-2);  // เดือนต้องบวก 1 เนื่องจาก months เริ่มจาก 0
  const day = ("0" + today.getDate()).slice(-2);  // วันต้องใช้ 2 หลัก

  // กำหนดค่าให้กับ input
  document.getElementById('end-date').value = `${year}-${month}-${day}`;
</script>

    </div>

    <!-- Chart -->
    <div class="chart-container" style="width: 90%; max-width: 1200px;">
        <canvas id="usageChart"></canvas>
    </div>
  </div>

  <script>
  // ฟังก์ชันการแสดงกราฟที่อัปเดตทันทีเมื่อค่าถูกเปลี่ยนแปลง
  function updateChart() {
    const courtID = document.getElementById('court-select').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    // Clear existing chart instance
    const canvas = document.getElementById('usageChart');
    const ctx = canvas.getContext('2d');
    if (window.myChart) {
        window.myChart.destroy();
    }

    // เรียกข้อมูลจาก API หรือไฟล์ PHP
    fetch(`/web_badmintaon_khlong_6/php/fetch_usage_data.php?courtID=${courtID}&startDate=${startDate}&endDate=${endDate}`)
      .then(response => response.json())
      .then(data => {
        window.myChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: data.labels, // เวลา 11.00น - 00.00น
            datasets: [
              {
                label: 'การจองแบบทั่วไป',
                data: data.generalBookings,
                backgroundColor: '#79B7FF',
              },
              {
                label: 'การขอเปิดก๊วน',
                data: data.groupBookings,
                backgroundColor: '#FF0000',
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: true },
              title: { display: true, text: '' },
            },
            scales: {
              x: {
                title: { display: true, text: 'เวลาที่ใช้บริการ' },
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'จำนวนชั่วโมง' },
              },
            },
          },
        });
      });
  }

  // เพิ่ม event listener สำหรับการเปลี่ยนแปลงค่าของตัวเลือกทั้งหมด
  document.getElementById('court-select').addEventListener('change', updateChart);
  document.getElementById('start-date').addEventListener('change', updateChart);
  document.getElementById('end-date').addEventListener('change', updateChart);

  // เรียกฟังก์ชันเมื่อโหลดหน้าแรก
  window.addEventListener('DOMContentLoaded', function () {
    updateChart();
  });
</script>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
