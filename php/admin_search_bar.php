<?php
require '../php/dbconnect.php';
session_start();

// ตรวจสอบคำค้นหาจาก AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);

    $sql = "SELECT AccountID, Username, FirstName, LastName, Tel, LineID, Email, Status
            FROM account 
            WHERE Role = 'A' 
              AND (Username LIKE ? OR 
                   FirstName LIKE ? OR 
                   LastName LIKE ? OR 
                   Tel LIKE ? OR 
                   LineID LIKE ?)
            ORDER BY AccountID ASC";

    $searchTerm = "%" . $query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="booking-table"style="margin-left:270px">';
        echo '<thead>
                <tr>
                    <th>รายการ</th>
                    <th>ชื่อผู้ใช้งาน</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>ไอดีไลน์</th>
                    <th>อีเมล</th>
                    <th>สถานะบัญชี</th>
                    <th>จัดการผู้ดูแลระบบ</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $index = 1; // ตัวเลขเริ่มต้นรายการ

        while ($row = $result->fetch_assoc()) {
            $checked = $row['Status'] == 1 ? 'checked' : '';
            echo '<tr>';
            echo '<td>' . $index . '</td>';
            echo '<td>' . htmlspecialchars($row['Username']) . '</td>';
            echo '<td>' . htmlspecialchars($row['FirstName']) . '</td>';
            echo '<td>' . htmlspecialchars($row['LastName']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Tel']) . '</td>';
            echo '<td>' . htmlspecialchars($row['LineID']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Email']) . '</td>';
                        // ตรวจสอบรายการจองแบบทั่วไปและการขอเปิดก๊วนจาก booking
                        $bookingCheckSql = "SELECT BookingFormat, COUNT(*) AS BookingCount 
                        FROM booking 
                        WHERE AccountID = ?
                        GROUP BY BookingFormat";
    $bookingStmt = $conn->prepare($bookingCheckSql);
    $bookingStmt->bind_param("i", $row['AccountID']);
    $bookingStmt->execute();
    $bookingResult = $bookingStmt->get_result();

    $bookingData = [];
    while ($bookingRow = $bookingResult->fetch_assoc()) {
        $bookingData[$bookingRow['BookingFormat']] = $bookingRow['BookingCount'];
    }

    $generalBookingCount = $bookingData['การจองแบบทั่วไป'] ?? 0;
    $createSquadBookingCount = $bookingData['การขอเปิดก๊วน'] ?? 0;

    // ตรวจสอบจำนวนการขอเข้าเล่นแบบก๊วนตามรายการ (รวมสถานะยกเลิกด้วย)
    $groupMemberSql = "SELECT COUNT(*) AS MemberCount 
                       FROM groupmember 
                       WHERE AccountID = ?";
    $groupMemberStmt = $conn->prepare($groupMemberSql);
    $groupMemberStmt->bind_param("i", $row['AccountID']);
    $groupMemberStmt->execute();
    $groupMemberResult = $groupMemberStmt->get_result();
    $groupMemberRow = $groupMemberResult->fetch_assoc();

    $squadBookingCount = $groupMemberRow['MemberCount'] ?? 0;

    $totalBookingCount = $generalBookingCount + $squadBookingCount + $createSquadBookingCount;

            echo '<td>
                    <label class="switch">
                        <input type="checkbox" class="status-switch" onChange="activeFunction(this,' . htmlspecialchars($row["AccountID"]) . ')"  data-account-id="' . htmlspecialchars($row['AccountID']) . '" ' . $checked . '>
                        <span class="slider"></span>
                    </label>
                  </td>';
                  if ($totalBookingCount == 0) {
                    echo '<td><button class="btn btn-danger delete-member" data-account-id="' . htmlspecialchars($row['AccountID']) . '">ลบผู้ดูแลระบบ</button></td>';
                } 
                // else {
                //     echo '<td><button class="btn btn-secondary" disabled>ไม่สามารถลบได้</button></td>';
                // }
            echo '</tr>';

            $index++;
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<br><br>';
        echo '<p style="color:red;margin-left:150px">ไม่พบผู้ดูแลระบบที่ตรงกับคำค้นหา</p>';
    }

    

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo '<p>คำขอไม่ถูกต้อง</p>';
}
?>


