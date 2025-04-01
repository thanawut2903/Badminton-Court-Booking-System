<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบผู้ดูแล</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="Field schedule view page.css" rel="stylesheet">
</head>
<body>
  <!-- เมนูด้านบน -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="/web_badmintaon_khlong_6/Visitors/Homepage.php">
        <img src="/web_badmintaon_khlong_6/images/logo.jpg" alt="Logo" style="width: 40px; height: 40px;" class="me-2">
        <span>เว็บจองสนามแบดมินตัน คลอง6</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Homepage.php">หน้าหลัก</a></li>
          <li class="nav-item"><a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Member login page.php">เข้าสู่ระบบ</a></li>
          <li class="nav-item"><a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Membership page.php">สมัครสมาชิก</a></li>
          <li class="nav-item"><a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Contact us page.php">ติดต่อเรา</a></li>
          <li class="nav-item"><a id="admin-btn" class="nav-link btn mx-1" href="/web_badmintaon_khlong_6/Admin/Admin login page.php">ผู้ดูแลระบบ</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ตารางการจองสนาม</h1>
      </div>
    </div>
  </div>

  <div class="containerbooking mt-4 p-4 bg-light rounded shadow border" style="max-width: 750px; margin: auto;">
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
  </div>

    
  <div class="date-picker-container text-center">
  <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
  <button id="currentDate" class="btn btn-primary"></button>
  <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>




    <!-- ตาราง -->
    <div class="containertable">
    <h2 class="text-center">ตารางจองสนามแบด</h2>
    <table class="booking-table">
      <thead>
        <tr>
          <th>สนาม/เวลา</th>
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
          <th>23:00 - 00:00</th>
        </tr>
      </thead>
      <tbody id="booking-table-body">
        <!-- ตารางจะถูกเติมข้อมูลด้วย JavaScript -->
      </tbody>
    </table>
  </div>
    </div>




  <script src="/web_badmintaon_khlong_6/Visitors/JSviewfiled.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>