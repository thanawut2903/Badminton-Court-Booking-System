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
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Edit contact us.css" rel="stylesheet">
</head>
<body>
<?php include '../php/admin_menu.php' ?>
  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>แก้ไขข้อมูลหน้าติดต่อสนาม</h1>
      </div>
    </div>

    <div class="custom-box2">
    <?php
    require '../php/dbconnect.php';

    // ดึงข้อมูลจากฐานข้อมูล
    $query = "SELECT InfoID, ItemName, ItemDetail FROM info WHERE InfoID BETWEEN 1 AND 8 ORDER BY InfoID ASC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<form id="contactForm" action="../php/editcontact.php" method="POST" enctype="multipart/form-data">';

        while ($row = $result->fetch_assoc()) {
            echo '<div class="mb-3">';
            echo '<label for="ItemDetail_' . $row['InfoID'] . '" class="form-label">' . htmlspecialchars($row['ItemName']) . '</label>';

            if ($row['InfoID'] == 6 || $row['InfoID'] == 7 || $row['InfoID'] == 8) { // แก้ไขรูปภาพไอคอน
                if (file_exists($row['ItemDetail'])) {
                    echo '<img src="' . htmlspecialchars($row['ItemDetail']) . '" alt="Icon Preview" style="max-height: 100px; display: block; margin-bottom: 10px;">';
                }
                echo '<input type="file" name="ItemDetail_' . $row['InfoID'] . '" class="form-control" accept="image/*">';
            } elseif ($row['InfoID'] == 2) { // กรณี Google Maps ใช้ textarea
                echo '<textarea name="ItemDetail_' . $row['InfoID'] . '" class="form-control" rows="3">' . htmlspecialchars($row['ItemDetail']) . '</textarea>';
            } else {
                echo '<input type="text" name="ItemDetail_' . $row['InfoID'] . '" class="form-control" value="' . htmlspecialchars($row['ItemDetail']) . '">';
            }

            echo '</div>';
        }
        echo '<div class="text-center mt-4">';
        echo '<button type="button" class="btn-previous" onclick="confirmBack()">ย้อนกลับ</button>';
        echo '<button type="button" class="btn-save" onclick="confirmSave()">บันทึกข้อมูล</button>';
        echo '</div>';
        echo '</form>';
    } else {
        echo '<p class="text-center">ไม่พบข้อมูล</p>';
    }

    $conn->close();
    ?>

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
                document.getElementById("contactForm").submit();
            }
        });
    }
  </script>

</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
