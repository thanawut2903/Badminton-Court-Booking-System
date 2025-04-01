<?php
// เชื่อมต่อฐานข้อมูล
require '../php/dbconnect.php';

// รับข้อมูลวันที่จากคำขอ
$requestPayload = file_get_contents("php://input");
$data = json_decode($requestPayload, true);

if (!isset($data['date'])) {
    echo json_encode([
        'success' => false,
        'message' => 'วันที่ไม่ถูกต้อง'
    ]);
    exit;
}

$date = $data['date']; // วันที่ที่ส่งมาจากฝั่งไคลเอนต์

// ดึงข้อมูลการจองจากฐานข้อมูล พร้อม Status
$sql = "SELECT CourtID as court, StartTime, EndTime, Status FROM booking WHERE BookingDate = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedSlots = [];
while ($row = $result->fetch_assoc()) {
    $bookedSlots[] = [
        'court' => $row['court'],
        'startTime' => $row['StartTime'],
        'endTime' => $row['EndTime'],
        'status' => $row['Status']
    ];
}

$stmt->close();
$conn->close();

// ส่งข้อมูลการจองกลับในรูปแบบ JSON
echo json_encode([
    'success' => true,
    'bookedSlots' => $bookedSlots
]);
?>
