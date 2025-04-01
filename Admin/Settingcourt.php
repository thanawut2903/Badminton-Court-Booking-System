<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $set_day = $_POST['set_day'] ?? '';
    $court_price = $_POST['court_price'] ?? '';
    $max_players = $_POST['max_players'] ?? '';
    $max_playergang = $_POST['max_playergang'] ?? '';
    $price_group = $_POST['price_group'] ?? '';
    

    // อัปเดตข้อมูลในฐานข้อมูล
    $updates = [
        ["id" => 12, "value" => $set_day],
        ["id" => 13, "value" => $court_price],
        ["id" => 14, "value" => $max_players],
        ["id" => 15, "value" => $max_playergang],
        ["id" => 16, "value" => $price_group],
    ];

    foreach ($updates as $update) {
        $stmt = $conn->prepare("UPDATE info SET ItemDetail = ? WHERE InfoID = ?");
        $stmt->bind_param("si", $update['value'], $update['id']);
        $stmt->execute();
        $stmt->close();
    }

    echo "<p class='text-center'>บันทึกข้อมูลเรียบร้อยแล้ว</p>";
}

// ดึงข้อมูลจากฐานข้อมูลตาม InfoID 12-16
$query = "SELECT InfoID, ItemDetail FROM info WHERE InfoID BETWEEN 12 AND 16 ORDER BY InfoID ASC";
$result = $conn->query($query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[$row['InfoID']] = $row['ItemDetail'];
    }
}
$conn->close();
?>

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Settingcourt.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php' ?>
 
  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>การตั้งค่าต่างๆของสนาม</h1>
      </div>
    </div>



    <div class="form-container">
        <form action="../php/settingscourt.php" method="POST">
            <div class="form-group mb-3">
    <label for="set-day">ตั้งค่าการจองสนามล่วงหน้า:</label>
    <div class="input-group">
        <input type="text" id="set-day" name="set_day" class="form-control"
            placeholder="จำนวนวันที่จองล่วงหน้า" value="<?php echo htmlspecialchars($data[12] ?? ''); ?>" required>
        <span class="input-group-text">วัน</span>
    </div>
</div>
            <div class="form-group mb-3">
                <label for="court-price">ตั้งค่าราคาค่าบริการต่อชั่วโมง:</label>
                <div class="input-group">
                <input type="text" id="court-price" name="court_price" class="form-control"
                 placeholder="ราคาค่าบริการ" value="<?php echo htmlspecialchars($data[13] ?? ''); ?>" required>
                 <span class="input-group-text">บาท</span>
            </div>

            <div class="form-group mb-3">
                <label for="price-group">ตั้งค่าราคาค่าบริการของก๊วน:</label>
                <div class="input-group">
                <input type="text" id="price-group" name="price_group" class="form-control" 
                placeholder="ราคาค่าบริการของก๊วน" value="<?php echo htmlspecialchars($data[16] ?? ''); ?>" required>
                <span class="input-group-text">บาท</span> 
            </div>

            <!-- <div class="form-group mb-3">
                <label for="max-players">จำนวนผู้เล่นสูงสุด:</label>
                <div class="input-group">
                <input type="text" id="max-players" name="max_players" class="form-control" 
                placeholder="จำนวนผู้เล่นสูงสุด" value="<?php echo htmlspecialchars($data[14] ?? ''); ?>" required>
                <span class="input-group-text">คน</span> 
            </div> -->

            <div class="form-group mb-3">
                <label for="max-playergang">จำนวนผู้เล่นสูงสุดของการเล่นแบบก๊วน:</label>
                <div class="input-group">
                <input type="text" id="max-playergang" name="max_playergang" class="form-control" 
                placeholder="จำนวนผู้เล่นสูงสุดของการเล่นแบบก๊วน" value="<?php echo htmlspecialchars($data[15] ?? ''); ?>" required>
                <span class="input-group-text">คน</span> 
            </div>
            </div>

            <div class="custom-boxprevious">
    <!-- ปุ่มย้อนกลับ -->
    <button type="button" class="btn-previous" onclick="confirmBack()">ย้อนกลับ</button>

    <div class="custom-save">
        <!-- ปุ่มบันทึก -->
        <button type="button" class="btn-save" onclick="confirmSave()">บันทึก</button>
    </div>
</div>
</form>
</div>


<script>
    function confirmBack() {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการย้อนกลับไปยังหน้าก่อนหน้านี้?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ย้อนกลับ!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                history.back();
            }
        });
    }

    function confirmSave() {
        Swal.fire({
            title: "ยืนยันการบันทึก?",
            text: "โปรดยืนยันว่าคุณต้องการบันทึกข้อมูลนี้",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "ใช่, บันทึกเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector("form").submit();
            }
        });
    }
</script>




    

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
