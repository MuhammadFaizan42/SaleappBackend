<?php
require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Only POST method allowed"]);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

$email = $input["email"] ?? "";
$password = $input["password"] ?? "";

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

try {
    $db = db();

    // ⚠️ Make sure table USERS exists and columns EMAIL, PASSWORD exist
    $sql = "SELECT ID, NAME, EMAIL, PASSWORD FROM USERS WHERE EMAIL = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        exit;
    }

    // Password Verify (DB me password hashed hona chahiye)
    if (!password_verify($password, $user["PASSWORD"])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user" => [
            "id" => $user["ID"],
            "name" => $user["NAME"],
            "email" => $user["EMAIL"]
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Login failed",
        "error" => $e->getMessage()
    ]);
}
