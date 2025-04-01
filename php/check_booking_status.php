<?php
require '../php/dbconnect.php'; // เชื่อมต่อภาษฐ์

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่าข้อมูล CourtID ถูกส่งมาหรือไม่
if (!isset($data['CourtID']) || !is_numeric($data['CourtID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้อมที่ส่งมาหรือไม่ถูกต้น']);
    exit;
}

$courtID = (int)$data['CourtID'];

// ตรวจสอบการจองในสนามที่ระบุ
$sql = "SELECT COUNT(*) AS bookingCount 
        FROM booking 
        WHERE CourtID = ? 
        AND BookingDate = CURDATE() 
        AND Status IN ('รอชำระเงิน', 'จองสำเร็จ', 'เปิดก๊วนสำเร็จ')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $courtID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hasBookings = $row['bookingCount'] > 0;
    echo json_encode(['success' => true, 'hasBookings' => $hasBookings]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถรวจสอบข้อมูลได้']);
}

$stmt->close();
$conn->close();
?>
