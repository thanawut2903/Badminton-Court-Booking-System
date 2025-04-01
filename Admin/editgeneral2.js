document.addEventListener('DOMContentLoaded', function () {
    const radioButton = document.getElementById('edit-time-checkbox');
    const formContainer = document.getElementById('edit-time-form');
  
    // Event เมื่อเลือก Radio Button
    radioButton.addEventListener('change', function () {
      if (radioButton.checked) {
        formContainer.style.display = 'block'; // แสดงฟอร์ม
      } else {
        formContainer.style.display = 'none'; // ซ่อนฟอร์ม
      }
    });
  });
  