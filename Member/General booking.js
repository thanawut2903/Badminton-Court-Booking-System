const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
let selectedDate = (urlParams.get('date')) 
    ? new Date(urlParams.get('date') + 'T00:00:00') 
    : new Date();
selectedDate.setHours(0, 0, 0, 0); 

document.addEventListener('DOMContentLoaded', function () {
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const bookingTableBody = document.getElementById("booking-table-body");
    const infoBox = document.getElementById("info-box");
    const selectedDateSpan = document.getElementById("selected-date");
    const selectedTimeSpan = document.getElementById("selected-time");
    const selectedHoursSpan = document.getElementById("selected-hours");
    const clearButton = document.getElementById("clear-button");
    const selectedPriceSpan = document.getElementById("selected-price");
    const submitButton = document.getElementById("submit-button");
    const startTimeSelect = document.getElementById("start-time");
    const endTimeSelect = document.getElementById("endtime");

    let servicePrice = 0;


    document.getElementById("searchFreeDate").addEventListener('click', function () {
        updateDateButton();
        renderBookingTable();
    }
)



    // ดึงราคาค่าบริการจากฐานข้อมูล
    fetch('../php/getServicePrice.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                servicePrice = parseFloat(data.price);
                console.log("Service Price per Hour:", servicePrice);
            } else {
                console.error("Error fetching service price:", data.message);
            }
        })
        .catch(error => console.error("Error fetching service price:", error));

    // อัปเดตปฏิทิน Flatpickr
    const flatpickrInstance = flatpickr(currentDateButton, {
        dateFormat: "Y-m-d", // รูปแบบวันที่และเวลาพร้อมนาที,
        defaultDate: selectedDate,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                selectedDate = selectedDates[0];
                selectedDate.setHours(0, 0, 0, 0);
                updateDateButton();
                renderBookingTable();
            }
        }
    });

    function updateDateButton() {
        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });
        currentDateButton.textContent = formattedDate;
        flatpickrInstance.setDate(selectedDate);
    }

