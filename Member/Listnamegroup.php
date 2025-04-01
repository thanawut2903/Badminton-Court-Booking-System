<?php
session_start();
include '../php/member_navbar.php';
require '../php/dbconnect.php';

// ตรวจสอบว่าได้รับ BookingID ผ่าน GET หรือไม่
if (!isset($_GET['bookingId'])) {
  echo "<p>ไม่พบข้อมูลการจอง</p>";
  exit;
}

$bookingId = $conn->real_escape_string($_GET['bookingId']);

// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%W ที่ %e %M') as BookingDate,
            (YEAR(b.BookingDate) + 543) AS BookingYear,  
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            b.Status, 
            b.CourtID, 
            b.Price,
            b.MaxPlayer, 
            a.FirstName, 
            a.LastName,
            a.Tel,
            a.LineID,
            a.Username,
            b.MaxPlayer AS MaxMember,
            (SELECT COUNT(*) 
             FROM groupmember gm 
             WHERE gm.BookingID = b.BookingID 
               AND gm.Status NOT IN ('ยกเลิก', 'ไม่อนุมัติ')) AS MemberCount
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

// นับจำนวนผู้เล่นที่ลงชื่อ
$sqlCountPlayers = "SELECT COUNT(*) as PlayerCount FROM groupmember WHERE BookingID = ? AND Status = 'ขอเข้าเล่นสำเร็จ'";
$stmtCount = $conn->prepare($sqlCountPlayers);
$stmtCount->bind_param("i", $bookingId);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();

if ($resultCount->num_rows > 0) {
    $rowCount = $resultCount->fetch_assoc();
    $playerCount = $rowCount['PlayerCount'];
} else {
    $playerCount = 0;
}


// Query ข้อมูลสมาชิกในก๊วนที่เกี่ยวข้องกับ BookingID
$sql = "SELECT 
            gm.GroupMemberID, 
            gm.BookingID, 
            gm.Status, 
            b.CourtID, 
            a.FirstName AS MemberName,
            (SELECT ItemDetail FROM info WHERE infoID = 16) AS Pricegruop,
            (SELECT COUNT(*) FROM groupmember gm WHERE gm.BookingID = b.BookingID AND gm.Status != 'ยกเลิก'AND gm.Status != 'ไม่อนุมัติ') as MemberCount
        FROM groupmember gm
        JOIN account a ON gm.AccountID = a.AccountID
        JOIN booking b ON gm.BookingID = b.BookingID
        WHERE gm.BookingID = ?
        ORDER BY gm.GroupMemberID";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

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
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link href="Listnamegroup.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <div class="container mt-4">
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>รายชื่อสมาชิกก๊วน</h1>
      </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <table class="booking-table" id="Gangbooking">
        <thead>
          <tr>
            <th>รายการ</th>
            <th id="BookingDate">ชื่อสมาชิก</th>
            <th id="CourtID">สนามที่</th>
            <th id="price">ราคาค่าบริการ</th>
            <th id="Status">สถานะ</th>
            <th>จัดการสถานะ</th>
          </tr>
        </thead>
        <tbody>
          <?php $index = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>

              <td><?= $index ?></td>
              <td><?= $row['MemberName'] ?></td>
              <td>สนามที่ <?= $row['CourtID'] ?></td>
              <td> <?= $row['Pricegruop'] ?> บาท</td>
              <td><?= $row['Status'] ?></td>
              <?php $index++; ?>
              <td>
                <?php if ($row['Status'] == 'รอชำระเงิน'): ?>
                  <button class="btn btn-success approve-booking" data-groupmember-id="<?= $row['GroupMemberID'] ?>">อนุมัติ</button>
                  <button class="btn btn-danger cacel-booking" data-groupmember-id="<?= $row['GroupMemberID'] ?>">ไม่อนุมัติ</button>
                <?php elseif ($row['Status'] == 'ไม่อนุมัติ'): ?>
                  <span style="color:red;">ไม่อนุมัติ</span>
                  <?php elseif ($row['Status'] == 'ยกเลิก'): ?>
                    <span style="color:red;">ยกเลิกแล้ว</span>
                <?php else: ?>
                  <span style="color:green;">อนุมัติแล้ว</span>
                <?php endif; ?>
              </td>

            </tr>
          <?php endwhile; ?>

        </tbody>
      </table>

    <?php else: ?>
      <p style="margin-top:20px;color:red">ไม่มีข้อมูลสมาชิกในก๊วน</p>
    <?php endif; ?>


    <?php
    require '../php/dbconnect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // รับ GroupMemberID ที่ส่งมาจาก AJAX
      $groupMemberId = intval($_POST['groupMemberId']);

      // ตรวจสอบว่า GroupMemberID ถูกส่งมาหรือไม่
      if (!$groupMemberId) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล GroupMemberID"]);
        exit;
      }

      // อัปเดตสถานะในฐานข้อมูล
      $sql = "UPDATE groupmember SET Status = 'ขอเข้าเล่นสำเร็จ' WHERE GroupMemberID = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $groupMemberId);

      if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "เปลี่ยนสถานะสำเร็จ"]);
      } else {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเปลี่ยนสถานะ"]);
      }

      $stmt->close();
      $conn->close();
      exit;
    }
    ?>

