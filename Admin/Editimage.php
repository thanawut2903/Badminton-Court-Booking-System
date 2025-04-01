<?php
session_start();
include '../php/admin_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เว็บจองสนามแบดมินตัน คลอง 6</title>
  <!-- Bootstrap CSS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- สำหรับไอคอน -->
  <!-- ลิงก์ไปยังไฟล์ CSS -->
  <link href="Editimage.css" rel="stylesheet">
</head>
<body>
  
<?php include '../php/admin_menu.php'; ?>


  <div class="container mt-4">
    <!-- กล่องข้อความ -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 5vh;">
      <div class="custom-box text-center">
        <h1>แก้ไขรูปภาพหน้าหลัก</h1>
      </div>
    </div>

<?php
require '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

$stmt = $conn->prepare("SELECT ImageID, ImagePath FROM Image ORDER BY ImageID ASC");
$stmt->execute();
$result = $stmt->get_result();

?>
<div class="custom-box2">
  <div class="table-container">
    <h3>รูปภาพหน้าหลัก</h3>
    <table class="image-table">
      <thead>
        <tr>
          <th>รายการ</th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $index = 1;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($index) . '</td>';
              $imagePath = '../' . htmlspecialchars($row['ImagePath']);
              if (file_exists($imagePath)) {
                  echo '<td><img src="' . $imagePath . '" alt="รูปภาพ" class="image-thumbnail" onclick="showImage(this)" /></td>';
              } else {
                  echo '<td><p>ไม่พบรูปภาพ</p></td>';
              }

              echo '<td>';
              echo '<form action="../php/image_management.php" method="POST" enctype="multipart/form-data" style="display: inline;" onsubmit="return confirm(\'คุณต้องการแก้ไขรูปภาพนี้หรือไม่?\');">';
              echo '<input type="hidden" name="action" value="edit">';
              echo '<input type="hidden" name="ImageID" value="' . htmlspecialchars($row['ImageID']) . '" />';
              echo '<input type="file" name="newImage" accept="image/*" required style="margin-bottom: 10px;" />';
              echo '<button type="submit" class="edit-button">แก้ไขรูปภาพ</button>';
              echo '</form>';

              echo '<form id="form-delete-' . htmlspecialchars($row['ImageID']) . '" action="../php/image_management.php" method="POST" style="display: inline;" onsubmit="return confirmDelete(' . htmlspecialchars($row['ImageID']) . ');">';
              echo '<input type="hidden" name="action" value="delete">';
              echo '<input type="hidden" name="ImageID" value="' . htmlspecialchars($row['ImageID']) . '" />';
              echo '<button type="submit" class="delete-button">ลบรูปภาพ</button>';
              echo '</form>';
              echo '</td>';
              echo '</tr>';
              $index++;
          }
      } else {
          echo '<tr><td colspan="3">ไม่มีข้อมูลรูปภาพ</td></tr>';
      }
      ?>
      </tbody>
    </table>
  </div>

  <div class="content-container d-flex justify-content-center mt-4">
    <div class="custom-boxfile text-center p-4 bg-light rounded shadow-lg border" style="max-width: 600px; width: 100%;">
       <!-- เพิ่ม trigger ที่ใช้คลิกเพื่อเลือกไฟล์ -->
       <h3 id="upload-trigger" class="text-dark fw-bold mb-3 upload-hover" style="cursor: pointer;margin-top:-10px">เพิ่มรูปภาพ</h3>
       <form id="form-add" action="../php/image_management.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmAdd();">
    <input type="hidden" name="action" value="add">
    <div class="file-upload-container d-flex flex-column align-items-center">
        <!-- ช่องสำหรับอัปโหลดไฟล์ -->
        <input type="file" id="image-upload" name="ImagePath" accept="image/*" required class="form-control-file d-none" onchange="updateFileName()">
        <div class="d-flex align-items-center justify-content-center mt-4"style="margin-bottom:-200px">
            <!-- แสดงชื่อไฟล์ที่เลือก -->
            <span id="file-name" class="text-muted me-3">ยังไม่ได้เลือกไฟล์</span>
            <button type="submit" class="btn btn-success fw-bold" style="width: 120px;">✅ ยืนยัน</button>
            </div>
            </div>
            <p style="font-size: 14px; color: red; margin-top: 70px;">
    อัปโหลดไฟล์รูปภาพที่มีนามสกุล .jpg, .jpeg, .png เท่านั้น <br>
    ขนาดไฟล์ไม่เกิน 2 MB
  </p>
        </div>
    </div>
