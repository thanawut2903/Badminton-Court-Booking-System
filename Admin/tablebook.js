const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
let selectedDate = (urlParams.get('date')) ? new Date(urlParams.get('date') + 'T00:00:00') : new Date();
selectedDate.setHours(0, 0, 0, 0);



document.addEventListener('DOMContentLoaded', function () {
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const bookingTableBody = document.getElementById("booking-table-body");
    let servicePrice = 0;

    document.getElementById("searchFreeDate").addEventListener('click', function () {
        updateDateButton();
        renderBookingTable();
    });

    // Fetch service price from the database
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

    // Initialize Flatpickr for date selection
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

    // Update the current date button
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
                const startTime = parseInt(slot.startTime.split(':')[0]);
                const endTime = parseInt(slot.endTime.split(':')[0]);

                for (let hour = startTime; hour < endTime; hour++) {
                    const formattedTime = `${hour}:00 - ${hour + 1}:00`;

                    let cell = document.querySelector(`.available[data-court="${slot.court}"][data-time="${formattedTime}"]`);

                    // 📌 ถ้าสนามเดิมปิด ให้หา "สนามที่เปิดอยู่" เพื่อย้ายการจอง
                    if (!cell && (slot.status === 'รอชำระเงิน' || slot.status === 'รออนุมัติ')) {
                        let moved = false;
                        // ค้นหาสนามที่ว่างสำหรับย้ายการจอง
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

                    // จัดการสถานะการจองในกรณีที่พบ cell
                    if (cell) {
                        cell.classList.remove('available');
                        // ตั้งค่าสถานะตามประเภทการจอง
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




    
    

    

    // Event listeners for navigation buttons
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
