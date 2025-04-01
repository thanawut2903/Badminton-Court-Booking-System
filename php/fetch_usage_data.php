<?php
include '../php/dbconnect.php';

$courtID = isset($_GET['courtID']) && $_GET['courtID'] !== 'all' ? $_GET['courtID'] : null;
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

$response = [
    'labels' => ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
    'generalBookings' => array_fill(0, 13, 0), // Initializing counts for each hour
    'groupBookings' => array_fill(0, 13, 0),
    'generalBookingHours' => array_fill(0, 13, 0), // เก็บชั่วโมงที่มีการจองแบบทั่วไป
    'groupBookingHours' => array_fill(0, 13, 0), // เก็บชั่วโมงที่มีการขอเปิดก๊วน
];

// ตรวจสอบว่าเริ่มต้นและสิ้นสุดวันที่มีการกำหนดหรือไม่
if ($startDate && $endDate) {
    // กำหนด SQL query ที่จะดึงข้อมูลการจอง
    $query = "SELECT HOUR(StartTime) AS BookingHour, BookingFormat, COUNT(*) AS BookingCount
              FROM booking
              WHERE BookingDate BETWEEN ? AND ?
              AND Status != 'ยกเลิก'";  // เพิ่มเงื่อนไขกรองการจองที่มีสถานะไม่เป็น 'ยกเลิก'

    // ตรวจสอบว่าได้เลือก CourtID หรือไม่
    if ($courtID) {
        $query .= " AND CourtID = ?";
    }

    // จัดกลุ่มข้อมูลตามชั่วโมงและประเภทการจอง
    $query .= " GROUP BY HOUR(StartTime), BookingFormat";

    $stmt = $conn->prepare($query);

    if ($courtID) {
        $stmt->bind_param('ssi', $startDate, $endDate, $courtID);
    } else {
        $stmt->bind_param('ss', $startDate, $endDate);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $hourIndex = $row['BookingHour'] - 11; // แปลงเวลา 11:00 - 23:00 เป็นดัชนี 0 - 12
        

        if ($hourIndex >= 0 && $hourIndex <= 12) {
            if ($row['BookingFormat'] === 'การจองแบบทั่วไป') {
                // ถ้ามีการจองแบบทั่วไปในชั่วโมงนั้น ให้เพิ่มค่าตามจำนวนการจอง
                $response['generalBookings'][$hourIndex] = $row['BookingCount'];
                // เก็บว่าในชั่วโมงนี้มีการจองแบบทั่วไป
                $response['generalBookingHours'][$hourIndex] = 1; 
            } elseif ($row['BookingFormat'] === 'การขอเปิดก๊วน') {
                // ถ้ามีการจองแบบขอเปิดก๊วนในชั่วโมงนั้น ให้เพิ่มค่าตามจำนวนการจอง
                $response['groupBookings'][$hourIndex] = $row['BookingCount'];
                // เก็บว่าในชั่วโมงนี้มีการจองแบบขอเปิดก๊วน
                $response['groupBookingHours'][$hourIndex] = 1;
            }
        }
    }

    $stmt->close();
}

// นับจำนวนชั่วโมงที่มีการจองในแต่ละประเภท
$response['generalBookingHoursCount'] = array_sum($response['generalBookingHours']); // นับชั่วโมงที่มีการจองแบบทั่วไป
$response['groupBookingHoursCount'] = array_sum($response['groupBookingHours']); // นับชั่วโมงที่มีการขอเปิดก๊วน

header('Content-Type: application/json');
echo json_encode($response); // ส่งข้อมูลในรูปแบบ JSON
?>
