<?php
// ตรวจสอบว่าเป็นคำขอ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากการส่งของ JavaScript
    $data = json_decode(file_get_contents('php://input'), true);
    
    // ตรวจสอบว่ามีข้อมูลที่ต้องการ
    if (isset($data['firstName']) && isset($data['lastName']) && isset($data['username'])) {
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $username = $data['username'];

        // ตรวจสอบข้อมูลให้ปลอดภัย (sanitization)
        $firstName = htmlspecialchars(trim($firstName));
        $lastName = htmlspecialchars(trim($lastName));
        $username = htmlspecialchars(trim($username));

        // เชื่อมต่อฐานข้อมูล
        require 'dbconnect.php'; // เชื่อมต่อฐานข้อมูล

        // สร้างคำสั่ง SQL เพื่อค้นหาข้อมูลที่ซ้ำ
        $query = "SELECT * FROM account WHERE FirstName = ? OR LastName = ? OR Username = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(['error' => 'Error preparing statement']);
            exit;
        }

        $stmt->bind_param('sss', $firstName, $lastName, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // ตรวจสอบผลลัพธ์ที่ได้จากการค้นหาฐานข้อมูล
        if ($result->num_rows > 0) {
            // หากพบข้อมูลที่ซ้ำ
            $exists = true;
            $firstNameExists = false;
            $lastNameExists = false;
            $usernameExists = false;

            // ตรวจสอบแต่ละฟิลด์
            while ($row = $result->fetch_assoc()) {
                if ($row['FirstName'] === $firstName) {
                    $firstNameExists = true;
                }
                if ($row['LastName'] === $lastName) {
                    $lastNameExists = true;
                }
                if ($row['Username'] === $username) {
                    $usernameExists = true;
                }
            }

            // ส่งผลลัพธ์กลับไปให้ JavaScript
            echo json_encode([
                'exists' => $exists,
                'firstNameExists' => $firstNameExists,
                'lastNameExists' => $lastNameExists,
                'usernameExists' => $usernameExists
            ]);
        } else {
            // หากไม่มีข้อมูลซ้ำ
            echo json_encode([
                'exists' => false
            ]);
        }

        $stmt->close();
        $conn->close();

    } else {
        // หากข้อมูลไม่ครบถ้วน
        echo json_encode([
            'error' => 'Missing first name, last name or username data'
        ]);
    }
} else {
    // หากไม่ใช่คำขอ POST
    echo json_encode([
        'error' => 'Invalid request method'
    ]);
}
