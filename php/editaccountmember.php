<?php
require 'dbconnect.php';
include 'header.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $tel = $_POST['Tel'];
    $lineID = $_POST['LineID'];
    $username = $_POST['Username'];
    $password = !empty($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : null;
    $userId = $_SESSION['user_id'];

    if (!$userId) {
        die("กรุณาเข้าสู่ระบบก่อน");
    }

    // ตรวจสอบว่ามี username ซ้ำหรือไม่
    $checkUsernameSql = "SELECT AccountID FROM account WHERE Username = ? AND AccountID != ?";
    $checkStmt = $conn->prepare($checkUsernameSql);
    if ($checkStmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $checkStmt->bind_param("si", $username, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        die("<script>alert('ชื่อผู้ใช้นี้มีอยู่แล้ว'); window.history.back();</script>");
    }

// ดึงชื่อไฟล์เดิมจากฐานข้อมูล
$sql = "SELECT ImageAccount FROM account WHERE AccountID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $imagePath = $user['ImageAccount']; // พาธรูปภาพเดิม
} else {
    die("ไม่พบข้อมูลผู้ใช้งาน");
}

// ตรวจสอบว่ามีไฟล์อัปโหลดใหม่หรือไม่
if (isset($_FILES['ImagePath']) && $_FILES['ImagePath']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "../profileimages/";

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die("ไม่สามารถสร้างโฟลเดอร์ profileimages");
        }
    }

    // ใช้ชื่อไฟล์เดิมในการบันทึกทับ
    $targetFilePath = "../" . $imagePath;

    // อัปโหลดไฟล์และบันทึกทับรูปเดิม
    if (!move_uploaded_file($_FILES['ImagePath']['tmp_name'], $targetFilePath)) {
        die("ไม่สามารถอัปโหลดรูปภาพได้");
    }
}

// อัปเดตข้อมูลในฐานข้อมูล
$sql = "UPDATE account SET FirstName = ?, LastName = ?, Tel = ?, LineID = ?, Username = ?, Password = IFNULL(?, Password) WHERE AccountID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ssssssi", $firstName, $lastName, $tel, $lineID, $username, $password, $userId);

if ($stmt->execute()) {
    echo "<script>
        Swal.fire({
            title: 'สำเร็จ!',
            text: 'อัปเดตข้อมูลสำเร็จ',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '../Member/Member home page.php';
        });
    </script>";
} else {
    die("เกิดข้อผิดพลาด: " . $stmt->error);
}

} else {
    $userId = $_SESSION['user_id'];
    $sql = "SELECT FirstName, LastName, Tel, LineID, Username, ImageAccount FROM account WHERE AccountID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("ไม่พบข้อมูลผู้ใช้งาน");
    }
}
?>
