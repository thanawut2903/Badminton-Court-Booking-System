<?php
header('Content-Type: application/json');

// รับข้อมูล JSON
$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'] ?? null;
$CourtID = $data['CourtID'] ?? null;

if (!$bookingId || !$CourtID) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

session_start();
require '../php/dbconnect.php';

// ตรวจสอบว่ามีการล็อกอินและดึง AccountID
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}
$accountId = $_SESSION['user_idadmin'];

// ดึง FirstName ของผู้อนุมัติ
$sql = "SELECT FirstName FROM account WHERE AccountID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $approvedBy = $row['FirstName']; // ชื่อผู้อนุมัติ
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลผู้อนุมัติ']);
    exit;
}

$approvedDT = date('Y-m-d H:i:s'); // วันที่และเวลาที่อนุมัติ

// ตรวจสอบว่า CourtID ว่างในช่วงเวลาที่เลือกหรือไม่
$checkSql = "SELECT * FROM booking WHERE CourtID = ? AND BookingDate = (SELECT BookingDate FROM booking WHERE BookingID = ?) 
AND ((StartTime < (SELECT EndTime FROM booking WHERE BookingID = ?) AND EndTime > (SELECT StartTime FROM booking WHERE BookingID = ?)) 
AND Status = 'จองสำเร็จ')";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param('siii', $CourtID, $bookingId, $bookingId, $bookingId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'สนามที่เลือกถูกจองแล้วในช่วงเวลานี้']);
    exit;
}

// อัปเดตข้อมูล
$stmt = $conn->prepare("UPDATE booking SET CourtID = ?, Status = 'เปิดก๊วนสำเร็จ', ApprovedBy = ?, ApprovedDT = ? WHERE BookingID = ?");
$stmt->bind_param('sssi', $CourtID, $approvedBy, $approvedDT, $bookingId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ', 'redirect' => '../Admin/Edit open gang.php']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}

$stmt->close();
$conn->close();
?>
