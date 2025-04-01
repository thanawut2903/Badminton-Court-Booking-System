<?php
if (isset($_GET['month']) && isset($_GET['court'])) {
    $month = $_GET['month'];
    $court = $_GET['court'];

    include '../php/dbconnect.php';

    if (empty($month)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid month parameter.'
        ]);
        exit;
    }

    // เช็คกรณีเลือกทั้งหมด (all)
    if ($court === 'all') {
        $stmt = $conn->prepare("
            SELECT 
                CourtID, 
                (SELECT CourtName FROM court WHERE CourtID = b.CourtID AND CourtID > 0) AS courtName,
                SUM(CASE WHEN BookingFormat = 'การจองแบบทั่วไป' THEN b.NumberHours ELSE 0 END) AS generalBookingHours,
                SUM(CASE WHEN BookingFormat = 'การขอเปิดก๊วน' THEN b.NumberHours ELSE 0 END) AS groupBookingHours
            FROM booking b
            WHERE DATE_FORMAT(BookingDate, '%Y-%m') = ? AND CourtID > 0
            GROUP BY CourtID
        ");
        $stmt->bind_param("s", $month);
    } else {
        // เช็คกรณีเลือกสนามเดียว
        $court = intval($court); // ตรวจสอบว่าเป็นตัวเลข
        if ($court <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid court parameter.'
            ]);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT 
                (SELECT CourtName FROM court WHERE CourtID = ? AND CourtID > 0) AS courtName,
                SUM(CASE WHEN BookingFormat = 'การจองแบบทั่วไป' THEN b.NumberHours ELSE 0 END) AS generalBookingHours,
                SUM(CASE WHEN BookingFormat = 'การขอเปิดก๊วน' THEN b.NumberHours ELSE 0 END) AS groupBookingHours
            FROM booking b
            WHERE DATE_FORMAT(BookingDate, '%Y-%m') = ? AND CourtID = ? AND CourtID > 0
        ");
        $stmt->bind_param("isi", $court, $month, $court);
    }

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to prepare the SQL statement.'
        ]);
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($court === 'all') {
        // กรณีสนามทั้งหมด
        $courts = [];
        while ($row = $result->fetch_assoc()) {
            $courts[] = [
                'name' => $row['courtName'],
                'generalBookingHours' => (int)$row['generalBookingHours'],
                'groupBookingHours' => (int)$row['groupBookingHours'],
            ];
        }
        echo json_encode([
            'success' => true,
            'courts' => $courts
        ]);
    } else {
        // กรณีเลือกสนามเดียว
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'courtName' => $data['courtName'],
                'generalBookingHours' => (int)$data['generalBookingHours'],
                'groupBookingHours' => (int)$data['groupBookingHours']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the selected month and court.'
            ]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Month and court parameters are required.'
    ]);
}
?>
