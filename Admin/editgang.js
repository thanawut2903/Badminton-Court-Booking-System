document.addEventListener("DOMContentLoaded", function () {
    const approveButtons = document.querySelectorAll(".btn-approve");
    const cancelButtons = document.querySelectorAll(".btn-cancel");
  
    // เมื่อกดปุ่ม "อนุมัติการขอเล่น"
    approveButtons.forEach(button => {
      button.addEventListener("click", function () {
        const row = this.closest("tr");
        const statusCell = row.querySelector(".status");
        statusCell.textContent = "ขอเข้าเล่นสำเร็จ";
        statusCell.className = "status green"; // เปลี่ยนสีเป็นเขียว
      });
    });
  
    // เมื่อกดปุ่ม "ยกเลิก"
    // cancelButtons.forEach(button => {
    //   button.addEventListener("click", function () {
    //     const row = this.closest("tr");
    //     const statusCell = row.querySelector(".status");
    //     statusCell.textContent = "ยกเลิก";
    //     statusCell.className = "status red"; // เปลี่ยนสีเป็นแดง
    //   });
    // });
  });
  