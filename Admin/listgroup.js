document.addEventListener('DOMContentLoaded', function () {
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const urlParams = new URLSearchParams(window.location.search);

    // อ่านค่า date จาก URL ถ้ามี, ถ้าไม่มีให้ใช้วันที่ปัจจุบัน
    let selectedDate;
    if (urlParams.get("date")) {
        selectedDate = new Date(parseInt(urlParams.get("date")));
    } else {
        selectedDate = new Date();
    }

    // ป้องกันปัญหา Time Zone โดยตั้งเวลาเป็น 12:00:00 (แทน 00:00:00)
    selectedDate.setHours(12, 0, 0, 0);  // ตั้งเวลาเป็น 12:00:00

    console.log("Initial selectedDate:", selectedDate.toISOString());

    // สร้าง Flatpickr instance
    const flatpickrInstance = flatpickr(currentDateButton, {
        dateFormat: "Y-m-d",
        defaultDate: selectedDate.toISOString().split('T')[0], // แปลงเป็น YYYY-MM-DD
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                selectedDate = selectedDates[0];
                selectedDate.setHours(12, 0, 0, 0);  // ตั้งเวลาเป็น 12:00:00

                urlParams.set('date', selectedDate.getTime());
                window.location.href = "?" + urlParams.toString();

                console.log("After flatpickr change:", selectedDate.toISOString());
            }
        }
    });

    // ฟังก์ชันอัปเดตปุ่มวันที่
    function updateDateButton() {
        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });

        currentDateButton.textContent = formattedDate;
        flatpickrInstance.setDate(selectedDate.toISOString().split('T')[0]);
    }

    // ปุ่มเปลี่ยนวัน (ย้อนกลับ)
    prevDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() - 1);
        selectedDate.setHours(12, 0, 0, 0);

        urlParams.set('date', selectedDate.getTime());
        window.location.href = "?" + urlParams.toString();

        console.log("After prevDay button click:", selectedDate.toISOString());
    });

    // ปุ่มเปลี่ยนวัน (ไปข้างหน้า)
    nextDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() + 1);
        selectedDate.setHours(12, 0, 0, 0);

        urlParams.set('date', selectedDate.getTime());
        window.location.href = "?" + urlParams.toString();

        console.log("After nextDay button click:", selectedDate.toISOString());
    });

    // อัปเดตปุ่มวันที่หลังจากตั้งค่า selectedDate ที่ถูกต้อง
    updateDateButton();  // เรียกฟังก์ชันเพื่ออัปเดตวันที่
});
