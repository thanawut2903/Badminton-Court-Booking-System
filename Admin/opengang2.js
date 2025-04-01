document.addEventListener('DOMContentLoaded', function () {
    const currentDateButton = document.getElementById("currentDate");
    const prevDayButton = document.getElementById("prevDay");
    const nextDayButton = document.getElementById("nextDay");
    const bookingTableBody = document.getElementById("booking-table-body");
    const infoBox = document.getElementById("info-box");
    const selectedDateSpan = document.getElementById("selected-date");
    const selectedTimeSpan = document.getElementById("selected-time");
    const selectedHoursSpan = document.getElementById("selected-hours");
    const calculatedPriceSpan = document.getElementById("calculated-price");
    const clearButton = document.getElementById("clear-button");
    const submitButton = document.getElementById("submit-button");
    const courtSelect = document.getElementById("court-select");
    

    let selectedDate = new Date();
    let selectedSlots = [];

    function updateDateButton() {
        const formattedDate = selectedDate.toLocaleDateString("th-TH", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });
        currentDateButton.textContent = formattedDate;
    }

    function renderBookingTable() {
        bookingTableBody.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            let row = `<tr><td>สนาม ${i}</td>`;
            for (let j = 0; j < 12; j++) {
                row += `<td class="available" data-court="${i}" data-time="${11 + j}:00 - ${12 + j}:00"></td>`;
            }
            row += '</tr>';
            bookingTableBody.innerHTML += row;
        }
        fetchBookedSlots();
    }

    function fetchBookedSlots() {
        fetch('../php/getBookedSlots.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ date: selectedDate.toISOString().split('T')[0] })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.bookedSlots.forEach(slot => {
                    const startTime = parseInt(slot.startTime.split(':')[0]);
                    const endTime = parseInt(slot.endTime.split(':')[0]);

                    for (let hour = startTime; hour < endTime; hour++) {
                        const formattedTime = `${hour}:00 - ${hour + 1}:00`;

                        const cell = document.querySelector(`.available[data-court="${slot.court}"][data-time="${formattedTime}"]`);
                        if (cell) {
                            cell.classList.remove('available');
                            cell.classList.add('booked');
                            cell.textContent = "จองแล้ว";
                            cell.style.backgroundColor = "red";
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
    

    // bookingTableBody.addEventListener("click", function (event) {
    //     if (event.target.classList.contains("available")) {
    //         const selectedCell = event.target;
    //         if (selectedCell.classList.contains("selected")) {
    //             selectedCell.classList.remove("selected");
    //         } else {
    //             selectedCell.classList.add("selected");
    //         }
    //         selectedSlots = Array.from(document.querySelectorAll(".selected")).map(cell => ({
    //             court: cell.getAttribute("data-court"),
    //             time: cell.getAttribute("data-time")
    //         }));
    //         updateInfoBox();
    //     }
    // });

    // function updateInfoBox() {
    //     if (selectedSlots.length > 0) {
    //         const times = selectedSlots.map(slot => slot.time).join(', ');
    //         const hours = selectedSlots.length;
    //         const price = hours * 150;
    //         selectedDateSpan.textContent = currentDateButton.textContent;
    //         selectedTimeSpan.textContent = times;
    //         selectedHoursSpan.textContent = `${hours} ชั่วโมง`;
    //         calculatedPriceSpan.textContent = `${price} บาท`;
    //         infoBox.style.display = 'block';
    //     } else {
    //         infoBox.style.display = 'none';
    //     }
    // }

    function updateInfoBox() {
        const infoBox = document.getElementById("info-box");
        const startTime = document.getElementById('start-time').value;
        const endTime = document.getElementById('end-time').value;
        const court = document.getElementById('court-select').value;
    
        console.log("Start Time:", startTime);
        console.log("End Time:", endTime);
        console.log("Court:", court);
    
        // ตรวจสอบว่าข้อมูลครบถ้วน
        if (!startTime) {
            infoBox.innerHTML = `<p>กรุณาเลือกเวลาเริ่มต้น</p>`;
            return;
        }
    
        if (!endTime) {
            infoBox.innerHTML = `<p>กรุณาเลือกเวลาสิ้นสุด</p>`;
            return;
        }
    
        if (!court) {
            infoBox.innerHTML = `<p>กรุณาเลือกสนาม</p>`;
            return;
        }
    
        // ตรวจสอบความสมเหตุสมผลของเวลา
        if (startTime >= endTime) {
            infoBox.innerHTML = `<p>เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น</p>`;
            return;
        }
    
        // แสดงข้อมูลใน infoBox
        infoBox.innerHTML = `
            <div>
                <p><strong>เวลาเริ่มต้น:</strong> ${startTime}</p>
                <p><strong>เวลาสิ้นสุด:</strong> ${endTime}</p>
                <p><strong>สนาม:</strong> สนาม ${court}</p>
            </div>
        `;
    }
    
    

    clearButton.addEventListener("click", function () {
        selectedSlots = [];
        document.querySelectorAll(".selected").forEach(cell => cell.classList.remove("selected"));
        updateInfoBox();
    });

    submitButton.addEventListener("click", function () {
        if (selectedSlots.length > 0 && courtSelect.value) {
            
            const bookingData = {
                date: selectedDate,//currentDateButton.textContent,
                slots: selectedSlots,
                court: courtSelect.value,
                hours: parseInt(selectedHoursSpan.textContent),
            };

            fetch('../php/save_gangbookingadmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookingData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("เปิดก๊วนสำเร็จ!");
                    location.reload();
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("ไม่สามารถส่งคำขอได้: " + error.message);
            });
        } else {
            alert("กรุณาเลือกช่วงเวลาและสนามก่อนส่งคำขอ!");
        }
    });

    updateDateButton();
    renderBookingTable();
});
