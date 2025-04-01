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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Addcourt.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>เพิ่มสนาม</h1>
      </div>
    </div>

    <div class="form-container">
        <form id="courtForm" action="../php/add_court.php" method="POST">
            <!-- ชื่อสนาม -->
            <div class="form-group">
                <label for="court-name">ชื่อสนาม</label>
                <input type="text" id="court-name" name="CourtName" placeholder="ชื่อสนาม" required>
            </div>
                <!-- เวลาเปิดและปิด -->
                <!-- เวลาเปิดและปิด -->
<div class="time-group">
    <div>
        <label for="open-time">เวลาเปิดของสนาม</label>
        <select id="open-time" name="OpenTime" required>
            <option value="" selected disabled>เวลาเปิดสนาม</option>
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
    </div>
    <div>
        <label for="close-time">เวลาปิดของสนาม</label>
        <select id="close-time" name="CloseTime" required>
            <option value="" selected disabled>เวลาปิดสนาม</option>
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
</div>
            <br>
            <div class="custom-boxprevious">
                <button type="button" class="btn-previous" onclick="confirmBack()">ย้อนกลับ</button>
                <div class="custom-save">
                    <button type="button" class="btn-add" onclick="confirmSubmit()">เพิ่มสนาม</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function confirmBack() {
            Swal.fire({
                title: "คุณแน่ใจหรือไม่?",
                text: "คุณต้องการย้อนกลับไปยังหน้าก่อนหน้านี้?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ใช่, ย้อนกลับ!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    history.back();
                }
            });
        }

        function confirmSubmit() {
            let openTime = document.getElementById("open-time").value;
            let closeTime = document.getElementById("close-time").value;

            if (openTime >= closeTime) {
                Swal.fire({
                    title: "ข้อผิดพลาด!",
                    text: "เวลาเปิดต้องน้อยกว่าเวลาปิด",
                    icon: "error",
                    confirmButtonText: "ตกลง"
                });
                return;
            }

            Swal.fire({
                title: "ยืนยันการเพิ่มสนาม?",
                text: "โปรดยืนยันว่าคุณต้องการเพิ่มข้อมูลนี้",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#4CAF50",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่, เพิ่มเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("courtForm").submit();
                }
            });
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
