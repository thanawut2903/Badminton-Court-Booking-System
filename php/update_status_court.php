<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();
header('Content-Type: application/json');

// ตรวจสอบว่าคำขอเป็นแบบ PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // อ่านข้อมูล JSON ที่ส่งมา
    $input = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าข้อมูลที่จำเป็นครบถ้วนหรือไม่
    if (isset($input['CourtID']) && isset($input['CourtStatus'])) {
        $courtID = $input['CourtID'];
        $courtStatus = $input['CourtStatus'];

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้: ' . $conn->connect_error]);
            exit;
        }

        // อัปเดตสถานะของสนามในฐานข้อมูล
        $stmt = $conn->prepare("UPDATE court SET CourtStatus = ? WHERE CourtID = ?");
        $stmt->bind_param('ii', $courtStatus, $courtID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'อัปเดตสถานะของสนามสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตสถานะ: ' . $stmt->error]);
        }

        // ปิดการเชื่อมต่อ
        $stmt->close();
        $conn->close();
    } else {
        // echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'รองรับเฉพาะคำขอแบบ PUT']);
}
?>
