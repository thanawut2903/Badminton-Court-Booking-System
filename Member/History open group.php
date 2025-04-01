<?php
session_start();
include '../php/member_navbar.php';
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>   
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="History open group.css" rel="stylesheet">
</head>
<body>
  
<?php
include '../php/member_menu.php';
?>

<div class="container mt-4">
    <!-- กล่องข้อความ -->
        <!-- ปุ่มข้อความประวัติการขอเข้าเล่นแบบก๊วน -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ประวัติการขอเปิดสนามแบบก๊วน</h1>
      </div>
    </div>
  <!-- ตารางประวัติการขอเปิดสนามแบบก๊วน -->
  <div class="container1">
  <?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
  exit;
}

$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session

// Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่
$sql = "SELECT BookingID, 
        DATE_FORMAT(BookingDate, 'วัน%Wที่ %e %M') as BookingDate,
        (YEAR(BookingDate) + 543) AS BookingYear,  
        TIME_FORMAT(StartTime, '%H:%i น.') as StartTime, 
        TIME_FORMAT(EndTime, '%H:%i น.') as EndTime, 
        NumberHours, BookingFormat, Status, CourtID, Price,MaxPlayer,PaymentSlips,
        (SELECT COUNT(*) FROM groupmember gm 
         WHERE gm.BookingID = booking.BookingID 
         AND gm.Status != 'ยกเลิก' 
         AND gm.Status != 'ไม่อนุมัติ') as MemberCount
        FROM booking 
        WHERE AccountID = ? 
        AND BookingFormat = 'การขอเปิดก๊วน'  
        ORDER BY BookingID DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountID);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
  echo '<table class="booking-table" id="Gangbooking">';
  echo '<thead>
          <tr>
              <th>รายการ</th>
              <th id="BookingDate">วันที่จอง</th>
              <th id="Time">เวลาที่เปิดก๊วน</th>
              <th id="Numberhours">ชั่วโมงที่จอง</th>
              <th id="Status">สถานะ</th>
              <th id="CourtID">สนามที่</th>
              <th id="Price">ค่าบริการ</th>
              <th>สลิปการโอนเงิน</th>
              <th>รายชื่อสมาชิกก๊วน</th>
              <th>จำนวนสมาชิก</th>
              <th>จัดการก๊วน</th>
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
      echo '<td>' . $index. '</td>';
      echo '<td>' . $formattedDate . '</td>';
      echo '<td>' . $row['StartTime'] .' - '. $row['EndTime'].'</td>';
      echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';

      // กำหนดสถานะการจอง
      $statusClass = '';
      $actionButton = '';
      $statusStyle = '';

      switch ($row['Status']) {
          case 'จองสำเร็จ':
              $statusClass = 'success';
              $statusStyle = 'color:green;';
                $actionButton = '<button class="btn btn-danger cancel-button" data-id="' . $row['BookingID'] . '">ยกเลิก</button>';
              break;
              case 'รอชำระเงิน':
                $statusClass = 'pending';
                $statusStyle = 'color:orange;';
            
                if (!empty($row['PaymentSlips'])) {
                    // ถ้ามีสลิปการโอนเงินแล้ว แสดงปุ่ม "แก้ไขสลิป"
                    $actionButton = '<button class="btn btn-warning edit-slip-button" data-url="/web_badmintaon_khlong_6/Member/Editslipgroup.php?bookingID=' . urlencode($row['BookingID']) . '">แก้ไขสลิป</button>';
                    $actionButton .= '<button class="btn btn-danger cancel-button" style="margin-left:10px;" data-id="' . $row['BookingID'] . '">ยกเลิก</button>';      
                } else {
                    // ถ้ายังไม่มีสลิป แสดงปุ่ม "ชำระเงิน"
                    $actionButton = '<button class="btn btn-primary pay-button" data-url="/web_badmintaon_khlong_6/Member/Paymentwithownergroup.php?bookingID=' . urlencode($row['BookingID']) . '">ชำระเงิน</button>';
                    $actionButton .= '<button class="btn btn-danger cancel-button" style="margin-left:10px;" data-id="' . $row['BookingID'] . '">ยกเลิก</button>';
                }
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
                    $statusClass = 'pending-approval';
                    $statusStyle = 'color:#34e1eb;'; 
      }

      echo '<td><span class="status ' . $statusClass .'"style="'.$statusStyle.'">' . $row['Status'] . '</span></td>';   
      if ($row['CourtID'] == 0) {
        echo '<td>ยังไม่ระบุสนาม</td>';
    } else {
        echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
    }    
      echo '<td>' . $row['Price'] . ' บาท</td>';
      echo '<td>';
      if ($row['PaymentSlips']) {
        // ตรวจสอบว่า path ที่เก็บอยู่ใน PaymentSlips มีรูปแบบถูกต้องหรือไม่
        $slipPath = $row['PaymentSlips'];  // Path ที่เก็บใน PaymentSlips
        // ใช้ path แบบสัมพัทธ์ (relative path)
        $filePath = "../" . $slipPath; // เพิ่ม "../" เพื่อให้สามารถเข้าถึงจากโฟลเดอร์ที่ต้องการ
        echo '<a href="' . $filePath . '" target="_blank"><img src="' . $filePath . '" alt="Payment Slip" style="width: 50px; height: 100px;"/></a>';
    } else {
        echo '<span style="color:red">ยังไม่มีสลิปการโอนเงิน</span>';
    }
      echo '<td>';
      if($row['Status'] == 'จองสำเร็จ'){
      echo '<a href="Listnamegroup.php?bookingId=' . $row['BookingID'] . '" class="btn btn-info Listgroup">รายชื่อสมาชิกก๊วน</a>';
      }else{
        echo '<span class="status ' . $statusClass .'"style="'.$statusStyle.'">' . $row['Status'] . '</span>'; 
      }
      echo '<td>' . $row['MemberCount'] . ' / ' . $row['MaxPlayer'] . ' คน</td>';
      echo '<td>' . $actionButton . '</td>';
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


  <!-- Script สำหรับการยกเลิกการจอง -->
  <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cancel-button').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.dataset.id;

            // ใช้ SweetAlert2 สำหรับยืนยันการยกเลิก
            Swal.fire({
                title: "ยืนยันการยกเลิก?",
                text: "คุณแน่ใจว่าต้องการยกเลิกการจองนี้?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ใช่, ยกเลิกเลย!",
                cancelButtonText: "ไม่, กลับไป",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../php/cancel_booking_member.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ bookingId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "ยกเลิกสำเร็จ!",
                                text: "การจองของคุณถูกยกเลิกเรียบร้อย",
                                icon: "success",
                                confirmButtonColor: "#3085d6"
                            }).then(() => {
                                location.reload(); // รีโหลดหน้าเมื่อยกเลิกสำเร็จ
                            });
                        } else {
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: data.message,
                                icon: "error",
                                confirmButtonColor: "#d33"
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด!",
                            text: "ไม่สามารถยกเลิกได้ในขณะนี้",
                            icon: "error",
                            confirmButtonColor: "#d33"
                        });
                    });
                }
            });
        });
    });
});
  </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pay-button').forEach(button => {
        button.addEventListener('click', function() {
            const paymentUrl = this.dataset.url;

            Swal.fire({
                title: "ยืนยันการชำระเงิน?",
                text: "คุณต้องการดำเนินการไปยังหน้าชำระเงินหรือไม่?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่, ไปที่หน้าชำระเงิน!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = paymentUrl;
                }
            });
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-slip-button').forEach(button => {
        button.addEventListener('click', function() {
            const paymentUrl = this.dataset.url;

            Swal.fire({
                title: "แก้ไขสลิปการโอนเงิน?",
                text: "คุณต้องการดำเนินการไปยังหน้าชำระเงินหรือไม่?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่, แก้ไขสลิปการโอนเงิน!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = paymentUrl;
                }
            });
        });
    });
});
</script>
</div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
