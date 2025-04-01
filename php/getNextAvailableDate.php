<?php
// เชื่อมต่อฐานข้อมูล
require '../php/dbconnect.php';

// ตั้งค่า Header เป็น JSON
header('Content-Type: application/json');

// เปิดการแสดงข้อผิดพลาดสำหรับการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

// รับข้อมูลจากคำขอ
$requestPayload = file_get_contents("php://input");
$data = json_decode($requestPayload, true);

// ตรวจสอบว่ามีข้อมูลที่จำเป็น
if (!isset($data['date'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
    exit;
}

$currentDate = $data['date'];

// ตรวจสอบว่ามีการจองในวันที่ปัจจุบันหรือไม่
$sqlCurrentDate = "SELECT COUNT(*) AS totalBookings FROM booking WHERE BookingDate = ?";
$stmtCurrent = $conn->prepare($sqlCurrentDate);
if ($stmtCurrent === false) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในคำสั่ง SQL (ตรวจสอบวันที่ปัจจุบัน)'
    ]);
    exit;
}

$stmtCurrent->bind_param("s", $currentDate);
$stmtCurrent->execute();
$resultCurrent = $stmtCurrent->get_result();
$currentRow = $resultCurrent->fetch_assoc();
$stmtCurrent->close();

if ($currentRow['totalBookings'] > 0) {
    // มีการจองในวันที่ปัจจุบัน
    echo json_encode([
        'success' => true,
        'redirect' => false,
        'message' => 'มีการจองในวันที่ค้นหา'
    ]);
    exit;
}

// ค้นหาวันถัดไปที่ไม่มีการจอง
$sqlNextDate = "
SELECT DATE_ADD(?, INTERVAL 1 DAY) AS nextAvailableDate
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM booking WHERE BookingDate = DATE_ADD(?, INTERVAL 1 DAY)
)
LIMIT 1;
";

$stmtNext = $conn->prepare($sqlNextDate);
if ($stmtNext === false) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในคำสั่ง SQL (ค้นหาวันถัดไป)'
    ]);
    exit;
}

$stmtNext->bind_param("ss", $currentDate, $currentDate);
$stmtNext->execute();
$resultNext = $stmtNext->get_result();
$nextDateRow = $resultNext->fetch_assoc();
$stmtNext->close();

if ($nextDateRow && $nextDateRow['nextAvailableDate']) {
    echo json_encode([
        'success' => true,
        'redirect' => true,
        'nextAvailableDate' => $nextDateRow['nextAvailableDate'],
        'message' => 'ไม่มีการจองในวันที่ค้นหา, โปรดไปยังวันถัดไป: ' . $nextDateRow['nextAvailableDate']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่มีวันว่างถัดไปในระบบ'
    ]);
}

$conn->close();
?>