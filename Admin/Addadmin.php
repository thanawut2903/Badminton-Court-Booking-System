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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Addadmin.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php' ?>


<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow" style="width: 30rem;">
      <h3 class="text-center mb-4">เพิ่มผู้ดูแลระบบ</h3>

   <form id="adminForm" action="../php/add_admin.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmit(event)">

      <!-- Input รูปภาพ User -->
      <div class="container1">
  <p>รูปภาพ
    <span style="display: inline-block; width: 10px;"></span>
    <input type="file" id="imageInput" name="ImageAccount" accept="image/*">
  </p>
  
  <!-- ข้อความแนะนำเกี่ยวกับประเภทไฟล์และขนาด -->
  <p style="font-size: 12px; color: #555;">
    อัปโหลดไฟล์รูปภาพที่มีนามสกุล .jpg, .jpeg, .png เท่านั้น ขนาดไฟล์ไม่เกิน 5MB
  </p>
  
  <div style="width: 100%; display: flex; justify-content: center; margin-top: 10px;">
    <img id="preview" style="max-width: 200px; width: 200px; height: 200px; border-radius: 50%; object-fit: cover; display: none; border: 2px solid #ccc;">
  </div>
</div>


    <!-- ชื่อ นามสกุล -->
    <div id="inputtext" class="d-flex align-items-center justify-content-center">
    <div class="mb-3">
        <label for="FirstName" class="form-label">ชื่อ</label>
        <input type="text" class="form-control" id="FirstName" name="FirstName" placeholder="กรอกชื่อจริง" required onblur="checkName()">
        <small id="nameError" class="text-danger" style="display: none;">ชื่อหรือชื่อจริงนี้มีผู้ใช้แล้ว</small>
    </div>
    <div class="mb-3">
        <label for="LastName" class="form-label">นามสกุล</label>
        <input type="text" class="form-control" id="LastName" name="LastName" placeholder="กรอกนามสกุล" required onblur="checkName()">
        <small id="lastNameError" class="text-danger" style="display: none;">นามสกุลนี้มีผู้ใช้แล้ว</small>
    </div>
</div>

<script>

    
  // ฟังก์ชันตรวจสอบชื่อและนามสกุล
function checkName() {
    var firstName = document.getElementById("FirstName").value;
    var lastName = document.getElementById("LastName").value;
    var username = document.getElementById("Username").value; // เพิ่มการรับค่า username
    var nameError = document.getElementById("nameError");
    var lastNameError = document.getElementById("lastNameError");

    // ส่งคำขอไปตรวจสอบฐานข้อมูล
    fetch('../php/check-name.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ firstName: firstName, lastName: lastName, username: username })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // เพิ่มเพื่อดูข้อมูลที่ได้รับจาก PHP
        if (data.exists) {
            // แสดงข้อผิดพลาดถ้ามีชื่อซ้ำ
            if (data.firstNameExists) {
                nameError.style.display = "block";
            } else {
                nameError.style.display = "none";
            }
        } else {
            nameError.style.display = "none"; // ซ่อนข้อความผิดพลาด
            lastNameError.style.display = "none"; // ซ่อนข้อความผิดพลาด
        }
    });
}
    </script>


    <!-- เบอร์โทรศัพท์ LINE ID -->
    <div id="inputtext" class="d-flex align-items-center justify-content-center">
    <div class="mb-3">
    <label for="Tel" class="form-label">เบอร์โทรศัพท์</label>
    <input type="tel" class="form-control" id="Tel" name="Tel" placeholder="กรอกเบอร์โทรศัพท์" 
           required maxlength="10" 
           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
           oninput="validateTel()">
    <small id="telError" class="text-danger" style="display: none;">กรุณากรอกเบอร์โทรศัพท์ให้ครบ 10 หลัก</small>
</div>
        <div class="mb-3">
            <label for="LineID" class="form-label">LINE ID</label>
            <input type="text" class="form-control" id="LineID" name="LineID" placeholder="กรอกไลน์ไอดี" required>
        </div>
    </div>

    <!-- ชื่อผู้ใช้งาน อีเมล -->
    <div id="inputtext" class="d-flex align-items-center justify-content-center">
    <div class="mb-3">
    <label for="Username" class="form-label">ชื่อผู้ใช้งาน</label>
    <input type="text" class="form-control" id="Username" name="Username" placeholder="กรอกชื่อผู้ใช้งาน" required onblur="checkUsername()">
    <small id="usernameError" class="text-danger" style="display: none;">ชื่อผู้ใช้งานนี้ถูกใช้ไปแล้ว</small>
</div>
        <div class="mb-3">
            <label for="Email" class="form-label">อีเมล</label>
            <input type="email" class="form-control" id="Email" name="Email" placeholder="กรอกอีเมล" required>
        </div>
    </div>

    <!-- รหัสผ่าน ยืนยันรหัสผ่าน -->
    <div id="inputtext" class="d-flex align-items-center justify-content-center">
    <div class="mb-3">
        <label for="Password" class="form-label">รหัสผ่าน</label>
        <input type="password" class="form-control" id="Password" name="Password" placeholder="กรอกรหัสผ่าน" required
               pattern="^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$"
               title="รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร, มีตัวพิมพ์ใหญ่ และตัวเลข"
               oninput="validatePassword()">
        <small class="text-danger" id="passwordError" style="display: none;">
            รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร, มีตัวพิมพ์ใหญ่ และตัวเลข
        </small>
    </div>

    <div class="mb-3">
        <label for="ConfirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="กรอกรหัสผ่าน" required
               oninput="validateConfirmPassword()">
        <small class="text-danger" id="passwordMatchError" style="display: none;">
            รหัสผ่านและยืนยันรหัสผ่านต้องตรงกัน
        </small>
    </div>
