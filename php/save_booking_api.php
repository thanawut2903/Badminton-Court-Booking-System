<?php
header('Content-Type: application/json');

// รับข้อมูล JSON
$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'] ?? null;
$CourtID = $data['CourtID'] ?? null;
$StartTime = $data['StartTime'] ?? null;
$EndTime = $data['EndTime'] ?? null;
$editTime = $data['editTime'] ?? false;

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

// ดึงข้อมูลการจองเดิม
$sql = "SELECT StartTime, EndTime FROM booking WHERE BookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existingBooking = $result->fetch_assoc();
    if (!$editTime) {
        $StartTime = $existingBooking['StartTime'];
        $EndTime = $existingBooking['EndTime'];
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการจองเดิม']);
    exit;
}

// แปลง 00:00 เป็น 24:00 หากพบ
if ($EndTime === '00:00' || $EndTime === '00:00:00') {
    $EndTime = '24:00';
}

// ตรวจสอบสถานะของสนามที่เลือกก่อน
$checkCourtStatusSql = "SELECT CourtStatus FROM court WHERE CourtID = ?";
$checkCourtStatusStmt = $conn->prepare($checkCourtStatusSql);
$checkCourtStatusStmt->bind_param('i', $CourtID);
$checkCourtStatusStmt->execute();
$checkCourtStatusResult = $checkCourtStatusStmt->get_result();

if ($checkCourtStatusResult->num_rows > 0) {
    $courtStatusRow = $checkCourtStatusResult->fetch_assoc();
    if ($courtStatusRow['CourtStatus'] == 0) {
        echo json_encode(['success' => false, 'message' => 'สนามที่เลือกถูกปิด ไม่สามารถทำการจองได้']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลสนาม']);
    exit;
}

// ตรวจสอบว่า CourtID ว่างในช่วงเวลาที่เลือกหรือไม่
$checkSql = "SELECT * FROM booking WHERE CourtID = ? AND BookingDate = (SELECT BookingDate FROM booking WHERE BookingID = ?) 
AND ((StartTime < ? AND EndTime > ?) AND (Status = 'จองสำเร็จ' OR Status = 'เปิดก๊วนสำเร็จ'))";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param('siss', $CourtID, $bookingId, $EndTime, $StartTime);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'สนามที่เลือกถูกจองแล้วในช่วงเวลานี้']);
    exit;
}

// อัปเดตข้อมูลรวมถึงเวลา
$stmt = $conn->prepare("UPDATE booking SET CourtID = ?, StartTime = ?, EndTime = ?, Status = 'จองสำเร็จ', ApprovedBy = ?, ApprovedDT = ? WHERE BookingID = ?");
$stmt->bind_param('sssssi', $CourtID, $StartTime, $EndTime, $approvedBy, $approvedDT, $bookingId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ', 'redirect' => '../Admin/Edit open gang.php']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}

$stmt->close();
$conn->close();
?>
