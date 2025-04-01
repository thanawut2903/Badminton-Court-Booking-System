<?php
session_start();
include '../php/member_navbar.php';
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
  <link href="STpatmentowner.css" rel="stylesheet">
</head>
<body>


  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ชำระค่าบริการ</h1>
      </div>
      </div>
  

  <!-- ปุ่มข้อความวิธีการชำระเงิน -->
  <div class="custom-box1 text-center">
    <h4>1.แอดไลน์ line id : 0946518833
        <a href="https://line.me/ti/p/~0946518833" target="_bank">
        <img src="/web_badmintaon_khlong_6/Images/line icon2.png" id="imgline">
        </a>
    </h4>
  </div>

  <div class="custom-box1 text-center">
    <h3>2.แจ้งชื่อที่ใช้งานในระบบจองสนามแบด</h3>
  </div>

  <div class="custom-box1 text-center">
    <h3>3.ชำระเงินตามอัตรการใช้บริการ</h3>
  </div>

<div class="container1 mt-4">
    <div class="custom-box2 text-center">
        <h3>ชำระเงินที่</h3>
    </div>

<div class="text1 text-left">
        <h3 id="BankName">ธนาคาร : ไทยพาณิชย์ (SCB)</h3>
    </div>
    <div class="text1 text-left">
        <h3 id="AccountNumber">เลขที่บัญชี : 3602750148</h3>
    </div>
    <div class="text1 text-left">
        <h3 id="AccountName">ชื่อบัญชี : นลพรรณ สีหมวด</h3>
    </div>
    </div>

<div class="custom-box1 text-center">
    <h3>4.ส่งสลิปการโอนเงินเพื่อยืนยันการจองทางไลน์</h3>
  </div>

  <div class="custom-box1 text-center">
    <h3>5.รอทางสนามยืนยันการจอง</h3>
  </div>

  <div style="text-align: center; margin-top: 50px;">
        <button class="custom-button" onclick="location.href='/web_badmintaon_khlong_6/Member/Status open group.php'">เช็คสถานะการจอง</button>
    <div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
    </div>

    </div>



  </div>
  </div>


 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
