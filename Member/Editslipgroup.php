<?php 
require "../php/dbconnect.php";
session_start();
include '../php/member_navbar.php';

// ดึง bookingID จาก URL
if (!isset($_GET['bookingID'])) {
    echo "ไม่พบข้อมูลการจอง";
    exit;
}
$bookingId = $_GET['bookingID'];

// ดึงข้อมูลสลิปจากฐานข้อมูล
$sql = "SELECT PaymentSlips FROM booking WHERE BookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$slipPath = $row['PaymentSlips']; // ไฟล์สลิปที่อัปโหลด

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>อัปโหลดสลิปโอนเงิน</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="Editslip.css" rel="stylesheet">
</head>
<body>
<?php
include '../php/member_menu.php';
?>

<div class="container">
    <h3 class="text-center">อัปโหลดสลิปโอนเงิน</h3>

    <div class="text-center mt-3">
        <?php if (!empty($slipPath)) : ?>
            <p>สลิปที่อัปโหลดแล้ว:</p>
            <a href="../<?= htmlspecialchars($slipPath) ?>" target="_blank">
                <img src="../<?= htmlspecialchars($slipPath) ?>" alt="Payment Slip" style="width: 200px; height: auto; border: 1px solid #ccc;">
            </a>
            <p><small>หากต้องการเปลี่ยนแปลง ให้เลือกไฟล์ใหม่แล้วกดอัปโหลด</small></p>
        <?php else : ?>
            <p style="color: red;">ยังไม่มีสลิปการโอนเงิน</p>
        <?php endif; ?>
    </div>

    <form action="../php/upload_slip_group.php" method="post" enctype="multipart/form-data" class="text-center mt-3">
        <input type="hidden" name="bookingID" value="<?= $bookingId ?>">
        <input type="file" name="payment_slip" accept="image/png, image/jpeg" class="form-control mb-3" style="width: 500px; margin: 0 auto;" required>
        <button type="submit" class="btn btn-success">อัปโหลดสลิป</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
