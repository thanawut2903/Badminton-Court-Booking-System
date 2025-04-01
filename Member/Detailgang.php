<?php
session_start();
include '../php/member_navbar.php';
require '../php/dbconnect.php';


$bookingId = $conn->real_escape_string($_GET['bookingId']);


// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, '%W ที่ %d %M') as BookingDate, 
            (YEAR(b.BookingDate) + 543) AS BookingYear, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            b.Status, 
            b.CourtID, 
            b.Price, 
            a.FirstName, 
            a.LastName,
            a.Tel,
            a.LineID,
            a.Username,
            (SELECT ItemDetail FROM info WHERE infoID = 16) AS Pricegruop
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        WHERE b.BookingID = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $booking = $result->fetch_assoc();
} else {
  echo "<p>ไม่พบข้อมูลการจอง</p>";
  exit;
}

$stmt->close();
$conn->close();

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


$bookingDate = $booking['BookingDate'];
$bookingYear = $booking['BookingYear']; // ปี พ.ศ.
$formattedDate = strtr($bookingDate, array_merge($thaiMonths, $thaiDays)).' '.$bookingYear;
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
  <link href="Detailgang.css" rel="stylesheet">
</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>การขอเข้าเล่นแบบก๊วน</h1>
      </div>
    </div>


<div class="container mt-0">
    <!-- กล่องข้อความ -->
    <div class="booking-details-container">
  <div class="booking-header">
    <h3>รายละเอียดการของก๊วน</h3>
  </div>
  <div class="booking-details">
    <p><strong>วันที่จอง :</strong> <?=  $formattedDate ?></p>
    <p>
    <strong>เวลาที่จอง :</strong> <?= $booking['StartTime'] ?>  - <?= $booking['EndTime'] ?>
</p>
    <!-- <p><strong>รูปแบบการจอง :</strong> <?= $booking['BookingFormat'] ?></p> -->
    <p><strong>ชั่วโมงที่จอง :</strong> <?= $booking['NumberHours'] ?> ชั่วโมง</p>
    <p><strong>สนามที่ :</strong> <?= $booking['CourtID'] ?> </p>
    <p><strong>ราคา : </strong><?= $booking['Pricegruop']?> บาท</p>
    <!-- <p style="margin-top: 10px;"><strong>ราคาที่ต้องชำระ : </strong><?= $booking['Price'] ?> บาท</p> -->
  </div>
  <div class="user-header">
    <h3>ข้อมูลของผู้เปิดก๊วน</h3>
  </div>
  <div class="user-details">
    <p><strong>ชื่อ :</strong> <?=$booking['FirstName'] ?></p>
    <p><strong>ชื่อผู้ใช้งาน :</strong> <?=$booking['Username']?></p>
    <p><strong>LINE ID :</strong> <?=$booking['LineID']?></p>
    <p><strong>เบอร์โทรศัพท์ : </strong>  <?= $booking['Tel'] ?></p>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function saveBooking() {
    // รับค่า BookingID จาก URL
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const bookingId = urlParams.get('bookingId');

    if (!bookingId) {
        Swal.fire({
            title: 'ไม่พบ Booking ID!',
            text: 'กรุณาลองใหม่อีกครั้ง.',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    // ส่งข้อมูลไปยัง Backend
    fetch('../php/save_groupmember.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            bookingId: bookingId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'บันทึกข้อมูลสำเร็จ!',
                    text: 'คุณจะถูกพามาที่หน้าประวัติการจอง.',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = "Gang history.php"; // เปลี่ยนเส้นทางไปยังหน้า Gang history.php
                });
            } else {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: data.message + '. กรุณาลองอีกครั้ง.',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                title: 'ไม่สามารถบันทึกข้อมูลได้',
                text: 'กรุณาลองใหม่.',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        });
}
</script>




<div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
    <div class="custom-save">
    <button class="btn-save" onclick="saveBooking()">ยืนยันการขอเข้าเล่น</button>
    </div>
    



    <!-- Bootstrap JS -->
    <!-- <script src="listgroup.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
