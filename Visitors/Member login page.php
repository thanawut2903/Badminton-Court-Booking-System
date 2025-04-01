<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบสมาชิก</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <!-- ลิงก์ไปยังไฟล์ CSS -->
    <link href="Member login page.css" rel="stylesheet">
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
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Membership page.php">สมัครสมาชิก</a>
          </li>
          <li class="nav-item">
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Contact us page.php">ติดต่อเรา</a>
          </li>
          <li class="nav-item">
            <a id="admin-btn" class="nav-link btn mx-1" href="/web_badmintaon_khlong_6/Admin/Admin login page.php">ผู้ดูแลระบบ</a>
          </li>
        <!-- เมนูด้านขวา -->
      </div>
    </div>
  </nav>

  <div class="container d-flex align-items-center justify-content-center vh-100">
  <!-- ฟอร์ม Login -->
  <div class="login-card">
    <h3 class="text-center mb-4">เข้าสู่ระบบสมาชิก</h3>
    <form method="POST" action="../php/login.php" id="formlogin">
        <!-- ชื่อผู้ใช้งาน -->
        <div class="mb-3">
            <label for="UserName" class="form-label">ชื่อผู้ใช้งาน</label>
            <input type="text" class="form-control" id="UserName" name="Username" placeholder="กรอกชื่อผู้ใช้งาน" required>
        </div>
        <!-- รหัสผ่าน -->
        <div class="mb-3">
            <label for="Password" class="form-label">รหัสผ่าน</label>
            <input type="password" class="form-control" id="Password" name="Password" placeholder="กรอกรหัสผ่าน" required>
        </div>
        <!-- ลืมรหัสผ่าน -->
        <div class="text-end">
            <a href="/web_badmintaon_khlong_6/Visitors/New password request page.php" class="text-link">ลืมรหัสผ่าน?</a>
        </div>
        <!-- ปุ่มล็อกอิน -->
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-custom w-100">ล็อคอิน 🔐</button>
        </div>
        <!-- สมัครสมาชิก -->
        <div class="text-center mt-3">
            <a href="/web_badmintaon_khlong_6/Visitors/Membership page.php" class="text-link">สมัครสมาชิก</a>
        </div>
    </form>
</div>



  
<!-- รูปภาพด้านขวา -->
<div>
  <img src="/web_badmintaon_khlong_6/images/hulman1.jpg" alt="hulman" class="custom-image1 img-fluid ms-1">
</div>
</div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
