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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Notapproved.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>รายการจองที่ยกเลิกแล้ว</h1>
      </div>
    </div>


    <div class="filter-container">
    <a href="/web_badmintaon_khlong_6/Admin/Allstatus.php" class="btn1 filter-all">รายการทั้งหมด</a>
    <a href="/web_badmintaon_khlong_6/Admin/Nopay.php" class="btn1 filter-pending">ยังไม่ได้ชำระเงิน</a>
    <a href="/web_badmintaon_khlong_6/Admin/Approve.php" class="btn1 filter-approved">อนุมัติแล้ว</a>
    <a href="/web_badmintaon_khlong_6/Admin/Notapproved.php" class="btn1 filter-cancelled">ยกเลิกแล้ว</a>
</div>


<div class="container">

<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการแก้ไข"]);
    exit;
  }

  $accountID = $_SESSION['user_idadmin'];

  // Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่
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
  b.CancelReason,
  c.FirstName as cfn,
  a.role as cancelRole,  
  a.FirstName,
  ct.CourtName 
FROM booking b
JOIN account a ON b.AccountID = a.AccountID
LEFT JOIN account c ON b.CancelBy = c.AccountID 
LEFT JOIN court ct ON b.CourtID = ct.CourtID
WHERE b.Status = 'ยกเลิก' 
ORDER BY b.BookingDate DESC";

  
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  

if ($result->num_rows > 0) {
    echo '<table class="booking-table" id="Gangbooking">';
    echo '<thead>
            <tr>
                <th>รายการ</th>
                <th id="BookingDate">วันที่จอง</th>
                <th id="Time">เวลาที่จอง</th>
                <th id="Numberhours">ชั่วโมงที่จอง</th>
                <th id="BookingFormat">รูปแบบการจอง</th>
                <th id="Status">สถานะ</th>
                <th id="CourtID">สนามที่</th>
                <th id="FristName">จองโดย</th>
                <th>เหตุผลการยกเลิก</th>
                <th>ยกเลิกการจอง</th>
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
      echo '<tr>';
      echo '<td>' . $index . '</td>';
      echo '<td>' . $formattedDate . '</td>';
      if (strpos($row['EndTime'], '24') === 0) {
        $row['EndTime'] = '00:00 น.';
    }
      echo '<td>' . $row['StartTime'] .' - '.$row['EndTime']. '</td>';
      echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
      echo '<td>' . $row['BookingFormat'] . '</td>';

      // กำหนดสถานะการจอง
      $statusClass = '';
      $actionButton = '';
      $approveButton = '';
      $statusStyle = '';


      switch ($row['Status']) {
          case 'จองสำเร็จ':
              $statusClass = 'success';
              $cancelButton = '<button class="btn btn-danger">ยกเลิก</button>';
              $statusStyle = 'color:green;';
              break;
          case 'รอชำระเงิน':
              $statusClass = 'pending';
              $actionButton = '<a href="/web_badmintaon_khlong_6/Member/Payment page with owner.php" class="btn btn-primary">ชำระเงิน</a>';
              $approveButton = '<a href="/web_badmintaon_khlong_6/Member/Payment page with owner.php" class="btn-approve">vo6</a>';
              $statusStyle = 'color:orange;';
              break;
          case 'ใช้บริการแล้ว':
              $statusClass = 'used';
              $actionButton = 'ใช้บริการแล้ว';
              $statusStyle = 'color:gray;';
              break;
          case 'ยกเลิก':
          $statusClass = 'cancel';
          $statusStyle = 'color:red;';
            
      }

      echo '<td><span class="status ' . $statusClass . '"style="'.$statusStyle.'">' . $row['Status'] . '</span></td>';
      echo '<td>' . htmlspecialchars($row['CourtName'] ?? 'ยังไม่ระบุ') . '</td>';
      echo '<td>' . $row['FirstName'] . '</td>';
      echo '<td>';
      if ($row['Status'] == 'รอชำระเงิน') {
          echo '<button class="btn btn-success approve-booking" data-booking-id="' . $row['BookingID'] . '">อนุมัติ</button>';
      }else if ($row['Status'] == 'ยกเลิก'){
        echo '<span style="color:red;">'.$row['CancelReason'].'</span>';
      }
      else {
          echo '<span style="color:green;">อนุมัติแล้ว</span>';
      }
      echo '</td>';
      echo '<td>';
      if ($row['Status'] == 'รอชำระเงิน') {
          echo '<button class="btn btn-success approve-booking" data-booking-id="' . $row['BookingID'] . '">อนุมัติ</button>';
      } else if ($row['Status'] == 'ยกเลิก') {
          // ✅ ตรวจสอบ role ว่าเป็น 'a' หรือไม่
          if ($row['cancelRole'] == 'A') {
              echo '<span style="color:red;">ยกเลิกโดยผู้ดูแลระบบ</span>';
          } else {
              echo '<span style="color:red;">ยกเลิกโดย<br>' . htmlspecialchars($row['cfn']) . '</span>';
          }
      } else {
          echo '<span style="color:green;">อนุมัติแล้ว</span>';
      }
      echo '</td>';      
      echo '</tr>';

      $index++;
  }

  echo '</tbody>';
  echo '</table>';
} else {
  echo '<p>ไม่มีประวัติการจอง</p>';
}

$stmt->close();
$conn->close();
?>



    <div class="custom-boxprevious">
    <button class="btn-previous" onclick="window.location.href='/web_badmintaon_khlong_6/Admin/Homepage admin.php'">ย้อนกลับ</button>
    </div>

    

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
