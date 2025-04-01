<?php
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
if (isset($_SESSION['user_idadmin']) && $_SESSION['user_idadmin'] === 'admin') {
    // ลบข้อมูลในเซสชัน
    session_unset();
    session_destroy();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'ออกจากระบบสำเร็จ!',
            text: 'ผู้ดูแลระบบได้ออกจากระบบเรียบร้อยแล้ว',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '/web_badmintaon_khlong_6/Visitors/Homepage.php';
        });
    </script>
    <?php
} else {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'คุณไม่มีสิทธิ์ในการเข้าถึง!',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '/web_badmintaon_khlong_6/Visitors/Homepage.php';
        });
    </script>
    <?php
}
?>
