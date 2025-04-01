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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Nopay.css" rel="stylesheet">
</head>
<body>
  
    <?php include '../php/admin_menu.php'; ?>
  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>รายการที่รอชำระเงิน	</h1>
      </div>
    </div>

    <div class="filter-container">
    <a href="/web_badmintaon_khlong_6/Admin/Allstatus.php" class="btn1 filter-all">รายการทั้งหมด</a>
    <a href="/web_badmintaon_khlong_6/Admin/Nopay.php" class="btn1 filter-pending">รอชำระเงิน</a>
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
            b.PaymentSlips,  
            a.FirstName,
            ct.CourtName  
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        LEFT JOIN court ct ON b.CourtID = ct.CourtID
        WHERE b.Status = 'รอชำระเงิน'
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
              <th id="Price">ค่าบริการ</th>
              <th>สลิปการโอนเงิน</th>
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
      echo '<td>' . $formattedDate . '</td>';
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
      }

      echo '<td><span class="status ' . $statusClass . '"style="'.$statusStyle.'">' . $row['Status'] . '</span></td>';
      echo '<td>' . htmlspecialchars($row['CourtName'] ?? 'ยังไม่ระบุ') . '</td>';
      echo '<td>' . $row['FirstName'] . '</td>';
      echo '<td>' . $row['Price'] . ' บาท</td>';
      echo '<td>';
      if ($row['PaymentSlips']) {
        // ตรวจสอบว่า path ที่เก็บอยู่ใน PaymentSlips มีรูปแบบถูกต้องหรือไม่
        $slipPath = $row['PaymentSlips'];  // Path ที่เก็บใน PaymentSlips
        // ใช้ path แบบสัมพัทธ์ (relative path)
        $filePath = "../" . $slipPath; // เพิ่ม "../" เพื่อให้สามารถเข้าถึงจากโฟลเดอร์ที่ต้องการ
        echo '<a href="' . $filePath . '" target="_blank"><img src="' . $filePath . '" alt="Payment Slip" style="width: 50px; height: 100;"/></a>';
    } else {
        echo '<span style="color:red;">ยังไม่มีสลิปการโอนเงิน</span>';
    }
      echo '<td>';
      if ($row['Status'] !== 'จองสำเร็จ') {
          echo '<button class="btn btn-success approve-booking" data-booking-id="' . $row['BookingID'] . '">อนุมัติ</button>';
      } else {
          echo '<span style="color:green;">อนุมัติแล้ว</span>';
      }
      if ($row['Status'] == 'รอชำระเงิน'){
        echo '<button class="btn btn-cancel cancel-booking" style="margin-left:10px;" data-booking-id="' .$row['BookingID']. '">ยกเลิก</button>';
      }
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

    <div class="custom-boxprevious">
    <button class="btn-previous" onclick="window.location.href='/web_badmintaon_khlong_6/Admin/Homepage admin.php'">ย้อนกลับ</button>
    </div>

    

    <!-- Bootstrap JS -->
    <script src="/web_badmintaon_khlong_6/Admin/listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
