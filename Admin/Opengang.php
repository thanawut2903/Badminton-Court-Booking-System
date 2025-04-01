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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Opengang.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>เปิดสนามแบบก๊วนโดยผู้ดูแล</h1>
      </div>
    </div>
  </div>

  <div class="containerbooking mt-4 p-4 bg-light rounded shadow border" style="max-width: 750px;">
  <h4 class="text-center mb-4 text-dark fw-semibold">ค้นหาวันว่าง</h4>
  <form id="search-form" class="d-flex flex-column align-items-center">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center mb-3">
      <div class="form-group d-flex align-items-center me-md-3 mb-2 mb-md-0">
        <label for="day-select" class="me-2 text-dark fw-semibold">เลือกวัน:</label>
        <select id="day-select" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
          <option value="" disabled selected>กรุณาเลือกวัน</option>
          <option value="จันทร์">จันทร์</option>
          <option value="อังคาร">อังคาร</option>
          <option value="พุธ">พุธ</option>
          <option value="พฤหัสบดี">พฤหัสบดี</option>
          <option value="ศุกร์">ศุกร์</option>
          <option value="เสาร์">เสาร์</option>
          <option value="อาทิตย์">อาทิตย์</option>
        </select>
      </div>

      <div class="form-group d-flex align-items-center me-md-3 mb-2 mb-md-0">
        <label for="start-timeS" class="me-2 text-dark fw-semibold">เวลาเริ่มต้น:</label>
        <select id="start-timeS" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
          <option value="" disabled selected>กรุณาเลือกเวลาเริ่มต้น</option>
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
          <option value="22:00">22:00</option>
          <option value="23:00">23:00</option>
        </select>
      </div>

      <div class="form-group d-flex align-items-center">
        <label for="end-timeS" class="me-2 text-dark fw-semibold">เวลาสิ้นสุด:</label>
        <select id="end-timeS" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
          <option value="" disabled selected>กรุณาเลือกเวลาสิ้นสุด</option>
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
          <option value="24:00">24:00</option>
        </select>
      </div>
      <button type="submit"class="btn btn-dark px-4 btn-lg py-2 w-50"style="margin-top:28px;margin-left:10px">ค้นหาวัน</button>
    </div>
    <button type="button" class="btn btn-primary" id="searchFreeDate" hidden>hidden</button>
  </form>
</div>

<script>
document.getElementById('search-form').addEventListener('submit', function(event) {
  event.preventDefault(); // ป้องกันการรีเฟรชหน้า

  const selectedDay = document.getElementById('day-select').value;
  const startTime = document.getElementById('start-timeS').value;
  const endTime = document.getElementById('end-timeS').value;

  if (!selectedDay || !startTime || !endTime) {
    alert('กรุณากรอกข้อมูลให้ครบถ้วน');
    return;
  }

  if (startTime >= endTime) {
    alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด');
    return;
  }

  fetch('../php/searchAvailability.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      dayOfWeek: selectedDay,
      startTime: startTime,
      endTime: endTime
    })
  })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        selectedDate = new Date(data.nextAvailableDate);
        Swal.fire({
                icon: 'success',
                title: '🎉 พบวันว่าง!',
                text: `✅ มีวันว่างในวันที่: ${data.nextAvailableDate}`,
                confirmButtonColor: '#28a745'
            });
        document.getElementById("searchFreeDate").click();
      } else {
        alert(`ไม่พบวันว่าง: ${data.message}`);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('เกิดข้อผิดพลาดในการค้นหา');
    });
});
</script>

  </div>
    <!-- ตาราง -->
    <div class="containertable">
    <h2 class="text-center">ตารางจองสนามแบด</h2>
    
  <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>

