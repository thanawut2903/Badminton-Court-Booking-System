<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าค่าที่ส่งมาครบถ้วนหรือไม่
    $fields = [
        'ItemDetail_1' => 'ที่อยู่ของสนาม',
        'ItemDetail_2' => 'โค้ดฝัง Google Maps',
        'ItemDetail_3' => 'URL Facebook',
        'ItemDetail_4' => 'LINE ID',
        'ItemDetail_5' => 'เบอร์โทรศัพท์'
    ];

    $iconFields = [
        'ItemDetail_6' => 6, // Facebook Icon
        'ItemDetail_7' => 7, // Line Icon
        'ItemDetail_8' => 8  // Phone Icon
    ];

    $errors = [];
    $uploadDir = '../uploads/';

    // Process text fields
    foreach ($fields as $fieldKey => $fieldName) {
        if (!isset($_POST[$fieldKey]) || empty(trim($_POST[$fieldKey]))) {
            $errors[] = $fieldName;
        } else {
            $itemDetail = trim($_POST[$fieldKey]);
            $sql = "UPDATE info SET ItemDetail = ? WHERE ItemName = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ss", $itemDetail, $fieldName);
                $stmt->execute();
                $stmt->close();
            } else {
                $errors[] = "SQL Error: " . $conn->error;
            }
        }
    }

    // Process file uploads
    foreach ($iconFields as $fieldKey => $infoId) {
        if (isset($_FILES[$fieldKey]) && $_FILES[$fieldKey]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES[$fieldKey]['tmp_name'];
            $fileName = basename($_FILES[$fieldKey]['name']);
            $targetPath = $uploadDir . $fileName;

            // Validate file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[] = "$fileName ไม่ถูกต้อง ".implode(', ', $allowedExtensions);
                continue;
            }

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Update database with file path
                $sql = "UPDATE info SET ItemDetail = ? WHERE InfoID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("si", $targetPath, $infoId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $errors[] = "SQL Error: " . $conn->error;
                }
            } else {
                $errors[] = "ไม่สามรถเก็บไฟล์ $fileName";
            }
        }
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\n", $errors) . "'); window.history.back();</script>";
    } else {
        echo "<script>alert('อัปเดตสำเร็จ'); window.location.href = '../Admin/Homepage admin.php';</script>";
    }
}

$conn->close();
?>
