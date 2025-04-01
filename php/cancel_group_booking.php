<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่า BookingID ที่ส่งมาจาก AJAX
    $bookingId = isset($_POST['bookingId']) ? intval($_POST['bookingId']) : 0;

    // ตรวจสอบว่ามีการส่ง BookingID มาหรือไม่
    if (!$bookingId) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล BookingID"]);
        exit;
    }

    // อัปเดตสถานะในฐานข้อมูลเป็น "ยกเลิก"
    $sql = "UPDATE booking SET Status = 'ยกเลิก' WHERE BookingID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "สถานะการจองถูกเปลี่ยนเป็น \"ยกเลิก\" สำเร็จ"]);
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
