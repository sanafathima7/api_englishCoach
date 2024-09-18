<?php

// Enable error reporting for debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // Ensure the user_id is passed as a query parameter
    if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

        // Optionally filter by ispublished if provided in the query
        $ispublished_condition = "";
        if (isset($_GET['ispublished']) && ($_GET['ispublished'] == '0' || $_GET['ispublished'] == '1')) {
            $ispublished = mysqli_real_escape_string($conn, $_GET['ispublished']);
            $ispublished_condition = " AND question_users.ispublished = '$ispublished'";
        }

        // Query to select the user's questions and replies
        $query = "SELECT question_users.user_question,question_users.question_num, question_users.ispublished, question_replys.question_reply 
                  FROM question_users 
                  LEFT JOIN question_replys ON question_users.question_num = question_replys.question_num 
                  WHERE question_users.user_id = '$user_id' $ispublished_condition";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            $data = [
                'status' => 500,
                'message' => 'Error executing query: ' . mysqli_error($conn),
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
            exit();
        }

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
                'message' => 'No questions found for this user',
            ];
            header("HTTP/1.0 204 No Content");
            echo json_encode($data);
        }
    } else {
        // User ID not provided
        $data = [
            'status' => 400,
            'message' => 'User ID not provided',
        ];
        header("HTTP/1.0 400 Bad Request");
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

$conn->close();
