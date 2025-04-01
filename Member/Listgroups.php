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
  <link href="Listgroups.css" rel="stylesheet">
</head>
<body>

<?php
include '../php/member_menu.php';
?>
  

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
</div>

<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการแก้ไข"]);
    exit;
}

$accountID = $_SESSION['user_id'];

// รับค่า currentDate จาก JavaScript ผ่าน POST หรือใช้วันที่ปัจจุบัน
$currentDate = isset($_GET['date']) ? date("Y-m-d", intval($_GET['date']) / 1000) : date("Y-m-d");

// Query ข้อมูลการจองที่เกี่ยวข้องกับวันที่ที่เลือก
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%W ที่ %d %M') as BookingDate,
            (YEAR(b.BookingDate) + 543) AS BookingYear,  
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            b.Status, 
            b.CourtID, 
            b.Price, 
            b.CancelReason,
            b.MaxPlayer,
            c.FirstName as cfn,
            a.FirstName,
            a.Role,
            b.AccountID as OwnerID,
            (SELECT ItemDetail FROM info WHERE infoID = 16) AS Pricegruop,
            (SELECT COUNT(*) FROM groupmember gm WHERE gm.BookingID = b.BookingID AND gm.Status != 'ยกเลิก'AND gm.Status != 'ไม่อนุมัติ') as MemberCount,
            (SELECT COUNT(*) FROM groupmember gm WHERE gm.BookingID = b.BookingID AND gm.AccountID = ?) as UserBooked
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        LEFT JOIN account c ON b.CancelBy = c.AccountID
        WHERE b.BookingFormat = 'การขอเปิดก๊วน' 
        AND b.Status IN ('เปิดก๊วนสำเร็จ', 'จองสำเร็จ')
        AND b.BookingDate = ?
        ORDER BY b.BookingID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $accountID, $currentDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="booking-table" id="Gangbooking">';
    echo '<thead>
            <tr>
                <th>รายการ</th>
                <th id="BookingDate">วันที่จอง</th>
                <th id="StartTime">เวลาที่จอง</th>
                <th id="Numberhours">ชั่วโมงของก๊วน</th>
                <th id="CourtID">สนามที่</th>
                <th id="FristName">จองโดย</th>
                <th id="Listgroup">จำนวนสมาชิก</th>
                <th id="Cancelgroup">ค่าบริการ</th>
                <th id="Cancelgroup">การจัดการ</th>
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
        echo '<td>' . $row['StartTime'] .' - '. $row['EndTime'].'</td>';
        echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
        echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
        echo '<td>' . $row['FirstName'] . '<br>(' . ($row['Role'] == 'M' ? 'สมาชิก' : 'ผู้ดูแล') . ')</td>';
        echo '<td>' . $row['MemberCount'] . ' / ' . $row['MaxPlayer'] . ' คน</td>';
        echo '<td>' . $row['Pricegruop'] . ' บาท</td>';
        echo '<td>';

        if ($row['UserBooked'] > 0) {
            // ตรวจสอบว่าสถานะของสมาชิกที่ลงชื่อเป็น 'ยกเลิก' หรือไม่
            $checkCancelSql = "SELECT COUNT(*) AS CancelCount FROM groupmember 
                               WHERE BookingID = ? AND AccountID = ? AND Status = 'ยกเลิก'";
            $stmtCancel = $conn->prepare($checkCancelSql);
            $stmtCancel->bind_param('ii', $row['BookingID'], $accountID);
            $stmtCancel->execute();
            $cancelResult = $stmtCancel->get_result();
            $cancelRow = $cancelResult->fetch_assoc();
            $stmtCancel->close();
        
            if ($cancelRow['CancelCount'] > 0) {
                // ถ้าเคยยกเลิก ให้สามารถจองใหม่ได้
                echo '<a href="Detailgang.php?bookingId=' . $row['BookingID'] . '" class="btn btn-primary join-gang" data-id="' . $row['BookingID'] . '">ลงชื่อเล่นก๊วน</a>';
            } else {
                // ถ้าลงชื่อก๊วนแล้ว และยังไม่ได้ยกเลิก
                echo '<span style="color:green">ลงชื่อก๊วนนี้แล้ว</span>';
            }
        } elseif ($row['MemberCount'] >= $row['MaxPlayer']) {
            // ถ้าก๊วนเต็มแล้ว
            echo '<button class="btn btn-secondary" disabled>เต็มแล้ว</button>';
        } elseif ($row['OwnerID'] == $accountID) {
            // ถ้าเป็นเจ้าของก๊วน ให้สามารถดูรายชื่อสมาชิกในก๊วน
            echo '<a href="Listnamegroup.php?bookingId=' . $row['BookingID'] . '" class="btn btn-info Listgroup">รายชื่อสมาชิกก๊วน</a>';
        } else {
            // ปกติสามารถกดลงชื่อเล่นก๊วนได้
            echo '<a href="Detailgang.php?bookingId=' . $row['BookingID'] . '" class="btn btn-primary join-gang" data-id="' . $row['BookingID'] . '">ลงชื่อเล่นก๊วน</a>';
        }
        
        echo '</td>';
        echo '</tr>';

        $index++;
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p style="color:red;margin-top:20px;margin-left:120px">ไม่มีประวัติการจอง</p>';
}

$stmt->close();
$conn->close();
?>

<!-- <div class="custom-boxprevious">
    <button class="btn-previous" onclick="window.location.href='../Member/Member home page.php'">ย้อนกลับ</button>
</div> -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.join-gang').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // ป้องกันการเปลี่ยนหน้าโดยตรง
            
            const bookingId = this.getAttribute('data-id');
            const targetUrl = this.href; 

            Swal.fire({
                title: "ยืนยันการเข้าร่วม?",
                text: "คุณต้องการลงชื่อเล่นก๊วนนี้หรือไม่?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "ใช่, ลงชื่อ!",
                cancelButtonText: "ยกเลิก",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // ไม่มี alert แสดงข้อความใดๆ หลังจากลงชื่อ
                    window.location.href = targetUrl; // ไปที่หน้ารายละเอียด
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