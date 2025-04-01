<?php
require '../php/dbconnect.php';

header('Content-Type: application/json');

try {
    // Query เพื่อดึงราคาค่าบริการจาก infoID = 13
    $sql = "SELECT ItemDetail FROM info WHERE infoID = 13";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $servicePrice = $row['ItemDetail']; // ดึงราคาค่าบริการ

        echo json_encode([
            'success' => true,
            'price' => $servicePrice
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลราคาค่าบริการ'
        ]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>
