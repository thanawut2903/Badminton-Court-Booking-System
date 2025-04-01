<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bankName = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
    $accountNumber = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
    $accountName = isset($_POST['account_name']) ? trim($_POST['account_name']) : '';

    // ตรวจสอบว่าข้อมูลครบถ้วน
    if (empty($bankName) || empty($accountNumber) || empty($accountName)) {
        echo "<script>
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                window.history.back();
              </script>";
        exit;
    }

    // อัพเดตข้อมูลในฐานข้อมูล
    $query = "UPDATE paymentcontact SET BankName = ?, AccountNumber = ?, AccountName = ? WHERE PaymentcontactID = 1"; // อัพเดตที่แถวแรก (แก้ไข PaymentcontactID ตามที่คุณต้องการ)
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $bankName, $accountNumber, $accountName);

    if ($stmt->execute()) {
        echo "<script>
        alert('บันทึกสำเร็จ!');
                window.location.href = '../Admin/Homepage admin.php';
              </script>";
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . $stmt->error . "');
                window.history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>
            alert('วิธีการร้องขอไม่ถูกต้อง');
            window.history.back();
          </script>";
}
