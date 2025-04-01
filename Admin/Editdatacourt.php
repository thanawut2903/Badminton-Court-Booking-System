<?php
session_start();
include '../php/admin_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editdatacourt.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>แก้ไขข้อมูลสนาม</h1>
      </div>
    </div>

    <div class="form-container">
    <form method="POST" action="../php/update_court.php">
<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่งค่า courtID มาหรือไม่
if (!isset($_GET['courtID'])) {
    die('Error: ไม่พบตัวแปร courtID ใน URL');
}

$courtID = intval($_GET['courtID']);

// ดึงข้อมูลสนามทั้งหมดจากฐานข้อมูล
$query = "SELECT CourtID, CourtName, CourtStatus, OpenTime, CloseTime FROM court";
$result = $conn->query($query);

if (!$result) {
    die('Error: ไม่สามารถดึงข้อมูลสนามทั้งหมดได้: ' . $conn->error);
}

// ดึงข้อมูลสนามที่ต้องการแก้ไขจากฐานข้อมูล
$stmt = $conn->prepare("SELECT CourtName, OpenTime, CloseTime FROM court WHERE CourtID = ?");
if (!$stmt) {
    die('Error: การเตรียมคำสั่ง SQL ผิดพลาด: ' . $conn->error);
}

$stmt->bind_param('i', $courtID);
$stmt->execute();
$stmt->bind_result($courtName, $openTime, $closeTime);

if (!$stmt->fetch()) {
    die('Error: ไม่พบข้อมูลสนามสำหรับ CourtID ที่ระบุ');
}

$stmt->close();
$conn->close();
?>


  
    <div class="form-group">
      <input hidden value="<?php echo $_GET['courtID'];?>" name="court_id"/>
    <label for="court-name">ชื่อสนาม:</label>
    <input type="text" id="court-name" name="court_name" value="<?php echo htmlspecialchars($courtName); ?>" required>
    <br>
    </div>

    <div class="time-group">
    <div>
        <label for="open-time">เวลาเปิดของสนาม</label>
        <select id="open-time" name="open_time" required>
            <option value="" disabled <?= empty($openTime) ? 'selected' : '' ?>>-- เวลาเปิดของสนาม --</option>
            <?php
            $times = ["11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00"];
            foreach ($times as $time) {
                echo "<option value=\"$time\"" . ($time === $openTime ? " selected" : "") . ">$time</option>";
            }
            ?>
        </select>
    </div>
    <div id="test">
        <label for="close-time">เวลาปิดของสนาม</label>
        <select id="close-time" name="close_time" required>
            <option value="" disabled <?= empty($closeTime) ? 'selected' : '' ?>>-- เวลาปิดของสนาม --</option>
            <?php
            $times = ["12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00", "24:00"];
            foreach ($times as $time) {
                echo "<option value=\"$time\"" . ($time === $closeTime ? " selected" : "") . ">$time</option>";
            }
            ?>
        </select>
    </div>
</div>
<br>
<div class="custom-boxprevious">
    <button class="btn-previous" type="button" onclick="history.back()">ย้อนกลับ</button>
    <div class="custom-save">
        <button type="submit" class="btn-add">บันทึก</button>
    </div>
</div>
</form>
</div>

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
