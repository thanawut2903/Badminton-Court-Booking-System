<?php
require '../php/dbconnect.php';
include 'header.php';

if (isset($_GET['code'])) {
    $verificationCode = $_GET['code'];
    
    // ตรวจสอบว่าโค้ดยืนยันมีอยู่ในฐานข้อมูลหรือไม่
    $query = "SELECT * FROM account WHERE VerificationCode = ? AND Status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // อัปเดตสถานะเป็น '1' และตั้งค่า Role เป็น 'M'
        $updateQuery = "UPDATE account SET Status = '1', Role = 'M', VerificationCode = NULL WHERE VerificationCode = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $verificationCode);
        if ($stmt->execute()) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'ยืนยันอีเมลสำเร็จ!',
                    text: 'สามารถเข้าสู่ระบบได้',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '../Visitors/Member login page.php';
                });
            </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถยืนยันอีเมลได้ กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '../Visitors/Membership page.php';
                });
            </script>";
        }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'ลิงก์ไม่ถูกต้อง!',
                    text: 'ลิงก์ยืนยันอาจไม่ถูกต้อง หรือบัญชีได้รับการยืนยันแล้ว',
                    icon: 'warning',
                    confirmButtonText: 'กลับไปหน้าเข้าสู่ระบบ'
                }).then(() => {
                    window.location.href = '../Visitors/Member login page.php';
                });
            </script>";
        }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่มีโค้ดยืนยัน กรุณาตรวจสอบอีเมลของคุณ',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '../Visitors/Member login page.php';
                });
            </script>";
        }
        

$conn->close();
?>
