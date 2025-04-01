<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่า GroupMemberID ที่ส่งมาจาก AJAX
    $groupMemberId = isset($_POST['groupMemberId']) ? intval($_POST['groupMemberId']) : 0;

    // ตรวจสอบว่ามีการส่ง GroupMemberID มาหรือไม่
    if (!$groupMemberId) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล GroupMemberID"]);
        exit;
    }

    // อัปเดตสถานะในฐานข้อมูลเป็น "ขอเข้าเล่นสำเร็จ"
    $sql = "UPDATE groupmember SET Status = 'ขอเข้าเล่นสำเร็จ' WHERE GroupMemberID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $groupMemberId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "อนุมัติการขอเข้าเล่นสำเร็จ ?"]);
    } else {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเปลี่ยนสถานะ"]);
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo json_encode(["success" => false, "message" => "วิธีการเรียกไม่ถูกต้อง"]);
    exit;
}
?>
