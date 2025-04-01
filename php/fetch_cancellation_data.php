<?php
// Enable error reporting to debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
include '../php/dbconnect.php';


// รับค่าปีและเดือนจาก URL
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');  // ถ้าไม่มีให้ใช้ปีปัจจุบัน
$month = isset($_GET['month']) ? $_GET['month'] : '';  // ถ้าไม่มีให้ใช้เดือนปัจจุบัน
// ตรวจสอบค่าปี
if (empty($year)) {
    die('Error: Year is required.');
}

// สร้างคำสั่ง SQL
$query = "SELECT b.CancelReason, 
                  CONCAT(a.FirstName, ' ', a.LastName) AS UserName, 
                  COUNT(*) as CancelCount,
                  c.FirstName AS CancelByName,
                  c.Role AS CancelByRole
           FROM booking b
           JOIN account a ON b.AccountID = a.AccountID
           LEFT JOIN account c ON b.CancelBy = c.AccountID
           WHERE b.Status = 'ยกเลิก' 
           AND a.Role != 'A' 
           AND (YEAR(b.CancelDT) = '$year' 
           AND (MONTH(b.CancelDT) = '$month' OR '$month' = ''))
           GROUP BY b.CancelReason, b.AccountID, b.CancelBy, c.Role 
           ORDER BY CancelCount DESC";


// ดึงข้อมูลจากฐานข้อมูล
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Error executing query: ' . mysqli_error($conn)); // แสดงข้อผิดพลาดจาก query
}

$cancellations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cancellations[] = $row;
}

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode($cancellations);
mysqli_close($conn);
?>
