<?php
header('Content-Type: application/json');
session_start();

// เชื่อมต่อฐานข้อมูล
require '../php/dbconnect.php';

// รับข้อมูลจากคำขอ
$data = json_decode(file_get_contents('php://input'), true);
$bookingId = $data['bookingId'] ?? null;
$CancelReason = $data['cancelReason'] ?? '';

if (empty($CancelReason)) {
    echo json_encode(['success' => false, 'message' => 'เหตุผลการยกเลิกไม่ครบถ้วน']);
    exit;
}

$cancelDT = date('Y-m-d H:i:s'); // วันที่และเวลาที่อนุมัติ


// บันทึกข้อมูลลงในฐานข้อมูล
$stmt = $conn->prepare("UPDATE booking SET CancelReason = ? , Status = 'ยกเลิก', CancelBy = ?,CancelDT = ? WHERE BookingID = ? ");
$stmt->bind_param('sisi', $CancelReason, $_SESSION['user_idadmin'],$cancelDT, $bookingId);

if ($stmt->execute()) {

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}

$stmt->close();
$conn->close();
?>
