<?php
require '../php/dbconnect.php';
session_start();

// ตรวจสอบว่าส่งข้อมูลผ่าน POST และในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['bookingId'])) {
        $bookingId = $conn->real_escape_string($data['bookingId']);

        // ตรวจสอบว่ามีการจองนี้อยู่หรือไม่
        $checkSql = "SELECT BookingID FROM booking WHERE BookingID = ?";
        $stmt = $conn->prepare($checkSql);

        if ($stmt) {
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // ส่งผู้ใช้งานไปยังหน้า Reasoncancel.php เพื่อเลือกสนามและอัปเดตข้อมูล
                echo json_encode([
                    "success" => true, 
                    "message" => "โปรดเลือกสนามก่อนเปลี่ยนสถานะ", 
                    "redirect" => "../admin/Reasoncancel.php?bookingId=" . $bookingId
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "ไม่พบรายการจองที่ระบุ"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการตรวจสอบรายการจอง"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "คำขอไม่ถูกต้อง"]);
}

$conn->close();
?>
