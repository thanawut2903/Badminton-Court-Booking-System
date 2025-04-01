<?php
session_start();
include '../php/admin_navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Settingpayment.css" rel="stylesheet">
</head>
<body>
 
<?php include '../php/admin_menu.php' ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>การตั้งค่าช่องทางการชำระเงิน</h1>
      </div>
    </div>



    <div class="form-container">
    <form action="../php/update_payment.php" method="POST">
        <?php
        require '../php/dbconnect.php';

        // ดึงข้อมูลการชำระเงินล่าสุดจากฐานข้อมูล
        $query = "SELECT BankName, AccountNumber, AccountName FROM paymentcontact WHERE PaymentcontactID = 1"; // เปลี่ยน ID ตามที่คุณต้องการ
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $bankName = htmlspecialchars($row['BankName']);
            $accountNumber = htmlspecialchars($row['AccountNumber']);
            $accountName = htmlspecialchars($row['AccountName']);
        } else {
            $bankName = "";
            $accountNumber = "";
            $accountName = "";
            echo "<p style='color: red;'>ไม่พบข้อมูลการชำระเงิน</p>";
        }
        ?>
        
        <!-- ชื่อธนาคาร -->
        <div class="form-group">
            <label for="bank-name">ชื่อธนาคาร :</label>
            <input type="text" id="bank-name" name="bank_name" value="<?php echo $bankName; ?>" placeholder="ชื่อธนาคาร" required>
        </div>

        <!-- เลขที่บัญชี -->
        <div class="form-group">
            <label for="account-number">เลขที่บัญชี :</label>
            <input type="text" id="account-number" name="account_number" value="<?php echo $accountNumber; ?>" placeholder="เลขที่บัญชี" required>
        </div>

        <!-- ชื่อบัญชี -->
        <div class="form-group">
            <label for="account-name">ชื่อบัญชี :</label>
            <input type="text" id="account-name" name="account_name" value="<?php echo $accountName; ?>" placeholder="ชื่อบัญชี" required>
        </div>

        <div class="custom-boxprevious">
            <!-- ปุ่มย้อนกลับ -->
            <button type="button" class="btn-previous" onclick="confirmBack()">ย้อนกลับ</button>

            <div class="custom-save">
                <!-- ปุ่มบันทึก -->
                <button type="button" class="btn-save" onclick="confirmSave()">บันทึก</button>
            </div>
        </div>
    </form>
</div>

<script>
    function confirmBack() {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการย้อนกลับไปยังหน้าก่อนหน้านี้?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ย้อนกลับ!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                history.back();
            }
        });
    }

    function confirmSave() {
        Swal.fire({
            title: "ยืนยันการบันทึก?",
            text: "โปรดยืนยันว่าคุณต้องการบันทึกข้อมูลนี้",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "ใช่, บันทึกเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector("form").submit();
            }
        });
    }
</script>




    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
