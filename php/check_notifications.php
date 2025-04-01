<?php
require '../php/dbconnect.php';

// ดึงจำนวนการจองแบบทั่วไปที่ยังไม่ได้ดำเนินการ
$sqlGeneral = "SELECT COUNT(*) as count FROM booking WHERE Status = 'รอชำระเงิน' AND BookingFormat = 'การจองแบบทั่วไป'";
$resultGeneral = $conn->query($sqlGeneral);
$generalCount = ($resultGeneral->num_rows > 0) ? $resultGeneral->fetch_assoc()['count'] : 0;

// ดึงจำนวนการขอเข้าเล่นแบบก๊วน
$sqlGang = "SELECT COUNT(*) as count FROM groupmember WHERE Status = 'รอชำระเงิน'";
$resultGang = $conn->query($sqlGang);
$gangCount = ($resultGang->num_rows > 0) ? $resultGang->fetch_assoc()['count'] : 0;

// ดึงจำนวนการขอเปิดสนามแบบก๊วน
$sqlOpenGang = "SELECT COUNT(*) as count FROM booking WHERE Status = 'รอชำระเงิน' AND BookingFormat = 'การขอเปิดก๊วน'";
$resultOpenGang = $conn->query($sqlOpenGang);
$openGangCount = ($resultOpenGang->num_rows > 0) ? $resultOpenGang->fetch_assoc()['count'] : 0;

$conn->close();

// ส่งผลลัพธ์เป็น JSON
echo json_encode([
    'general' => $generalCount,
    'gang' => $gangCount,
    'openGang' => $openGangCount
]);
?>
