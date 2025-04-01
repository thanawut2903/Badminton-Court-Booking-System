<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่า GroupMemberID และตรวจสอบว่ามีการส่งมาหรือไม่
    $groupMemberId = isset($_POST['groupMemberId']) ? intval($_POST['groupMemberId']) : 0;

    if ($groupMemberId <= 0) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล GroupMemberID"]);
        exit;
    }

    // อัปเดตสถานะในฐานข้อมูลเป็น "ไม่อนุมัติ"
    $sql = "UPDATE groupmember SET Status = 'ไม่อนุมัติ' WHERE GroupMemberID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $groupMemberId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "ไม่อนุมัติการขอเข้าเล่นสำเร็จ"]);
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
