<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('fuctionblog.php');
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request (for preflight checks)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.0 200 OK");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (empty($inputData)) {
        echo json_encode([
            'status' => 400,
            'message' => 'No input data provided'
        ]);
        exit();
    }

    $storeblog = storeblog($inputData);

    echo $storeblog;
} else {
    $data = [
        'status' => 405,
        'message' => $_SERVER['REQUEST_METHOD'] . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
