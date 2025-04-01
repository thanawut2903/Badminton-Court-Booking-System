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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="Historyopengang.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ประวัติการเปิดสนามแบบก๊วน</h1>
      </div>
    </div>
    <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>
    <br>
    <div class="dropdown-container">
      <label for="field-select">เลือกสนามที่ต้องการ:</label>
      <select id="field-select" class="field-dropdown" onchange="filterByField()">
        <option value="">สนามทั้งหมด</option>
        <option value="1">สนามที่ 1</option>
        <option value="2">สนามที่ 2</option>
        <option value="3">สนามที่ 3</option>
        <option value="4">สนามที่ 4</option>
        <option value="5">สนามที่ 5</option>
      </select>
    </div>

    <div class="container mt-4">
    <?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการแก้ไข"]);
    exit;
}

$accountID = $_SESSION['user_idadmin'];

// รับค่า currentDate จาก JavaScript ผ่าน POST หรือใช้วันที่ปัจจุบัน
$currentDate = isset($_GET['date']) ? date("Y-m-d",intval($_GET['date'])/1000) : date("Y-m-d");

// สร้าง SQL Query เพื่อดึงข้อมูลเฉพาะวันที่ที่เลือก
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%Wที่ %d %M') as BookingDate, 
            (YEAR(b.BookingDate) + 543) AS BookingYear, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            CASE 
                WHEN b.Status = 'จองสำเร็จ' THEN 'เปิดก๊วนสำเร็จ'
                ELSE b.Status
            END AS Status, 
            b.CourtID, 
            a.FirstName AS BookerName,
            a.Role
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        WHERE b.BookingFormat = 'การขอเปิดก๊วน'
        AND DATE(b.BookingDate) = ?
        ORDER BY b.BookingID DESC";

$stmt = $conn->prepare($sql);

// ใช้ bind_param เพื่อผูกตัวแปรกับ query
$stmt->bind_param('s', $currentDate);

$stmt->execute();
$result = $stmt->get_result();


      if ($result->num_rows > 0) {
        echo '<table class="booking-table" id="Gangbooking">';
        echo '<thead>
                <tr>
                  <th>รายการ</th>
                  <th>วันที่จอง</th>
                  <th>เวลาที่จอง</th>
                  <th>ชั่วโมงที่จอง</th>
                  <th>สถานะ</th>
                  <th>สนามที่</th>
                  <th>จองโดย</th>
                  <th>จัดการ</th>
                </tr>
              </thead>';

        echo '<tbody id="booking-tbody">';

        $index = 1;

        while ($row = $result->fetch_assoc()) {
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

          echo '<tr data-booking-id="' . $row['BookingID'] . '">';
          echo '<tr class="court-row" data-court="' . $row['CourtID'] . '">';
          echo '<td>' . $index . '</td>';
          echo '<td>' . $formattedDate . '</td>';
          echo '<td>' . $row['StartTime'] .' - '.$row['EndTime'] . '</td>';
          echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
          
          $statusStyle = '';
          switch ($row['Status']) {
              case 'เปิดก๊วนสำเร็จ':
                  $statusStyle = 'color:green;';
                  break;
              case 'รอชำระเงิน':
                  $statusStyle = 'color:orange;';
                  break;
              case 'ยกเลิก':
                  $statusStyle = 'color:red;';
                  break;
                  case 'รออนุมัติ':
                    $statusStyle = 'color:#34e1eb;';
                    break;
          }
          
          echo '<td style="' . $statusStyle . '">' . $row['Status'] . '</td>';
          if ($row['CourtID'] == 0) {
            echo '<td>ยังไม่ระบุสนาม</td>';
        } else {
            echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
        }         
          $role = $row['Role'] === 'M' ? 'สมาชิก' : 'ผู้ดูแล';
          echo '<td>' . $row['BookerName'] . ' (' . $role . ')</td>'; // ข้อมูล "จองโดย"
          
          // ปรับตำแหน่งปุ่ม "ยกเลิก" และ "รายชื่อสมาชิกก๊วน" ให้อยู่ในช่องเดียวกัน
          echo '<td>';
          if ($row['Role'] === 'A' && $row['Status'] !== 'ยกเลิก') {
            echo '<a href="Listnamegroup.php?bookingId=' . $row['BookingID'] . '" class="btn btn-primary">รายชื่อสมาชิกก๊วน</a>';
        }

        if ($row['Role'] === 'A' && $row['Status'] === 'เปิดก๊วนสำเร็จ') { 
          echo '<button class="btn btn-danger cancel-btn" data-booking-id="' . $row['BookingID'] . '" style="margin-left:10px;">ยกเลิก</button>';
      }      
          echo '</td>'; // ปิด td ของ "จัดการ"
          echo '</tr>';
          $index++;
          }
          echo '</tbody></table>';
          } else {
              echo '<p style="color:red;margin-left:150px;">ไม่มีประวัติการเปิดสนาม</p>';
          }
          
          

      $conn->close();
      ?>
    </div>

    <script>
    function searchByDate() {
      const searchValue = document.getElementById('search-input').value.toLowerCase();
      const rows = document.querySelectorAll('.court-row');

      rows.forEach(row => {
        const bookingDate = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

        if (bookingDate.includes(searchValue)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    function filterByField() {
      const selectedField = document.getElementById('field-select').value;
      const rows = document.querySelectorAll('.court-row');

      rows.forEach(row => {
        if (!selectedField || row.dataset.court === selectedField) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    document.addEventListener("DOMContentLoaded", function () {
    const cancelButtons = document.querySelectorAll('.cancel-booking');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id'); // อ่าน bookingId จาก data-booking-id
            console.log(bookingId); // ตรวจสอบใน console

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
                    fetch('../php/cancel_booking_admin.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ bookingId }) // ส่งข้อมูล bookingId ไป
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: data.message, // ข้อความจากการยกเลิก
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                window.location.reload(); // รีเฟรชหน้าหลังจากยกเลิกสำเร็จ
                            });
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

<script>    
document.addEventListener('DOMContentLoaded', function () {
    const cancelButtons = document.querySelectorAll('.cancel-btn');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-booking-id'); // ดึง bookingId โดยตรงจากปุ่ม
            console.log("Booking ID:", bookingId); // ตรวจสอบค่า bookingId ใน console

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
    <script src="/web_badmintaon_khlong_6/Admin/listgroup.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
