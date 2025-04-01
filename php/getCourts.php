<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

$sql = "SELECT courtID FROM court WHERE courtID > 0 ORDER BY courtID";
$result = $conn->query($sql);

$availableCourts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableCourts[] = $row['courtID']; // เก็บค่า courtID ใน array
    }
}

$conn->close();

// ส่งออก JSON
echo json_encode($availableCourts);
?>
