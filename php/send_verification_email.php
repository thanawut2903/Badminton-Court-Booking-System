<?php
require 'path/to/PHPMailer.php'; // ปรับเส้นทางตามที่ตั้งของ PHPMailer
require 'path/to/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // กำหนดค่าการตั้งค่า SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // โฮสต์ SMTP สำหรับ Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'fordatom4@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
    $mail->Password = 'pzfw kyag xapv hpuo'; // ใช้รหัสผ่านแอปพลิเคชัน Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ผู้ส่งและผู้รับ
    $mail->setFrom('fordatom4@gmail.com', 'ชื่อผู้ส่ง');
    $mail->addAddress('user_email@example.com', 'ชื่อผู้รับ'); // อีเมลผู้รับ

    // อ่านไฟล์ HTML
    $htmlTemplate = file_get_contents('../Visitor/Submitmail.php'); // ระบุเส้นทางที่ถูกต้อง


    if (!$htmlTemplate) {
        throw new Exception('ไม่สามารถโหลดไฟล์ Submitmail.php ได้');
    }

    // สร้างลิงก์ยืนยัน
    $verificationLink = 'https://example.com/verify.php?token=12345'; // แทนที่ด้วยโทเค็นจริง

    // แทนค่าตัวแปรในไฟล์ HTML
    $htmlContent = str_replace('{{verification_link}}', $verificationLink, $htmlTemplate);

    // กำหนดเนื้อหาอีเมล
    $mail->isHTML(true);
    $mail->Subject = 'ยืนยันอีเมลของคุณ';
    $mail->Body = $htmlContent;

    // ส่งอีเมล
    $mail->send();
    echo 'อีเมลยืนยันถูกส่งเรียบร้อยแล้ว';
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: {$mail->ErrorInfo}";
}
