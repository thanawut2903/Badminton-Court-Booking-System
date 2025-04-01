// เมื่อผู้ใช้กดปุ่มยืนยัน
document.getElementById("submit-btn").addEventListener("click", function () {
    // ดึงตัวเลือก Radio ที่ถูกเลือก
    const reason = document.querySelector('input[name="cancel-reason"]:checked');
    const otherReason = document.getElementById("other-reason").value;
  
    // ตรวจสอบตัวเลือกและเปลี่ยนหน้า
    if (reason) {
      switch (reason.value) {
        case "timeout":
          window.location.href = "/web_badmintaon_khlong_6/Admin/Edit status general3.php"; // URL สำหรับเหตุผลไม่ชำระ
          break;
        case "no-service":
          window.location.href = "/web_badmintaon_khlong_6/Admin/Edit status general3.php"; // URL สำหรับเหตุผลไม่มาใช้บริการ
          break;
        case "other":
          if (otherReason.trim()) {
            alert("เหตุผลอื่น: " + otherReason); // สามารถส่งไปยัง Backend ได้
            window.location.href = "/web_badmintaon_khlong_6/Admin/Edit status general3.php"; // URL สำหรับเหตุผลอื่น
          } else {
            alert("กรุณากรอกเหตุผลเพิ่มเติม");
          }
          break;
        default:
          alert("โปรดเลือกเหตุผลการยกเลิก");
      }
    } else {
      alert("โปรดเลือกเหตุผลการยกเลิก");
    }
  });
  