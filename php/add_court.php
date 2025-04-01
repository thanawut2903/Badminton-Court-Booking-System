<?php
// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบค่าที่รับจากฟอร์ม และตั้งค่าเริ่มต้นหากไม่มีการส่งข้อมูล
    $courtID = isset($_POST['CourtID']) && !empty($_POST['CourtID']) ? $_POST['CourtID'] : null;
    $courtName = isset($_POST['CourtName']) ? $_POST['CourtName'] : '';
    $openTime = isset($_POST['OpenTime']) ? $_POST['OpenTime'] : '';
    $closeTime = isset($_POST['CloseTime']) ? $_POST['CloseTime'] : '';
    $courtStatus = isset($_POST['CourtStatus']) ? $_POST['CourtStatus'] : '1'; // ค่าเริ่มต้นคือ '1'

    // ตรวจสอบความถูกต้องของข้อมูลที่สำคัญ
    if (empty($courtName) || empty($openTime) || empty($closeTime)) {
        die("กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน");
    }

    // เชื่อมต่อฐานข้อมูล
    require 'dbconnect.php'; // ไฟล์เชื่อมต่อฐานข้อมูล
    // $conn = new mysqli('localhost', 'username', 'password', 'database_name');

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
    }

    // สร้างค่า CourtID แบบอัตโนมัติถ้าไม่ได้ส่งมา
    if (is_null($courtID)) {
        $result = $conn->query("SELECT MAX(CourtID) AS max_id FROM court");
        $row = $result->fetch_assoc();
        $courtID = $row['max_id'] + 1;
    }

    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO court (CourtID, CourtName, OpenTime, CloseTime, CourtStatus) 
            VALUES (?, ?, ?, ?, ?);";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("เกิดข้อผิดพลาดในการเตรียมคำสั่ง: " . $conn->error);
    }

    // ผูกค่าพารามิเตอร์
    $stmt->bind_param('issss', $courtID, $courtName, $openTime, $closeTime, $courtStatus);

    // ดำเนินการเพิ่มข้อมูล
    if ($stmt->execute()) {
        echo "เพิ่มข้อมูลสนามสำเร็จ";
        header("Location: ../Admin/Editcourt.php");
        
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
} else {
    echo "การส่งข้อมูลไม่ถูกต้อง";
}
?>
