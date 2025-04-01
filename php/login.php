<?php
session_start();
require 'dbconnect.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Username = trim($_POST['Username']); // ลบช่องว่างรอบๆ ข้อมูล
    $Password = $_POST['Password'];

    // ค้นหาข้อมูลผู้ใช้ในฐานข้อมูล
    $sql = "SELECT AccountID, Username, Password, ImageAccount, role, status FROM Account WHERE Username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $Username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // ตรวจสอบสถานะผู้ใช้
            if ($row['status'] == 0) {
                echo "<script>
                        alert('บัญชีของคุณถูกปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                        window.history.back();
                      </script>";
                exit();
            }

            // ตรวจสอบรหัสผ่าน
            if (password_verify($Password, $row['Password'])) {
                // กำหนดเซสชัน
                $_SESSION['user_id'] = $row['AccountID'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['profile_image'] = $row['ImageAccount'] ?: 'uploads/default.png'; // กำหนดรูป default หากไม่มีในฐานข้อมูล

                echo "<script>
                Swal.fire({
                    title: 'เข้าสู่ระบบสำเร็จ!',
                    text: 'ยินดีต้อนรับกลับ!',
                    icon: 'success',
                    confirmButtonText: 'ไปที่หน้าแรก'
                }).then(() => {
                    window.location.href = '../Member/Member home page.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    title: 'รหัสผ่านไม่ถูกต้อง!',
                    text: 'กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ลองอีกครั้ง'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
        } else {
            echo "<script>
                Swal.fire({
                    title: 'ไม่พบชื่อผู้ใช้งานในระบบ! ⚠️',
                    text: 'โปรดตรวจสอบชื่อผู้ใช้และลองอีกครั้ง',
                    icon: 'warning',
                    confirmButtonText: 'ย้อนกลับ'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล! 🛢❌');
              </script>";
    }

    $conn->close();
}
?>
