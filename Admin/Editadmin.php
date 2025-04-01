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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editadmin.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php' ?> 

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>จัดการผู้ดูแลระบบ</h1>
      </div>
    </div>


    <div class="wrapper">
        <div class="search-container">
            <div class="search-label">ค้นหาผู้ดูแลระบบ</div>
            <input type="text" id="search-input" class="search-input" placeholder="ค้นหาผู้ดูแลระบบจาก ชื่อ นามสกุล / ชื่อผู้ใช้งาน / LINE ID / เบอร์โทรศัพท์">
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
            console.log('../php/admin_search_bar.php?query='+encodeURIComponent(query))
            fetch('../php/admin_search_bar.php?query='+query, {
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("change", function (event) {
        if (event.target.classList.contains("status-switch")) {
            const switchElement = event.target;
            const id = switchElement.getAttribute("data-account-id");
            const newStatus = switchElement.checked ? 1 : 0;
            const prevChecked = !switchElement.checked; // เก็บค่าก่อนเปลี่ยน

            console.log(`Switch clicked! ID: ${id}, New Status: ${newStatus}`);

            if (!id) {
                console.error("Missing account ID!");
                return;
            }

            const putStatusAccount = (id, status, switchElement) => {
                fetch("../php/update_admin_status.php", {
                    method: "PUT", 
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ accountId: id, status: status, _method: "PUT" }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                title: "สำเร็จ!",
                                text: "สถานะบัญชีได้รับการอัปเดตเรียบร้อย",
                                icon: "success",
                                confirmButtonText: "ตกลง",
                            });
                        } else {
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: data.message,
                                icon: "error",
                                confirmButtonText: "ตกลง",
                            });
                            switchElement.checked = prevChecked;
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด!",
                            text: "ไม่สามารถอัปเดตสถานะได้",
                            icon: "error",
                            confirmButtonText: "ตกลง",
                        });
                        switchElement.checked = prevChecked;
                    });
            };

            if (newStatus === 1) {
                // เปิดใช้งานโดยไม่ต้องยืนยัน
                putStatusAccount(id, 1, switchElement);
            } else {
                // ถ้าจะปิดใช้งาน ให้ยืนยันก่อน
                Swal.fire({
                    title: "ยืนยันการปิดการใช้งาน?",
                    text: `คุณต้องการปิดการใช้งาน Account ID: ${id} ใช่หรือไม่?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "ใช่, ปิดใช้งาน!",
                    cancelButtonText: "ยกเลิก",
                }).then((result) => {
                    if (result.isConfirmed) {
                        putStatusAccount(id, 0, switchElement);
                    } else {
                        switchElement.checked = prevChecked;
                    }
                });
            }
        }
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
                title: 'เกิดข้อผิดพลาด!',
                text: 'Account ID is missing',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        // ยืนยันก่อนลบ
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบผู้ดูแลระบบนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../php/delete_admin.php', {
                    method: 'POST', // ตรวจสอบให้แน่ใจว่า POST ตรงกับ delete_admin.php
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
                            title: 'สำเร็จ!',
                            text: 'ลบผู้ดูแลระบบสำเร็จ',
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            event.target.parentElement.parentElement.remove();
                        });
                    } else {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถลบผู้ดูแลระบบได้: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                });
            }
        });
    }
});
</script>


    <!-- <div class="custom-boxprevious"style="margin-top:15px">
    <button class="btn-previous" onclick="history.back()">ย้อนกลับ</button>
    </div> -->

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
