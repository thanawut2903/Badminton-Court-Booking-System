<?php 
  require '../php/dbconnect.php'
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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Homepage.css" rel="stylesheet">
</head>
<body>
  <!-- เมนูด้านบน -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
      <!-- โลโก้และชื่อสนาม -->
      <a class="navbar-brand d-flex align-items-center" href="Homepage.php">
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
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>สนามแบดมินตัน คลอง6</h1>
      </div>
    </div>
    


    
  <!-- รูปภาพหน้าหลัก -->
<!-- ขยับเนื้อหา -->
<div class="content-container" style="margin-left: 0px;">
    <!-- Carousel -->
    <div id="carouselExample1" class="carousel slide mt-3" data-bs-ride="carousel">
      <!-- Indicators -->
      <div class="carousel-indicators">
      
    <?php
    $sql = "SELECT ImageID,ImagePath FROM Image";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    for($i = 1; $i <= mysqli_num_rows($result); $i++){
      if($i == 1){
        echo '<button type="button" data-bs-target="#carouselExample1" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>';
        continue;
      }
      $pre = $i-1;
      echo "<button type='button' data-bs-target='#carouselExample1' data-bs-slide-to='$pre' aria-label='Slide $i'></button>";
    }
    echo '</div>
    <div class="carousel-inner">';
    $num = 1;
  
    while ($row = $result->fetch_assoc()) {
      $data[$row['ImageID']] = htmlspecialchars($row['ImagePath']);
      //print_r($row);
      if($num == 1)
      echo "<div class='carousel-item active'>";
      else
      echo "<div class='carousel-item'>";
      echo "
          <img src='../{$row["ImagePath"]}' class='d-block w-100' alt='Slide {$num}'>
        </div>
      ";
      $num++;
  }
      echo '<button class="carousel-control-prev" type="button" data-bs-target="#carouselExample1" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExample1" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>';
    ?>
    </div>


    <!-- ปุ่มข้อความการจองสนาม -->
   <div class="content-container" style="margin-left: 0px;">
  <div class="custom-box1 text-left">
  <h3>การจองสนาม</h3>
  </div>

  
  <div class="custom-box11 text-left">
  <div class="custom-text1 text-left">
    <?php 
    $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID = 9";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $itemDetail = str_replace("\n","<br>",$row["Itemdetail"]);
    echo "<p>{$itemDetail}</p>"
    ?>
  </div>
</div>

  <!-- ปุ่มข้อความการชำระเงิน -->
  <div class="custom-box1 text-left">
    <h3>การชำระเงิน</h3>
  </div>

  <div class="custom-box12 text-left">
    <?php
    $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID = 10";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $itemDetail = str_replace("\n","<br>",$row["Itemdetail"]);
    echo"<p>{$itemDetail}</p><br> "
    ?>
  </div>



  <!-- ปุ่มข้อความเกี่ยวกับสนามแบดมินตัน -->
  <div class="custom-box1 text-left">
    <h3>เกี่ยวกับสนามแบดมินตัน</h3>
  </div>

  <div class="custom-box13 text-left">
  <?php 
  $sql = "SELECT InfoID,ItemName,Itemdetail FROM info WHERE InfoID = 11";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $itemDetail = str_replace("\n","<br>",$row["Itemdetail"]);
  echo "<p>{$itemDetail}</p> "
  ?>
  </div>
  </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
