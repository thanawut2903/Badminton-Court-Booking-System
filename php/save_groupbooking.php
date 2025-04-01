<?php
session_start();
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
    exit;
}

$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session

// ตรวจสอบว่าข้อมูลถูกส่งมาในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data['date'], $data['startTime'], $data['endTime'])) {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
        exit;
    }

    $bookingDate = $conn->real_escape_string($data['date']);
    $startTime = $conn->real_escape_string($data['startTime']);
    $endTime = $conn->real_escape_string($data['endTime']);

    // ดึงค่าการจองล่วงหน้าสูงสุด
    $sql = "SELECT ItemDetail FROM info WHERE infoID = 12";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $bookingAdvanceLimit = (int)$row['ItemDetail']; // ค่าในวัน
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลการตั้งค่าจองล่วงหน้า"]);
        exit;
    }

    // ตรวจสอบการจองล่วงหน้า
    $currentDate = date('Y-m-d');
    $currentDateObj = new DateTime($currentDate);
    $bookingDateObj = new DateTime($bookingDate);
    $dateDiff = $currentDateObj->diff($bookingDateObj)->days;

    if ($dateDiff > $bookingAdvanceLimit) {
        echo json_encode(["success" => false, "message" => "ไม่สามารถจองล่วงหน้าเกิน $bookingAdvanceLimit วันได้"]);
        exit;
    }

    // ดึงจำนวนผู้เล่นสูงสุด
    $sql = "SELECT ItemDetail FROM info WHERE infoID = 15";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxPlayer = (int)$row['ItemDetail']; // ดึงค่าจาก ItemDetail
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
        $servicePrice = (float)$row['ItemDetail']; // ดึงราคาค่าบริการต่อชั่วโมง
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลราคาค่าบริการ"]);
        exit;
    }

    // ตรวจสอบว่าเวลาเริ่มต้นน้อยกว่าเวลาสิ้นสุด
    if (strtotime($startTime) >= strtotime($endTime)) {
        echo json_encode(["success" => false, "message" => "เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด"]);
        exit;
    }

    // ตรวจสอบจำนวนสนามทั้งหมดจากตาราง court (ไม่นับ CourtID = 0)
    $sql = "SELECT COUNT(*) as totalCourts FROM court WHERE CourtID != 0 AND CourtStatus = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalCourts = (int)$row['totalCourts']; // จำนวนสนามทั้งหมด
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลจำนวนสนาม"]);
        exit;
    }

// ตรวจสอบจำนวนการจองในช่วงเวลานั้น (รวมการจองที่ยังไม่ได้กำหนดสนาม)
$checkSql = "SELECT COUNT(*) as bookedCourts 
    FROM booking 
    WHERE BookingDate = ? 
    AND Status IN ('รอชำระเงิน', 'เปิดก๊วนสำเร็จ', 'จองสำเร็จ', 'รออนุมัติ')
    AND (
        (StartTime < ? AND EndTime > ?) OR
        (StartTime < ? AND EndTime > ?) OR
        (StartTime <= ? AND EndTime >= ?)
    )";

$stmt = $conn->prepare($checkSql);
$stmt->bind_param("sssssss", $bookingDate, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$bookedCourts = (int)$row['bookedCourts'];

// ตรวจสอบจำนวนสนามที่ว่าง
if ($bookedCourts >= $totalCourts) {
    echo json_encode([
        "success" => false,
        "message" => "ช่วงเวลานี้สนามถูกจองเต็มแล้ว กรุณาเลือกช่วงเวลาอื่น",
        "debug" => [
            "bookedCourts" => $bookedCourts,
            "totalCourts" => $totalCourts
        ]
    ]);
    exit;
}


    $requestDT = date('Y-m-d'); // วันที่จองคือวันที่ปัจจุบัน
    $numberHours = (strtotime($endTime) - strtotime($startTime)) / 3600; // คำนวณจำนวนชั่วโมง
    $totalPrice = $numberHours * $servicePrice; // คำนวณราคาทั้งหมด
    $BookingFormat = "การขอเปิดก๊วน";
    $status = "รอชำระเงิน";

    // บันทึกข้อมูลการจองลงฐานข้อมูล
    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO booking (AccountID, RequestDT, BookingDate, StartTime, EndTime, NumberHours, BookingFormat, CourtID, Status, MaxPlayer, Price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssssi", $accountID, $requestDT, $bookingDate, $startTime, $endTime, $numberHours, $BookingFormat, $status, $maxPlayer, $totalPrice);

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึก: " . $stmt->error);
        }

        $conn->commit();
        header('Content-Type: application/json');
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
