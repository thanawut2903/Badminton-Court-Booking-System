<?php
require 'dbconnect.php'; // เชื่อมต่อฐานข้อมูล
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าข้อมูลจากฟอร์ม
    $FirstName = trim($_POST['FirstName']);
    $LastName = trim($_POST['LastName']);
    $Tel = trim($_POST['Tel']);
    $LineID = trim($_POST['LineID']);
    $Email = trim($_POST['Email']);
    $Username = trim($_POST['Username']);
    $Password = $_POST['Password'];
    $ConfirmPassword = $_POST['ConfirmPassword'];

    // ตรวจสอบรหัสผ่านและยืนยันรหัสผ่านว่าตรงกัน
    if ($Password !== $ConfirmPassword) {
        echo "<script>alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน!'); window.history.back();</script>";
        exit();
    }

    // เข้ารหัสรหัสผ่าน
    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

    // ตรวจสอบ Username ซ้ำ
    $sqlCheckUsername = "SELECT COUNT(*) AS userCount FROM Account WHERE Username = ?";
    $stmt = $conn->prepare($sqlCheckUsername);
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['userCount'] > 0) {
        echo "<script>
                alert('ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว!');
                window.history.back();
              </script>";
        exit();
    }
    $stmt->close();

    // ตรวจสอบการอัปโหลดไฟล์
    $imagePath = "profileimages/default.png"; // กำหนดค่าเริ่มต้น
    if (isset($_FILES['ImageAccount']) && $_FILES['ImageAccount']['error'] == 0) {
        $targetDir = "../profileimages/"; // โฟลเดอร์เก็บไฟล์
        $fileName = "profile_" . strtolower($Username);
        $fileType = strtolower(pathinfo($_FILES['ImageAccount']['name'], PATHINFO_EXTENSION)); // ใช้ชื่อไฟล์จริง

        // ตรวจสอบประเภทไฟล์
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
            $maxFileSize = 5 * 1024 * 1024; // ขนาดไฟล์สูงสุด 5MB
            if ($_FILES['ImageAccount']['size'] > $maxFileSize) {
                echo "<script>alert('ขนาดไฟล์ใหญ่เกินไป! ขนาดไฟล์ต้องไม่เกิน 5MB'); window.history.back();</script>";
                exit();
            }

            // กำหนดที่เก็บไฟล์
            $imagePath = "profileimages/" . $fileName . "." . $fileType;

            // ทำการอัปโหลดไฟล์
            if (!move_uploaded_file($_FILES['ImageAccount']['tmp_name'], $targetDir . $fileName . "." . $fileType)) {
                echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('ประเภทไฟล์ไม่ถูกต้อง! รองรับ JPG, JPEG, PNG และ GIF'); window.history.back();</script>";
            exit();
        }
    }

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $sql = "INSERT INTO Account (FirstName, LastName, Tel, LineID, Email, Username, Password, ImageAccount, Status, Role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 'A')"; // Role = 'A' สำหรับ Admin

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $FirstName, $LastName, $Tel, $LineID, $Email, $Username, $hashedPassword, $imagePath);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'เพิ่มผู้ดูแลระบบสำเร็จ!',
                    showConfirmButton: false,
                }).then(function() {
                    window.location.href = '../Admin/Editadmin.php'; // เปลี่ยนเส้นทางหลังจากแสดง SweetAlert
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: '" . $stmt->error . "',
                    showConfirmButton: true
                }).then(function() {
                    window.history.back(); // ย้อนกลับไปยังหน้าก่อนหน้า
                });
              </script>";
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>
