<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าส่งข้อมูลผ่าน POST และในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accountId'], $data['status'])) {
        $accountId = $conn->real_escape_string($data['accountId']);
        $newStatus = $conn->real_escape_string($data['status']);

        // ตรวจสอบสถานะว่าเป็น 0 หรือ 1
        if (!in_array($newStatus, [0, 1])) {
            echo json_encode(["success" => false, "message" => "สถานะไม่ถูกต้อง"]);
            exit;
        }

        // อัปเดตสถานะในฐานข้อมูล
        $sql = "UPDATE account SET Status = ? WHERE AccountID = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $newStatus, $accountId);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "อัปเดตสถานะบัญชีสำเร็จ"]);
            } else {
                echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการอัปเดตสถานะ"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
