<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับ BookingID ที่ส่งมาจาก AJAX
    $bookingId = isset($_POST['bookingId']) ? intval($_POST['bookingId']) : 0;

    // ตรวจสอบว่ามีการส่ง BookingID มาหรือไม่
    if (!$bookingId) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล BookingID"]);
        exit;
    }

    // ตรวจสอบเวลาปัจจุบันกับเวลาการจอง
    $sql = "SELECT BookingDate, StartTime FROM booking WHERE BookingID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $bookingDateTime = new DateTime($row['BookingDate'] . ' ' . $row['StartTime']);
        $currentDateTime = new DateTime();
        $interval = $currentDateTime->diff($bookingDateTime);

        // ตรวจสอบว่าต่างกันเกิน 30 นาทีหรือไม่
        if ($interval->invert === 0 && $interval->i < 30 && $interval->h === 0 && $interval->d === 0) {
            echo json_encode(["success" => false, "message" => "ไม่สามารถยกเลิกได้หลังเวลาจองเริ่มต้นเกิน 30 นาที"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลการจอง"]);
        exit;
    }

    // อัปเดตสถานะในฐานข้อมูลเป็น "ยกเลิก"
    $sql = "UPDATE groupmember SET Status = 'ยกเลิก' WHERE BookingID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "เปลี่ยนสถานะเป็น \"ยกเลิก\" สำเร็จ"]);
    } else {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเปลี่ยนสถานะ"]);
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo json_encode(["success" => false, "message" => "คำขอไม่ถูกต้อง"]);
    exit;
}
?>
