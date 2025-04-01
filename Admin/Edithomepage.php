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
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Edithomepage.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>แก้ไขเนื้อหาหน้าหลัก</h1>
      </div>
    </div>

    <div class="custom-box2">
    <form id="updateInfoForm" action="../php/updateinfo.php" method="POST">
    <?php
    require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

    // ดึงข้อมูลจากฐานข้อมูล
    $query = "SELECT InfoID, ItemDetail FROM info WHERE InfoID IN (9, 10, 11)";
    $result = $conn->query($query);

    // กำหนดค่าข้อมูลเริ่มต้น
    $data = [
        9 => '',
        10 => '',
        11 => ''
    ];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[$row['InfoID']] = htmlspecialchars($row['ItemDetail']);
        }
    }

    $conn->close();
    ?>

    <!-- ปุ่มข้อความการจองสนาม -->
    <div class="content-container" style="margin-left: 20px;">
        <div class="custom-box1 text-left">
            <h3>การจองสนาม</h3>
        </div>
        <div class="custom-box11 text-left">
            <textarea name="item_detail_9" rows="5" class="form-control"><?php echo $data[9]; ?></textarea>
        </div>

        <!-- ปุ่มข้อความการชำระเงิน -->
        <div class="custom-box1 text-left">
            <h3>การชำระเงิน</h3>
        </div>
        <div class="custom-box11 text-left">
            <textarea name="item_detail_10" rows="5" class="form-control"><?php echo $data[10]; ?></textarea>
        </div>

        <!-- ปุ่มข้อความเกี่ยวกับสนามแบดมินตัน -->
        <div class="custom-box1 text-left">
            <h3>เกี่ยวกับสนามแบดมินตัน</h3>
        </div>
        <div class="custom-box11 text-left">
            <textarea name="item_detail_11" rows="10" class="form-control"><?php echo $data[11]; ?></textarea>
        </div>
    </div>
    <div class="custom-save">
        <button type="button" class="btn-save" onclick="confirmSave()">บันทึกข้อมูล</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSave() {
        Swal.fire({
            title: "ยืนยันการบันทึก?",
            text: "โปรดยืนยันว่าคุณต้องการบันทึกข้อมูลนี้",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "ใช่, บันทึกเลย!",
            cancelButtonText: "ยกเลิก",
            reverseButtons: true // สลับตำแหน่งของปุ่ม
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("updateInfoForm").submit();
            }
        });
    }
</script>

</div>
</div>

  <script src="/web_badmintaon_khlong_6/Admin/homepage.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
