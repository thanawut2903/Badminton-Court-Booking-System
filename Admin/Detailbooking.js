const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
let selectedDate = (urlParams.get('date')) ? new Date(urlParams.get('date')) : new Date();

document.addEventListener('DOMContentLoaded', function () {    
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const bookingTableBody = document.getElementById("booking-table-body");
    let servicePrice = 0;


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
        dateFormat: "Y-m-d",
        defaultDate: selectedDate,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                selectedDate = selectedDates[0];
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

// ฟังก์ชันในการหาสนามที่ว่าง
// ฟังก์ชันค้นหาสนามว่างที่สามารถจองได้
function findAvailableCourt(startTime, endTime, bookedSlots, availableCourts) {
    for (let courtId of availableCourts) {
        let isAvailable = true;
        
        // ตรวจสอบว่ามีการจองทับซ้อนหรือไม่
        for (let slot of bookedSlots) {
            // ตรวจสอบว่าเวลาจองซ้อนกับสนามนี้หรือไม่
            if (slot.court === courtId) {
                let bookedStart = parseInt(slot.startTime.trim().split(':')[0], 10);
                let bookedEnd = parseInt(slot.endTime.trim().split(':')[0], 10);
                
                // ถ้าเวลาทับซ้อนกัน
                if ((startTime >= bookedStart && startTime < bookedEnd) || (endTime > bookedStart && endTime <= bookedEnd)) {
                    isAvailable = false; // ถ้าสนามถูกจองแล้ว
                    break;
                }
            }
        }

        if (isAvailable) {
            return courtId; // หากสนามนี้ว่าง ให้คืนสนามนี้
        }
    }

    return null; // ถ้าไม่พบสนามที่ว่าง
}

    // ฟังก์ชันแสดงตารางจอง
// ฟังก์ชันแสดงตารางจอง
// Render booking table
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

// ฟังก์ชันดึงการจองที่ทำไปแล้ว
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

            // 📌 ดึงค่าช่วงเวลาจาก input hidden
            const userBookingStart = parseInt(document.getElementById('bookingstarttime').value.split(':')[0], 10);
            const userBookingEnd = parseInt(document.getElementById('bookingendtime').value.split(':')[0], 10);

            console.log("🟣 เวลาจองของผู้ใช้:", userBookingStart, "-", userBookingEnd);

            // หา court ที่เปิดอยู่
            document.querySelectorAll(".available[data-court]").forEach(cell => {
                const courtId = cell.dataset.court;
                if (!availableCourts[courtId]) {
                    availableCourts[courtId] = true;
                }
            });

            data.bookedSlots.forEach(slot => {
                const startTime = parseInt(slot.startTime.split(':')[0], 10);
                const endTime = parseInt(slot.endTime.split(':')[0], 10);

                for (let hour = startTime; hour < endTime; hour++) {
                    const formattedTime = `${hour}:00 - ${hour + 1}:00`;

                    let cell = document.querySelector(`.available[data-court="${slot.court}"][data-time="${formattedTime}"]`);

                    // 📌 ถ้าสนามเดิมปิด ให้หา "สนามที่เปิดอยู่" เพื่อย้ายการจอง
                    if (!cell && (slot.status === 'รอชำระเงิน' || slot.status === 'รออนุมัติ')) {
                        let moved = false;

                        // ค้นหาสนามที่เปิดอยู่ในช่วงเวลานี้
                        for (let courtId in availableCourts) {
                            const newCell = document.querySelector(`.available[data-court="${courtId}"][data-time="${formattedTime}"]`);
                            if (newCell) {
                                cell = newCell;  // ย้ายการจองไปสนามใหม่
                                slot.court = courtId; // เปลี่ยนสนามให้กับการจอง
                                moved = true;
                                break;
                            }
                        }

                        // ถ้าย้ายไม่สำเร็จให้ไม่ทำอะไร
                        if (!moved) {
                            console.warn(`ไม่สามารถย้ายการจองไปสนามอื่นได้ในช่วงเวลา ${formattedTime}`);
                            return; // หยุดทำงานหากไม่สามารถย้ายไปสนามใหม่ได้
                        }
                    }

                    if (cell) {
                        cell.classList.remove('available');
                        // ✅ ถ้าสถานะเป็น "รออนุมัติ" และช่วงเวลาตรงกับ input hidden → เปลี่ยนเป็นสีม่วง
                        if (slot.status === 'รออนุมัติ' && startTime === userBookingStart && endTime === userBookingEnd) {
                            cell.classList.add('pending-approval');
                            cell.textContent = "รออนุมัติ";
                            cell.style.backgroundColor = "purple"; // สีม่วง
                        } else if (slot.status === 'เปิดก๊วนสำเร็จ' || slot.status === 'จองสำเร็จ') {
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
                            cell.style.backgroundColor = "#34e1eb"; // สีฟ้า (สำหรับรายการที่ไม่ใช่ของผู้ใช้)
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

    updateDateButton();
    renderBookingTable();
});
