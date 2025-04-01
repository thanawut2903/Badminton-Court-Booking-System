<?php
//header('Access-Control-Allow-Origin: *');
session_start();
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง", "test" => $_SESSION]);
    exit;
}
$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session


$sql = "SELECT ItemDetail FROM info WHERE InfoID = 16";

// ตรวจสอบว่าข้อมูลถูกส่งมาในรูปแบบ JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // รับข้อมูล JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าข้อมูลที่ต้องการครบถ้วนหรือไม่
    if (isset($data['date'], $data['time'],$data['slots'], $data['hours'], $data['price'])) {
        $bookingDate = $conn->real_escape_string($data['date']);
        $startTime = $conn->real_escape_string(explode(' - ', $slots[0]['time'])[0]);
        $endTime = $conn->real_escape_string(explode(' - ', $slots[array_key_last($slots)]['time'])[1]);
        $numberHours = $conn->real_escape_string($data['hours']);
        $price = $conn->real_escape_string($data['price']);
        $courtID = $conn->real_escape_string($data['courtID'] ?? null);
        $slots = $data['slots'];

        // สำหรับค่าที่ไม่ส่งมาให้ใส่ค่าเริ่มต้น
        $requestDT = date('Y-m-d');
        $bookingFormat = "การขอเปิดก๊วน";
        $maxPlayer = $row['ItemDetail'];
        $groupMessage = "nomessage";
        $status = "รอชำระเงิน";
        $approvedBy = NULL;
        $approvedDT = NULL;
        $cancelBy = NULL;
        $cancelDT = NULL;   
        $cancelReason = NULL;

        // สร้างคำสั่ง SQL เพื่อบันทึกข้อมูล
        $sql = "INSERT INTO booking (AccountID, RequestDT, BookingDate, StartTime, EndTime, NumberHours, Price, CourtID, BookingFormat, MaxPlayer, GroupMessage, Status, ApprovedBy, ApprovedDT, CancelBy, CancelDT, CancelReason) 
                VALUES ('$accountID', '$requestDT', '$bookingDate', '$startTime', '$endTime', '$numberHours', '$price', '$courtID', '$bookingFormat', '$maxPlayer', '$groupMessage', '$status', NULL, NULL, NULL, NULL, NULL)";

        // ตรวจสอบการเชื่อมต่อและคำสั่ง SQL
        if (!$conn) {
            echo json_encode(["success" => false, "message" => "Database connection failed: " . mysqli_connect_error()]);
            exit;
        }

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "การจองสำเร็จ"]);
        } else {
            echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการรัน SQL: " . $conn->error, "sql" => $sql]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน: โปรดระบุ CourtID"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
