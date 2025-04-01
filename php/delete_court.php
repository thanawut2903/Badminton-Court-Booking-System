<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();
header('Content-Type: application/json');

// ตรวจสอบว่าคำขอเป็นแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // อ่านข้อมูล JSON ที่ส่งมา
    $input = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าข้อมูลที่จำเป็นครบถ้วนหรือไม่
    if (isset($input['CourtID'])) {
        $courtID = $input['CourtID'];



        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้: ' . $conn->connect_error]);
            exit;
        }

        // ลบสนามจากฐานข้อมูล
        $stmt = $conn->prepare("DELETE FROM court WHERE CourtID = ?");
        $stmt->bind_param('i', $courtID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'ลบสนามสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบสนาม: ' . $stmt->error]);
        }

        // ปิดการเชื่อมต่อ
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'รองรับเฉพาะคำขอแบบ POST']);
}
?>