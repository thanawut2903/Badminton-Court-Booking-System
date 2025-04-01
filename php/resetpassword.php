<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['Username'];

    // ตรวจสอบว่า username มีอยู่ในฐานข้อมูลหรือไม่
    $sql = "SELECT AccountID, Email FROM account WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("SQL Error: " . $conn->error);
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $accountId = $user['AccountID'];
        $email = $user['Email'];

        // สร้างรหัสผ่านใหม่แบบสุ่ม
        $newPassword = bin2hex(random_bytes(4)); // รหัสผ่านยาว 8 ตัวอักษร
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // อัปเดตรหัสผ่านในฐานข้อมูล
        $sql = "UPDATE account SET Password = ? WHERE AccountID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("SQL Error: " . $conn->error);
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("si", $hashedPassword, $accountId);
        if ($stmt->execute()) {
            error_log("Password updated successfully for AccountID: $accountId");

            // ส่งอีเมลพร้อมรหัสผ่านใหม่
            $subject = "📢 แจ้งเตือน: รหัสผ่านใหม่สำหรับเข้าสู่ระบบ";
            $message = "<html><body style='font-family: Arial, sans-serif; color: #333;'>\n";
            $message .= "<h2 style='color: #007bff;'>🔐 แจ้งเตือนการรีเซ็ตรหัสผ่าน</h2>\n";
            $message .= "<p>เรียน คุณผู้ใช้,</p>\n";
            $message .= "<p>เราได้รับคำขอให้รีเซ็ตรหัสผ่านของคุณ และได้ทำการสร้างรหัสผ่านใหม่ให้คุณเรียบร้อยแล้ว</p>\n";
            $message .= "<p><strong>🆕 รหัสผ่านใหม่ของคุณคือ:</strong> <span style='font-size: 18px; color: #d9534f;'><strong>$newPassword</strong></span></p>\n";
            $message .= "<p>กรุณาใช้รหัสผ่านใหม่นี้เข้าสู่ระบบ และ <strong>เปลี่ยนรหัสผ่านทันที</strong> เพื่อความปลอดภัยของบัญชีของคุณ</p>\n";
            $message .= "</body></html>";


            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'fordatom4@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
                $mail->Password = 'pzfw kyag xapv hpuo'; // ใช้รหัสผ่านแอปพลิเคชัน Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->CharSet = 'UTF-8';
                $mail->setFrom('noreply@example.com', 'Web Badminton Khlong 6'); // ซ่อน Gmail จริง
                $mail->addReplyTo('noreply@example.com', 'Web Badminton Khlong 6');                
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;

                $mail->send();
                echo "<script>alert('รหัสผ่านใหม่ถูกส่งไปที่อีเมลของคุณแล้ว'); window.location.href = '../Visitors/Member login page.php';</script>";
            } catch (Exception $e) {
                error_log("Failed to send email: " . $mail->ErrorInfo);
                echo "<script>alert('ไม่สามารถส่งอีเมลได้ กรุณาลองอีกครั้ง'); window.history.back();</script>";
            }
        } else {
            error_log("Failed to update password for AccountID: $accountId. Error: " . $stmt->error);
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน'); window.history.back();</script>";
        }
    } else {
        error_log("Username not found: $username");
        echo "<script>alert('ไม่พบชื่อผู้ใช้งานนี้ในระบบ'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>
