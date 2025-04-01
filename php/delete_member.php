<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าส่งข้อมูลผ่าน POST และในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accountId'])) {
        $accountId = $conn->real_escape_string($data['accountId']);

        // ตรวจสอบว่าบัญชีนี้ไม่มีรายการจอง
        $checkSql = "SELECT COUNT(*) AS BookingCount FROM booking WHERE AccountID = ?";
        $stmt = $conn->prepare($checkSql);

        if ($stmt) {
            $stmt->bind_param("i", $accountId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['BookingCount'] == 0) {
                // ลบบัญชีสมาชิก
                $deleteSql = "DELETE FROM account WHERE AccountID = ?";
                $deleteStmt = $conn->prepare($deleteSql);

                if ($deleteStmt) {
                    $deleteStmt->bind_param("i", $accountId);

                    if ($deleteStmt->execute()) {
                        echo json_encode(["success" => true, "message" => "ลบสมาชิกสำเร็จ"]);
                    } else {
                        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการลบสมาชิก"]);
                    }

                    $deleteStmt->close();
                } else {
                    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่งลบสมาชิก"]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "ไม่สามารถลบสมาชิกได้ เนื่องจากมีรายการจอง"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการตรวจสอบรายการจอง"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
