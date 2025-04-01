<?php
session_start();
include '../php/dbconnect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = mysqli_real_escape_string($conn, $_POST['FirstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['LastName']);
    $tel = mysqli_real_escape_string($conn, $_POST['Tel']);
    $lineID = mysqli_real_escape_string($conn, $_POST['LineID']);
    $username = mysqli_real_escape_string($conn, $_POST['Username']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = password_hash($_POST['Password'], PASSWORD_BCRYPT);
    $verificationCode = md5(uniqid(rand(), true));
    $status = 'pending';

    // ตรวจสอบว่ามีอีเมลซ้ำหรือไม่
    $checkEmailQuery = "SELECT * FROM account WHERE Email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('อีเมลนี้ถูกใช้แล้ว'); window.location.href='../Visitors/Membership%20page.php';</script>";
        exit();
    }

 // กำหนดค่าเริ่มต้นของรูปภาพเป็น default.png
$imagePath = "profileimages/default.png"; 

// ตรวจสอบและอัปโหลดรูปภาพ
if (isset($_FILES["ImageAccount"]) && $_FILES["ImageAccount"]["error"] == 0) {
    $targetDir = "../profileimages/"; // โฟลเดอร์สำหรับเก็บไฟล์
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }
    
    $imageFileType = strtolower(pathinfo($_FILES["ImageAccount"]["name"], PATHINFO_EXTENSION));

    // ตั้งชื่อไฟล์เป็น username_timestamp
    $fileName = "profile_" . strtolower($username) . "." . $imageFileType;
    $targetFilePath = $targetDir . $fileName;

    // ตรวจสอบชนิดไฟล์ (อนุญาตเฉพาะ JPG, PNG, JPEG, GIF)
    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["ImageAccount"]["tmp_name"], $targetFilePath)) {
            $imagePath = "profileimages/" . $fileName; // เก็บเฉพาะพาธสำหรับบันทึกลงฐานข้อมูล
        }
    }
}

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $query = "INSERT INTO account (FirstName, LastName, Tel, LineID, Username, Email, Password, VerificationCode, Status, ImageAccount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssss", $firstName, $lastName, $tel, $lineID, $username, $email, $password, $verificationCode, $status, $imagePath);

    if ($stmt->execute()) {
        $verificationLink = "http://localhost/web_badmintaon_khlong_6/php/verify_email.php?code=$verificationCode";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'fordatom4@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
            $mail->Password = 'pzfw kyag xapv hpuo'; // ใช้ **App Password** แทนรหัสผ่านจริง
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8'; // เพิ่มบรรทัดนี้เพื่อกำหนดการเข้ารหัส UTF-8
            $mail->setFrom('fordatom4@gmail.com', 'Web Badminton Khlong 6'); // Gmail ต้องใช้เป็น from จริง
            $mail->addReplyTo('noreply@example.com', 'Web Badminton Khlong 6'); // ให้ผู้รับเห็นอีเมลนี้แทน
            $mail->addAddress($email);
            $mail->Subject = 'ยืนยันอีเมลของคุณ';
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 30px; background-color: #f9f9f9; text-align: center; border-radius: 10px; border: 1px solid #ddd;'>
                    <div style='background-color: #28a745; padding: 15px; border-radius: 10px 10px 0 0;'>
                        <h2 style='color: white; margin: 0;'>🔒 ยืนยันการสมัครสมาชิก</h2>
                    </div>
                    <div style='padding: 20px;'>
                        <p style='font-size: 18px; color: #333;'>สวัสดี,</p>
                        <p style='font-size: 16px; color: #555;'>
                            ขอบคุณที่สมัครสมาชิกกับเรา กรุณาคลิกที่ปุ่มด้านล่างเพื่อยืนยันอีเมลของคุณ
                        </p>
                        <a href='$verificationLink' style='display: inline-block; background-color: #28a745; color: white; padding: 15px 30px; text-decoration: none; font-size: 18px; font-weight: bold; border-radius: 5px; margin-top: 20px;'>✅ ยืนยันอีเมล</a>
                        <p style='margin-top: 30px; font-size: 14px; color: #777;'>หากคุณไม่ได้สมัครสมาชิก กรุณาเพิกเฉยอีเมลฉบับนี้</p>
                    </div>
                </div>
            ";
            
    include 'header.php';
    if ($mail->send()) {
    echo "<script>
        Swal.fire({
            title: 'สมัครสำเร็จ!',
            text: 'กรุณายืนยันอีเมลของคุณ',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = 'https://mail.google.com/mail/';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'ไม่สามารถส่งอีเมลยืนยันได้ กรุณาลองใหม่อีกครั้ง',
            icon: 'error',
            confirmButtonText: 'ลองอีกครั้ง'
        });
    </script>";
}
} catch (Exception $e) {
    echo "<script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: '{$mail->ErrorInfo}',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '../Visitors/Register.php';
        });
    </script>";
}
} else {
    echo "<script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'เกิดข้อผิดพลาดในการลงทะเบียน',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '../Visitors/Register.php';
        });
    </script>";
}


    $stmt->close();
    $conn->close();
}
?>