</div>
<script>
function checkUsername() {
    var username = document.getElementById("Username").value;
    var usernameError = document.getElementById("usernameError");

    // ตรวจสอบว่ามีค่าหรือไม่ก่อนเรียก API
    if (username.trim() === "") {
        usernameError.style.display = "none";
        return;
    }

    fetch('../php/check-username.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: username })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            usernameError.style.display = "block"; // แสดงข้อความแจ้งเตือน
        } else {
            usernameError.style.display = "none"; // ซ่อนข้อความแจ้งเตือน
        }
    })
    .catch(error => console.error('Error:', error));
}
    function validatePassword() {
        var password = document.getElementById("Password").value;
        var passwordError = document.getElementById("passwordError");

        var isValid = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);

        if (!isValid) {
            passwordError.style.display = "block"; // แสดงข้อความแจ้งเตือน
        } else {
            passwordError.style.display = "none"; // ซ่อนข้อความแจ้งเตือน
        }

        validateConfirmPassword(); // ตรวจสอบการยืนยันรหัสผ่านใหม่เมื่อรหัสผ่านเปลี่ยน
    }

    function validateConfirmPassword() {
        var password = document.getElementById("Password").value;
        var confirmPassword = document.getElementById("ConfirmPassword").value;
        var passwordMatchError = document.getElementById("passwordMatchError");

        if (password !== confirmPassword) {
            passwordMatchError.style.display = "block"; // แสดงข้อความแจ้งเตือน
        } else {
            passwordMatchError.style.display = "none"; // ซ่อนข้อความแจ้งเตือน
        }
    }
</script>


    <!-- ปุ่มเพิ่มผู้ดูแลระบบ -->
    <div class="mt-3 text-end">
        <button type="submit" class="btn btnsub-custom w-100">เพิ่มผู้ดูแลระบบ</button>
    </div>
</form>
</div>
</div>

<script>
    function validateTel() {
        const telInput = document.getElementById("Tel");
        const telError = document.getElementById("telError");

        if (telInput.value.length === 10) {
            telError.style.display = "none"; // ซ่อนข้อความแจ้งเตือน
        } else {
            telError.style.display = "block"; // แสดงข้อความแจ้งเตือน
        }
    }
</script>

<script>
function confirmSubmit(event) {
    event.preventDefault(); // ป้องกันการส่งฟอร์มทันที

    const firstName = document.getElementById("FirstName").value;
    const lastName = document.getElementById("LastName").value;
    const username = document.getElementById("Username").value;
    const password = document.getElementById("Password").value;
    const confirmPassword = document.getElementById("ConfirmPassword").value;
    
    // ตรวจสอบว่า password และ confirmPassword ตรงกันหรือไม่
    if (password !== confirmPassword) {
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        return false;
    }

    // เช็คชื่อ-นามสกุลซ้ำในฐานข้อมูล
    fetch('../php/check-name.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ firstName: firstName, lastName: lastName, username: username })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            let errorMessage = "ข้อมูลต่อไปนี้ซ้ำในระบบ:\n";
            if (data.firstNameExists) {
                errorMessage += "ชื่อ\n";
            }
            if (data.lastNameExists) {
                errorMessage += "นามสกุล\n";
            }
            if (data.usernameExists) {
                errorMessage += "ชื่อผู้ใช้งาน\n";
            }
            
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            return; // หยุดการส่งฟอร์มเมื่อพบข้อมูลซ้ำ
        }

        // เช็คชื่อผู้ใช้งานซ้ำในฐานข้อมูล
        return fetch('../php/check-username.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username })
        });
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ชื่อผู้ใช้งานนี้ถูกใช้แล้ว',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            return; // หยุดการส่งฟอร์มถ้ามีชื่อผู้ใช้งานซ้ำ
        }

        // ถ้าทุกอย่างถูกต้อง ให้ส่งฟอร์ม
        Swal.fire({
            title: 'ยืนยันการเพิ่มผู้ดูแลระบบ?',
            text: 'โปรดยืนยันว่าคุณต้องการเพิ่มข้อมูลนี้',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4CAF50',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, เพิ่มเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("adminForm").submit(); // ส่งฟอร์มเมื่อกดยืนยัน
            }
        });
    })
    .catch(error => console.error('Error:', error));
}

// แสดงตัวอย่างรูปภาพที่เลือก
const imageInput = document.getElementById("imageInput");
const preview = document.getElementById("preview");

imageInput.addEventListener("change", function(event) {
    const file = event.target.files[0];
    
    // ตรวจสอบขนาดไฟล์ (5MB = 5 * 1024 * 1024 bytes)
    if (file && file.size > 5 * 1024 * 1024) {
        // ใช้ SweetAlert แทน alert
        Swal.fire({
            title: 'ขนาดไฟล์เกิน 5MB!',
            text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB.',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        // ลบไฟล์ที่เลือก
        imageInput.value = ""; 
        preview.style.display = "none"; // ซ่อนตัวอย่างรูปภาพ
    } else if (file) {
        // แสดงตัวอย่างภาพถ้าไฟล์ขนาดไม่เกิน 5MB
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = "none"; // ซ่อนตัวอย่างภาพถ้าไม่มีไฟล์
    }
});
</script>


    </div>
<!-- ปุ่มย้อนกลับ -->
<div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
</div>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <!-- ลิงก์ไปยังไฟล์ Script -->
  <script src="addamin.js"></script>
</body>
</html>
