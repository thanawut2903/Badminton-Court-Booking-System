<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

// ดึงข้อมูลจากตาราง court โดยไม่รวม CourtID = 0
$sql = "SELECT CourtID, CourtName, CourtStatus, OpenTime, CloseTime FROM court WHERE CourtID != 0";
$result = $conn->query($sql);

$courts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courts[] = $row;
    }
}

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode($courts);

$conn->close();
?>
