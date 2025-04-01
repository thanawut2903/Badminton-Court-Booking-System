<?php 
  require "../php/dbconnect.php";
  session_start();
  include '../php/member_navbar.php';

  // เชื่อมต่อฐานข้อมูล
  require '../php/dbconnect.php';

  // ดึงข้อมูล LineID และ Tel
  $sqlInfo = "SELECT infoID, ItemDetail FROM info WHERE infoID IN (4, 5)";
  $resultInfo = $conn->query($sqlInfo);

  $lineID = "ไม่พบข้อมูล"; // ค่าเริ่มต้น
  $Tel = "ไม่พบข้อมูล";   // ค่าเริ่มต้น

  while ($rowInfo = $resultInfo->fetch_assoc()) {
      if ($rowInfo['infoID'] == 4) {
          $lineID = $rowInfo['ItemDetail']; // ดึง LineID
      } elseif ($rowInfo['infoID'] == 5) {
          $Tel = $rowInfo['ItemDetail']; // ดึงเบอร์โทรศัพท์
      }
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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="payment.css" rel="stylesheet">
</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <div class="container">
    <!-- กล่องข้อความ -->
    <div class="containerd d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ชำระค่าบริการ</h1>
      </div>
    </div>

    <!-- ข้อมูลวิธีการชำระเงิน -->
    <div class="custom-box12 text-center">
      <h4>1.แอดไลน์ Line ID : <?= $lineID ?>
        <a href="https://line.me/ti/p/~0946518833" target="_blank">
        <img src="/web_badmintaon_khlong_6/Images/line icon2.png" id="imgline"><br>
        </a> หรือ โทร : <?= $Tel?> 
      </h4>
    </div>

    <div class="custom-box1 text-center">
      <h3>2.แจ้งชื่อที่ใช้งานในระบบจองสนามแบด</h3>
    </div>

    <div class="custom-box1 text-center">
      <h3>3.ชำระเงิน ราคาที่ต้องชำระเงินตามอัตราค่าบริการ</h3>
    </div>

    <div class="container12 mt-4">
      <div class="custom-box2 text-center">
        <h3>ชำระเงินที่</h3>
      </div>

      <?php 
      // ดึงข้อมูลธนาคารจากตาราง paymentcontact
      $sqlPayment = "SELECT PaymentcontactID, BankName, AccountNumber, AccountName FROM paymentcontact";
      $resultPayment = $conn->query($sqlPayment);
      if ($resultPayment->num_rows > 0) {
          while ($rowPayment = $resultPayment->fetch_assoc()) {
              echo "
                <div class='text1 text-left'>
                    <h3 id='BankName'>ธนาคาร : {$rowPayment['BankName']}</h3>
                </div>
                <div class='text1 text-left'>
                    <h3 id='AccountNumber'>เลขที่บัญชี : {$rowPayment['AccountNumber']}</h3>
                </div>
                <div class='text1 text-left'>
                    <h3 id='AccountName'>ชื่อบัญชี : {$rowPayment['AccountName']}</h3>
                </div>
              ";
          }
      } else {
          echo "<p>ไม่พบข้อมูลการชำระเงิน</p>";
      }
      ?>

      <div class="custom-box1 text-center"style="margin-top: 40px;">
        <h3>4.ส่งสลิปการโอนเงินเพื่อยืนยันการจอง</h3>
      </div>

      <div class="custom-box1 text-center">
        <h3>5.อัปโหลดสลิปการโอนเงินลงในระบบ</h3>
      </div>

      <div class="custom-box1 text-center">
        <h3>6.รอทางสนามยืนยันการจอง</h3>
      </div>

      <!-- ปุ่มย้อนกลับ -->
      <!-- <div class="custom-boxprevious">
          <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
      </div> -->
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
