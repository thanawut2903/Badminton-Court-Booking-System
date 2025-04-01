<?php
require '../php/dbconnect.php';
session_start();

// ตรวจสอบคำค้นหาจาก AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];

    // SQL ค้นหา
    $sql = "SELECT AccountID, Username, FirstName, LastName, Tel, LineID, Email, Status,Role
            FROM account 
             WHERE (Role = 'M' OR Role IS NULL OR Role = '')
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
                    <th>การใช้บริการแบบทั่วไป</th>
                    <th>การขอเข้าเล่นแบบก๊วน</th>
                    <th>การขอเปิดก๊วน</th>
                    <th>สถานะบัญชี</th>
                    <th>จัดการสมาชิก</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $index = 1;

        while ($row = $result->fetch_assoc()) {
            $checked = $row['Status'] == 1 ? 'checked' : '';
            echo '<tr>';
            echo '<td>' . $index . '</td>';
            echo '<td>' . $row['Username'] . '</td>';
            echo '<td>' . $row['FirstName'] . '</td>';
            echo '<td>' . $row['LastName'] . '</td>';
            echo '<td>' . $row['Tel'] . '</td>';
            echo '<td>' . $row['LineID'] . '</td>';
            echo '<td>' . $row['Email'] . '</td>';

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

            // จำนวนการจองแบบทั่วไป
            echo "<td>";
            if ($generalBookingCount == 0) {
                echo "<button class=\"btn history\" style=\"background-color: #d3d3d3;\" disabled>";
                echo "$generalBookingCount ครั้ง</button>";
            } else {
                echo '<button class="btn history" onclick="window.location.href=\'/web_badmintaon_khlong_6/Admin/Detailgeneral.php?accountid=' . $row['AccountID'] . '\'">';
                echo $generalBookingCount . ' ครั้ง</button>';
            }
            echo "</td>";

            // จำนวนการขอเข้าเล่นแบบก๊วน
            echo "<td>";
            if ($squadBookingCount == 0) {
                echo "<button class=\"btn history\" style=\"background-color: #d3d3d3;\" disabled>";
                echo "$squadBookingCount ครั้ง</button>";
            } else {
                echo '<button class="btn history" onclick="window.location.href=\'/web_badmintaon_khlong_6/Admin/Detailgang.php?accountid=' . $row['AccountID'] . '\'">';
                echo $squadBookingCount . ' ครั้ง</button>';
            }
            echo "</td>";

            // จำนวนการขอเปิดก๊วน
            echo "<td>";
            if ($createSquadBookingCount == 0) {
                echo "<button class=\"btn history\" style=\"background-color: #d3d3d3;\" disabled>";
                echo "$createSquadBookingCount ครั้ง</button>";
            } else {
                echo '<button class="btn history" onclick="window.location.href=\'/web_badmintaon_khlong_6/Admin/Detailopengang.php?accountid=' . $row['AccountID'] . '\'">';
                echo $createSquadBookingCount . ' ครั้ง</button>';
            }
            echo "</td>";

            // สถานะบัญชีจัดการ
           // ตรวจสอบ Role และกำหนดข้อความแสดงผล
$roleText = (!isset($row['Role']) || trim($row['Role']) === '') ? 'รอการยืนยันอีเมล' : $row['Role'];

echo '<td>
      <label class="switch">
          <input type="checkbox" class="status-switch" onChange="activeFunction(this,' . $row["AccountID"] . ')"  data-account-id="' . $row['AccountID'] . '" ' . $checked . '>
          <span class="slider"></span>
      </label>
      </td>';

$totalBookingCount = $generalBookingCount + $squadBookingCount + $createSquadBookingCount;

// ตรวจสอบเงื่อนไข Role และจำนวนการจอง
echo '<td>';
if ($roleText === 'รอการยืนยันอีเมล') {
    echo '<span style="color: orange; font-weight: bold;">รอการยืนยันอีเมล</span>';
} elseif ($totalBookingCount == 0) {
    echo '<button class="btn btn-danger delete-member" data-account-id="' . htmlspecialchars($row['AccountID']) . '">ลบสมาชิก</button>';
} 
// else {
//     // echo '<button class="btn btn-secondary" disabled>ไม่สามารถลบได้</button>';
// }
echo '</td>';


            echo '</tr>';
            $index++;
        }
        echo '</tbody></table>';
    } else {
        echo '<br><br><p style=color:red;margin-left:170px;>ไม่พบสมาชิกที่ตรงกับคำค้นหา</p>';
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo '<p>คำขอไม่ถูกต้อง</p>';
}
?>