</form>
    </div>
</div>

<style>
/* เพิ่มเอฟเฟกต์เมื่อ hover */
.upload-hover {
    transition: color 0.3s ease, transform 0.2s ease;
}

.upload-hover:hover {
    transform: scale(1.1); /* ขยายเล็กน้อยเมื่อ hover */
}
</style>

<script>
document.getElementById("upload-trigger").addEventListener("click", function() {
    document.getElementById("image-upload").click();
});

function updateFileName() {
    const input = document.getElementById("image-upload");
    const fileNameDisplay = document.getElementById("file-name");
    const previewImg = document.getElementById("preview-img");

    if (input.files.length > 0) {
        const file = input.files[0];

        // Check if the file type is JPG, JPEG, or PNG
        const validTypes = ["image/jpeg", "image/png"];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                title: "ประเภทไฟล์ไม่ถูกต้อง!",
                text: "กรุณาเลือกไฟล์ที่มีประเภท .jpg, .jpeg หรือ .png เท่านั้น",
                icon: "error",
                confirmButtonText: "ตกลง"
            });
            input.value = ""; // Clear the selected file
            fileNameDisplay.textContent = "ยังไม่ได้เลือกไฟล์";
            previewImg.src = ""; // Clear the preview image
            previewImg.classList.add("d-none");
            return;
        }

        // File size check (must be less than 2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                title: "ไฟล์ใหญ่เกินไป!",
                text: "กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 2MB",
                icon: "error",
                confirmButtonText: "ตกลง"
            });
            input.value = ""; // Clear the selected file
            fileNameDisplay.textContent = "ยังไม่ได้เลือกไฟล์";
            previewImg.src = ""; // Clear the preview image
            previewImg.classList.add("d-none");
            return;
        }

        fileNameDisplay.textContent = file.name;
        fileNameDisplay.classList.remove("text-muted");
        fileNameDisplay.classList.add("text-dark", "fw-semibold");

        // Show the preview image
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewImg.classList.remove("d-none");
        };
        reader.readAsDataURL(file);
    } else {
        fileNameDisplay.textContent = "ยังไม่ได้เลือกไฟล์";
        previewImg.src = "";
        previewImg.classList.add("d-none");
    }
}



function confirmAdd() {
    // นับจำนวนรูปภาพในตาราง
    const imageCount = document.querySelectorAll(".image-table tbody tr").length;

    // จำกัดจำนวนรูปสูงสุดที่ 20 รูป
    if (imageCount >= 20) {
        Swal.fire({
            title: "ไม่สามารถเพิ่มรูปภาพได้!",
            text: "คุณสามารถเพิ่มรูปภาพได้สูงสุด 20 รูป",
            icon: "warning",
            confirmButtonText: "ตกลง"
        });
        return false;
    }

    Swal.fire({
        title: "ยืนยันการเพิ่มรูปภาพ?",
        text: "คุณต้องการเพิ่มรูปภาพนี้ใช่หรือไม่?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4CAF50",
        cancelButtonColor: "#d33",
        confirmButtonText: "ใช่, เพิ่มเลย!",
        cancelButtonText: "ยกเลิก"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("form-add").submit();
        }
    });

    return false; // ป้องกันการส่งฟอร์มโดยอัตโนมัติ
}

function confirmDelete(imageID) {
    Swal.fire({
        title: "คุณแน่ใจหรือไม่?",
        text: "คุณต้องการลบรูปภาพนี้หรือไม่?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "ใช่, ลบเลย!",
        cancelButtonText: "ยกเลิก"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("form-delete-" + imageID).submit();
        }
    });

    return false;
}

document.getElementById("file-upload").addEventListener("change", function () {
    const fileName = this.files[0] ? this.files[0].name : "ยังไม่ได้เลือกไฟล์";
    document.getElementById("ImagePath").textContent = fileName;
  })

  function showImage(imgElement) {
    Swal.fire({
        imageUrl: imgElement.src,
        imageWidth: 1000,
        imageAlt: 'รูปภาพขยาย',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'swal-wide'
        }
    });
}
</script>



<?php
$conn->close();
?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
