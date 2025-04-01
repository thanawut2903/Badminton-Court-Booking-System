<?php
session_start();
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
    exit;
}

$accountID = $_SESSION['user_idadmin']; // ดึง AccountID จาก Session

// ตรวจสอบว่าข้อมูลถูกส่งมาในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // รับข้อมูล JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าข้อมูลที่ต้องการครบถ้วนหรือไม่
    if (isset($data['date'], $data['startTime'], $data['endTime'], $data['court'])) {
        $bookingDate = $conn->real_escape_string($data['date']);
        $startTime = $conn->real_escape_string($data['startTime']);
        $endTime = $conn->real_escape_string($data['endTime']);
        $court = $conn->real_escape_string($data['court']);
        $numberHours = (strtotime($endTime) - strtotime($startTime)) / 3600;

        if ($numberHours <= 0) {
            echo json_encode(["success" => false, "message" => "เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด"]);
            exit;
        }

        // เริ่มต้นการบันทึกข้อมูล
        $requestDT = date('Y-m-d');
        $status = "เปิดก๊วนสำเร็จ";
        $BookingFormat = "การขอเปิดก๊วน";

        $conn->begin_transaction();
        try {
            // สร้างคำสั่ง SQL เพื่อบันทึกข้อมูล
            $sql = "INSERT INTO booking (AccountID, RequestDT, BookingDate, StartTime, EndTime, NumberHours, CourtID, BookingFormat, Status) 
                    VALUES ('$accountID', '$requestDT', '$bookingDate', '$startTime', '$endTime', '$numberHours', '$court', '$BookingFormat', '$status')";

            if (!$conn->query($sql)) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึก: " . $conn->error);
            }

            // ยืนยันการบันทึกข้อมูล
            $conn->commit();
            echo json_encode(["success" => true, "message" => "เปิดก๊วนสำเร็จ"]);
        } catch (Exception $e) {
            $conn->rollback(); // ยกเลิกการเปลี่ยนแปลงหากเกิดข้อผิดพลาด
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
