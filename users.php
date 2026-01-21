<?php
require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Only GET method allowed"]);
    exit;
}

try {
    $conn = oci_db_connect();

    $sql = "SELECT * FROM USERS";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    $users = [];
    while ($row = oci_fetch_assoc($stid)) {
        $users[] = $row;
    }

    oci_free_statement($stid);
    oci_close($conn);

    echo json_encode([
        "success" => true,
        "data" => $users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Users fetch failed",
        "error" => $e->getMessage()
    ]);
}
