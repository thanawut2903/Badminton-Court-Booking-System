<?php
// admin_login.php
session_start();
require 'dbconnect.php'; // เชื่อมต่อฐานข้อมูล
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Username = trim($_POST['Username']);
    $Password = $_POST['Password'];

    // ตรวจสอบข้อมูลในฐานข้อมูล เฉพาะ Admin (Role = 'A')
    $sql = "SELECT AccountID,Username, FirstName, LastName, Password, ImageAccount, Role, Status FROM Account WHERE Username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $Username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();

            // ตรวจสอบสถานะบัญชี
            if ($admin['Status'] == 0) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        title: 'บัญชีถูกปิดใช้งาน ❌',
                        text: 'กรุณาติดต่อผู้ดูแลระบบ',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        window.history.back();
                    });
                </script>";
                exit();
            }

            // ตรวจสอบสิทธิ์ Role
            if ($admin['Role'] != 'A') {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        title: 'สิทธิ์ไม่ถูกต้อง ⚠️',
                        text: 'คุณไม่มีสิทธิ์เข้าถึงระบบ Admin',
                        icon: 'warning',
                        confirmButtonText: 'ย้อนกลับ'
                    }).then(() => {
                        window.history.back();
                    });
                </script>";
                exit();
            }

            // ตรวจสอบรหัสผ่าน
            if (password_verify($Password, $admin['Password'])) {
                // ตั้งค่า Session สำหรับ Admin
                $_SESSION['user_idadmin'] = $admin['AccountID']; 
                $_SESSION['AdminName'] = $admin['FirstName'] . " " . $admin['LastName'];
                $_SESSION['Username'] = $admin['Username'];      
                $_SESSION['ImageAccount'] = $admin['ImageAccount'];

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        title: 'เข้าสู่ระบบสำเร็จ! ✅',
                        text: 'ยินดีต้อนรับ Admin',
                        icon: 'success',
                        confirmButtonText: 'ไปที่หน้าแอดมิน'
                    }).then(() => {
                        window.location.href = '../Admin/Homepage admin.php';
                    });
                </script>";
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    Swal.fire({
                        title: 'รหัสผ่านไม่ถูกต้อง ❌',
                        text: 'กรุณาลองใหม่อีกครั้ง',
                        icon: 'error',
                        confirmButtonText: 'ลองใหม่'
                    }).then(() => {
                        window.history.back();
                    });
                </script>";
            }
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'ไม่พบชื่อผู้ใช้! ❌',
                    text: 'กรุณาตรวจสอบข้อมูลอีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ย้อนกลับ'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
        $stmt->close();
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด! ⚠️',
                text: 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        </script>";
    }

    $conn->close();
}
?>
