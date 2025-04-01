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
  <link href="Gang history.css" rel="stylesheet">
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
        <h1>ประวัติการขอเข้าเล่นแบบก๊วน</h1>
      </div>
      </div>

      
  <!-- ตารางประวัติการขอเข้าเล่นแบบก๊วน -->
  <div class="container1">
  <?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
    exit;
}

$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session

// Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่ผ่าน groupmember และ booking
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
            gm.Status as GroupStatus,
            (SELECT ItemDetail FROM info WHERE infoID = 16) AS Pricegruop,
            acc.FirstName AS OwnerFirstName,
            acc.Role AS OwnerRole
        FROM groupmember gm
        JOIN booking b ON gm.BookingID = b.BookingID
        JOIN account acc ON b.AccountID = acc.AccountID
        WHERE gm.AccountID = ?
        ORDER BY b.BookingID DESC";


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
                <th id="Time">เวลาที่จอง</th>
                <th id="Numberhours">ชั่วโมงที่จอง</th>
                <th id="CourtID">สนามที่</th>
                <th id="Price">ค่าบริการ</th>
                <th>สถานะก๊วน</th>
                <th>เปิดก๊วนโดย</th>
                <th>จัดการการจอง</th>
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
        echo '<td>' . $row['StartTime'] .' - '. $row['EndTime'] . '</td>';
        echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';

            // กำหนดสถานะการจอง
            $statusClass = '';
            $actionButton = '';
            $approveButton = '';
            $statusStyle = '';


            switch ($row['GroupStatus']) {
              case 'ขอเข้าเล่นสำเร็จ':
                  $statusClass = 'success';
                  $cancelButton = '<button class="btn btn-danger">ยกเลิก</button>';
                  $statusStyle = 'color:green;';
                  break;
                  case 'รอชำระเงิน':
                    $statusClass = 'pending';
                    $actionButton = '<button class="btn btn-primary pay-button" data-url="/web_badmintaon_khlong_6/Member/Payment page with owner.php">ชำระเงิน</button>';
                    $statusStyle = 'color:orange;';
                    break;   
              case 'ใช้บริการแล้ว':
                  $statusClass = 'used';
                  $actionButton = 'ใช้บริการแล้ว';
                  $statusStyle = 'color:gray;';
                  break;
              case 'ยกเลิก':
              case 'ไม่อนุมัติ'; 
              $statusClass = 'cancel';
              $statusStyle = 'color:red;';
              break;
              case 'รออนุมัติ':
                $statusClass = 'pending-approval';
                $statusStyle = 'color:#34e1eb;'; 
            }
            
        echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
        echo '<td>' . $row['Pricegruop'] . ' บาท</td>';
        echo '<td>';
      echo '<span style="' . $statusStyle . '">' . $row['GroupStatus'] . '</span>';
      echo '</td>';
      
      switch ($row['OwnerRole']){
        case 'A':
            $role = 'ผู้ดูแล';
            break;
        case 'M':
            $role = 'สามชิก';
      }

      echo '<td>' . $row['OwnerFirstName'].'<br>'.'('.$role.')'. '</td>';
      echo '<td>';
if ($row['GroupStatus'] == 'รอชำระเงิน') {
    $paymentUrl = ($row['OwnerRole'] == 'A') 
    ? '/web_badmintaon_khlong_6/Member/Paymentgroupwithowner.php?bookingID=' . rawurlencode($row['BookingID'])
    : '/web_badmintaon_khlong_6/Member/Payment%20page%20with%20member.php?bookingId=' . rawurlencode($row['BookingID']);
    echo '<button class="btn btn-primary pay-button" style="margin-right:10px;" data-url="' . $paymentUrl . '">ชำระเงิน</button>';
}         
      if ($row['GroupStatus'] == 'รอชำระเงิน' || $row['GroupStatus'] == 'ขอเข้าเล่นสำเร็จ') {
        echo '<button class="btn btn-danger cancel-booking" data-booking-id="' . $row['BookingID'] . '">ยกเลิก</button>';
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelButtons = document.querySelectorAll('.cancel-booking');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-booking-id');

            Swal.fire({
                title: "ยืนยันการยกเลิก?",
                text: "คุณต้องการยกเลิกการขอเข้าเล่นก๊วนนี้หรือไม่?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ใช่, ยกเลิกเลย!",
                cancelButtonText: "ไม่, กลับไป",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../php/cancel_group_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `bookingId=${bookingId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "ยกเลิกสำเร็จ!",
                                text: data.message,
                                icon: "success",
                                confirmButtonColor: "#3085d6"
                            }).then(() => {
                                location.reload(); // รีโหลดหน้าเพื่ออัปเดตข้อมูล
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
                            text: "ไม่สามารถยกเลิกได้ กรุณาลองใหม่อีกครั้ง",
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
</div> 
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
