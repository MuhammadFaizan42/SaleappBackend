<?php
require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Only POST method allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$email = $input["email"] ?? "";
$password = $input["password"] ?? "";

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

$conn = oci_db_connect();

// Find user by email
$sql = "SELECT ID, NAME, EMAIL, PASSWORD FROM USERS WHERE EMAIL = :email";
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ":email", $email);
oci_execute($stid);

$user = oci_fetch_assoc($stid);

oci_free_statement($stid);
oci_close($conn);

if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    exit;
}

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