<!-- ตาราง -->

    <table class="booking-table">
      <thead>
        <tr>
          <th>สนาม / เวลา</th>
          <th>11:00 - 12:00</th>
          <th>12:00 - 13:00</th>
          <th>13:00 - 14:00</th>
          <th>14:00 - 15:00</th>
          <th>15:00 - 16:00</th>
          <th>16:00 - 17:00</th>
          <th>17:00 - 18:00</th>
          <th>18:00 - 19:00</th>
          <th>19:00 - 20:00</th>
          <th>20:00 - 21:00</th>
          <th>21:00 - 22:00</th>
          <th>22:00 - 23:00</th>
          <th>23:00 - 24:00</th>
        </tr>
      </thead>
      <tbody id="booking-table-body">
        <!-- ตารางจะถูกเติมข้อมูลด้วย JavaScript -->
      </tbody>
    </table>
    </div>

    <!-- ตาราง -->
<div class="containerbooking mt-3 p-4 bg-light rounded shadow border" style="max-width: 600px; margin-left : 750px">
  <h3 class="text-center text-dark mb-4">เลือกเวลาที่ต้องการจอง</h3>
  <div class="row align-items-center mb-4">
    <!-- เวลาเริ่มต้น -->
    <div class="col">
  <label for="start-time" class="form-label fw-semibold">เวลาเริ่มต้น</label>
  <select id="start-time" class="form-select border-primary" onchange="updateInfoBox()">
  </select>
</div>

<script>
  const startTimeSelect = document.getElementById("start-time");

  startTimeSelect.innerHTML = '<option value="" selected>-- เวลาเริ่มต้น --</option>';

  for (let hour = 11; hour <= 23; hour++) {
    startTimeSelect.innerHTML += `<option value="${hour}:00">${hour}:00</option>`;
  }
</script>


    <!-- เวลาสิ้นสุด -->
    <div class="col">
  <label for="end-time" class="form-label fw-semibold">เวลาสิ้นสุด</label>
  <select id="endtime" class="form-select border-primary" onchange="updateInfoBox()">
  </select>
</div>

<script>
  const endTimeSelect = document.getElementById("endtime");

  // เพิ่มตัวเลือกเริ่มต้น
  endTimeSelect.innerHTML = '<option value="" selected>-- เวลาสิ้นสุด --</option>';

  // วนลูปสร้าง option ตั้งแต่ 12:00 - 24:00
  for (let hour = 12; hour <= 24; hour++) {
    endTimeSelect.innerHTML += `<option value="${hour}:00">${hour}:00</option>`;
  }
</script>

<?php
require '../php/dbconnect.php';
$sql = "SELECT COUNT(*) AS total FROM court WHERE courtID != 0";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalCourts = $row['total']; 
?>


  <!-- เลือกสนาม -->
  <div class="row align-items-center mb-4">
  <div class="col">
  <label for="court-select" class="form-label fw-semibold">เลือกสนาม</label>
  <select id="court-select" class="form-select border-primary" onchange="updateInfoBox()">
    <option value="" selected>-- เลือกสนาม --</option>
    <?php
      // วนลูปแสดงสนามจาก 1 ถึงจำนวนสนามที่นับได้
      for ($i = 1; $i <= $totalCourts; $i++) {
          echo "<option value='{$i}'>สนาม {$i}</option>";
      }
    ?>
  </select>
</div>
<?php $conn->close(); ?>
  </div>

  <!-- ปุ่มยืนยัน & ล้างข้อมูล -->
  <div class="text-center mt-4">
    <button id="clear-button" class="btn btn-danger fw-bold mx-2" style="width: 120px; height: 50px; border-radius: 8px;">ล้างข้อมูล</button>
    <button id="submit-button" class="btn btn-success fw-bold mx-2" style="width: 120px; height: 50px; border-radius: 8px;">ยืนยัน</button>
  </div>
</div>
  </div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inviteCheckbox = document.getElementById('invite-checkbox');
        const inviteMessage = document.getElementById('invite-message');

        inviteCheckbox.addEventListener('change', function () {
            inviteMessage.disabled = !this.checked;
        });
    });
</script>


  <script src="/web_badmintaon_khlong_6/Admin/opengang.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
