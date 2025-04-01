<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $set_day = isset($_POST['set_day']) ? $_POST['set_day'] : '';
    $court_price = isset($_POST['court_price']) ? $_POST['court_price'] : '';
    $max_players = isset($_POST['max_players']) ? $_POST['max_players'] : '';
    $max_playergang = isset($_POST['max_playergang']) ? $_POST['max_playergang'] : '';
    $price_group = isset($_POST['price_group']) ? $_POST['price_group'] : '';
    
    // เตรียมคำสั่ง SQL สำหรับการอัปเดตข้อมูล
    $updates = [
        ["InfoID" => 12, "ItemDetail" => $set_day],
        ["InfoID" => 13, "ItemDetail" => $court_price],
        ["InfoID" => 14, "ItemDetail" => $max_players],
        ["InfoID" => 15, "ItemDetail" => $max_playergang],
        ["InfoID" => 16, "ItemDetail" => $price_group],
    ];

    $success = true;

    foreach ($updates as $update) {
        $sql = "UPDATE info SET ItemDetail = ? WHERE InfoID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $update['ItemDetail'], $update['InfoID']);

        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }

    // ตรวจสอบว่าการอัปเดตสำเร็จหรือไม่
    if ($success) {
        echo "<script>
            window.location.href='../Admin/Homepage admin.php';
        </script>";
    }
     else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล'); window.history.back();</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('ไม่รองรับการเรียกใช้งานรูปแบบนี้'); window.history.back();</script>";
}
?>
