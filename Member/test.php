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
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="STbookinghistory.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ประวัติการจองสนามแบบทั่วไป</h1>
      </div>
    </div>
  </div>

  <!-- ตารางประวัติการจองสนามแบบทั่วไป -->
  <div class="container1">
    <!-- ตารางจะถูกแสดงจาก PHP ด้านล่าง -->

<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
  exit;
}

$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session

// Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่
$sql = "SELECT
        BookingID, DATE_FORMAT(BookingDate, '%W ที่ %d %M %Y') as BookingDate, 
        TIME_FORMAT(StartTime, '%H:%i น.') as StartTime, 
        TIME_FORMAT(EndTime, '%H:%i น.') as EndTime, 
        NumberHours, BookingFormat, Status, CourtID, Price,CancelReason,CancelBy, 
        TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(BookingDate, ' ', StartTime)) AS TimeToStart
        FROM booking 
        WHERE AccountID = ? AND BookingFormat = 'การจองแบบทั่วไป'  
        ORDER BY BookingID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo '<table class="booking-table" id="Generalbooking">';
  echo '<thead>
          <tr>
              <th>รายการ</th>
              <th id="BookingDate">วันที่จอง</th>
              <th id="StartTime">เวลาที่จองเริ่มต้น</th>
              <th id="EndTime">เวลาที่จองสิ้นสุด</th>
              <th id="Numberhours">ชั่วโมงที่จอง</th>
              <th id="BookingFormat">รูปแบบการจอง</th>
              <th id="Status">สถานะ</th>
              <th id="CourtID">สนามที่</th>
              <th id="Price">ค่าบริการ</th>
              <th>ยกเลิกการจอง</th>
          </tr>
      </thead>';
  echo '<tbody>';

  $index = 1;

  // แสดงข้อมูลในแต่ละแถว
  while ($row = $result->fetch_assoc()) {
      echo '<tr>';
      echo '<td>' . $index. '</td>';
      echo '<td>' . $row['BookingDate'] . '</td>';
      echo '<td>' . $row['StartTime'] . '</td>';
      echo '<td>' . $row['EndTime'] . '</td>';
      echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
      echo '<td>' . $row['BookingFormat'] . '</td>';

      // กำหนดสถานะการจอง
      $statusClass = '';
      $actionButton = '';
      $statusStyle = '';

      switch ($row['Status']) {
          case 'จองสำเร็จ':
              $statusClass = 'success';
              $statusStyle = 'color:green;';
              if ($row['TimeToStart'] > 30) {
                  $actionButton = '<button class="btn btn-danger cancel-button" data-id="' . $row['BookingID'] . '">ยกเลิก</button>';
              } else {
                  $actionButton = '<span style="color:gray;">หมดเวลายกเลิก</span>';
              }
              break;
          case 'รอชำระเงิน':
              $statusClass = 'pending';
              $actionButton = '<a href="/web_badmintaon_khlong_6/Member/Payment page with owner.php" class="btn btn-primary">ชำระเงิน</a>';
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
              break;
      }

      echo '<td><span class="status ' . $statusClass . '" style="' . $statusStyle . '">' . $row['Status'] . '</span></td>';
      echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
      echo '<td>' . $row['Price'] . ' บาท</td>';
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

  </div>

  <div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
  </div>

  <!-- Script สำหรับการยกเลิกการจอง -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = this.dataset.id;
                const confirmCancel = confirm("คุณแน่ใจว่าต้องการยกเลิกการจองนี้?");
                if (confirmCancel) {
                    fetch('../php/cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ bookingId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("การยกเลิกสำเร็จ");
                            location.reload();
                        } else {
                            alert("เกิดข้อผิดพลาด: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("ไม่สามารถยกเลิกได้ในขณะนี้");
                    });
                }
            });
        });
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
