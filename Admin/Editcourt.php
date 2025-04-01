<?php
session_start();
include '../php/admin_navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง6</title>
  <!-- Bootstrap CSS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editcourt.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php'; ?>

  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>การจัดการสนาม</h1>
      </div>
    </div>

    <div class="container">
<?php
require '../php/dbconnect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_idadmin'])) {
  echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
  exit;
}

$accountID = $_SESSION['user_idadmin']; // ดึง AccountID จาก Session

// Query ข้อมูลการจองที่เกี่ยวข้องกับ AccountID ที่ล็อกอินอยู่
$sql = "SELECT CourtID,CourtName,CourtStatus,OpenTime,CloseTime FROM court WHERE CourtID != 0 ";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

  echo '<table class="booking-table" id="editcourt">';
  echo '<thead>
  <tr>
      <th>รายการ</th>
      <th id="CourtName">ชื่อสนาม</th>
      <th id="OpenTime">เวลาเปิด-ปิดของสนาม</th>
      <th id="CourtStatus">สถานะของสนาม</th>
      <th id="Editdatacourt">จัดการสนาม</th>
  </tr>
</thead>';
echo '<tbody>';
}
$index = 1;

// แสดงข้อมูลในแต่ละแถว
while ($row = $result->fetch_assoc()) {
$checked = $row['CourtStatus'] == 1 ? 'checked' : '';
  echo '<tr>';
      echo '<td>' . $index . '</td>';
      echo '<td>' . $row['CourtName'] . '</td>';
      echo '<td>' . date('H:i', strtotime($row['OpenTime'])) . ' - ' . 
      ($row['CloseTime'] == '24:00:00' ? '24:00' : date('H:i', strtotime($row['CloseTime']))) . ' น.</td>';  
      echo '<td>
                    <label class="switch">
                        <input type="checkbox" class="status-switch" onChange="activeFunction(this,'.$row["CourtID"].')"  data-account-id="' . $row['CourtID'] . '" ' . $checked . '>
                        <span class="slider"></span>
                    </label>
                  </td>';
    echo '<td>';
    echo '<button class="btn edit" onclick="window.location.href=\'/web_badmintaon_khlong_6/Admin/Editdatacourt.php?courtID=' . $row['CourtID'] . '\'">แก้ไขสนาม</button>';

    if (isset($row["CourtID"])) {
        // สร้างปุ่มลบสนามโดยใช้ echo
        echo '<button class="btn delete" style="margin-left:10px" onclick="deleteCourt(' . $row["CourtID"] . ')">ลบสนาม</button>';
    } else {
        echo '<p style="color: red;">Error: ไม่พบตัวแปร $courtID</p>';
    }
      $index++;
  }
  echo '</tbody>';
  echo '</table>';

?>

<script>
// Event listener สำหรับการเปลี่ยนสถานะ
const switches = document.querySelectorAll('.status-switch');

const putStatusCourt = (id, status) => {
    fetch('../php/update_status_court.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ CourtID: id, CourtStatus: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: 'สถานะของสนามได้รับการอัปเดตเรียบร้อย',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        location.reload(); // รีเฟรชหน้า
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
            });
};

const activeFunction = (ele, id) => {
    if (ele.checked) {
        putStatusCourt(id, 1);
    } else {
        Swal.fire({
            title: 'ยืนยันการปิดใช้งาน?',
            text: "คุณต้องการปิดการใช้งานสนาม ID: " + id + " ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ปิดเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../php/check_booking_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ CourtID: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hasBookings) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถปิดสนามได้!',
                            text: 'เนื่องจากมีการจองอยู่ในระบบ',
                            confirmButtonColor: '#d33'
                        });
                        ele.checked = true; // เปลี่ยนสถานะกลับไปเปิด
                    } else {
                        putStatusCourt(id, 0);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถตรวจสอบสถานะการจองได้',
                        confirmButtonColor: '#d33'
                    });
                    ele.checked = true; // เปลี่ยนสถานะกลับไปเปิด
                });
            } else {
                ele.checked = true; // เปลี่ยนสถานะกลับไปเปิดหากยกเลิก
            }
        });
    }
};

switches.forEach(switchElement => {
    switchElement.addEventListener('change', function() {
        const CourtID = this.getAttribute('data-court-id');
        const newStatus = this.checked ? 1 : 0;

        // ส่งคำขอ AJAX เพื่ออัปเดตสถานะในฐานข้อมูล
        fetch('../php/update_status_court.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ CourtID, CourtStatus: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'สถานะของสนามได้รับการอัปเดตเรียบร้อย',
                    confirmButtonColor: '#3085d6'
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
        });
    });
});

// ฟังก์ชันสำหรับการลบสนาม
function deleteCourt(courtID) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: 'คุณแน่ใจหรือไม่ว่าต้องการลบสนาม ID: ' + courtID + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/web_badmintaon_khlong_6/php/delete_court.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ CourtID: courtID })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: 'สนามถูกลบเรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
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
                    text: 'ไม่สามารถลบสนามได้',
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
}
</script>


   </div>
  </div> 
  <div class="addcourt">
    <button class="btn add" onclick="confirmAddCourt()">เพิ่มสนาม</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAddCourt() {
        Swal.fire({
            title: "ยืนยันการเพิ่มสนาม?",
            text: "คุณต้องการไปที่หน้าสร้างสนามใหม่หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "ใช่, ไปเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/web_badmintaon_khlong_6/Admin/Addcourt.php";
            }
        });
    }
</script>

    <!-- Bootstrap JS -->
    <script src="listgroup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
