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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Edit open gang.css" rel="stylesheet">
</head>
<body>
 
<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>จัดการขอเปิดสนามแบบก๊วน</h1>
      </div>
    </div>
    <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
    </div>
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


// Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%Wที่ %e %M') as BookingDate,
            (YEAR(b.BookingDate) + 543) AS BookingYear,  
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            CASE 
                WHEN b.Status = 'จองสำเร็จ' THEN 'เปิดก๊วนสำเร็จ'
                WHEN b.Status = 'รอชำระเงิน' THEN 'รอชำระเงิน'
                WHEN b.Status = 'ยกเลิก' THEN 'ยกเลิก'
                ELSE b.Status
            END AS Status, 
            b.CourtID, 
            b.Price, 
            b.CancelReason,
            c.FirstName as cfn,
            a.FirstName 
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        LEFT JOIN account c ON b.CancelBy = c.AccountID
        WHERE b.BookingFormat = 'การขอเปิดก๊วน' 
        AND DATE(b.BookingDate) = ?
        ORDER BY b.BookingID DESC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentDate);
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
              <th id="Status">สถานะ</th>
              <th id="CourtID">สนามที่</th>
              <th id="FirstName">จองโดย</th>
              <th>อนุมัติการจอง</th>
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
      echo '<td>' . $formattedDate. '</td>';
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
          case 'รออนุมัติ':
            $statusStyle = 'color:#34e1eb;';
            
      }

      echo '<td><span class="status ' . $statusClass . '"style="'.$statusStyle.'">' . $row['Status'] . '</span></td>';
        echo '<td>';
        if ($row['CourtID'] == 0) {
            echo 'ยังไม่ระบุสนาม';
        } else {
            echo 'สนามที่ ' . $row['CourtID'];
        }
        echo '</td>';

      echo '<td>' . $row['FirstName'] . '</td>';
      echo '<td>';
      if ($row['Status'] == 'รออนุมัติ') {
          echo '<button class="btn btn-success approve-booking" style="color:black;" data-booking-id="' . $row['BookingID'] . '">อนุมัติ</button>';
      } else if ($row['Status'] == 'ยกเลิก') {
          echo '<span style="color:red;">' . $row['CancelReason'] . '</span>';
      } else if ($row['Status'] != 'รอชำระเงิน') { // ✅ ห้ามแสดง "อนุมัติแล้ว" ถ้าสถานะเป็น "รอชำระเงิน"
          echo '<span style="color:green;">อนุมัติแล้ว</span>';
      }
      
      // ✅ แก้ไขปุ่มยกเลิกให้ถูกต้อง
      if ($row['Status'] == 'รอชำระเงิน' || $row['Status'] == 'รออนุมัติ') {
          echo '<button class="btn btn-cancel cancel-booking" style="color:black; margin-left:10px;" data-booking-id="' . $row['BookingID'] . '">ยกเลิก</button>';
      } else if ($row['Status'] == 'ยกเลิก') {
          echo '<span style="color:red;">ยกเลิกโดย<br>' . $row['cfn'] . '</span>';
      }
      
      

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


<script>
// Event listener สำหรับปุ่มอนุมัติการจอง
document.addEventListener("DOMContentLoaded", function () {
    const approveButtons = document.querySelectorAll('.approve-booking');

    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');

            Swal.fire({
                title: 'ยืนยันการอนุมัติ?',
                text: "คุณต้องการอนุมัติการจองนี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'อนุมัติ!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งคำขอ AJAX เพื่ออนุมัติการจอง
                    fetch('../php/approve_booking.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ bookingId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // เปลี่ยนเส้นทางไปยังหน้า Detailbooking.php ทันที
                            window.location.href = data.redirect;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถอนุมัติการจองได้ กรุณาลองใหม่',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        });
    });
});

</script>


<script>
/// Event listener สำหรับปุ่มไม่อนุมัติการจอง
document.addEventListener("DOMContentLoaded", function () {
    const cancelButtons = document.querySelectorAll('.cancel-booking');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            console.log(bookingId);

            Swal.fire({
                title: 'ยืนยันการยกเลิก?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ยกเลิกเลย!',
                cancelButtonText: 'กลับไป'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งคำขอ AJAX เพื่อยกเลิกการจอง
                    fetch('../php/cancel_booking.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ bookingId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // เปลี่ยนเส้นทางไปยังหน้า Detailbooking.php โดยไม่มี popup แจ้งเตือน
                            window.location.href = data.redirect;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถยกเลิกการจองได้ กรุณาลองใหม่',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        });
    });
});
</script>
    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
