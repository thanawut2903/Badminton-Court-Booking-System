<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>สมัครสมาชิก</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Membership page.css" rel="stylesheet">
</head>
<body>
  <!-- เมนูด้านบน -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
      <!-- โลโก้และชื่อสนาม -->
      <a class="navbar-brand d-flex align-items-center" href="/web_badmintaon_khlong_6/Visitors/Homepage.php">
        <img src="/web_badmintaon_khlong_6/images/logo.jpg" alt="Logo" style="width: 40px; height: 40px;" class="me-2">
        <span>เว็บจองสนามแบดมินตัน คลอง6</span>
      </a>
      <!-- เมนู -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Homepage.php">หน้าหลัก</a>
          </li>
          <li class="nav-item">
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Field schedule view page.php">ดูสนามว่าง</a>
          </li>
          <li class="nav-item">
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Member login page.php">เข้าสู่ระบบ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Contact us page.php">ติดต่อเรา</a>
          </li>
          <li class="nav-item">
            <a id="admin-btn" class="nav-link btn mx-1" href="/web_badmintaon_khlong_6/Admin/Admin login page.php">ผู้ดูแลระบบ</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container d-flex align-items-center justify-content-center vh-100">
    <!-- ฟอร์ม register -->
    <div class="card p-4 shadow" style="width: 30rem;">
      <h3 class="text-center mb-4">สมัครสมาชิก</h3>
      <!-- Form Start -->
      <form method="POST" action="../php/register.php" enctype="multipart/form-data">
        <!-- Input รูปภาพ User -->
        <div class="container1">
          <p>รูปภาพ<span style="display: inline-block; width: 10px;"></span>
          <input type="file" id="imageInput" name="ImageAccount" accept="image/*"></p>
            <!-- ข้อความแนะนำเกี่ยวกับประเภทไฟล์และขนาด -->
  <p style="font-size: 16px; color: red;">
    อัปโหลดไฟล์รูปภาพที่มีนามสกุล .jpg, .jpeg, .png เท่านั้น <br> ขนาดไฟล์ไม่เกิน 2 MB
  </p>
          <div style="width: 100%; display: flex; justify-content: center; margin-top: 10px;">
          <img id="preview" style="max-width: 200px; width: 200px; height: 200px; border-radius: 50%; object-fit: cover; display: none; border: 2px solid #ccc;">
          </div>
        </div>

        <!-- ชื่อ นามสกุล -->
        <div class="d-flex align-items-center justify-content-center mt-4">
          <div style="margin-right:20px" class="mb-3">
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

        <!-- เบอร์โทรศัพท์ -->
        <div class="d-flex align-items-center justify-content-center">
        <div style="margin-right:20px" class="mb-3">
        <label for="Tel" class="form-label">เบอร์โทรศัพท์</label>
<input type="tel" class="form-control" id="Tel" name="Tel" placeholder="กรอกเบอร์โทรศัพท์" 
       maxlength="10" required onkeypress="return event.charCode >= 48 && event.charCode <= 57"
       oninput="validateTel()">

<small id="telError" class="text-danger" style="display: none;">กรุณากรอกเบอร์โทรศัพท์ให้ครบ 10 หลัก</small>

<script>
function validateTel() {
    let telInput = document.getElementById("Tel");
    let errorText = document.getElementById("telError");

    if (telInput.value.length < 10) {
        errorText.style.display = "block"; // แสดงข้อความเตือน
    } else {
        errorText.style.display = "none"; // ซ่อนข้อความเตือน
    }
}
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

            // if (data.lastNameExists) {
            //     lastNameError.style.display = "block";
            // } else {
            //     lastNameError.style.display = "none";
            // }
        } else {
            nameError.style.display = "none"; // ซ่อนข้อความผิดพลาด
            lastNameError.style.display = "none"; // ซ่อนข้อความผิดพลาด
        }
    });
}
    </script>
</script>
          </div>
          <div class="mb-3">
            <label for="LineID" class="form-label">LINE ID</label>
            <input type="text" class="form-control" id="LineID" name="LineID" placeholder="กรอกไลน์ไอดี" required>
          </div>
        </div>

        <!-- ชื่อผู้ใช้งาน อีเมล -->
        <div class="d-flex align-items-center justify-content-center">
        <div style="margin-right:20px" class="mb-3">
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
        <div class="d-flex align-items-center justify-content-center">
        <div style="margin-right:20px" class="mb-3">
        <label for="Password" class="form-label">รหัสผ่าน</label>
<input type="password" class="form-control" id="Password" name="Password" placeholder="กรอกรหัสผ่าน" required
       pattern="^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$" title="รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร, มีตัวพิมพ์ใหญ่ และตัวเลข">
<small class="text-danger" id="passwordError" style="display: none;">รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร, มีตัวพิมพ์ใหญ่ และตัวเลข</small>


<script>
document.getElementById("Password").addEventListener("input", function() {
    var password = this.value;
    var errorMessage = document.getElementById("passwordError");

    // ตรวจสอบว่าอักขระตรงตามเงื่อนไขหรือไม่
    var isValid = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);
    
    if (!isValid) {
        errorMessage.style.display = "block"; // แสดงข้อความเตือน
    } else {
        errorMessage.style.display = "none"; // ซ่อนข้อความเตือน
    }
});
</script>
          </div>
          <div class="mb-3">
          <label for="ConfirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
<input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="กรอกรหัสผ่าน" required>
<small class="text-danger" id="passwordMatchError" style="display: none;">รหัสผ่านและยืนยันรหัสผ่านต้องตรงกัน</small>
          </div>
        </div>

        <script>
document.getElementById("ConfirmPassword").addEventListener("input", function() {
    var password = document.getElementById("Password").value;
    var confirmPassword = this.value;
    var errorMessage = document.getElementById("passwordMatchError");

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
    if (password !== confirmPassword) {
        errorMessage.style.display = "block"; // แสดงข้อความเตือน
    } else {
        errorMessage.style.display = "none"; // ซ่อนข้อความเตือน
    }
});
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
</script>

        <!-- ปุ่มสมัครสมาชิก -->
        <div class="mt-3 text-end">
          <button type="submit" class="btn btnsub-custom w-100">สมัครสมาชิก</button>
        </div>
      </form>
      <div class="custom-boxprevious">
        <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- ลิงก์ไปยังไฟล์ Script -->
  <script>
   document.getElementById('imageInput').addEventListener('change', function () {
  const file = this.files[0];
  const preview = document.getElementById('preview');

  // ตรวจสอบขนาดไฟล์ (5MB = 5 * 1024 * 1024 bytes)
  if (file && file.size > 2 * 1024 * 1024) {
    // แสดง SweetAlert ถ้าขนาดไฟล์เกิน 5MB
    Swal.fire({
      title: 'ขนาดไฟล์เกิน 2MB!',
      text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB.',
      icon: 'error',
      confirmButtonText: 'ตกลง'
    });

    // ลบไฟล์ที่เลือก (เคลียร์ฟิลด์ input)
    this.value = '';
    preview.style.display = 'none'; // ซ่อนตัวอย่างรูปภาพ
  } else if (file) {
    // อ่านและแสดงตัวอย่างภาพถ้าขนาดไฟล์ไม่เกิน 2MB
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none'; // ซ่อนตัวอย่างภาพถ้าไม่มีไฟล์
  }
});

  </script>
</body>
</html>
