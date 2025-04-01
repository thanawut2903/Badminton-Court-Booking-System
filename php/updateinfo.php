<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล
include '../php/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $bookingInfo = isset($_POST['item_detail_9']) ? trim($_POST['item_detail_9']) : '';
    $paymentInfo = isset($_POST['item_detail_10']) ? trim($_POST['item_detail_10']) : '';
    $aboutInfo = isset($_POST['item_detail_11']) ? trim($_POST['item_detail_11']) : '';


    

    // ตรวจสอบข้อมูลก่อนอัปเดต
    if (empty($bookingInfo) || empty($paymentInfo) || empty($aboutInfo)) {
        echo "<script>alert('ข้อมูลไม่ครบถ้วน'); window.history.back();</script>";
        exit;
    }

    $conn->begin_transaction(); // เริ่มการทำธุรกรรม

    try {
        // อัปเดตข้อมูลการจองสนาม
        $query = "UPDATE info SET ItemDetail = ? WHERE InfoID = 9";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $bookingInfo);
        $stmt->execute();

        // อัปเดตข้อมูลการชำระเงิน
        $query = "UPDATE info SET ItemDetail = ? WHERE InfoID = 10";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $paymentInfo);
        $stmt->execute();

        // อัปเดตข้อมูลเกี่ยวกับสนามแบดมินตัน
        $query = "UPDATE info SET ItemDetail = ? WHERE InfoID = 11";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $aboutInfo);
        $stmt->execute();

        $conn->commit(); // ยืนยันการทำธุรกรรม

        echo "<script>
        Swal.fire({
            title: 'สำเร็จ!',
            text: 'อัปเดตข้อมูลเรียบร้อยแล้ว',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '../Admin/Homepage admin.php';
        });
    </script>";
    
    } catch (Exception $e) {
        $conn->rollback(); // ยกเลิกการทำธุรกรรมในกรณีเกิดข้อผิดพลาด
        error_log($e->getMessage());
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('วิธีการร้องขอไม่ถูกต้อง'); window.history.back();</script>";
}

$conn->close();
?>
