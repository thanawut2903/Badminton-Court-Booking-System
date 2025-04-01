<?php 
  require "../php/dbconnect.php";
  session_start();
  include '../php/member_navbar.php';

// ตรวจสอบว่าได้รับ BookingID ผ่าน GET หรือไม่
if (!isset($_GET['bookingId'])) {
    echo "<p>ไม่พบข้อมูลการจอง</p>";
    exit;
}

$bookingId = $conn->real_escape_string($_GET['bookingId']);

// ดึงข้อมูล LineID ของผู้ที่จอง
$sql = "SELECT a.LineID,a.Tel 
        FROM booking b 
        JOIN account a ON b.AccountID = a.AccountID
        WHERE b.BookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lineID = $row['LineID']; // ดึง LineID
    $Tel = $row['Tel'];
} else {
    $lineID = "ไม่พบข้อมูล"; // ถ้าไม่มีข้อมูล
    $Tel = "ไม่พบข้อมูล";
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Payment page with member.css" rel="stylesheet">
</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="containerd d-flex justify-content-center align-items-center" style="height: 10vh;">
      <div class="custom-box text-center">
        <h1>ชำระค่าบริการ</h1>
        <div class="custom-box12 text-center">
    <h4>1.แอดไลน์ Line ID : <?= htmlspecialchars($lineID) ?>
        <a  href="https://line.me/ti/p/~<?= urlencode($lineID) ?>" target="_bank">
            <img style="margin-top:0px" src="/web_badmintaon_khlong_6/Images/line icon2.png" id="imgline"><br>
        <a> หรือ โทร : </a> <?= urlencode($Tel) ?>  
        </a>
    </h4>
        </div>
        <div class="custom-box1 text-center">
    <h4>2.แจ้งชื่อที่ใช้งานในระบบจองสนามแบด</h4>
  </div>
        <div class="custom-box1 text-center">    
    <h4>3.ส่งสลิปการโอนเงินเพื่อยืนยันการจองทางไลน์</h4>
        </div>
        <div class="custom-box1 text-center">
    <h4>4.รอทางเจ้าของก๊วนอนุมัติการจอง</h4>
    </div>
    <!-- ปุ่มย้อนกลับ -->
<div class="custom-boxprevious" style="margin-top:50px">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
</div>
  </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
