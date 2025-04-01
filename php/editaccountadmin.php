<?php
require 'dbconnect.php';
include 'header.php';
session_start();

if (!isset($_SESSION['user_idadmin'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

$userId = $_SESSION['user_idadmin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $tel = $_POST['Tel'];
    $lineID = $_POST['LineID'];
    $username = $_POST['Username'];
    $password = !empty($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : null;

    // ดึงข้อมูลปัจจุบันของผู้ใช้จากฐานข้อมูล
    $sql = "SELECT Username, ImageAccount FROM account WHERE AccountID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("ไม่พบข้อมูลผู้ใช้");
    }

    $user = $result->fetch_assoc();
    $currentUsername = $user['Username'];
    $imagePath = $user['ImageAccount'];

    // ตรวจสอบว่า Username ซ้ำหรือไม่ (ถ้ามีการเปลี่ยนแปลง)
    if ($username !== $currentUsername) {
        $checkQuery = "SELECT AccountID FROM account WHERE Username = ?";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bind_param("s", $username);
        $stmtCheck->execute();
        $checkResult = $stmtCheck->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น'); window.history.back();</script>";
            exit();
        }
    }

    // จัดการอัปโหลดรูปภาพใหม่
    if (isset($_FILES['ImagePath']) && $_FILES['ImagePath']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../profileimages/"; // เปลี่ยนโฟลเดอร์เก็บไฟล์เป็น profileimages

        // ตรวจสอบการสร้างโฟลเดอร์ (ถ้าไม่มีโฟลเดอร์จะสร้างขึ้น)
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
            die("เกิดข้อผิดพลาดในการสร้างโฟลเดอร์สำหรับอัปโหลดไฟล์");
        }

        // สร้างชื่อไฟล์ใหม่ โดยใช้ profileimages_ ตามด้วยชื่อผู้ใช้
        $imageFileType = strtolower(pathinfo($_FILES['ImagePath']['name'], PATHINFO_EXTENSION));
        $imageName = "profile_" . strtolower($username) . "." . $imageFileType;
        $newImagePath = $targetDir . $imageName;

        // เซฟทับไฟล์เดิมถ้ามีชื่อไฟล์เดียวกัน
        if (!move_uploaded_file($_FILES['ImagePath']['tmp_name'], $newImagePath)) {
            die("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
        }

        // กำหนดที่เก็บไฟล์ในฐานข้อมูล
        $imagePath = "profileimages/" . $imageName;
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    if (!empty($password)) {
        $sql = "UPDATE account SET FirstName=?, LastName=?, Tel=?, LineID=?, Username=?, Password=?, ImageAccount=? WHERE AccountID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $firstName, $lastName, $tel, $lineID, $username, $password, $imagePath, $userId);
    } else {
        $sql = "UPDATE account SET FirstName=?, LastName=?, Tel=?, LineID=?, Username=?, ImageAccount=? WHERE AccountID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $firstName, $lastName, $tel, $lineID, $username, $imagePath, $userId);
    }

    if ($stmt->execute()) {
        // หากอัปเดตสำเร็จ
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ข้อมูลอัปเดตสำเร็จ!',
                showConfirmButton: true,
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.href = '../Admin/Homepage admin.php'; // ไปยังหน้าอื่นหลังจากแสดงข้อความ
            });
        </script>";
    } else {
        die("เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error);
    }
    
}    
?>
