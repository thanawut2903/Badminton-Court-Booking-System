<?php
session_start();
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
    exit;
}

$accountID = $_SESSION['user_idadmin']; // ดึง AccountID จาก Session

// ตรวจสอบว่าข้อมูลถูกส่งมาในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data['date'], $data['startTime'], $data['endTime'], $data['court'])) {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
        exit;
    }

    $bookingDate = $conn->real_escape_string($data['date']);
    $startTime = $conn->real_escape_string($data['startTime']);
    $endTime = $conn->real_escape_string($data['endTime']);
    $court = $conn->real_escape_string($data['court']);
    $groupMessage = isset($data['groupMessage']) ? $conn->real_escape_string($data['groupMessage']) : null;

    // ดึงจำนวนผู้เล่นสูงสุด
    $sql = "SELECT ItemDetail FROM info WHERE infoID = 15";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxPlayer = $row['ItemDetail']; // ดึงค่าจาก ItemDetail
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลจำนวนผู้เล่นสูงสุด"]);
        exit;
    }

    // ดึงราคาค่าบริการต่อชั่วโมง
    $sql = "SELECT ItemDetail FROM info WHERE infoID = 13";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $servicePrice = (float) $row['ItemDetail']; // ดึงราคาค่าบริการต่อชั่วโมง
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลราคาค่าบริการ"]);
        exit;
    }

    // ตรวจสอบว่าเวลาเริ่มต้นน้อยกว่าเวลาสิ้นสุด
    if (strtotime($startTime) >= strtotime($endTime)) {
        echo json_encode(["success" => false, "message" => "เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด"]);
        exit;
    }

    $requestDT = date('Y-m-d'); // วันที่จองคือวันที่ปัจจุบัน
    $numberHours = (strtotime($endTime) - strtotime($startTime)) / 3600; // คำนวณจำนวนชั่วโมง
    $totalPrice = $numberHours * $servicePrice; // คำนวณราคาทั้งหมด
    $BookingFormat = "การขอเปิดก๊วน";
    $status = "เปิดก๊วนสำเร็จ";

    // ตรวจสอบความซ้ำซ้อนของการจอง (ยกเว้นสถานะที่ถูกยกเลิก)
    $checkSql = "SELECT * FROM booking WHERE BookingDate = '$bookingDate' AND CourtID = '$court' AND Status != 'ยกเลิก' AND (
        (StartTime <= '$startTime' AND EndTime > '$startTime') OR
        (StartTime < '$endTime' AND EndTime >= '$endTime') OR
        ('$startTime' <= StartTime AND '$endTime' >= EndTime))";

    $result = $conn->query($checkSql);
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "ช่วงเวลานี้ไม่สามารถทำการจองได้ เนื่องจากมีการจองแล้ว"]);
        exit;
    }

    // บันทึกข้อมูลการจองลงฐานข้อมูล
    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO booking (AccountID, RequestDT, BookingDate, StartTime, EndTime, NumberHours, BookingFormat, CourtID, Status, MaxPlayer, Price, GroupMessage) 
                VALUES ('$accountID', '$requestDT', '$bookingDate', '$startTime', '$endTime', '$numberHours', '$BookingFormat', '$court', '$status', '$maxPlayer', '$totalPrice', ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $groupMessage);

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึก: " . $stmt->error);
        }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "บันทึกข้อมูลสำเร็จ"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
