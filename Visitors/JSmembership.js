// ดักจับ Event เมื่อมีการเลือกไฟล์
const imageInput = document.getElementById('imageInput');
const preview = document.getElementById('preview');

imageInput.addEventListener('change', function () {
    const file = this.files[0]; // รับไฟล์ที่เลือก
  
    if (file) {
        const reader = new FileReader(); // ใช้ FileReader อ่านไฟล์
        reader.onload = function (e) {
            preview.src = e.target.result; // ใส่ Base64 URL ใน src ของ img
            preview.style.display = 'block'; // แสดงภาพ
        };
        reader.readAsDataURL(file); // อ่านไฟล์เป็น Base64
    } else {
        preview.style.display = 'none'; // หากไม่มีไฟล์ ซ่อนภาพ
    }
});

// ดักจับ Event การ Submit ฟอร์มเพื่อส่งอีเมลยืนยัน
const form = document.querySelector('form');
form.addEventListener('submit', function (event) {
    event.preventDefault(); // ป้องกันการรีเฟรชหน้า

    const formData = new FormData(this); // รวบรวมข้อมูลจากฟอร์ม

    fetch('../php/submitmail.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('ส่งอีเมลยืนยันเรียบร้อย! กรุณาตรวจสอบอีเมลของคุณ');
            window.location.href = 'Homepage.php'; // เปลี่ยนเส้นทางหลังส่งสำเร็จ
        } else {
            alert('เกิดข้อผิดพลาดในการส่งอีเมล: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('ไม่สามารถส่งอีเมลได้ในขณะนี้');
    });
});