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
                // ทำการอัปเดตสถานะของการจองเป็น 'ยกเลิก'
                $updateSql = "UPDATE booking SET Status = 'ยกเลิก' WHERE BookingID = ?";
                $updateStmt = $conn->prepare($updateSql);

                if ($updateStmt) {
                    $updateStmt->bind_param("i", $bookingId);
                    if ($updateStmt->execute()) {
                        echo json_encode(["success" => true, "message" => "ยกเลิกการจองเรียบร้อย"]);
                    } else {
                        echo json_encode(["success" => false, "message" => "ไม่สามารถยกเลิกการจองได้"]);
                    }

                    $updateStmt->close();
                } else {
                    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการอัปเดตสถานะ"]);
                }
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
