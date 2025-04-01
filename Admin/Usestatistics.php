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
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="Usestatistics.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- เชื่อมต่อ Chart.js -->
</head>
<body>

<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>สถิติการใช้สนาม</h1>
      </div>
    </div>
    <div class="chart-container text-center">

        <label for="year-input">กรอกปี:</label>
        <input type="number" id="year-input" class="form-control w-auto d-inline-block mb-3" placeholder="กรอกปี เช่น 2025">

        <label for="month-selector">เลือกเดือน:</label>
        <select id="month-selector" class="form-select w-auto d-inline-block mb-3">
            <option value="">-- เลือกเดือน --</option>
            <option value="01">มกราคม</option>
            <option value="02">กุมภาพันธ์</option>
            <option value="03">มีนาคม</option>
            <option value="04">เมษายน</option>
            <option value="05">พฤษภาคม</option>
            <option value="06">มิถุนายน</option>
            <option value="07">กรกฎาคม</option>
            <option value="08">สิงหาคม</option>
            <option value="09">กันยายน</option>
            <option value="10">ตุลาคม</option>
            <option value="11">พฤศจิกายน</option>
            <option value="12">ธันวาคม</option>
        </select>

        <label for="court-selector">เลือกสนาม:</label>
        <select id="court-selector" class="form-select w-auto d-inline-block mb-3">
            <option value="all">สนามทั้งหมด</option>
            <option value="1">สนามที่ 1</option>
            <option value="2">สนามที่ 2</option>
            <option value="3">สนามที่ 3</option>
            <option value="4">สนามที่ 4</option>
            <option value="5">สนามที่ 5</option>
        </select>

        <canvas id="usageChart" style="max-height: 400px;"></canvas>
    </div>


    <!-- Bootstrap JS -->
    <script src="usestatistics.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  </div>
</body>
</html>
