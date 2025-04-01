<?php
require '../php/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Debug: ตรวจสอบค่าที่ส่งมาจากฟอร์ม
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
    
    // ✅ ถ้าต้องการให้โค้ดทำงาน ให้คอมเมนต์ exit ออก
    // exit;

    if (!isset($_POST['action'])) {
        die("<script>alert('ไม่มีค่า action ถูกส่งมา'); window.history.back();</script>");
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            if (isset($_FILES['ImagePath']) && $_FILES['ImagePath']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['ImagePath']['tmp_name'];
                $fileName = $_FILES['ImagePath']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $safeFileName = preg_replace('/[^a-zA-Z0-9-_]/', '', pathinfo($fileName, PATHINFO_FILENAME));
                    $newFileName = 'uploads/' . $safeFileName . '.' . $fileExtension;
                    $destPath = '../' . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $query = "INSERT INTO image (ImagePath) VALUES (?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $newFileName);

                        if ($stmt->execute()) {
                            echo "<script>alert('✅ เพิ่มรูปภาพสำเร็จ'); window.location.href = '../Admin/Editimage.php';</script>";
                        } else {
                            echo "<script>alert('❌ บันทึกข้อมูลไม่สำเร็จ'); window.history.back();</script>";
                        }
                    } else {
                        echo "<script>alert('❌ การอัปโหลดล้มเหลว'); window.history.back();</script>";
                    }
                } else {
                    echo "<script>alert('⚠️ ประเภทไฟล์ไม่รองรับ'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('⚠️ กรุณาเลือกไฟล์รูปภาพ'); window.history.back();</script>";
            }
            break;

        case 'edit':
            if (isset($_POST['ImageID']) && isset($_FILES['newImage'])) {
                $imageId = intval($_POST['ImageID']);
                $fileTmpPath = $_FILES['newImage']['tmp_name'];
                $fileName = $_FILES['newImage']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $safeFileName = preg_replace('/[^a-zA-Z0-9-_]/', '', pathinfo($fileName, PATHINFO_FILENAME));
                    $newFileName = 'uploads/' . $safeFileName . '.' . $fileExtension;
                    $destPath = '../' . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $query = "UPDATE image SET ImagePath = ? WHERE ImageID = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("si", $newFileName, $imageId);

                        if ($stmt->execute()) {
                            echo "<script>alert('✅ แก้ไขรูปภาพสำเร็จ'); window.location.href = '../Admin/Editimage.php';</script>";
                        } else {
                            echo "<script>alert('❌ แก้ไขข้อมูลในฐานข้อมูลไม่สำเร็จ'); window.history.back();</script>";
                        }
                    } else {
                        echo "<script>alert('❌ การอัปโหลดล้มเหลว'); window.history.back();</script>";
                    }
                } else {
                    echo "<script>alert('⚠️ ประเภทไฟล์ไม่รองรับ'); window.history.back();</script>";
                }
            }
            break;

        case 'delete':
            if (isset($_POST['ImageID'])) {
                $imageId = intval($_POST['ImageID']);

                // ✅ ดึงที่อยู่ไฟล์จากฐานข้อมูล
                $query = "SELECT ImagePath FROM image WHERE ImageID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $imageId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $imagePath = '../' . $row['ImagePath'];

                    // ✅ ลบไฟล์ออกจากโฟลเดอร์
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }

                    // ✅ ลบข้อมูลในฐานข้อมูล
                    $query = "DELETE FROM image WHERE ImageID = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $imageId);

                    if ($stmt->execute()) {
                        echo "<script>alert('✅ ลบรูปภาพสำเร็จ'); window.location.href = '../Admin/Editimage.php';</script>";
                    } else {
                        echo "<script>alert('❌ การลบข้อมูลล้มเหลว'); window.history.back();</script>";
                    }
                } else {
                    echo "<script>alert('❌ ไม่พบข้อมูลรูปภาพ'); window.history.back();</script>";
                }
            }
            break;

        default:
            echo "<script>alert('⚠️ คำสั่งไม่ถูกต้อง'); window.history.back();</script>";
            break;
    }
} else {
    echo "<script>alert('⚠️ วิธีการร้องขอไม่ถูกต้อง'); window.history.back();</script>";
}

$conn->close();
?>
