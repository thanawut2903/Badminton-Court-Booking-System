<?php
session_start(); // เริ่มเซสชัน

// ตรวจสอบ Role ของผู้ใช้งาน
if (isset($_SESSION['user_idadmin'])) {
    $role = 'admin';
} elseif (isset($_SESSION['user_id'])) {
    $role = 'member';
} else {
    $role = null;
}

include 'header.php';

// ลบข้อมูลเซสชันเฉพาะ Role ที่ต้องการ
if ($role === 'admin') {
    unset($_SESSION['user_idadmin']); // ลบเฉพาะข้อมูล Admin
    unset($_SESSION['role']); // หากเก็บ Role ในเซสชัน
    echo "<script>
        Swal.fire({
            title: 'ออกจากระบบสำเร็จ!',
            text: 'คุณได้ออกจากระบบแล้ว',
            icon: 'success',
            confirmButtonText: 'กลับสู่หน้าแรก'
        }).then(() => {
            window.location.href = '/web_badmintaon_khlong_6/Visitors/Homepage.php';
        });
    </script>";
    exit;
} elseif ($role === 'member') {
    unset($_SESSION['user_id']); // ลบเฉพาะข้อมูล Member
    unset($_SESSION['role']); 
    echo "<script>
        Swal.fire({
            title: 'ออกจากระบบสำเร็จ!',
            text: 'คุณได้ออกจากระบบแล้ว',
            icon: 'success',
            confirmButtonText: 'กลับสู่หน้าแรก'
        }).then(() => {
            window.location.href = '/web_badmintaon_khlong_6/Visitors/Homepage.php';
        });
    </script>";
    exit;
} else {
    echo "<script>
        Swal.fire({
            title: '⚠️ ไม่พบข้อมูลการเข้าสู่ระบบ!',
            text: 'กรุณาเข้าสู่ระบบใหม่',
            icon: 'warning',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = '/web_badmintaon_khlong_6/Visitors/Homepage.php';
        });
    </script>";
    exit;
}
?>
