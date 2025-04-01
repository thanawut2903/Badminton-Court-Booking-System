<?php
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require_once __DIR__ . "/dbconnect.php";

// ข้อมูลที่จะเพิ่ม
$firstname = "ก้องเกียรติ ";
$lastname = "รัตนวงศ์";
$tel = "0935696931";
$lineid = "bbb";
$email = "bbb@gmail.com";
$username = "bbb";
$password = "bbb";  // รหัสผ่านเดิม
$hashed_password = password_hash($password, PASSWORD_BCRYPT);  // เข้ารหัส bcrypt
$imageaccount = "profileimages/profile_bbb.jpg";
$status = 1;
$role = "M";

// คำสั่ง SQL พร้อมใช้ prepared statement
$sql = "INSERT INTO Account (FirstName, LastName, Tel, LineID, Email, Username, Password, VerificationCode, ImageAccount, Status, Role) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?)";

// เตรียม statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssi", $firstname, $lastname, $tel, $lineid, $email, $username, $hashed_password, $imageaccount, $status, $role);

// Execute และตรวจสอบ
if ($stmt->execute()) {
    echo "✅ Insert สำเร็จ!";
} else {
    echo "❌ Insert ไม่สำเร็จ: " . $stmt->error;
}

// ปิด statement และ connection
$stmt->close();
$conn->close();
?>
