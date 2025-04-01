<?php
session_start();
include '../php/admin_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง 6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <!-- <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> สำหรับไอคอน -->
  <link href="Historyuse.css" rel="stylesheet">
</head>
<body>

  <?php
  include '../php/admin_menu.php' 
  ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>ประวัติการใช้สนาม</h1>
      </div>
    </div>

    <div class="wrapper">
  <div class="search-container">
    <div class="search-label">ค้นหาประวัติการใช้สนาม</div>
    <input type="text" id="search-input" class="search-input" placeholder="ค้นหาประวัติการใช้สนามจาก ชื่อ-นามสกุลผู้จอง" oninput="searchByName()">
    <div class="search-icon">🔍</div>
  </div>
</div>
  </div>

  <div class="date-picker-container text-center">
    <button id="prevDay" class="btn btn-outline-primary me-2">◀</button>
    <button id="currentDate" class="btn btn-primary"></button>
    <button id="nextDay" class="btn btn-outline-primary ms-2">▶</button>
  </div>

  <div class="dropdown-container">
    <label for="field-select">เลือกสนามที่ต้องการ:</label>
    <select id="field-select" class="field-dropdown">
      <option value="">สนามทั้งหมด</option>
      <option value="1">สนามที่ 1</option>
      <option value="2">สนามที่ 2</option>
      <option value="3">สนามที่ 3</option>
      <option value="4">สนามที่ 4</option>
      <option value="5">สนามที่ 5</option>
    </select>
  </div>
  <br>

  <div id="booking-table-container">
<?php
require '../php/dbconnect.php';

if (!isset($_SESSION['user_idadmin'])) {
    echo '<p style="color:red; text-align:center;">กรุณาเข้าสู่ระบบก่อนทำการแก้ไข</p>';
    exit;
}

$accountID = $_SESSION['user_idadmin'];
$currentDate = isset($_GET['date']) ? date("Y-m-d", intval($_GET['date']) /1000) : date("Y-m-d");
$courtFilter = isset($_GET['court']) && $_GET['court'] !== '' ? intval($_GET['court']) : null;

// print_r($currentDate);


$sql = "SELECT 
            b.BookingID, 
            DATE_FORMAT(b.BookingDate, '%Wที่ %d %M %Y') as BookingDate, 
            TIME_FORMAT(b.StartTime, '%H:%i น.') as StartTime, 
            TIME_FORMAT(b.EndTime, '%H:%i น.') as EndTime, 
            b.NumberHours, 
            b.CourtID,
            b.BookingFormat,
            b.Status, 
            CONCAT(a.FirstName, ' ', a.LastName) as FullName,
            a.Role,
            a.LineID
        FROM booking b
        JOIN account a ON b.AccountID = a.AccountID
        WHERE b.BookingDate = ?";


if ($courtFilter) {
    $sql .= " AND b.CourtID = ?";
}

$sql .= " ORDER BY b.BookingID DESC";

$stmt = $conn->prepare($sql);
if ($courtFilter) {
    $stmt->bind_param('si', $currentDate, $courtFilter);
} else {
    $stmt->bind_param('s', $currentDate);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="booking-table">';
    echo '<thead>
            <tr>
                <th>รายการ</th>
                <th>วันที่จอง</th>
                <th>เวลาเริ่มจอง</th>
                <th>รูปแบบการจอง</th>
                <th>สนามที่</th>
                <th>สถานะ</th>
                <th>จองโดย</th>
                <th>ไอดีไลน์</th>
            </tr>
          </thead>';
    echo '<tbody>';

    $index = 1;
    while ($row = $result->fetch_assoc()) {
      // แปลงวันที่เป็นฟอร์แมตภาษาไทย
      $thaiMonths = [
          'January' => 'มกราคม', 'February' => 'กุมภาพันธ์', 'March' => 'มีนาคม',
          'April' => 'เมษายน', 'May' => 'พฤษภาคม', 'June' => 'มิถุนายน',
          'July' => 'กรกฎาคม', 'August' => 'สิงหาคม', 'September' => 'กันยายน',
          'October' => 'ตุลาคม', 'November' => 'พฤศจิกายน', 'December' => 'ธันวาคม'
      ];
      
      $thaiDays = [
          'Monday' => 'จันทร์', 'Tuesday' => 'อังคาร', 'Wednesday' => 'พุธ',
          'Thursday' => 'พฤหัสบดี', 'Friday' => 'ศุกร์', 'Saturday' => 'เสาร์', 'Sunday' => 'อาทิตย์'
      ];
  
      $bookingDate = $row['BookingDate'];
      $formattedDate = strtr($bookingDate, array_merge($thaiMonths, $thaiDays));
  
      $fullName = $row['FullName'];
      
      echo '<tr class="court-row" data-court="' . $row['CourtID'] . '" data-name="' . strtolower($fullName) . '">';
      echo '<td>' . $index . '</td>';
      echo '<td>' .'วัน'. $formattedDate . '</td>';
      echo '<td>' . $row['StartTime'] .' - '. $row['EndTime'] . '</td>';
      echo '<td>' . $row['BookingFormat'].'</td>'; 
      echo '<td>';
      if ($row['CourtID'] == 0) {
          echo 'ยังไม่ระบุสนาม';
      } else {
          echo 'สนามที่ ' . $row['CourtID'];
      }
      echo '</td>';
            // กำหนดสถานะการจอง
            $statusClass = '';
            $actionButton = '';
            $approveButton = '';
            $statusStyle = '';
            switch ($row['Status']) {
              case 'จองสำเร็จ':
              case 'เปิดก๊วนสำเร็จ':
                  $statusClass = 'success';
                  $statusStyle = 'color:green;';
                  break;
              case 'รอชำระเงิน':
                  $statusClass = 'pending';
                  $statusStyle = 'color:orange;';
                  break;
              case 'ยกเลิก':
              $statusClass = 'cancel';
              $statusStyle = 'color:red;';
              break;
              case 'รออนุมัติ':
                $statusStyle = 'color:#34e1eb;';
                break;
      }
      echo '<td><span class="status ' . $statusClass . '"style="'.$statusStyle.'">' . $row['Status'] . '</span></td>';
      echo '<td>' . $fullName . ' (' . ($row['Role'] === 'M' ? 'สมาชิก' : 'ผู้ดูแล') . ')</td>';
      echo '<td>' . $row['LineID'].'</td>'; 
      echo '</tr>';
      $index++;
  }
  

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p style="color:red; text-align:center; margin-top:50px;margin-left:150px">ไม่มีประวัติการใช้สนาม</p>';
}

