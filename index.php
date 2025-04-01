<?php
session_start(); // เริ่ม session

// ตรวจสอบว่ามีการล็อกอินหรือยัง
if (isset($_SESSION['user_idadmin'])) {
    // ถ้าเป็น Admin ให้ไปหน้าแอดมิน
    header("Location: Admin/Homepage admin.php");
    exit();
} elseif (isset($_SESSION['user_id'])) {
    // ถ้าเป็นสมาชิกทั่วไป ให้ไปหน้าสมาชิก
    header("Location: Member/Member home page.php");
    exit();
} else {
    // ถ้ายังไม่ได้ล็อกอิน ให้ไปที่หน้า login
    header("Location: Visitors/Homepage.php");
    exit();
}
?>
