<?php
session_start();
include '../php/admin_navbar.php';
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
  <link href="Edit status gang.css" rel="stylesheet">
</head>
<body>
<?php include '../php/admin_menu.php'; ?>
  
<div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>จัดการสถานะการขอเข้าเล่นแบบก๊วน</h1>
      </div>
  </div>
  <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
    </div>
    <div class="container">
<div class="container">
<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
  echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
  exit;
}

$accountID = $_SESSION['user_idadmin']; // ดึง AccountID จาก Session

// รับค่า currentDate จาก JavaScript ผ่าน POST หรือใช้วันที่ปัจจุบัน
$currentDate = isset($_GET['date']) ? date("Y-m-d",intval($_GET['date'])/1000) : date("Y-m-d");

// Query ข้อมูลการจองเฉพาะที่ Role = A และ BookingFormat = 'การขอเปิดก๊วน'
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%Wที่ %e %M') as BookingDate, 
            (YEAR(b.BookingDate) + 543) AS BookingYear, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            b.Status, 
            b.CourtID, 
            b.Price,
            b.Maxplayer, 
            b.CancelReason,
            c.FirstName as cfn,
            a.Role,
            a.FirstName, 
            (SELECT COUNT(*) FROM groupmember gm WHERE gm.BookingID = b.BookingID AND gm.Status != 'ยกเลิก'AND gm.Status != 'ไม่อนุมัติ') as MemberCount,
            (SELECT ItemDetail FROM info WHERE infoID = 15) AS MaxMember
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        LEFT JOIN account c ON b.CancelBy = c.AccountID
        WHERE b.BookingFormat = 'การขอเปิดก๊วน' 
        AND a.Role = 'A'
        AND DATE(b.BookingDate) = ?
        ORDER BY b.BookingID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentDate); // ผูกตัวแปรวันที่เข้า SQL
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo '<table class="booking-table" id="Gangbooking">';
  echo '<thead>
          <tr>
              <th>รายการ</th>
              <th id="BookingDate">วันที่จอง</th>
              <th id="StartTime">เวลาที่จอง</th>
              <th id="Numberhours">ชั่วโมงที่จอง</th>
              <th id="CourtID">สนามที่</th>
              <th id="FirstName">เปิดโดย</th>
              <th>จำนวนสมาชิก</th>
              <th>รายชื่อสมาชิกก๊วน</th>
          </tr>
      </thead>';
  echo '<tbody>';

  $index = 1;

  // แสดงข้อมูลในแต่ละแถว
  while ($row = $result->fetch_assoc()) {
    // แปลงวันที่เป็นฟอร์แมตภาษาไทย
    $thaiMonths = [
      'January' => 'มกราคม',
      'February' => 'กุมภาพันธ์',
      'March' => 'มีนาคม',
      'April' => 'เมษายน',
      'May' => 'พฤษภาคม',
      'June' => 'มิถุนายน',
      'July' => 'กรกฎาคม',
      'August' => 'สิงหาคม',
      'September' => 'กันยายน',
      'October' => 'ตุลาคม',
      'November' => 'พฤศจิกายน',
      'December' => 'ธันวาคม'
  ];
  
  $thaiDays = [
      'Monday' => 'จันทร์',
      'Tuesday' => 'อังคาร',
      'Wednesday' => 'พุธ',
      'Thursday' => 'พฤหัสบดี',
      'Friday' => 'ศุกร์',
      'Saturday' => 'เสาร์',
      'Sunday' => 'อาทิตย์'
  ];

  $bookingDate = $row['BookingDate'];
  $bookingYear = $row['BookingYear']; // ปี พ.ศ.
  $formattedDate = strtr($bookingDate, array_merge($thaiMonths, $thaiDays)).' '.$bookingYear;

  $bookingId = $row['BookingID']; // กำหนดค่าจาก row ปัจจุบัน
  // var_dump($bookingId); // ตรวจสอบค่า bookingId ว่ามีจริงไหม
  
  $sqlCountPlayers = "SELECT COUNT(*) as PlayerCount FROM groupmember WHERE BookingID = ? AND Status = 'ขอเข้าเล่นสำเร็จ'";
  $stmtCount = $conn->prepare($sqlCountPlayers);
  $stmtCount->bind_param("i", $bookingId);
  $stmtCount->execute();
  $resultCount = $stmtCount->get_result();
  
  if ($rowCount = $resultCount->fetch_assoc()) {
      $playerCount = $rowCount['PlayerCount'] ?? 0; // ใช้ ?? 0 ป้องกัน NULL
  } else {
      $playerCount = 0;
  }
  
      echo '<tr>';
      echo '<td>' . $index. '</td>';
      echo '<td>' . $formattedDate . '</td>';
      echo '<td>' . $row['StartTime'] .' - '.$row['EndTime']. '</td>';
      echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';

      // กำหนดสถานะการจอง
      $statusClass = '';
      $actionButton = '';
      $approveButton = '';
      $statusStyle = '';

      switch ($row['Status']) {
          case 'เปิดก๊วนสำเร็จ':
              $statusClass = 'success';
              $statusStyle = 'color:green;';
              break;
          case 'รอชำระเงิน':
              $statusClass = 'pending';
              $statusStyle = 'color:orange;';
              break;
          case 'ใช้บริการแล้ว':
              $statusClass = 'used';
              $statusStyle = 'color:gray;';
              break;
          case 'ยกเลิก':
              $statusClass = 'cancel';
              $statusStyle = 'color:red;';
              break;
      }

      
      echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
      echo '<td>' . $row['FirstName'] . '</td>';
      echo '<td>'.$row['MemberCount']." / ". $row['Maxplayer'] ." คน". '</td>';
      echo '<td>';
      if ($row['Status'] != 'ยกเลิก') {
          echo '<a href="Listnamegroup.php?bookingId=' . $row['BookingID'] . '" class="btn btn-primary Listgroup">รายชื่อสมาชิกก๊วน</a>';
      } else {
        echo '<span style="color: red;">ยกเลิกแล้ว</span>';
      }
      echo '</td>'; 
      echo '</tr>';

      $index++;
  }

  echo '</tbody>';
  echo '</table>';
} else {
  echo '<p style="color:red;margin-left:150px">ไม่มีประวัติการจอง</p>';
}  

$stmt->close();
$conn->close();
?>
    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