$stmt->close();
$conn->close();
?>
  </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const fieldSelect = document.getElementById("field-select");

    // รีเซ็ตค่าเป็น "สนามทั้งหมด" เมื่อโหลดหน้าใหม่
    fieldSelect.value = "";

    fieldSelect.addEventListener("change", function () {
        const selectedCourt = this.value;
        const url = new URL(window.location.href);
        
        if (selectedCourt) {
            url.searchParams.set("court", selectedCourt);
        } else {
            url.searchParams.delete("court"); // ลบค่าออกจาก URL
        }

        window.location.href = url.toString(); // โหลดใหม่พร้อมค่าใหม่
    });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const fieldSelect = document.getElementById("field-select");
    const urlParams = new URLSearchParams(window.location.search);

    // คืนค่าตัวเลือกสนามที่เลือกไว้
    if (urlParams.has("court")) {
        fieldSelect.value = urlParams.get("court");
    }

    // คืนค่าค่าการค้นหาที่ป้อนไว้
    if (urlParams.has("search")) {
        searchInput.value = urlParams.get("search");
    }

    searchInput.addEventListener("input", function () {
        urlParams.set("search", searchInput.value);
        window.history.replaceState({}, "", `${window.location.pathname}?${urlParams}`);
        filterTable();
    });

    fieldSelect.addEventListener("change", function () {
        if (fieldSelect.value) {
            urlParams.set("court", fieldSelect.value);
        } else {
            urlParams.delete("court");
        }
        window.history.replaceState({}, "", `${window.location.pathname}?${urlParams}`);
        filterTable();
    });

    function filterTable() {
    const searchValue = document.getElementById("search-input").value.toLowerCase().trim();
    const selectedCourt = document.getElementById("field-select").value;
    const rows = document.querySelectorAll(".court-row");
    const table = document.querySelector(".booking-table");
    let index = 1;
    let hasVisibleRow = false;

    // ซ่อนตารางทั้งหมดเมื่อไม่มีผลลัพธ์
    table.style.display = "table";  // ให้ตารางแสดงก่อน ถ้ามีผลลัพธ์
    const noResultsMessage = document.getElementById("no-results-message");
    rows.forEach(row => {
        const bookingName = row.getAttribute("data-name");
        const courtID = row.getAttribute("data-court");

        const matchesSearch = bookingName.includes(searchValue) || searchValue === "";
        const matchesCourt = selectedCourt === "" || courtID === selectedCourt;

        if (matchesSearch && matchesCourt) {
            row.style.display = "";
            row.querySelector("td:first-child").textContent = index;
            index++;
            hasVisibleRow = true;
        } else {
            row.style.display = "none";
        }
    });

    // แสดงข้อความ "ไม่มีประวัติการใช้สนามที่ค้นหา" ถ้าไม่มีผลลัพธ์ที่ตรงกับการค้นหา
    if (!hasVisibleRow) {
        table.style.display = "none";  // ซ่อนตาราง
        if (!noResultsMessage) {
            const message = document.createElement("p");
            message.id = "no-results-message";
            message.style.color = "red";
            message.style.textAlign = "center";
            message.style.marginTop = "50px";
            message.textContent = "ไม่มีประวัติการใช้สนามที่ค้นหา";
            document.getElementById("booking-table-container").appendChild(message);
        }
    } else {
        if (noResultsMessage) {
            noResultsMessage.remove(); // ลบข้อความถ้ามีแถวที่แสดง
        }
    }
}
    // เรียกใช้ filterTable() เมื่อโหลดหน้า
    filterTable();
});


function searchByName() {
    const searchValue = document.getElementById("search-input").value.toLowerCase().trim();
    const rows = document.querySelectorAll(".court-row");

    rows.forEach(row => {
        const bookingName = row.getAttribute("data-name");
        row.style.display = bookingName.includes(searchValue) ? "" : "none";
    });
}
</script>

<div class="custom-boxprevious">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
</div>

<script src="/web_badmintaon_khlong_6/Admin/historyuse.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
