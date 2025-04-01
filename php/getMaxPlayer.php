<?php
// เชื่อมต่อฐานข้อมูล
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง info ที่ InfoID = 15
$sql = "SELECT ItemDetail FROM info WHERE InfoID = 15";
$result = $mysqli->query($sql);

// ตรวจสอบผลลัพธ์
if ($result->num_rows > 0) {
    // ดึงข้อมูลจากผลลัพธ์
    $row = $result->fetch_assoc();
    
    // ส่งค่า ItemDetail กลับไปในรูปแบบ JSON
    echo json_encode(['maxplayer' => $row['ItemDetail']]);
} else {
    echo json_encode(['maxplayer' => 0]); // กรณีไม่พบข้อมูล
}

// ปิดการเชื่อมต่อ
$mysqli->close();
?>
