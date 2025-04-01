<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();

if (
    ($_SERVER['REQUEST_METHOD'] === 'PUT' || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT')) 
    && isset($_SERVER['CONTENT_TYPE']) 
    && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accountId'], $data['status'])) {
        $accountId = intval($data['accountId']);
        $newStatus = intval($data['status']);

        // ตรวจสอบว่าสถานะเป็น 0 หรือ 1
        if (!in_array($newStatus, [0, 1], true)) {
            echo json_encode(["success" => false, "message" => "สถานะไม่ถูกต้อง"]);
            exit;
        }

        // เตรียมและดำเนินการอัปเดตฐานข้อมูล
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
