<?php

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";   // ชื่อเซิร์ฟเวอร์
$username = "root";         // ชื่อผู้ใช้งานฐานข้อมูล
$password = "";             // รหัสผ่านฐานข้อมูล
$dbname = "webbadminton";   // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);


// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";

?>
