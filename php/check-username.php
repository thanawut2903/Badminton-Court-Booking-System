<?php
include '../php/dbconnect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"]);

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM account WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode(["exists" => $result["count"] > 0]);
}
?>
