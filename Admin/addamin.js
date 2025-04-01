// ดักจับ Event เมื่อมีการเลือกไฟล์
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0]; // รับไฟล์ที่เลือก
    const preview = document.getElementById('preview'); // อ้างอิงถึง img
  
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

  
  