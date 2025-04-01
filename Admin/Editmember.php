<?php
session_start();
include '../php/admin_navbar.php';
require '../php/dbconnect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editmember.css" rel="stylesheet">
</head>
<body>

<?php include '../php/admin_menu.php' ?> 
 
  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>จัดการสมาชิกในระบบ</h1>
      </div>
    </div>


    <div class="wrapper">
        <div class="search-container">
            <div class="search-label">ค้นหาสมาชิก</div>
            <input type="text" id="search-input" class="search-input" placeholder="ค้นหาสมาชิกจาก ชื่อ นามสกุล / ชื่อผู้ใช้งาน / LINE ID / เบอร์โทรศัพท์">
            <div class="search-icon">🔍</div>
        </div>
    </div>
        <div id="search-results"></div>
    </div>

  <script>
        const searchUsers = (query) => {
            /* if (query.trim() === '') {
                searchResults.innerHTML = '';
                return;
            } */
            console.log('../php/member_search_bar.php?query='+encodeURIComponent(query))
            fetch('../php/member_search_bar.php?query='+query, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
            })
                .then(response => response.text())
                .then(data => {
                    searchResults.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        searchUsers("");
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');

        searchInput.addEventListener('input', function () {
            const query = this.value;
            console.log("event")
            searchUsers(query)
        });       
    </script>

<div class="container">

<script>
// Event listener สำหรับการเปลี่ยนสถานะ
const switches = document.querySelectorAll('.status-switch');

const putStatusAccount = (id, status) => {
    fetch('../php/update_account_status.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ accountId: id, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: "สำเร็จ!",
                text: "สถานะบัญชีได้รับการอัปเดตเรียบร้อย",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload(); // รีเฟรชหน้า
            });
        } else {
            Swal.fire({
                title: "เกิดข้อผิดพลาด",
                text: data.message,
                icon: "error"
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "ไม่สามารถอัปเดตสถานะได้",
            icon: "error"
        });
    });
}

const activeFunction = (ele, id) => {
    if (ele.checked) {
        putStatusAccount(id, 1);
    } else {
        Swal.fire({
            title: "ยืนยันการปิดใช้งาน?",
            text: "คุณต้องการปิดการใช้งาน Account ID: " + id + " ใช่หรือไม่?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ปิดการใช้งาน!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                putStatusAccount(id, 0);
            } else {
                ele.checked = true; // ถ้ายกเลิก ให้กลับไปเป็นเปิดใช้งาน
            }
        });
    }
}

switches.forEach(switchElement => {
    switchElement.addEventListener('change', function() {
        const accountId = this.getAttribute('data-account-id');
        activeFunction(this, accountId);
    });
});
</script>

<script>
// ตรวจสอบว่าไฟล์ JavaScript ถูกโหลด
console.log('Script Loaded');

// ใช้ Event Delegation สำหรับปุ่มที่ถูกสร้างแบบไดนามิก
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-member')) {
        const accountId = event.target.getAttribute('data-account-id');
        console.log('Dynamic button clicked, AccountID:', accountId);

        // ตรวจสอบก่อนส่งคำขอ
        if (!accountId) {
            Swal.fire({
                title: "ข้อผิดพลาด!",
                text: "ไม่พบ Account ID",
                icon: "error"
            });
            return;
        }

        // แสดง SweetAlert2 สำหรับการยืนยันการลบ
        Swal.fire({
            title: "ยืนยันการลบ?",
            text: "คุณต้องการลบสมาชิกนี้หรือไม่?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ลบเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่งคำขอ AJAX เพื่อลบข้อมูล
                fetch('../php/delete_member.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ accountId })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "ลบสำเร็จ!",
                            text: "สมาชิกถูกลบเรียบร้อย",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload(); // รีเฟรชหน้า
                        });
                    } else {
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด!",
                        text: "ไม่สามารถลบสมาชิกได้: " + error.message,
                        icon: "error"
                    });
                });
            }
        });
    }
});
</script>

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
