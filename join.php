<?php

// Enable error reporting for debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('fuctionblog.php');
require 'connection.php'; // Ensure the connection.php file connects to the database

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.0 200 OK");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'GET') {
    // Ensure the connection is established
    if (!$conn) {
        $data = [
            'status' => 500,
            'message' => 'Database connection failed'
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }

    // Query to select the user's questions and replies where the questions are published
    $query = "SELECT question_users.user_question, question_users.ispublished,question_replys.question_reply 
              FROM question_users 
              INNER JOIN question_replys ON question_users.question_num = question_replys.question_num 
              WHERE question_users.ispublished = 1";

    $result = mysqli_query($conn, $query);

    // Check if there are any results
    if (mysqli_num_rows($result) > 0) {
        $questions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $questions[] = $row;
        }

        // Return the results in JSON format
        echo json_encode($questions);
    } else {
        // No results found
        $data = [
            'status' => 204,
            'message' => 'No published questions found',
        ];
        header("HTTP/1.0 204 No Content");
        echo json_encode($data);
    }

} else {
    // Respond with 405 Method Not Allowed for non-GET requests
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>
