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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Listgroup.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>รายชื่อก๊วนที่เปิดให้ใช้บริการ</h1>
      </div>
    </div>
    <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div><br>
<div class="container">

<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการแก้ไข"]);
    exit;
}

$accountID = $_SESSION['user_idadmin'];

// ตรวจสอบค่า currentDate จาก URL ที่ได้รับ
$currentDate = isset($_GET['date']) ? date("Y-m-d", intval($_GET['date']) / 1000) : date("Y-m-d");



$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%Wที่ %e %M %Y') as BookingDate, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat,
            b.MaxPlayer, 
            b.Status, 
            b.CourtID, 
            b.Price, 
            b.CancelReason,
            c.FirstName as cfn,
            a.FirstName,
            a.Role,
            (SELECT COUNT(*) FROM groupmember gm WHERE gm.BookingID = b.BookingID AND gm.Status != 'ยกเลิก' AND gm.Status != 'ไม่อนุมัติ') as MemberCount,
            b.AccountID as OwnerID
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        LEFT JOIN account c ON b.CancelBy = c.AccountID
        WHERE b.BookingFormat = 'การขอเปิดก๊วน' 
        AND b.Status IN ('เปิดก๊วนสำเร็จ', 'จองสำเร็จ')
        AND b.BookingDate = ?
        ORDER BY b.BookingID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $currentDate); // bind ค่า currentDate
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="booking-table" id="Gangbooking">';
    echo '<thead>
            <tr>
                <th>รายการ</th>
                <th id="BookingDate">วันที่จอง</th>
                <th id="Time">เวลาที่จอง</th>
                <th id="Numberhours">ชั่วโมงของก๊วน</th>
                <th id="CourtID">สนามที่</th>
                <th id="Listgroup">จำนวนสมาชิก</th>
                <th id="FristName">จองโดย</th>
                <th id="Listgroup">การจัดการ</th>
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
                            $formattedDate = strtr($bookingDate, array_merge($thaiMonths, $thaiDays));

        echo '<tr data-booking-id="' . $row['BookingID'] . '">';
        echo '<td>' . $index . '</td>';
        echo '<td>' . $formattedDate . '</td>';
        echo '<td>' . $row['StartTime'] .' - '.$row['EndTime']. '</td>';
        echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
        echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
        echo '<td>' . $row['MemberCount'] . ' / ' . $row['MaxPlayer'] . ' คน</td>';
        echo '<td>' . $row['FirstName'] . '<br>(' . ($row['Role'] == 'M' ? 'สมาชิก' : 'ผู้ดูแล') . ')</td>';
        echo '<td>';

        // เงื่อนไขการแสดงปุ่ม
        if ($row['Role'] == 'A') {
            echo '<a href="Listnamegroup.php?bookingId=' . $row['BookingID'] . '" class="btn btn-primary"; style="margin-right:10px">รายชื่อสมาชิกก๊วน</a>';
            echo '<button class="btn btn-danger cancel-btn" data-booking-id="' . $row['BookingID'] . '">ยกเลิก</button>';
        } else {
            echo '-';
        }

        echo '</td>';
        echo '</tr>';

        $index++;
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p style="color:red;margin-left:160px">ไม่มีประวัติการจอง</p>';
}

$stmt->close();
$conn->close();
?>

<script>    
document.addEventListener('DOMContentLoaded', function () {
    const cancelButtons = document.querySelectorAll('.cancel-btn');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.closest('tr').getAttribute('data-booking-id');

            Swal.fire({
                title: "ยืนยันการยกเลิก?",
                text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกก๊วนนี้?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ใช่, ยกเลิกเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../php/cancel_group_booking.php', {
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
                                title: "สำเร็จ!",
                                text: data.message,
                                icon: "success",
                                confirmButtonColor: "#3085d6"
                            }).then(() => {
                                location.reload(); // รีโหลดหน้า
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
                            title: "ข้อผิดพลาด!",
                            text: "เกิดข้อผิดพลาดในการส่งคำขอ",
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


<!-- <div class="custom-boxprevious">
<button class="btn-previous" onclick="window.location.href='Homepage admin.php'">ย้อนกลับ</button>
</div> -->

<script src="/web_badmintaon_khlong_6/Admin/listgroup.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
