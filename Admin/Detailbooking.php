<?php
session_start();
include '../php/admin_navbar.php';


// ตรวจสอบว่าได้รับ BookingID ผ่าน GET หรือไม่
if (!isset($_GET['bookingId'])) {
    echo "<p>ไม่พบข้อมูลการจอง</p>";
    exit;
}

$bookingId = $conn->real_escape_string($_GET['bookingId']);


// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, 'วัน%Wที่ %d %M %Y') as BookingDate, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.BookingFormat, 
            b.Status, 
            b.CourtID, 
            b.Price,
            b.PaymentSlips,  
            a.FirstName, 
            a.LastName,
            a.Tel,
            a.LineID,
            a.Username
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
$formattedDate = strtr($bookingDate, array_merge($thaiMonths, $thaiDays));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Detailbooking.css" rel="stylesheet">
</head>
<body>


<?php include '../php/admin_menu.php'; ?>

    <!-- ตาราง -->
    <div class="containertable"> 
    <h2 class="text-center"></h2>

  <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>

    <!-- ตาราง -->
    <table class="booking-table">
      <thead>
      <tr>
      <th>สนาม/เวลา</th>
      <?php
        for ($hour = 11; $hour <= 23; $hour++) {
            echo "<th>{$hour}:00 - " . ($hour + 1) . ":00</th>";
        }
      ?>
    </tr>
      </thead>
      <tbody id="booking-table-body">
        <!-- ตารางจะถูกเติมข้อมูลด้วย JavaScript -->
      </tbody>
    </table>
  </div>
    </div>
    </div>
  

  <div class="container mt-0">
  <div class="booking-details-container">
    <div class="booking-header">
      <h3>รายละเอียดการจอง</h3>
    </div>
    <div class="booking-details">
      <!-- <p><strong>วันที่จอง :</strong> -->
      <p style="color: purple;"><strong style="color:purple">วันเวลาที่จอง :</strong> <span id="original-booking-time"> <?= $formattedDate ?> เวลา <?= $booking['StartTime'] ?> - <?= $booking['EndTime'] ?></span></p>
      <label for="edit-time-checkbox">
      <label for="edit-time-checkbox">
    <input type="checkbox" id="edit-time-checkbox">&nbsp;&nbsp;แก้ไขเวลา
</label>

<div id="time-edit-section" style="display: none;">
    <label for="start-time">เวลาเริ่มต้น:</label>
    <select id="start-time" onchange="updateBookingTime()">
        <option value="">-- เวลาเริ่มต้น --</option>
        <option value="11:00">11:00</option>
        <option value="12:00">12:00</option>
        <option value="13:00">13:00</option>
        <option value="14:00">14:00</option>
        <option value="15:00">15:00</option>
        <option value="16:00">16:00</option>
        <option value="17:00">17:00</option>
        <option value="18:00">18:00</option>
        <option value="19:00">19:00</option>
        <option value="20:00">20:00</option>
        <option value="21:00">21:00</option>
        <option value="22:00">22:00</option>
        <option value="23:00">23:00</option>
    </select>

    <label for="end-time">เวลาสิ้นสุด:</label>
    <select id="end-time" onchange="updateBookingTime()">
        <option value="">-- เวลาสิ้นสุด --</option>
        <option value="12:00">12:00</option>
        <option value="13:00">13:00</option>
        <option value="14:00">14:00</option>
        <option value="15:00">15:00</option>
        <option value="16:00">16:00</option>
        <option value="17:00">17:00</option>
        <option value="18:00">18:00</option>
        <option value="19:00">19:00</option>
        <option value="20:00">20:00</option>
        <option value="21:00">21:00</option>
        <option value="22:00">22:00</option>
        <option value="23:00">23:00</option>
        <option value="24:00">00:00</option>
    </select>
</div>




<p><strong>เวลาที่จองใหม่ :</strong> <span id="display-booking-time"><?= $booking['StartTime'] ?> - <?= $booking['EndTime'] ?></span></p>

<form action="">
<input type="hidden" id="bookingstarttime" name="bookingstarttime" value="<?= $booking['StartTime'] ?>">
<input type="hidden" id="bookingendtime" name="bookingendtime" value="<?= $booking['EndTime'] ?>">
</form>


