<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courtID = isset($_POST['court_id']) ? intval($_POST['court_id']) : 0;
    $courtName = isset($_POST['court_name']) ? trim($_POST['court_name']) : '';
    $openTime = isset($_POST['open_time']) ? trim($_POST['open_time']) : '';
    $closeTime = isset($_POST['close_time']) ? trim($_POST['close_time']) : '';
    // ตรวจสอบว่าข้อมูลครบถ้วน
    if (empty($courtID) || empty($courtName) || empty($openTime) || empty($closeTime)) {
        echo "<script>
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                //window.history.back();
              </script>";
        exit;
    }

    // เตรียมคำสั่ง SQL สำหรับการอัปเดตข้อมูลสนาม
    $query = "UPDATE court SET CourtName = ?, OpenTime = ?, CloseTime = ? WHERE CourtID = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>
                alert('การเตรียมคำสั่ง SQL ผิดพลาด: " . $conn->error . "');
                window.history.back();
              </script>";
        exit;
    }

    $stmt->bind_param("sssi", $courtName, $openTime, $closeTime, $courtID);

    if ($stmt->execute()) {
      echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
      echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      title: 'อัปเดตข้อมูลสำเร็จ!',
                      text: 'ข้อมูลสนามได้รับการอัปเดตเรียบร้อยแล้ว',
                      icon: 'success',
                      confirmButtonColor: '#28a745',
                      confirmButtonText: 'ตกลง'
                  }).then(() => {
                      window.location.href = '../Admin/Editcourt.php';
                  });
              });
            </script>";
  } else {
      echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
      echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      title: 'เกิดข้อผิดพลาด!',
                      text: 'ไม่สามารถอัปเดตข้อมูลได้: " . $stmt->error . "',
                      icon: 'error',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'ลองอีกครั้ง'
                  }).then(() => {
                      window.history.back();
                  });
              });
            </script>";
  }
}
?>
