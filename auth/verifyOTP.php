<?php
session_start();

// Accept JSON POST input
$data = json_decode(file_get_contents("php://input"), true);

// Simple validation
if (isset($data['verified']) && $data['verified'] === true) {
    $_SESSION['is_verified'] = true;
    echo json_encode(["status" => "ok"]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "failed", "message" => "Invalid request"]);
}
?>
