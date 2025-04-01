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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Reasoncancel.css" rel="stylesheet">
</head>
<body>
 
<?php include '../php/admin_menu.php' ?>

  <div class="cancel-container">
    <h2>เหตุผลที่ยกเลิกการจอง</h2>

   <!-- ตัวเลือก Radio Buttons -->
<label>
  <input type="radio" name="cancel-reason" value="ไม่ชำระในเวลาที่กำหนด"> ไม่ชำระในเวลาที่กำหนด
</label><br>

<label>
  <input type="radio" name="cancel-reason" value="ไม่มาใช้บริการในเวลาที่จอง"> ไม่มาใช้บริการในเวลาที่จอง
</label><br>

<label>
  <input type="radio" name="cancel-reason" value="other"> อื่นๆ : 
  <input type="text" id="other-reason" placeholder="***เหตุผลของการยกเลิก***">
</label><br><br>

<label>
  รายละเอียด(ถ้ามี): <br>
  <textarea id="details" rows="4" cols="40"></textarea>
</label><br><br>
</div>  

<div class="custom-boxprevious">
  <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
  <div class="custom-save">
    <button class="btn-save" id="submit-btn">ยืนยัน</button>
  </div>
</div>

<script>
// เมื่อผู้ใช้กดปุ่มยืนยัน
document.getElementById("submit-btn").addEventListener("click", function () {
    // ดึงตัวเลือก Radio ที่ถูกเลือก
    const reason = document.querySelector('input[name="cancel-reason"]:checked');
    const otherReason = document.getElementById("other-reason").value;
    const details = document.getElementById("details").value;

    // ตรวจสอบตัวเลือกเหตุผล
    if (!reason) {
        Swal.fire({
            icon: 'warning',
            title: 'โปรดเลือกเหตุผล!',
            text: 'กรุณาเลือกเหตุผลการยกเลิกก่อนดำเนินการ',
            confirmButtonColor: '#f39c12'
        });
        return;
    }

    let cancelReason = reason.value;

    // กรณีเลือก "อื่นๆ" แต่ยังไม่กรอกข้อความ
    if (cancelReason === "other") {
        if (otherReason.trim()) {
            cancelReason = otherReason.trim();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'กรุณากรอกเหตุผลเพิ่มเติม!',
                text: 'กรุณาพิมพ์เหตุผลเพิ่มเติมก่อนกดยืนยัน',
                confirmButtonColor: '#d33'
            });
            return;
        }
    }

    // ดึง BookingID จาก URL
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const bookingId = parseInt(urlParams.get('bookingId'));

    console.log(bookingId); // Debugging log

    // ส่งข้อมูลไปยัง Backend
    fetch('../php/save_reason_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cancelReason, details, bookingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: 'เหตุผลการยกเลิกของคุณถูกบันทึกเรียบร้อย',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.href = "/web_badmintaon_khlong_6/Admin/Allstatus.php"; // เปลี่ยนหน้าเมื่อสำเร็จ
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
            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ กรุณาลองใหม่',
            confirmButtonColor: '#d33'
        });
    });
});
</script>



    

    <!-- Bootstrap JS -->
    <!-- <script src="Reason.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
