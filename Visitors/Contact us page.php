<?php 
    require '../php/dbconnect.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ติดต่อสนามแบดมินตัน</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <!-- ลิงก์ไปยังไฟล์ CSS -->
    <link href="Contact us page.css" rel="stylesheet">
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
            <a id="admin-btn" class="nav-link btn mx-1" href="/web_badmintaon_khlong_6/Admin/Admin login page.php">ผู้ดูแลระบบ</a>
          </li>
      </div>
    </div>
  </nav>

  <div class="container d-flex align-items-center justify-content-center vh-100">
      <!-- ฟอร์ม Login -->
  

  <!-- ฟอร์ม ติดต่อเรา -->
   
  <div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow" style="width: 30rem;">
      <h3 class="text-center mb-4">ติดต่อสนามแบดมินตัน</h3>
      <form>
        
<div class="mt-3 text-end">

  <!-- ปุ่มข้อความการจองสนาม -->
  <div class="custom-box1 text-right">
    <h3>ที่อยู่ของสนาม</h3>
  </div>

  <div class="custom-box11 text-center">
    <?php
        $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID = 1";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo "
            <p>{$row["Itemdetail"]}</p>
        " 
    ?>
  </div>

  <div class="custom-boxfb">
    <?php 
    $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID IN(3,6)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($row["InfoID"] == 3){
          $urlfb = $row["Itemdetail"];
        }
        else if ($row["InfoID"] == 6){
          $imgfb = $row["Itemdetail"];
        }
     }
          echo "
          <a href='$urlfb' target='_blank' class='icon-link'>
              <img src='$imgfb' alt='facebook' class='custom-icon1'>      
          </a>"; 
      }
  ?>
    <div class="custom-textfb ms-0.5=">
        <p>สนามแบดมินตัน คลองหก</p>
    </div>
  </div>

  <div class="custom-boxline">
  <?php 
    $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID IN (4,7)";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0){
      while ($row = $result->fetch_assoc()){
        if ($row["InfoID"] == 4){
          $idline = $row["Itemdetail"];
        }
        else if ($row["InfoID"] == 7){
          $imgline = $row["Itemdetail"];
      }
    }
        echo"
        <a href='https://line.me/ti/p/~$idline' target='_blank class='icon-link'>
            <img src='$imgline' alt='line' class='custom-icon2'>
        </a>";
    }
  ?>
    <div class="custom-textline ms-3">
        <p>0946518833</p>
    </div>
  </div>



  <div class="custom-boxtel">
  <?php 
  $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID IN (5,8)";
  $result = $conn->query($sql);
  if ($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
      if ($row["InfoID"] == 5){
        $phonenumber = $row["Itemdetail"];
    }
    else if ($row["InfoID"] == 8){
      $imgphone = $row["Itemdetail"];
  }
    }
    echo "
        <img src='$imgphone' alt='tel' class='custom-icon3'>
        <div class='custom-texttel ms-3'>
        <p>$phonenumber</p>
        </div>
    ";    
}
  ?>
  </div>
      </form>
    </div>
  </div>

<!-- รูปภาพด้านขวา -->
<div class="map-container">
  <?php 
  $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID = 2";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  echo" {$row["Itemdetail"]}   
    </div>
</div>   
  ";  
  ?>
  </div>
  <div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
    </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
