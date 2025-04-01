<?php
header('Content-Type: application/json');
require_once '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบคำขอว่าเป็น POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

// รับข้อมูลจาก JSON ที่ส่งมา
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['dayOfWeek'], $input['startTime'], $input['endTime'])) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    exit;
}

$dayOfWeek = $input['dayOfWeek'];
$startTime = $input['startTime'];
$endTime = $input['endTime'];
$currentDate = date('Y-m-d'); // วันที่ปัจจุบัน

try {
    // แปลงวันในสัปดาห์เป็นตัวเลข (1 = วันอาทิตย์, ..., 7 = วันเสาร์)
    $daysMap = [
        "จันทร์" => 2,
        "อังคาร" => 3,
        "พุธ" => 4,
        "พฤหัสบดี" => 5,
        "ศุกร์" => 6,
        "เสาร์" => 7,
        "อาทิตย์" => 1
    ];

    if (!isset($daysMap[$dayOfWeek])) {
        echo json_encode(["success" => false, "message" => "วันในสัปดาห์ไม่ถูกต้อง"]);
        exit;
    }

    $dayNumber = $daysMap[$dayOfWeek];

    // ดึงจำนวนสนามทั้งหมด
    $sql = "SELECT COUNT(*) AS totalFields FROM court WHERE CourtID != 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalFields = $row['totalFields'];

    // ค้นหาวันที่สนามมีสนามว่าง
    $sql = "WITH RECURSIVE future_dates AS (
                SELECT ? AS date
                UNION ALL
                SELECT DATE_ADD(date, INTERVAL 1 DAY)
                FROM future_dates
                WHERE DATE_ADD(date, INTERVAL 1 DAY) <= DATE_ADD(?, INTERVAL 1 YEAR)
            )
            SELECT date AS BookingDate
            FROM future_dates
            WHERE DAYOFWEEK(date) = ?
              AND (
                  SELECT COUNT(*) 
                  FROM booking
                  WHERE booking.BookingDate = future_dates.date
                    AND ((booking.StartTime < ? AND booking.EndTime > ?) 
                         OR (booking.StartTime >= ? AND booking.StartTime < ?))
              ) < ?
              AND date >= ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssis", $currentDate, $currentDate, $dayNumber, $endTime, $startTime, $startTime, $endTime, $totalFields, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "redirect" => true,
            "nextAvailableDate" => $row['BookingDate'],
            "message" => "พบวันว่างในวัน {$dayOfWeek} ที่ {$row['BookingDate']}"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ไม่พบวันว่างตามเงื่อนไขที่ระบุ"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}

$conn->close();
?>
