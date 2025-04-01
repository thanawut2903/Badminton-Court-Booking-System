<?php
header('Content-Type: application/json');
session_start();

// เชื่อมต่อฐานข้อมูล
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}

$accountId = $_SESSION['user_id'];

// รับข้อมูลจากคำขอ
$data = json_decode(file_get_contents('php://input'), true);
$bookingId = $data['bookingId'] ?? null;

if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล Booking ID']);
    exit;
}

// ดึงข้อมูลการจองเพื่อตรวจสอบเวลาที่ยกเลิกได้
$sql = "SELECT TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(BookingDate, ' ', StartTime)) AS TimeToStart FROM booking WHERE BookingID = ? AND AccountID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $bookingId, $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $timeToStart = $row['TimeToStart'];



    // บันทึกสถานะการยกเลิก
    $cancelReason = "ยกเลิกโดยผู้ใช้";
    $cancelDT = date('Y-m-d H:i:s');

    $updateSql = "UPDATE booking SET Status = 'ยกเลิก', CancelReason = ?, CancelBy = ?, CancelDT = ? WHERE BookingID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('sisi', $cancelReason, $accountId, $cancelDT, $bookingId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'ยกเลิกการจองสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกการยกเลิกได้']);
    }

    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการจองหรือไม่มีสิทธิ์ยกเลิกการจองนี้']);
}

$stmt->close();
$conn->close();
?>
