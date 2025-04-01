<?php
session_start();
include '../php/admin_navbar.php';
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accountid'])) {
  $accountId = $_GET['accountid'];

  // Query เพื่อดึงข้อมูลสมาชิก การจอง และเจ้าของก๊วน พร้อม Role
  $sql = "SELECT 
              g.GroupMemberID,
              b.BookingDate,
              b.StartTime,
              b.EndTime,
              TIMESTAMPDIFF(HOUR, b.StartTime, b.EndTime) AS Hours,
              b.CourtID,
              g.Status,
              a.FirstName AS MemberName,
              a.LastName AS Memberlastname,
              owner.FirstName AS OwnerName,
              owner.Role AS OwnerRole
          FROM groupmember g
          INNER JOIN booking b ON g.BookingID = b.BookingID
          INNER JOIN account a ON g.AccountID = a.AccountID
          INNER JOIN account owner ON b.AccountID = owner.AccountID
          WHERE g.AccountID = ?";

  $stmt = $conn->prepare($sql);

  if (!$stmt) {
    echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    exit;
  }

  $stmt->bind_param("i", $accountId);
  $stmt->execute();
  $result = $stmt->get_result();

  $rows = [];
  $thaiMonths = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
  $daysOfWeek = ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์"];

  // ตรวจสอบว่ามีผลลัพธ์หรือไม่
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $row['OwnerRole'] = $row['OwnerRole'] === 'A' ? 'ผู้ดูแล' : 'สมาชิก';

          // แปลงวันที่เป็นภาษาไทย
          $date = new DateTime($row['BookingDate']);
          $thaiDate = "วัน".$daysOfWeek[$date->format('w')] . " ที่ " . $date->format('j') . " " . $thaiMonths[$date->format('n') - 1] . " " . ($date->format('Y') + 543);
          $row['ThaiBookingDate'] = $thaiDate;

          $rows[] = $row;
          $firstnamemember = $row["MemberName"];
          $lastnamemember = $row["Memberlastname"];
      }
  } else {
      echo "ไม่พบข้อมูลสมาชิก";
      exit;
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Invalid request";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Detailgang.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php' ?>

<div class="container mt-4">
  <!-- กล่องข้อความ -->
  <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
    <div class="custom-box text-center">
      <h1>รายการขอเข้าเล่นแบบก๊วน</h1>
    </div>
    <div class="custom-boxname text-center">
      <h3>ชื่อสมาชิก :&nbsp;</h3>
      <h3 id="<?php echo htmlspecialchars(($firstnamemember ?? '') . ' ' . ($lastnamemember ?? '')); ?>">
        <?php echo htmlspecialchars($firstnamemember . ' ' . $lastnamemember); ?>
    </h3>
    </div>
  </div>

  <div class="container">
    <h2 class="table-header"></h2>
    <table class="booking-table">
      <thead>
        <tr>
          <th>รายการ</th>
          <th>วันที่ก๊วนเปิดเล่น</th>
          <th>เวลาที่ก๊วนเปิดเล่น</th>
          <th>ชั่วโมงของก๊วน</th>
          <th>สนามที่ใช้บริการ</th>
          <th>เจ้าของก๊วน</th>
          <th>สถานะ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $index => $row): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['ThaiBookingDate']) ?></td>
            <td>
  <?php
  // แปลงเวลา StartTime และ EndTime
  $startTime = date('H:i', strtotime($row['StartTime'])) . ' น.';
  $endTime = date('H:i', strtotime($row['EndTime']));
  // ตรวจสอบว่า EndTime เป็น 00:00 หรือไม่
  if ($endTime === '00:00') {
      $endTime = '24:00';
  }
  $endTime .= ' น.';
  echo $startTime . ' - ' . $endTime;
  ?>
</td>

            <td><?= $row['Hours'] ?> ชั่วโมง</td>
            <td>สนามที่ <?= $row['CourtID'] ?></td>
            <td><?= htmlspecialchars($row['OwnerName']) ?><br>(<?= htmlspecialchars($row['OwnerRole']) ?>)</td>
            <td class="status <?= $row['Status'] === 'ขอเข้าเล่นสำเร็จ' ? 'green' : ($row['Status'] === 'รอชำระเงิน' ? 'orange' : 'red') ?>">
              <?= htmlspecialchars($row['Status']) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>