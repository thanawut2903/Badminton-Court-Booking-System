document.addEventListener('DOMContentLoaded', function() {
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const bookingTableBody = document.getElementById("booking-table-body");
    const urlParams = new URLSearchParams(window.location.search);
    let selectedDate = urlParams.get("date") ? new Date(parseInt(urlParams.get("date"))) : new Date(); // กำหนดวันที่เริ่มต้นเป็นวันที่ปัจจุบัน

  
    // ฟังก์ชันอัปเดตปุ่มวันที่
    function updateDateButton() {
        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });
        console.log(selectedDate)
        currentDateButton.textContent = formattedDate;
        flatpickrInstance.setDate(selectedDate); // อัปเดตปฏิทิน Flatpickr
        
    }
  
    // ฟังก์ชันการแสดงผลตารางจอง
    function renderBookingTable() {
        bookingTableBody.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            let row = `<tr><td>สนาม ${i}</td>`;
            for (let j = 0; j < 12; j++) {
                row += `<td class="available"></td>`;
            }
            row += '</tr>';
            bookingTableBody.innerHTML += row;
        }
    }
  
       // อัปเดตปฏิทิน Flatpickr
       const flatpickrInstance = flatpickr(currentDateButton, {
        dateFormat: "Y-m-d",
        defaultDate: selectedDate,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                for (i = 0; i < selectedDates.length; i++) {
                    console.log(selectedDates[i]);
                  }
                selectedDate = selectedDates[0];
                selectedDate.setTime(selectedDate.getTime()+(7*60*60*1000));
                urlParams.set('date', selectedDate.getTime());
                // urlParams.forEach((element) => {
                //     console.log(element);
                // });                
                window.location.search = urlParams;
                // alert (selectedDate);
                updateDateButton();
            }
        }
    });
    // ฟังก์ชันเมื่อกดปุ่มเปลี่ยนวัน
    prevDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() - 1);
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('date', selectedDate.getTime());
        window.location.search = urlParams;
        updateDateButton();
        renderBookingTable();
    });
    // ฟังก์ชันเมื่อกดปุ่มเปลี่ยนวัน
    nextDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() + 1);
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('date', selectedDate.getTime());
        window.location.search = urlParams;
        updateDateButton();
        renderBookingTable();
    });
  
    // ปรับปุ่มให้แสดงวันที่ปัจจุบันและแสดงตารางเริ่มต้น
    updateDateButton();
    renderBookingTable();
  });
  