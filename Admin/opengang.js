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
    const selectedCourtSpan = document.getElementById("selected-court");
    const selectedHoursSpan = document.getElementById("selected-hours");
    const clearButton = document.getElementById("clear-button");
    const selectedPriceSpan = document.getElementById("selected-price"); // แสดงราคา
    const submitButton = document.getElementById("submit-button");
    const courtSelect = document.getElementById("court-select");
    const startTimeSelect = document.getElementById("start-time");
    const endTimeSelect = document.getElementById("endtime");
    const inviteCheckbox = document.getElementById("invite-checkbox");
    const inviteMessage = document.getElementById("invite-message");

    document.getElementById("searchFreeDate").addEventListener('click', function () {
            updateDateButton();
            renderBookingTable();
        }
    )

    let servicePrice = 0; // เก็บราคาต่อชั่วโมง

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
                servicePrice = parseFloat(data.price); // ราคาต่อชั่วโมงจากฐานข้อมูล
                console.log("Service Price per Hour:", servicePrice);
            } else {
                console.error("Error fetching service price:", data.message);
            }
        })
        .catch(error => console.error("Error fetching service price:", error));

    // อัปเดตปฏิทิน Flatpickr
    const flatpickrInstance = flatpickr(currentDateButton, {
        dateFormat: "Y-m-d",
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

    // ฟังก์ชันอัปเดตปุ่มวันที่
    function updateDateButton() {
        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });
        currentDateButton.textContent = formattedDate;
        flatpickrInstance.setDate(selectedDate); // อัปเดตปฏิทิน Flatpickr
    }

    // ฟังก์ชันแสดงตารางจอง
    function renderBookingTable() {
        bookingTableBody.innerHTML = '';
        fetch('../php/get_court_details.php')
            .then(response => response.json())
            .then(courts => {
                courts.forEach(court => {
                    if (court.CourtID === 0) return; // Skip CourtID 0

                    const openTime = parseInt(court.OpenTime.split(':')[0], 10);
                    const closeTime = parseInt(court.CloseTime.split(':')[0], 10) || 24; // Change 00:00 to 24
                    const totalSlots = closeTime - openTime;
                    const maxSlots = 13; // Time slots from 11:00 to 00:00

                    let row = `<tr><td>${court.CourtName}</td>`;

                    // Check court status
                    if (court.CourtStatus == 0) {
                        for (let j = 0; j < maxSlots; j++) {
                            row += '<td class="unavailable" style="background-color: gray; color: black; text-align: center;">สนามปิด</td>';
                        }
                    } else {
                        for (let j = 0; j < maxSlots; j++) {
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
                fetchBookedSlots();
            })
            .catch(error => {
                console.error('Error fetching court details:', error);
            });
    }

 // ฟังก์ชันดึงเวลาที่ไม่ว่าง
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
                const startTime = parseInt(slot.startTime.split(':')[0]);
                const endTime = parseInt(slot.endTime.split(':')[0]);

                for (let hour = startTime; hour < endTime; hour++) {
                    const formattedTime = `${hour}:00 - ${hour + 1}:00`;

                    let cell = document.querySelector(`.available[data-court="${slot.court}"][data-time="${formattedTime}"]`);

                    // ถ้าสนามปิด ไม่ต้องย้ายรายการจอง
                    if (!cell && (slot.status === 'รอชำระเงิน' || slot.status === 'รออนุมัติ')) {
                        // ตรวจสอบกรณีที่สนามปิดโดยไม่ต้องย้ายการจอง
                        cell = document.querySelector(`.unavailable[data-court="${slot.court}"][data-time="${formattedTime}"]`);
                    }

                    if (cell) {
                        cell.classList.remove('available', 'unavailable', 'pending', 'pending-approval', 'booked');

                        // ตรวจสอบสถานะการจอง
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
                            cell.textContent = "";
                            cell.style.backgroundColor = "";
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




    // ฟังก์ชันเมื่อกดปุ่มเปลี่ยนวัน
    prevDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() - 1);
        updateDateButton();
        renderBookingTable();
    });
    nextDayButton.addEventListener("click", function () {
        selectedDate.setDate(selectedDate.getDate() + 1);
        updateDateButton();
        renderBookingTable();
    });

    // ฟังก์ชันอัปเดต Info Box
    function updateInfoBox() {
        const startTime = startTimeSelect.value;
        const endTime = endTimeSelect.value;
        const court = courtSelect.value;

        if (!startTime || !endTime || !court) {
            selectedDateSpan.textContent = "";
            selectedTimeSpan.textContent = "";
            selectedHoursSpan.textContent = "";
            selectedCourtSpan.textContent = "";
            selectedPriceSpan.textContent = ""; // ล้างข้อมูลราคา
            return;
        }

        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit"
        });

        const startTimeDate = new Date(`1970-01-01T${startTime}:00`);
        let endTimeDate = new Date(`1970-01-01T${endTime}:00`);


       // 🛠 ถ้า endTime เป็น "00:00" ให้ถือว่าเป็น "24:00" โดยเปลี่ยนชั่วโมงเป็น 24
if (endTime === "00:00") {
    endTimeDate.setHours(24, 0, 0, 0); // กำหนดให้เป็น 24:00 ของวันเดียวกัน
}

const hours = (endTimeDate - startTimeDate) / (1000 * 60 * 60);

if (hours <= 0) {
    Swal.fire({
        icon: 'error',
        title: 'ข้อผิดพลาด!',
        text: '⏰ เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด กรุณาเลือกใหม่',
        confirmButtonColor: '#d33'
    });
    return;
}
        
        const totalPrice = hours * servicePrice; // คำนวณราคา

        selectedDateSpan.textContent = formattedDate;
        selectedTimeSpan.textContent = `${startTime} - ${endTime}`;
        selectedHoursSpan.textContent = hours;
        selectedCourtSpan.textContent = court;
        selectedPriceSpan.textContent = ` ${totalPrice} บาท`; // แสดงราคา
    }

    // ฟังก์ชันล้างข้อมูล
    clearButton.addEventListener("click", function () {
        selectedDate = new Date(); // รีเซ็ตวันที่เป็นปัจจุบัน
        startTimeSelect.value = "";
        endTimeSelect.value = "";
        courtSelect.value = "";
        selectedDateSpan.textContent = "";
        selectedTimeSpan.textContent = "";
        selectedHoursSpan.textContent = "";
        selectedCourtSpan.textContent = "";
        updateDateButton();
        renderBookingTable();
    });

    // ฟังก์ชันยืนยัน
    submitButton.addEventListener('click', function () {
        const startTime = startTimeSelect.value;
        const endTime = endTimeSelect.value;
        const court = courtSelect.value;
    
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
    
        // ตรวจสอบสถานะของสนามที่เลือกว่าเปิดหรือปิด
        fetch('../php/get_court_details.php')
            .then(response => response.json())
            .then(courts => {
                const selectedCourt = courts.find(courtObj => courtObj.CourtID == court);
    
                // ถ้าสนามถูกปิด
                if (selectedCourt && selectedCourt.CourtStatus == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'สนามปิด!',
                        text: 'ไม่สามารถจองสนามที่ปิดได้ กรุณาเลือกสนามอื่น',
                        confirmButtonColor: '#d33'
                    });
                    return; // หยุดกระบวนการจอง
                }
    
                // ถ้าไม่ได้เลือกสนามหรือเวลาผิดพลาด
                if (!startTime || !endTime || !court) {
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
    
                // ทำการส่งข้อมูลการจอง
                const formattedDate = selectedDateFromCalendar.toLocaleDateString("sv-SE"); // ใช้รูปแบบปี-เดือน-วัน
                const bookingData = {
                    date: formattedDate,
                    startTime: startTime,
                    endTime: endTime,
                    court: court
                };
    
                console.log('Booking Data:', bookingData);
    
                // ✅ ใช้ SweetAlert2 เพื่อให้ผู้ใช้ยืนยัน
                Swal.fire({
                    title: 'กรุณายืนยันข้อมูลการจอง',
                    html: `
                        <strong>📅 วันที่จอง:</strong> ${formattedDate}<br>
                        <strong>⏰ เวลาจอง:</strong> ${startTime} น.  -  ${endTime} น.<br>
                        <strong>🏸 สนามที่:</strong> ${court}
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยันการจอง',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../php/save_gangbookingadmin2.php', {
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
                                    window.location.href = '../Admin/Edit%20open%20gang.php';
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
            })
            .catch(error => {
                console.error('Error fetching court details:', error);
            });
    });
    
    
    // แสดงรายละเอียด
    startTimeSelect.addEventListener("change", updateInfoBox);
    endTimeSelect.addEventListener("change", updateInfoBox);
    courtSelect.addEventListener("change", updateInfoBox);

    updateDateButton();
    renderBookingTable();
});