<script>
  // จับการกดปุ่มอนุมัติ
  const approveButtons = document.querySelectorAll('.approve-booking');
  approveButtons.forEach(button => {
    button.addEventListener('click', function() {
      const groupMemberId = this.getAttribute('data-groupmember-id');

      // แสดง SweetAlert ยืนยันก่อนดำเนินการ
      Swal.fire({
        title: 'ยืนยันการอนุมัติ?',
        text: "คุณต้องการอนุมัติรายการนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, อนุมัติ!',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          // ส่งคำขอ AJAX ไปยังเซิร์ฟเวอร์เพื่ออัปเดตสถานะ
          fetch('../php/update_status_membergroup.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `groupMemberId=${groupMemberId}`
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  title: 'สำเร็จ!',
                  text: data.message,
                  icon: 'success',
                  confirmButtonText: 'ตกลง'
                }).then(() => {
                  location.reload(); // รีโหลดหน้าหลังเปลี่ยนสถานะสำเร็จ
                });
              } else {
                Swal.fire({
                  title: 'ผิดพลาด!',
                  text: data.message,
                  icon: 'error',
                  confirmButtonText: 'ตกลง'
                });
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถส่งคำขอได้ กรุณาลองใหม่',
                icon: 'error',
                confirmButtonText: 'ตกลง'
              });
            });
        }
      });
    });
  });
</script>

    <script>
      // จับการกดปุ่มไม่อนุมัติ
      const cancelButtons = document.querySelectorAll('.cacel-booking');
      cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
          const groupMemberId = this.getAttribute('data-groupmember-id');

          // ส่งคำขอ AJAX ไปยังเซิร์ฟเวอร์เพื่ออัปเดตสถานะ
          fetch('../php/cancel_status_membergroup.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `groupMemberId=${groupMemberId}`
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert(data.message);
                location.reload(); // รีโหลดหน้าหลังเปลี่ยนสถานะสำเร็จ
              } else {
                alert(data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('เกิดข้อผิดพลาดในการส่งคำขอ');
            });
        });
      });
    </script>


    <div class="booking-details-container mt-4">
      <div class="booking-header">
        <h3>รายละเอียดการเปิดก๊วน</h3>
      </div>
      <div class="booking-details">
        <p><strong>วันที่จอง :</strong> <?= $formattedDate ?></p>
        <p><strong>เวลาที่จอง :</strong> <?= $booking['StartTime'] ?>  - <?= $booking['EndTime'] ?> </p>
        <p><strong>รูปแบบการจอง :</strong> <?= $booking['BookingFormat'] ?></p>
        <p><strong>ชั่วโมงที่จอง :</strong> <?= $booking['NumberHours'] ?> ชั่วโมง</p>
        <p><strong>สนามที่ :</strong> <?= $booking['CourtID'] ?> </p>
      </div>
      <div class="user-header">
        <h3>ข้อมูลของผู้เปิดก๊วน</h3>
      </div>
      <div class="user-details">
        <p><strong>ชื่อ :</strong> <?= $booking['FirstName'] ?></p>
        <p><strong>ชื่อผู้ใช้งาน :</strong> <?= $booking['Username'] ?></p>
        <p><strong>LINE ID :</strong> <?= $booking['LineID'] ?></p>
        <p><strong>เบอร์โทรศัพท์ : </strong> <?= $booking['Tel'] ?></p>
        <p>
    <strong>จำนวนผู้เล่น : </strong> 
    <span style="color: <?= $playerCount < $booking['MaxPlayer'] ? 'green' : 'red' ?>;">
        <?= $booking['MemberCount'] ?> / <?= $booking['MaxPlayer'] ?> คน 
    </span>
</p>
      </div>
    </div>



    <div class="custom-boxprevious">
      <button class="btn-previous" onclick="window.location.href='../Member/Member home page.php'">ย้อนกลับ</button>
    </div>
  </div>

  <script src="editgeneral2.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>

</html>