<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ขอรหัสผ่านใหม่</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <!-- ลิงก์ไปยังไฟล์ CSS -->
    <link href="New password request page.css" rel="stylesheet">
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
            <a class="nav-link btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Visitors/Member login page.php">เข้าสู่ระบบ</a>
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



      </div>
    </div>
  </nav>

  <div class="container d-flex align-items-center justify-content-center vh-100">
  <!-- ฟอร์ม Login -->
  <div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow" style="width: 30rem;">
      <h3 class="text-center mb-4">ขอรหัสผ่านใหม่</h3>
      <form action="../php/resetpassword.php" method="POST">
              <div class="mb-3">
                <label for="Username" class="form-label">ชื่อผู้ใช้งาน</label>
                <input type="text" class="form-control" id="UserName" name="Username" placeholder="กรอกชื่อผู้ใช้งาน" required><br>
                <p class="text-muted">(ระบบจะส่งรหัสผ่านใหม่ให้ผู้ใช้งานในอีเมล)</p>
              </div>

        <div class="mt-3 text-end">


        <button type="submit" class="btn btnsub-custom w-100">ขอรหัสผ่านใหม่ 🔑</button>
        
          <div class="text-center mt-3">

        </div>
      </form>

    </div>
  </div>
<!-- รูปภาพด้านขวา -->
<div>
  <img src="/web_badmintaon_khlong_6/images/hulman2.jpg" alt="hulman" class="custom-image1 img-fluid ms-1">
</div>
</div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
