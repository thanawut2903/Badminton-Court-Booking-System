<?php
session_start();
include '../php/admin_navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accountid'])) {
  $accountId = $_GET['accountid'];

  // Query เพื่อดึงข้อมูลการจองแบบทั่วไปของ AccountID นี้
  $sql = "SELECT b.BookingID, b.BookingDate, b.StartTime, b.EndTime, b.CourtID, b.Status, b.CancelReason, a.FirstName,a.LastName
          FROM booking b
          INNER JOIN account a ON b.AccountID = a.AccountID
          WHERE b.AccountID = ? AND b.BookingFormat = 'การขอเปิดก๊วน'";
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่ามีผลลัพธ์หรือไม่
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // ดึงแถวแรก
    $firstnamemember = $row["FirstName"]; // ใช้ชื่อคอลัมน์จริง
    $lastnamemember = $row["LastName"]; // ใช้ชื่อคอลัมน์จริง

    // echo $firstnamemember; // แสดงชื่อสมาชิก
} else {
    echo "ไม่พบข้อมูลสมาชิก";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Detailopengang.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>รายการขอเปิดก๊วน</h1>
      </div>
      <div class="custom-boxname text-center">
      <h3>ชื่อสมาชิก :&nbsp;</h3>
      <h3 id="<?php echo htmlspecialchars(($firstnamemember ?? '') . ' ' . ($lastnamemember ?? '')); ?>">
        <?php echo htmlspecialchars($firstnamemember . ' ' . $lastnamemember); ?>
    </h3>
      </div>
    </div>
    <br>



    <?php
require '../php/dbconnect.php';
// session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accountid'])) {
    $accountId = $_GET['accountid'];

    // Query เพื่อดึงข้อมูลการจองแบบทั่วไปของ AccountID นี้
    $sql = "SELECT b.BookingID, b.BookingDate, b.StartTime, b.EndTime, b.CourtID, b.Status, b.CancelReason, a.FirstName ,b.CourtID,b.NumberHours,c.CourtName
            FROM booking b
            INNER JOIN account a ON b.AccountID = a.AccountID
            INNER JOIN court c ON b.CourtID = c.CourtID
            WHERE b.AccountID = ? AND b.BookingFormat = 'การขอเปิดก๊วน'
            ORDER BY b.BookingID DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="booking-table">';
        echo '<thead>
                <tr>
                    <th>รายการ</th>
                    <th>วันที่จอง</th>
                    <th>เวลาที่จอง</th>
                    <th>ชั่วโมงที่จอง</th>
                    <th>สนามที่</th>
                    <th>สถานะ</th>
                    <th>เหตุผลยกเลิก</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $index = 1;

        while ($row = $result->fetch_assoc()) {
            // แปลงวันที่ BookingDate เป็นรูปแบบภาษาไทย
            $thaiMonths = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
            $daysOfWeek = ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์"];

            $timestamp = strtotime($row['BookingDate']);
            $dayOfWeek = $daysOfWeek[date("w", $timestamp)];
            $day = date("j", $timestamp);
            $month = $thaiMonths[date("n", $timestamp) - 1];
            $year = date("Y", $timestamp) + 543; // แปลงปีเป็นพุทธศักราช

            $formattedDate = "วัน" . $dayOfWeek . "ที่ " . $day . " " . $month . " " . $year;


            // แปลงเวลา StartTime และ EndTime ให้แสดงเฉพาะ HH:MM
            $startTime = date("H:i", strtotime($row['StartTime']));
            $endTime = date("H:i", strtotime($row['EndTime']));

            echo '<tr>';
            echo '<td>' . htmlspecialchars($index) . '</td>';
            echo '<td>' . htmlspecialchars($formattedDate) . '</td>';
            echo '<td>' . htmlspecialchars($startTime) . ' น.'.' - '. htmlspecialchars($endTime) . ' น.'.'</td>';
            echo '<td>' . htmlspecialchars($row['NumberHours']).' ชั่วโมง' . '</td>';
            echo '<td>' . htmlspecialchars($row['CourtName']) . '</td>';

            $statusClass = '';
            $statusStyle = '';

            switch($row['Status']){
              case 'จองสำเร็จ':
              case 'เปิดก๊วนสำเร็จ':
              $statusClass = 'success'; 
              $statusStyle = 'color:green';
              break;
              case 'รอชำระเงิน':
              $statusClass = 'pending';
              $statusStyle = 'color:orange';
              break;
              case 'ยกเลิก':
              $statusClass = 'cancel';
              $statusStyle = 'color:red';
              break;
            }
            echo '<td><span class="status '. $statusClass .'" style="' . $statusStyle . '">' . htmlspecialchars($row['Status']) . '</span></td>';
            echo '<td><span class="status" style="color:red;">' . ($row['CancelReason'] ? htmlspecialchars($row['CancelReason']) : '') . '</span></td>';
            echo '</tr>';
            $index++;
        }

        echo '</tbody></table>';

    } else {
        echo '<p style="color: red;">ไม่พบข้อมูลการจองแบบทั่วไปสำหรับบัญชีนี้</p>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<p style="color: red;">คำขอไม่ถูกต้อง</p>';
}
?>

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
