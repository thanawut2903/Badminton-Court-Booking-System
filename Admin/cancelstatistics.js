document.addEventListener('DOMContentLoaded', function() {
    const yearInput = document.getElementById('year-input');
    const monthSelector = document.getElementById('month-selector');
    let cancellationChart = null;

    // Fetch data from PHP and update the chart
    function fetchData() {
        const year = yearInput.value || new Date().getFullYear();  // ถ้าไม่ได้กรอกปีจะใช้ปีปัจจุบัน
        const month = monthSelector.value;
    
        console.log(`Fetching data for Year: ${year} Month: ${month}`);
    
        fetch(`../php/fetch_cancellation_data.php?year=${year}&month=${month}`)
        .then(response => response.text())  // ใช้ .text() แทน .json() เพื่อดูข้อมูลดิบที่ได้รับ
        .then(text => {
            console.log("Response Text:", text);  // พิมพ์ข้อมูลที่ได้รับจาก PHP
            try {
                const data = JSON.parse(text);  // พยายามแปลงเป็น JSON
                updateChart(data);
            } catch (error) {
                console.error("Error parsing JSON:", error);  // ถ้าแปลง JSON ไม่ได้
            }
        })        
            .catch(error => console.error('Error fetching data:', error));
    }
    // Event listener for changes in year or month
    yearInput.addEventListener('change', fetchData);
    monthSelector.addEventListener('change', fetchData);

    // Function to update the chart
    function updateChart(data) {
        const ctx = document.getElementById('cancellationChart').getContext('2d');
    
        if (cancellationChart) {
            cancellationChart.destroy();  // ลบกราฟเก่า
        }
    
        cancellationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.CancelReason),  // แสดงเหตุผลการยกเลิก
                datasets: [{
                    label: 'จำนวนครั้ง',
                    data: data.map(item => item.CancelCount),  // แสดงจำนวนครั้งที่ยกเลิก
                    backgroundColor: ['#00A9FF', '#FF9800', '#FF0000', '#4CAF50'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'จำนวนครั้ง' },
                    },
                },
            },
        });
    }

    fetchData();  // Initial call to fetch the data when the page loads
});
