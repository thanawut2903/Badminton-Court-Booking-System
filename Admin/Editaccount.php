<?php
require '../php/dbconnect.php'; // ตรวจสอบเส้นทางให้ถูกต้อง
session_start();
include '../php/admin_navbar.php';

// ตรวจสอบว่าเซสชันมีค่า AccountID หรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo "<script>alert('Session expired. Please login again.'); window.location.href = '../Admin/Admin login page.php';</script>";
    exit();
}

// ดึงข้อมูลจากฐานข้อมูล
$userId = $_SESSION['user_idadmin'];
$sql = "SELECT FirstName, LastName, Tel, LineID, Username, ImageAccount FROM account WHERE AccountID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $profileImage = !empty($user['ImageAccount']) ? $user['ImageAccount'] : 'uploads/default.png';
    $username = !empty($user['Username']) ? $user['Username'] : 'Guest';
} else {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้'); window.history.back();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editaccount.css" rel="stylesheet">
  <script>
    function validateForm() {
        const password = document.getElementById('Password').value;
        const confirmPassword = document.getElementById('ConfirmPassword').value;

        if (password !== confirmPassword) {
            alert('รหัสผ่านไม่ตรงกัน กรุณาลองอีกครั้ง');
            return false;
        }
        return true;
    }
  </script>
</head>
<body>

<?php include '../php/admin_menu.php'; ?>

  <!-- ฟอร์มแก้ไขบัญชีผู้ใช้งาน -->
  <div class="container1 text-center">
  <div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow custom-card" style="width: 60rem;">
      <div class="container2 text-center">
        <h3 class="text-center mb-4">แก้ไขบัญชีผู้ใช้งาน</h3>
      </div>

      <!-- ฟอร์มแก้ไขบัญชีผู้ใช้งาน -->
      <form action="../php/editaccountadmin.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <!-- Profile Picture -->
        <div class="editimage">
  <img id="profileImagePreview" src="/web_badmintaon_khlong_6/<?php echo $profileImage; ?>" alt="User Icon" class="rounded-circle" style="width: 150px; height: 150px;">
</div>
        <div class="content-container1 text-center">
          <div class="custom-boxfile text-center">
            <h3>เปลี่ยนรูปภาพ</h3>
          </div>

  <div class="file-upload-container" style="display: flex; align-items: center; height: 150px;">
  <input type="file" id="ImagePath" name="ImagePath" accept="image/*" style="width: 300px; height: 70px;" onchange="previewImage(event)">
  </div>
   <!-- ข้อความแนะนำ -->
   <p style="font-size: 16px; color: red; margin-top: -20px;">
    อัปโหลดไฟล์รูปภาพที่มีนามสกุล .jpg, .jpeg, .png เท่านั้น <br>
    ขนาดไฟล์ไม่เกิน 2 MB
  </p>
</div>

      
        <!-- Name Fields -->
        <div class="d-flex align-items-center justify-content-center">
          <div class="input-wrapper">
            <label for="FirstName">ชื่อ</label>
            <input type="text" name="FirstName" id="FirstName" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required>
          </div>

          <div class="input-wrapper">
            <label for="LastName">นามสกุล</label>
            <input type="text" name="LastName" id="LastName" value="<?php echo htmlspecialchars($user['LastName']); ?>" required>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="d-flex align-items-center justify-content-center">
          <div class="input-wrapper">
            <label for="Tel">เบอร์โทรศัพท์</label>
            <input type="text" name="Tel" id="Tel" value="<?php echo htmlspecialchars($user['Tel']); ?>" required>
          </div>

          <div class="input-wrapper">
            <label for="LineID">ไอดีไลน์</label>
            <input type="text" name="LineID" id="LineID" value="<?php echo htmlspecialchars($user['LineID']); ?>">
          </div>
        </div>

        <!-- Account Information -->
        <div class="d-flex align-items-center justify-content-center">
          <div class="input-wrapper1">
            <label for="Username">ชื่อผู้ใช้งาน</label>
            <input type="text" name="Username" id="Username" value="<?php echo htmlspecialchars($username); ?>" readonly>
          </div>

          <div class="input-wrapper">
            <label for="Password">รหัสผ่าน</label>
            <input type="password" name="Password" id="Password" placeholder=" เปลี่ยนรหัสผ่าน">
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="d-flex align-items-center justify-content-center mt-3">
          <div class="input-wrapper">
            <label for="ConfirmPassword">ยืนยันรหัสผ่าน</label>
            <input type="password" name="ConfirmPassword" id="ConfirmPassword" placeholder=" ยืนยันรหัสผ่าน">
          </div>
        </div>

<!-- Submit Button -->
<div class="d-flex align-items-center justify-content-center mt-4">
<div class="custom-boxprevious">
  <a href="../Admin/Homepage admin.php" class="btn-previous">ยกเลิก</a>
  <button type="button" class="btn-save" onclick="confirmSubmit()" style="margin-left:10px">บันทึก</button>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function confirmSubmit() {
    Swal.fire({
      title: 'ยืนยันการบันทึกข้อมูล?',
      text: "คุณแน่ใจหรือว่าต้องการบันทึกการเปลี่ยนแปลง?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'ยืนยัน',
      cancelButtonText: 'ยกเลิก',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // เมื่อผู้ใช้กดยืนยัน
        document.querySelector("form").submit(); // ส่งฟอร์ม
      } else {
        // เมื่อผู้ใช้ยกเลิก
        Swal.fire(
          'ยกเลิก',
          'การบันทึกข้อมูลถูกยกเลิกแล้ว!',
          'info'
        );
      }
    });
  }
</script>


<script>
  function previewImage(event) {
    const file = event.target.files[0]; // ได้ไฟล์ที่ผู้ใช้เลือก
    const reader = new FileReader();

    // ตรวจสอบขนาดไฟล์ (2MB = 2 * 1024 * 1024 bytes)
    if (file && file.size > 2 * 1024 * 1024) {
      // หากไฟล์เกินขนาด 2MB แสดง SweetAlert
      Swal.fire({
        title: 'ขนาดไฟล์เกิน 2MB!',
        text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 2MB.',
        icon: 'error',
        confirmButtonText: 'ตกลง'
      });

      // ลบไฟล์ที่เลือก
      event.target.value = '';
      return; // หยุดการทำงานในฟังก์ชันนี้
    }

    reader.onload = function(e) {
      const imagePreview = document.getElementById('profileImagePreview');
      imagePreview.src = e.target.result; // ตั้งค่าภาพที่เลือกเป็น src
    }

    if (file) {
      reader.readAsDataURL(file); // อ่านไฟล์ที่เลือก
    }
  }
</script>



    
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
