<?php
session_start();
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
include 'header.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนอัปโหลดสลิป'); window.location.href='../Visitors/Homepage.php';</script>";
    exit;
}

$accountID = $_SESSION['user_id']; // ดึง AccountID จาก Session

// รับ bookingID จากฟอร์ม
if (!isset($_POST['bookingID'])) {
    echo "<script>alert('ไม่พบข้อมูลการจอง'); history.back();</script>";
    exit;
}

$bookingID = $_POST['bookingID']; // BookingID จากฟอร์ม

// ตรวจสอบว่ามีการอัปโหลดไฟล์
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] == 0) {
    $file = $_FILES['payment_slip'];

    // ตรวจสอบขนาดไฟล์ (จำกัดที่ 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo "<script>alert('ไฟล์มีขนาดใหญ่เกินไป (จำกัด 5MB)'); history.back();</script>";
        exit;
    }

    // ตรวจสอบไฟล์ว่าเป็นภาพหรือไม่
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mimeType = mime_content_type($file['tmp_name']);
    $allowedMimeTypes = ['image/jpeg', 'image/png'];

    if (!in_array($fileExt, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
        echo "<script>alert('อัปโหลดได้เฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น'); history.back();</script>";
        exit;
    }

    // สร้างชื่อไฟล์ใหม่โดยใช้ bookingID
    $newFileName = "slippayment_{$bookingID}.{$fileExt}";
    $uploadPath = "../slippayment/" . $newFileName;

    // อัปโหลดไฟล์ไปยังโฟลเดอร์ slippayment/
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // บันทึกพาธลงใน booking.PaymentSlips
        $filePath = "slippayment/" . $newFileName;
        $sql = "UPDATE booking SET PaymentSlips = ?, Status = 'รออนุมัติ' WHERE BookingID = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "<script>alert('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL'); history.back();</script>";
            exit;
        }

        $stmt->bind_param("si", $filePath, $bookingID);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'อัปโหลดสลิปสำเร็จ!',
                    text: 'คุณถูกส่งไปยังประวัติการจอง',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '../Member/Booking history.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล',
                    confirmButtonText: 'ย้อนกลับ'
                }).then(() => {
                    history.back();
                });
            </script>";
        }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์',
                    confirmButtonText: 'ย้อนกลับ'
                }).then(() => {
                    history.back();
                });
            </script>";
        }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกไฟล์ก่อนอัปโหลด หรือไฟล์ไม่ถูกต้อง',
                    confirmButtonText: 'ย้อนกลับ'
                }).then(() => {
                    history.back();
                });
            </script>";
        }
        
?>