// ฟังก์ชันแสดงตารางจอง
function renderBookingTable() {
    bookingTableBody.innerHTML = '';
    let availableCourtsCount = 0; // ตัวนับจำนวนสนามที่สามารถจองได้
    let closedCourtsCount = 0; // ตัวนับจำนวนสนามที่ปิด

    fetch('../php/get_court_details.php')
        .then(response => response.json())
        .then(courts => {
            courts.forEach(court => {
                if (court.CourtID === 0) return; // ข้ามสนามที่มี CourtID เป็น 0

                const openTime = parseInt(court.OpenTime.split(':')[0], 10);
                const closeTime = parseInt(court.CloseTime.split(':')[0], 10) || 24; // เปลี่ยน 00:00 เป็น 24
                const totalSlots = closeTime - openTime;
                const maxSlots = 13; // ช่วงเวลาจาก 11:00 ถึง 00:00

                let row = `<tr><td>${court.CourtName}</td>`;

                // ตรวจสอบสถานะสนาม 1-4 ถ้าเต็ม
                if (court.CourtStatus == 0) {
                    closedCourtsCount++; // เพิ่มจำนวนสนามที่ปิด
                    for (let j = 0; j < maxSlots; j++) {
                        row += '<td class="unavailable" style="background-color: gray; color: black; text-align: center;">สนามปิด</td>';
                    }
                } else {
                    availableCourtsCount++; // เพิ่มจำนวนสนามที่เปิด
                    for (let j = 0; j < maxSlots; j++) {
                        // ตรวจสอบว่าเวลาที่สนามเปิดและปิดสามารถจองได้หรือไม่
                        if (j < totalSlots && (openTime + j) >= 11 && (openTime + j) < closeTime) {
                            row += `<td class="available" data-court="${court.CourtID}" data-time="${openTime + j}:00 - ${openTime + j + 1}:00"></td>`;
                        } else {
                            row += '<td class="unavailable" style="background-color: gray; color: black; text-align: center;">สนามปิด</td>';
                        }
                    }
                }

                row += '</tr>';
                bookingTableBody.innerHTML += row;
            });

            // เรียกดูการจองที่ทำไปแล้ว
            fetchBookedSlots(); // ดึงข้อมูลการจองที่ทำไปแล้ว

            // แสดงจำนวนสนามที่เปิดและปิด
            console.log(`Total Courts: ${courts.length}`);
            console.log(`Available Courts: ${availableCourtsCount}`);
            console.log(`Closed Courts: ${closedCourtsCount}`);

            // แสดงผลลัพธ์ในส่วน UI (ถ้าต้องการแสดงในหน้าเว็บ)
            const infoBox = document.getElementById("info-box");
            infoBox.textContent = `มีทั้งหมด ${courts.length} สนาม: สนามที่สามารถจองได้ ${availableCourtsCount} สนาม, สนามที่ปิด ${closedCourtsCount} สนาม`;
        })
        .catch(error => {
            console.error('Error fetching court details:', error);
        });
}

 // Fetch booked slots and update the table
 function fetchBookedSlots() {
    fetch('../php/getBookedSlotsAll.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ date: selectedDate.toLocaleDateString("sv-SE") })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const availableCourts = {}; // เก็บสถานะสนามที่เปิด

            // หา court ที่เปิดอยู่
            document.querySelectorAll(".available[data-court]").forEach(cell => {
                const courtId = cell.dataset.court;
                if (!availableCourts[courtId]) {
                    availableCourts[courtId] = true;
                }
            });

            data.bookedSlots.forEach(slot => {
                const startTime = parseInt(slot.startTime.split(':')[0], 10);  // ใช้ฐาน 10
                const endTime = parseInt(slot.endTime.split(':')[0], 10);  // ใช้ฐาน 10

                // ตรวจสอบแต่ละชั่วโมงจาก startTime ถึง endTime
                for (let hour = startTime; hour < endTime; hour++) {
                    const formattedTime = `${hour}:00 - ${hour + 1}:00`;

                    let cell = document.querySelector(`.available[data-court="${slot.court}"][data-time="${formattedTime}"]`);

                    // 📌 ถ้าสนามเดิมปิด ให้หา "สนามที่เปิดอยู่" เพื่อย้ายการจอง
                    if (!cell && (slot.status === 'รอชำระเงิน' || slot.status === 'รออนุมัติ')) {
                        let moved = false;
                        // ตรวจสอบการทับซ้อนของเวลา
                        for (let courtId in availableCourts) {
                            const newCell = document.querySelector(`.available[data-court="${courtId}"][data-time="${formattedTime}"]`);
                            if (newCell) {
                                cell = newCell;
                                slot.court = courtId; // ย้ายการจองไปสนามใหม่
                                moved = true;
                                break;
                            }
                        }

                        // ถ้าย้ายไม่สำเร็จให้ไม่ทำอะไร
                        if (!moved) {
                            return; // หยุดทำงานหากไม่สามารถย้ายไปสนามใหม่ได้
                        }
                    }

                    // ถ้ามีเซลล์ (สนามที่จอง)
                    if (cell) {
                        cell.classList.remove('available');
                        
                        // ตรวจสอบสถานะและแสดงสถานะที่สนามใหม่
                        if (slot.status === 'เปิดก๊วนสำเร็จ' || slot.status === 'จองสำเร็จ') {
                            cell.classList.add('booked');
                            cell.textContent = "จองแล้ว";
                            cell.style.backgroundColor = "red";
                        } else if (slot.status === 'รอชำระเงิน') {
                            cell.classList.add('pending');
                            cell.textContent = "รอชำระเงิน";
                            cell.style.backgroundColor = "orange";
                        } else if (slot.status === 'รออนุมัติ') {
                            cell.classList.add('pending-approval');
                            cell.textContent = "รออนุมัติ";
                            cell.style.backgroundColor = "#34e1eb"; // สีฟ้า
                        } else if (slot.status === 'ยกเลิก') {
                            cell.classList.add('available');
                        }
                    }
                }
            });
        } else {
            console.error("Failed to fetch booked slots: ", data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching booked slots:', error);
    });
}



    prevDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() - 1);
        updateDateButton();
        renderBookingTable();
    });

// ดึงค่า maxAdvanceDays จาก data-* attribute
const maxAdvanceDaysElement = document.getElementById('booking-settings');
const maxAdvanceDays = maxAdvanceDaysElement ? parseInt(maxAdvanceDaysElement.getAttribute('data-max-advance-days')) : 0;

console.log(maxAdvanceDays);  // ตรวจสอบค่าใน JavaScript

