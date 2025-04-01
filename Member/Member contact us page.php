<?php
session_start();
include '../php/member_navbar.php';
?>
<!DOCTYPE html>
<lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ติดต่อสนามแบดมินตัน</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <!-- ลิงก์ไปยังไฟล์ CSS -->
    <link href="Member contact us page.css" rel="stylesheet">
</head>
<body>

<?php
include '../php/member_menu.php';
?>

  <!-- ฟอร์ม ติดต่อเรา -->
   
  <div class="container1 d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow" style="width: 30rem;">
      <h3 class="text-center mb-4">ติดต่อสนามแบดมินตัน</h3>
      
    <form>
    <div class="mt-3 text-end">
        <!-- ปุ่มข้อความการจองสนาม -->
        <div class="custom-box1 text-right">
            <h3>ที่อยู่ของสนาม</h3>
        </div>

        <div class="custom-box11 text-center">
            <?php
            require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

            // ดึงข้อมูลจากฐานข้อมูล
            $query = "SELECT InfoID, ItemName, ItemDetail FROM info ORDER BY InfoID ASC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['InfoID'] == 1) { // ที่อยู่ของสนาม
                        echo '<p>' . htmlspecialchars($row['ItemDetail']) . '</p>';
                    }
                }
            }
            ?>
        </div>

        <div class="custom-boxfb">
            <?php
            $result->data_seek(0); // รีเซ็ตตัวชี้ผลลัพธ์
            while ($row = $result->fetch_assoc()) {
                if ($row['InfoID'] == 3) { // URL Facebook
                    $iconFacebook = "";
                    $result->data_seek(0);
                    while ($iconRow = $result->fetch_assoc()) {
                        if ($iconRow['InfoID'] == 6) {
                            $iconFacebook = htmlspecialchars($iconRow['ItemDetail']);
                            break;
                        }
                    }
                    echo '<a href="' . htmlspecialchars($row['ItemDetail']) . '" target="_blank">';
                    echo '<img src="' . $iconFacebook . '" alt="facebook" class="custom-icon1">';
                    echo '</a>';
                    echo '<div class="custom-textfb ms-0.5">';
                    echo '<p>สนามแบดมินตัน คลองหก</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <div class="custom-boxline">
            <?php
            $result->data_seek(0); // รีเซ็ตตัวชี้ผลลัพธ์
            while ($row = $result->fetch_assoc()) {
                if ($row['InfoID'] == 4) { // LINE ID
                    $iconLine = "";
                    $result->data_seek(0);
                    while ($iconRow = $result->fetch_assoc()) {
                        if ($iconRow['InfoID'] == 7) {
                            $iconLine = htmlspecialchars($iconRow['ItemDetail']);
                            break;
                        }
                    }
                    echo '<a href="https://line.me/ti/p/~' . htmlspecialchars($row['ItemDetail']) . '" target="_blank">';
                    echo '<img src="' . $iconLine . '" alt="line" class="custom-icon2">';
                    echo '</a>';
                    echo '<div class="custom-textline ms-3">';
                    echo '<p>' . htmlspecialchars($row['ItemDetail']) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <div class="custom-boxtel">
            <?php
            $result->data_seek(0); // รีเซ็ตตัวชี้ผลลัพธ์
            while ($row = $result->fetch_assoc()) {
                if ($row['InfoID'] == 5) { // เบอร์โทรศัพท์
                    $iconPhone = "";
                    $result->data_seek(0);
                    while ($iconRow = $result->fetch_assoc()) {
                        if ($iconRow['InfoID'] == 8) {
                            $iconPhone = htmlspecialchars($iconRow['ItemDetail']);
                            break;
                        }
                    }
                    echo '<a href="tel:' . htmlspecialchars($row['ItemDetail']) . '" target="_blank">';
                    echo '<img src="' . $iconPhone . '" alt="phone" class="custom-icon3">';
                    echo '</a>';
                    echo '<div class="custom-texttel ms-3">';
                    echo '<p>' . htmlspecialchars($row['ItemDetail']) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</form>
    </div>
<!-- รูปภาพด้านขวา -->
<div class="map-container">
    <?php
    $result->data_seek(0); // รีเซ็ตตัวชี้ผลลัพธ์
    while ($row = $result->fetch_assoc()) {
        if ($row['InfoID'] == 2) { // โค้ดฝัง Google Maps
            echo htmlspecialchars_decode($row['ItemDetail']);
        }
    }
    $conn->close();
    ?>
</div>
    </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
