<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_idadmin'])) {
    echo "<script>
            alert('กรุณาเข้าสู่ระบบก่อน!');
            window.location.href = '../Admin/Admin login page.php';
          </script>";
    exit();
}

require '../php/dbconnect.php';

$userId = $_SESSION['user_idadmin'];
$sql = "SELECT ImageAccount, Username FROM Account WHERE AccountID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $profileImage = $row['ImageAccount'] ?: 'uploads/default.png';
    $username = $row['Username'];
} else {
    $profileImage = 'uploads/default.png';
    $username = 'Unknown';
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
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="SThomepageadmin.css" rel="stylesheet">
</head>
<body>
  <!-- เมนูด้านบน -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
      <!-- โลโก้และชื่อสนาม -->
      <a class="navbar-brand d-flex align-items-center" href="/web_badmintaon_khlong_6/Admin/Homepage admin.php">
        <img src="/web_badmintaon_khlong_6/Images/logo.jpg" alt="Logo" style="width: 40px; height: 40px;" class="me-2">
        <span>เว็บจองสนามแบดมินตัน คลอง6</span>
      </a>
      <!-- เมนูปุ่มหน้าหลัก -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <!-- <div class="notification-icon">
              <i class="fa fa-bell"></i>
              <span class="notification-count" id="notification-count">0</span>
            </div> -->
            <!-- <a class="nav-link12 btn mx-1 custom-btn" href="/web_badmintaon_khlong_6/Admin/Homepage admin.php">หน้าหลัก</a> -->
          </li>

          <!-- เมนูด้านบนขวา บัญชี -->
          <div class="d-flex align-items-center">
            <a href="/web_badmintaon_khlong_6/Admin/Editaccount.php" style="text-decoration: none; color: inherit;">
              <img src="/web_badmintaon_khlong_6/<?php echo $profileImage; ?>" alt="User Icon" class="rounded-circle" style="width: 40px; height: 40px;">
              <span style="font-weight: bold; margin-left:10px; margin-right:10px; color: black; text-decoration: none;">
                  <?php echo $username; ?>
              </span>
            </a>
          </div>

          <div class="logout">
            <a href="/web_badmintaon_khlong_6/php/logout.php">
              <img src="/web_badmintaon_khlong_6/Images/logout icon.png" id="logouticon" style="width: 30px; height: 30px; margin-right: 1px;" alt="Logout">
            </a>
          </div>
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>