<script>
    document.getElementById('edit-time-checkbox').addEventListener('change', function () {
        const timeEditSection = document.getElementById('time-edit-section');
        if (!this.checked) {
          document.getElementById('start-time').value = "<?= $booking['StartTime'] ?>";
          document.getElementById('end-time').value = "<?= $booking['EndTime'] ?>";

        }
        timeEditSection.style.display = this.checked ? 'block' : 'none';
    });

    function updateBookingTime() {
        const startTime = document.getElementById('start-time').value;
        let endTime = document.getElementById('end-time').value;
        const startHour = parseInt(startTime.split(':')[0]);
        const endHour = parseInt(endTime.split(':')[0]);
        const originalHours = <?= $booking['NumberHours'] ?>;
        
        if (startTime && endTime) {
            if (endHour <= startHour) {
                alert("เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น");
                return;
            }
            
            if ((endHour - startHour) !== originalHours) {
                alert("เวลาที่เลือกต้องมีระยะเวลา " + originalHours + " ชั่วโมง");
                return;
            }
            if ($booking['EndTime'] === '24:00 น.' || $booking['EndTime'] === '24:00:00 น.' || $booking['EndTime'] === '24:00') {
    $booking['EndTime'] = '00:00 น.';
}
            
            document.getElementById('display-booking-time').textContent = `${startTime} - ${endTime}`;
        }
    }
</script>

      <p><strong>ชั่วโมงที่จอง :</strong> <?= $booking['NumberHours'] ?> ชั่วโมง</p>

      <?php
require '../php/dbconnect.php';

// ดึงจำนวนสนามจากฐานข้อมูล โดยไม่นับ courtID = 0
$sql = "SELECT COUNT(*) AS total FROM court WHERE courtID != 0";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalCourts = $row['total']; // จำนวนสนามที่ไม่นับ courtID = 0
?>

<label for="court-select"><strong>สนามที่ใช้บริการ :</strong></label>
<select id="court-select">
  <option value="">เลือกสนาม</option>
  <?php
    // ใช้ loop สร้าง <option> ตามจำนวนสนามที่มีอยู่
    for ($i = 1; $i <= $totalCourts; $i++) {
        echo "<option value='{$i}'>สนามที่ {$i}</option>";
    }
  ?>
</select>
      <p style="margin-top: 10px;"><strong>ราคาที่ต้องชำระ : </strong><?= $booking['Price'] ?> บาท</p>
      <p><strong>สลิปการโอนเงิน:</strong></p>
<?php
$slipPath = $booking['PaymentSlips'];
if (!empty($slipPath)) {
    $fullSlipPath = "/web_badmintaon_khlong_6/" . $slipPath;
    echo '<a href="' . $fullSlipPath . '" target="_blank">
            <img src="' . $fullSlipPath . '" alt="Payment Slip" style="width: 100px; height: 150px;margin-left:130px;margin-bottom:10px;margin-top:-15px"/>
          </a>';
} else {
    echo "<p>ยังไม่มีสลิปการโอนเงิน</p>";
}
?>
</a>
</label>
    </div>
    <div class="user-header">
      <h3>ข้อมูลของผู้จองใช้บริการ</h3>
    </div>
    <div class="user-details">
      <p><strong>ชื่อ :</strong> <?= $booking['FirstName'] ?></p>
      <p><strong>ชื่อผู้ใช้งาน :</strong> <?= $booking['Username'] ?></p>
      <p><strong>LINE ID :</strong> <?= $booking['LineID'] ?></p>
      <p><strong>เบอร์โทรศัพท์ : </strong> <?= $booking['Tel'] ?></p>
    </div>
  </div>




<div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
    <div class="custom-save">
    <button class="btn-save" onclick="saveBooking()">บันทึก</button>
    </div>
    </div>


<script>
  document.getElementById('edit-time-checkbox').addEventListener('change', function () {
    const timeEditSection = document.getElementById('time-edit-section');
    if (this.checked) {
        timeEditSection.style.display = 'block';
    } else {
        timeEditSection.style.display = 'none';
    }
});

function saveBooking() {
    const CourtID = document.getElementById('court-select').value;
    const editTime = document.getElementById('edit-time-checkbox').checked;
    const StartTime = document.getElementById('start-time').value;
    const EndTime = document.getElementById('end-time').value;

    if (!CourtID) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกสนาม!',
            text: 'กรุณาเลือกสนามก่อนทำการบันทึก',
            confirmButtonColor: '#f39c12'
        });
        return;
    }

    // Ensure bookingId is initialized properly
    const bookingId = "<?= json_encode($booking['BookingID']) ?>";

    if (!bookingId || bookingId === "null") {
        console.error('BookingID is not initialized');
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด!',
            text: 'ไม่สามารถดึงข้อมูลการจองได้',
            confirmButtonColor: '#d33'
        });
        return;
    }

    fetch('../php/save_booking_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ bookingId, CourtID, editTime, StartTime, EndTime })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกข้อมูลสำเร็จ!',
                text: 'การจองของคุณถูกบันทึกเรียบร้อยแล้ว',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.href = '../Admin/Allstatus.php';
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
            text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่',
            confirmButtonColor: '#d33'
        });
    });
}




function renderBookingTable(bookingId) {
    fetch(`../php/get_booking_table.php?bookingId=${bookingId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('booking-table-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching booking table:', error);
        });
}
</script>

    <!-- Bootstrap JS -->
    <script src="/web_badmintaon_khlong_6/Admin/Detailbooking.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
