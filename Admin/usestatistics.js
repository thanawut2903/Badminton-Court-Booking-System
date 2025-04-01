// ดึง DOM element ของสนาม, เดือน, และปี
const courtSelector = document.getElementById('court-selector');
const monthSelector = document.getElementById('month-selector');
const yearInput = document.getElementById('year-input');
const ctx = document.getElementById('usageChart').getContext('2d');

// ฟังก์ชันสำหรับ fetch ข้อมูล
async function fetchDataAndUpdateChart(year, month, court) {
    try {
        const formattedMonth = `${year}-${month}`;
        const response = await fetch(`../php/getChartData.php?month=${formattedMonth}&court=${court}`);
        const data = await response.json();

        if (data.success) {
            let chartData;
            if (court === 'all') {
                // กรณีเลือกสนามทั้งหมด
                const chartLabels = data.courts.map(court => court.name);
                const generalBookingData = data.courts.map(court => court.generalBookingHours);
                const groupBookingData = data.courts.map(court => court.groupBookingHours);

                chartData = {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'การจองแบบทั่วไป',
                            backgroundColor: '#00A9FF', // สีของการจองแบบทั่วไป
                            data: generalBookingData,
                            barThickness: 50,
                        },
                        {
                            label: 'การขอเปิดก๊วน',
                            backgroundColor: '#FF0000', // สีของการจองแบบก๊วน
                            data: groupBookingData,
                            barThickness: 50,
                        }
                    ],
                };
            } else {
                // กรณีเลือกสนามเดียว
                chartData = {
                    labels: [`${data.courtName || 'เลือกทั้งหมด'}`],
                    datasets: [
                        {
                            label: 'การจองแบบทั่วไป',
                            backgroundColor: '#00A9FF',
                            data: [data.generalBookingHours],
                            barThickness: 50,
                        },
                        {
                            label: 'การจองแบบก๊วน',
                            backgroundColor: '#FF0000',
                            data: [data.groupBookingHours],
                            barThickness: 50,
                        }
                    ],
                };
            }

            updateChart(chartData);
        } else {
            alert(data.message || 'ไม่พบข้อมูลสำหรับเดือนและสนามที่เลือก');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

// ฟังก์ชันสำหรับอัปเดต Chart
let usageChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true,
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'จำนวนชั่วโมงที่ใช้บริการ'
                },
                ticks: {
                    stepSize: 5, // กำหนดให้แสดงค่าเป็นจำนวนเต็ม
                    callback: function(value) {
                        return Number.isInteger(value) ? value : null;
                    }
                }
            }
        }
    }
});

function updateChart(chartData) {
    usageChart.data = chartData;
    usageChart.update();
}

// Event Listener สำหรับเปลี่ยนค่าเดือน, ปี, และสนาม
function updateChartData() {
    const selectedYear = yearInput.value;
    const selectedMonth = monthSelector.value;
    const selectedCourt = courtSelector.value;

    if (selectedYear && selectedMonth && selectedCourt) {
        fetchDataAndUpdateChart(selectedYear, selectedMonth, selectedCourt);
    } else {
        alert('กรุณากรอกปีและเลือกเดือนและสนาม');
    }
}

yearInput.addEventListener('input', updateChartData);
monthSelector.addEventListener('change', updateChartData);
courtSelector.addEventListener('change', updateChartData);

// โหลดข้อมูลเริ่มต้นสำหรับเดือนและสนามปัจจุบัน
const currentYear = new Date().getFullYear();
const currentMonth = new Date().toISOString().slice(5, 7); // รูปแบบ MM
yearInput.value = currentYear;
monthSelector.value = currentMonth;
const defaultCourt = courtSelector.options[0].value; // สมมติว่าค่าของสนามแรกคือ default
fetchDataAndUpdateChart(currentYear, currentMonth, defaultCourt);