const today = new Date(); // วันที่ปัจจุบัน

// ตรวจสอบว่า nextDayButton และ selectedDate ถูกกำหนดไว้หรือไม่
if (typeof nextDayButton !== 'undefined' && nextDayButton !== null && selectedDate) {
    nextDayButton.addEventListener("click", function () {
        let tempDate = new Date(selectedDate);
        tempDate.setDate(tempDate.getDate() + 1);

        // ตรวจสอบว่าวันที่ใหม่อยู่ภายในช่วงที่อนุญาตหรือไม่
        const maxAllowedDate = new Date(today);
        maxAllowedDate.setDate(today.getDate() + maxAdvanceDays);  // เพิ่มวันที่ที่จองล่วงหน้าได้

        // ตรวจสอบว่า tempDate อยู่ภายในช่วงที่อนุญาตหรือไม่
        if (tempDate <= maxAllowedDate) {
            selectedDate = tempDate;
            updateDateButton();
            renderBookingTable();
        } else {
            // ใช้ SweetAlert แทนการใช้ alert() ปกติ
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถจองเกิน ' + maxAdvanceDays + ' วันล่วงหน้าได้',
                text: 'กรุณาเลือกวันที่ใหม่ที่ไม่เกินจำนวนวันที่จองล่วงหน้า',
                confirmButtonText: 'ตกลง'
            });
        }
    });
} else {
    console.error('nextDayButton หรือ selectedDate ไม่ถูกต้อง');
}

    
    function updateInfoBox() {
        const startTime = startTimeSelect.value;
        const endTime = endTimeSelect.value;

        if (!startTime || !endTime) {
            selectedDateSpan.textContent = "";
            selectedTimeSpan.textContent = "";
            selectedHoursSpan.textContent = "";
            selectedPriceSpan.textContent = "";
            return;
        }

        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit"
        });

        const startTimeDate = new Date(`1970-01-01T${startTime}:00`);
        const endTimeDate = new Date(`1970-01-01T${endTime}:00`);
        const hours = (endTimeDate - startTimeDate) / (1000 * 60 * 60);

        if (hours <= 0) {
            // Show SweetAlert when hours are not valid
            Swal.fire({
                icon: 'error',
                title: 'เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด',
                text: 'กรุณาเลือกเวลใหม่อีกครั้ง',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                // Clear the fields after the user confirms the SweetAlert
                startTimeSelect.value = "";
                endTimeSelect.value = "";
                selectedDateSpan.textContent = "";
                selectedTimeSpan.textContent = "";
                selectedHoursSpan.textContent = "";
                selectedPriceSpan.textContent = "";
                
                updateDateButton();  // Update the button state after clearing
                renderBookingTable();  // Render the booking table again
            });
            return;
        }

        const totalPrice = hours * servicePrice;

        selectedDateSpan.textContent = formattedDate;
        selectedTimeSpan.textContent = `${startTime} - ${endTime}`;
        selectedHoursSpan.textContent = hours;
        selectedPriceSpan.textContent = ` ${totalPrice} บาท`;
    }

    clearButton.addEventListener("click", function () {
        selectedDate = new Date();
        startTimeSelect.value = "";
        endTimeSelect.value = "";
        selectedDateSpan.textContent = "";
        selectedTimeSpan.textContent = "";
        selectedHoursSpan.textContent = "";
        selectedPriceSpan.textContent = "";
        updateDateButton();
        renderBookingTable();
    });

    submitButton.addEventListener('click', function () {
        const startTime = startTimeSelect.value;
        let endTime = endTimeSelect.value;
    
        const selectedDateFromCalendar = currentDateButton._flatpickr.selectedDates[0];
    
        if (!selectedDateFromCalendar) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถดึงวันที่จากปฏิทินได้',
                confirmButtonColor: '#d33'
            });
            return;
        }
    
        // ✅ แปลงวันที่เป็นรูปแบบภาษาไทย เช่น "เสาร์ ที่ 01 กุมภาพันธ์ 2025"
        function formatThaiDate(date) {
            const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
    
            const day = thaiDays[date.getDay()];
            const dateNumber = date.getDate().toString().padStart(2, '0'); // เพิ่ม 0 ข้างหน้า
            const month = thaiMonths[date.getMonth()];
            const year = date.getFullYear() + 543; // ✅ แปลงเป็น พ.ศ.
        
            return `${day} ที่ ${dateNumber} ${month} ${year}`;
        }
    
        // ✅ เก็บวันที่ในสองรูปแบบ
        const formattedDateThai = formatThaiDate(selectedDateFromCalendar); // "เสาร์ ที่ 01 กุมภาพันธ์ 2025"
        const formattedDateISO = selectedDateFromCalendar.toLocaleDateString("sv-SE"); // ✅ ให้ได้ YYYY-MM-DD ตรงโซนเวลาของเครื่อง


            // ตรวจสอบว่าเป็นวันปัจจุบันหรือไม่
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const selectedDate = new Date(selectedDateFromCalendar);
    selectedDate.setHours(0, 0, 0, 0);

    const now = new Date();
    const selectedStartTime = new Date(selectedDateFromCalendar);
    const [startHour, startMinute] = startTime.split(":").map(Number);
    selectedStartTime.setHours(startHour, startMinute, 0, 0);

    if (selectedDate.getTime() === today.getTime() && selectedStartTime <= now) {
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถจองเวลาในอดีตได้!',
            text: 'กรุณาเลือกเวลาที่มากกว่าปัจจุบัน',
            confirmButtonColor: '#d33'
        });
        return;
    }
    
        if (!startTime || !endTime) {
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ครบถ้วน!',
                text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                confirmButtonColor: '#f39c12'
            });
            return;
        }


    
        if (startTime >= endTime) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด!',
                text: 'เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด',
                confirmButtonColor: '#d33'
            });
            return;
        }
    
        // ✅ คำนวณราคาทั้งหมด
        const startTimeDate = new Date(`1970-01-01T${startTime}:00`);
        const endTimeDate = new Date(`1970-01-01T${endTime}:00`);
        const hours = (endTimeDate - startTimeDate) / (1000 * 60 * 60);
    
        if (!servicePrice || isNaN(servicePrice)) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด!',
                text: 'ไม่สามารถดึงราคาบริการได้ กรุณาลองใหม่',
                confirmButtonColor: '#d33'
            });
            return;
        }
    
        const totalPrice = hours * servicePrice;
    
        if (totalPrice <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด!',
                text: 'ราคาค่าบริการไม่ถูกต้อง',
                confirmButtonColor: '#d33'
            });
            return;
        }


        // ✅ ใช้ SweetAlert2 เพื่อให้ผู้ใช้ยืนยัน
        Swal.fire({
            title: 'กรุณายืนยันข้อมูลการจอง',
            html: `
                <strong>📅 วันที่จอง:</strong> ${formattedDateThai}<br>
                <strong>⏰ เวลาจอง:</strong> ${startTime} น.  -  ${endTime} น.<br>
                <strong>💰 ราคาทั้งหมด:</strong> ${totalPrice} บาท
            `,
            icon: 'info',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ยืนยันการจอง',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // ✅ ถ้าผู้ใช้กด "ยืนยัน"
                const bookingData = {
                    date: formattedDateISO, // ใช้รูปแบบ YYYY-MM-DD ส่งไปที่ PHP
                    startTime: startTime,
                    endTime: endTime,
                    totalPrice: totalPrice
                };
    
                console.log('📌 Booking Data ส่งไป PHP:', bookingData);

                // ✅ ตรวจสอบว่าผู้ใช้เลือกวันที่ในอดีตหรือไม่
                const today = new Date();
                today.setHours(0, 0, 0, 0); // รีเซ็ตเวลาให้เป็นเที่ยงคืนของวันปัจจุบัน

                if (selectedDate < today) {
                Swal.fire({
                icon: "error",
                title: "ไม่สามารถจองวันในอดีตได้!",
                text: "กรุณาเลือกวันที่ตั้งแต่วันนี้เป็นต้นไป",
                confirmButtonColor: "#d33",
            });
            return;
            }
                fetch('../php/save_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(bookingData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกข้อมูลสำเร็จ!',
                            text: 'การจองของคุณถูกบันทึกเรียบร้อยแล้ว',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = '../Member/Booking history.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonColor: '#d33'
                    });
                });
                
            }
        });
    });
    

    

    startTimeSelect.addEventListener("change", updateInfoBox);
    endTimeSelect.addEventListener("change", updateInfoBox);

    updateDateButton();
    renderBookingTable();
});
