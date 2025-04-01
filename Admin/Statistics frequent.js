// const ctx = document.getElementById('usageChart').getContext('2d');

// // ข้อมูลกราฟ
// const data = {
//     labels: ['11 น.', '12 น.', '13 น.', '14 น.', '15 น.', '16 น.', '17 น.', '18 น.', '19 น.', '20 น.', '21 น.', '22 น.', '23 น.'],
//     datasets: [
//         {
//             label: 'จำนวนการใช้แบบทั่วไป',
//             data: [10, 10, 8, 12, 7, 11, 20, 30, 15, 20, 25, 20, 15],
//             backgroundColor: '#00A9FF',
//             stack: 'combined'
//         },
//         {
//             label: 'จำนวนการใช้แบบก๊วน',
//             data: [0, 0, 0, 0, 0, 0, 5, 5, 5, 5, 5, 5, 0],
//             backgroundColor: '#FF3B3B',
//             stack: 'combined'
//         }
//     ]
// };

// // ตัวเลือกกราฟ
// const options = {
//     responsive: true,
//     plugins: {
//         legend: { position: 'top' },
//         tooltip: { enabled: true }
//     },
//     scales: {
//         x: {
//             title: { display: true, text: 'เวลาใช้บริการ' },
//             stacked: true
//         },
//         y: {
//             title: { display: true, text: 'จำนวนชั่วโมง' },
//             beginAtZero: true,
//             stacked: true
//         }
//     }
// };

// // วาดกราฟ
// const myChart = new Chart(ctx, {
//     type: 'bar',
//     data: data,
//     options: options
// });

// // ปุ่มค้นหา (ถ้าต้องการรองรับการเปลี่ยนข้อมูล)
// document.getElementById('search-button').addEventListener('click', () => {
//     alert('คุณสามารถทำการกรองข้อมูลเพิ่มเติมได้ที่นี่!');
// });
