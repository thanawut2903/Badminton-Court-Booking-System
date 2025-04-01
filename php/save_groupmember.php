<?php
session_start();
header('Content-Type: application/json');

require '../php/dbconnect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}

$accountId = $_SESSION['user_id']; // ดึง AccountID จาก Session
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['bookingId'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบ Booking ID']);
    exit;
}

$bookingId = $data['bookingId'];
$status = 'รอชำระเงิน';

// บันทึกข้อมูลลงตาราง groupmember
$sql = "INSERT INTO groupmember (BookingID, AccountID, Status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iis', $bookingId, $accountId, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
