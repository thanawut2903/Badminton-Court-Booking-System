<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // รับค่า BookingID ที่ส่งมาจาก AJAX
    $bookingId = isset($_GET['bookingId']) ? intval($_GET['bookingId']) : 0;

    // ตรวจสอบว่ามีการส่ง BookingID มาหรือไม่
    if (!$bookingId) {
        echo "<p>ไม่พบข้อมูลการจอง</p>";
        exit;
    }

    // ดึงข้อมูลการจองจากฐานข้อมูล
    $sql = "SELECT 
                b.BookingID, 
                DATE_FORMAT(b.BookingDate, '%W ที่ %d %M %Y') as BookingDate, 
                TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
                TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
                b.NumberHours, 
                b.CourtID, 
                b.Price
            FROM booking b
            WHERE b.BookingID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead>
                <tr>
                    <th>รายการ</th>
                    <th>วันที่จอง</th>
                    <th>เวลาที่จองเริ่มต้น</th>
                    <th>เวลาที่จองสิ้นสุด</th>
                    <th>ชั่วโมงที่จอง</th>
                    <th>สนามที่</th>
                    <th>ราคา</th>
                </tr>
            </thead>';
        echo '<tbody>';

        $index = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $index . '</td>';
            echo '<td>' . $row['BookingDate'] . '</td>';
            echo '<td>' . $row['StartTime'] . '</td>';
            echo '<td>' . $row['EndTime'] . '</td>';
            echo '<td>' . $row['NumberHours'] . ' ชั่วโมง</td>';
            echo '<td>สนามที่ ' . $row['CourtID'] . '</td>';
            echo '<td>' . $row['Price'] . ' บาท</td>';
            echo '</tr>';

            $index++;
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo "<p>ไม่มีข้อมูลการจอง</p>";
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo "<p>คำขอไม่ถูกต้อง</p>";
    exit;
}
?>
