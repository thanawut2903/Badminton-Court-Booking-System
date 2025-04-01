<?php
// header('Content-Type: application/json');
session_start();
include '../php/member_navbar.php';
require '../php/dbconnect.php';

// Query เพื่อดึงค่าจาก infoID 12, 13, 14
$sql = "SELECT infoID, ItemDetail FROM info WHERE infoID IN (12, 13, 14)";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// ตัวแปรสำหรับเก็บค่าแต่ละ infoID
$datasetting = [
    'bookingAdvance' => null,
    'servicePrice' => null,
    'maxPlayer' => null
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['infoID']) {
            case 12:
                $datasetting['bookingAdvance'] = $row['ItemDetail']; // ตั้งค่าการจองสนามล่วงหน้า
                break;
            case 13:
                $datasetting['servicePrice'] = $row['ItemDetail']; // ตั้งค่าราคาค่าบริการ
                break;
            case 14:
                $datasetting['maxPlayer'] = $row['ItemDetail']; // จำนวนผู้เล่นสูงสุด
                break;
        }
    }
}
$stmt->close();
$conn->close();
// ส่งค่า JSON ไปยัง JavaScript
// echo json_encode($datasetting);
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
  <link href="General booking.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="/path/to/fontawesome.min.css"> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>การจองสนามแบบทั่วไป</h1>
      </div>
    </div>
  </div><br>

  <div class="containerbooking mt-1 p-4 bg-light rounded shadow border" style="max-width: 750px;">
  <h4 class="text-center mb-4 text-dark fw-semibold">ค้นหาวันว่าง</h4>
  <form id="search-form" class="d-flex flex-column align-items-center">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center mb-3">
      <div class="form-group d-flex align-items-center me-md-3 mb-2 mb-md-0">
        <label for="day-select" class="me-2 text-dark fw-semibold">เลือกวัน:</label>
        <select id="day-select" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
  <option value="" disabled selected>กรุณาเลือกวัน</option>
</select>
        <script>
  document.addEventListener("DOMContentLoaded", function () {
    const daySelect = document.getElementById("day-select");
    const days = ["จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์", "อาทิตย์"];

    days.forEach(day => {
      let option = document.createElement("option");
      option.value = day;
      option.textContent = day;
      daySelect.appendChild(option);
    });
  });
</script>
      </div>

      <div class="form-group d-flex align-items-center me-md-3 mb-2 mb-md-0">
        <label for="start-timeS" class="me-2 text-dark fw-semibold">เวลาเริ่มต้น:</label>
        <select id="start-timeS" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
  <option value="" disabled selected>กรุณาเลือกเวลาเริ่มต้น</option>
</select>
      </div>

      <div class="form-group d-flex align-items-center">
        <label for="end-timeS" class="me-2 text-dark fw-semibold">เวลาสิ้นสุด:</label>
        <select id="end-timeS" class="form-select w-auto border-primary text-dark" required style="padding-right: 2rem;">
  <option value="" disabled selected>กรุณาเลือกเวลาสิ้นสุด</option>
</select>

      </div>
      <button type="submit"class="btn btn-dark px-4 btn-lg py-2 w-50"style="margin-top:28px;margin-left:10px">ค้นหาวัน</button>
    </div>
    <button type="button" class="btn btn-primary" id="searchFreeDate" hidden>hidden</button>
  </form>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const startTimeSelect = document.getElementById("start-timeS");
    const endTimeSelect = document.getElementById("end-timeS");

    for (let hour = 11; hour <= 23; hour++) {
      let optionStart = document.createElement("option");
      optionStart.value = `${hour}:00`;
      optionStart.textContent = `${hour}:00`;
      startTimeSelect.appendChild(optionStart);
    }

    for (let hour = 12; hour <= 24; hour++) {
      let optionEnd = document.createElement("option");
      optionEnd.value = `${hour}:00`;
      optionEnd.textContent = `${hour}:00`;
      endTimeSelect.appendChild(optionEnd);
    }
  });
</script>
<script>
document.getElementById('search-form').addEventListener('submit', function(event) {
  event.preventDefault(); // ป้องกันการรีเฟรชหน้า

  const selectedDay = document.getElementById('day-select').value;
  const startTime = document.getElementById('start-timeS').value;
  const endTime = document.getElementById('end-timeS').value;

  if (!selectedDay || !startTime || !endTime) {
    Swal.fire({
      title: "ข้อมูลไม่ครบถ้วน!",
      text: "กรุณากรอกข้อมูลให้ครบถ้วน",
      icon: "warning",
      confirmButtonText: "ตกลง"
    });
    return;
  }

  if (startTime >= endTime) {
    Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด!',
            text: '⏰ เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด กรุณาเลือกใหม่',
            confirmButtonColor: '#d33'
        });
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
        }).then(() => {
          document.getElementById("searchFreeDate").click();
        });
      } else {
        Swal.fire({
          title: "ไม่พบวันว่าง!",
          text: data.message,
          icon: "warning",
          confirmButtonText: "ตกลง"
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        title: "เกิดข้อผิดพลาด!",
        text: "ไม่สามารถค้นหาวันที่ว่างได้ในขณะนี้",
        icon: "error",
        confirmButtonText: "ตกลง"
      });
    });
});

</script>
</form>
</div>
  </div>
    <!-- ตาราง -->
    <div class="containertable">
    <h2 class="text-center">ตารางจองสนามแบด</h2>
    <p class="text-center" style="color: red;margin-bottom:-10px">(สามารถจองล่วงหน้าได้ <?= $datasetting['bookingAdvance'] ?> วัน)</p>
  <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>
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
  <div class="containerbooking mt-2 p-4 bg-light rounded shadow border" style="max-width: 600px; margin-left:760px;">
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

<!-- ฝังค่า PHP ไปใน HTML -->
<div id="booking-settings" data-max-advance-days="<?= isset($datasetting['bookingAdvance']) ? $datasetting['bookingAdvance'] : 0; ?>"></div>

<script>
  const endTimeSelect = document.getElementById("endtime");

  // เพิ่มตัวเลือกเริ่มต้น
  endTimeSelect.innerHTML = '<option value="" selected>-- เวลาสิ้นสุด --</option>';

  // วนลูปสร้าง option ตั้งแต่ 12:00 - 24:00
  for (let hour = 12; hour <= 24; hour++) {
    endTimeSelect.innerHTML += `<option value="${hour}:00">${hour}:00</option>`;
  }
</script>
</div>

  <div class="text-center mt-4">
  <button id="clear-button" class="btn btn-danger fw-bold mx-2" style="width: 120px; height: 50px; border-radius: 8px;">ล้างข้อมูล</button>
  <button id="submit-button" class="btn btn-success fw-bold mx-2" style="width: 120px; height: 50px; border-radius: 8px; margin-top: -1px">ยืนยัน</button>
</div>
</div>
  <script src="/web_badmintaon_khlong_6/Member/General booking.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
