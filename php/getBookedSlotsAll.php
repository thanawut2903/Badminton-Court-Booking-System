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

// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT CourtID as court, StartTime, EndTime, BookingID, Status FROM booking WHERE BookingDate = ? ORDER BY CourtID DESC, BookingID ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedSlots = [];
while ($row = $result->fetch_assoc()) {
    $bookedSlots[] = [
        'id' => $row['BookingID'],
        'court' => $row['court'],
        'startTime' => $row['StartTime'],
        'endTime' => $row['EndTime'],
        'status' => $row['Status']
    ];
}
$stmt->close();

// ดึง CourtID ที่ว่างจากตาราง court
$sqlAvailableCourts = "
    SELECT * 
    FROM court 
    WHERE CourtID NOT IN (
        SELECT DISTINCT CourtID 
        FROM booking 
        WHERE BookingDate = ? 
          AND StartTime < EndTime 
          AND Status IN ('รอชำระเงิน', 'จองแล้ว', 'รออนุมัติ')
    )
";
$stmtAvailable = $conn->prepare($sqlAvailableCourts);
$stmtAvailable->bind_param("s", $date);
$stmtAvailable->execute();
$resultAvailable = $stmtAvailable->get_result();

$avaliableTime = [];
$availableCourts = [];
while ($row = $resultAvailable->fetch_assoc()) {
    $opDt = intval(DateTime::createFromFormat("H:i:s", $row['OpenTime'])->format('H'));
    $clDt = intval(DateTime::createFromFormat("H:i:s", $row['CloseTime'])->format('H'));
    $clDt = $clDt == 0 ? 24 : $clDt;
    $temp = array_fill(0, 14, 0);
    
    for ($i = 0; $i < 14; $i++) {
        if ($i + 11 >= $opDt && $i + 11 <= $clDt) {
            $temp[$i] = 1;
        }
    }
    array_push($avaliableTime, $temp);
    $availableCourts[] = $row['CourtID'];
}
$stmtAvailable->close();


// ตรวจสอบสถานะ "รอชำระเงิน" และ "รออนุมัติ" แล้วเลือกสนามว่างให้
foreach ($bookedSlots as &$slot) {
    // ตรวจสอบว่าการจองมีสถานะ "รอชำระเงิน" หรือ "รออนุมัติ"
    if ($slot['status'] === 'รอชำระเงิน' || $slot['status'] === 'รออนุมัติ') {
        $currentCourt = $slot['court'];
        
        // แปลงเวลาเริ่มต้นและเวลาสิ้นสุดเป็นชั่วโมง
        $stDt = intval(DateTime::createFromFormat("H:i:s", $slot['startTime'])->format('H')) - 11;
        $enDt = intval(DateTime::createFromFormat("H:i:s", $slot['endTime'])->format('H')) - 12;

        $shouldMove = false;

        // ตรวจสอบว่ามีการจองที่สำเร็จในสนามเดียวกันในเวลาเดียวกันหรือไม่
        foreach ($bookedSlots as $otherSlot) {
            if (
                ($otherSlot['status'] === 'จองสำเร็จ' || $otherSlot['status'] === 'เปิดก๊วนสำเร็จ') &&
                $otherSlot['court'] == $currentCourt &&
                !(intval(DateTime::createFromFormat("H:i:s", $otherSlot['endTime'])->format('H')) <= $stDt ||
                  intval(DateTime::createFromFormat("H:i:s", $otherSlot['startTime'])->format('H')) >= $enDt)
            ) {
                $shouldMove = true;
                break;
            }
        }

        // ถ้าพบว่าต้องย้ายรายการไปสนามว่าง
        if ($shouldMove) {
            $assignedCourt = false;

            // ตรวจสอบสนามที่ว่างทั้งหมด
            for ($i = 0; $i < count($avaliableTime); $i++) {
                $status = true;

                // ตรวจสอบเวลาว่างในสนาม
                for ($j = $stDt; $j < $enDt; $j++) {
                    if ($avaliableTime[$i][$j] == 2) {  // ตรวจสอบว่าไม่มีการจองในช่วงเวลานั้น
                        $status = false;
                        break;
                    }
                }

                // ถ้าสนามนี้ว่างให้ย้ายการจองไปสนามนี้
                if ($status) {
                    // ปรับสถานะสนามเป็นถูกจองในช่วงเวลานั้น
                    for ($j = $stDt; $j < $enDt; $j++) {
                        $avaliableTime[$i][$j] = 2;
                    }

                    // ตรวจสอบการย้ายไปสนามเดียวกันหรือไม่
                    if ($availableCourts[$i] !== $currentCourt) {
                        // ย้ายการจองทั้งหมดไปสนามที่ว่าง
                        foreach ($bookedSlots as &$moveSlot) {
                            if ($moveSlot['court'] == $currentCourt &&
                                $moveSlot['startTime'] == $slot['startTime'] &&
                                $moveSlot['endTime'] == $slot['endTime'] &&
                                ($moveSlot['status'] === 'รอชำระเงิน' || $moveSlot['status'] === 'รออนุมัติ')) {
                                $moveSlot['court'] = $availableCourts[$i]; // ย้ายสนาม
                            }
                        }

                        // ออกจากลูปเพราะย้ายทุกอย่างแล้ว
                        $assignedCourt = true;
                        break;
                    } else {
                        // ถ้าสนามที่เลือกคือสนามเดิม, ไม่ต้องย้าย
                        $assignedCourt = true;
                        break;
                    }
                }
            }

            // ถ้าไม่มีสนามที่ว่างให้ย้ายรายการ
            if (!$assignedCourt) {
                // กรณีที่ไม่มีสนามว่าง, จะไม่ย้ายรายการ
                foreach ($bookedSlots as &$moveSlot) {
                    if ($moveSlot['court'] == $currentCourt &&
                        $moveSlot['startTime'] == $slot['startTime'] &&
                        $moveSlot['endTime'] == $slot['endTime'] &&
                        ($moveSlot['status'] === 'รอชำระเงิน' || $moveSlot['status'] === 'รออนุมัติ')) {
                        $moveSlot['court'] = -1;  // ระบุว่าไม่สามารถย้ายได้
                        $moveSlot['status'] = 'ไม่สามารถย้ายได้';  // ระบุว่าไม่สามารถย้ายได้
                    }
                }
            }
        }
    }
}

// ส่งข้อมูลกลับ
echo json_encode([
    'success' => true,
    'bookedSlots' => $bookedSlots,
    'availableCourts' => $availableCourts
]);

?>
