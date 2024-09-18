<?php

// Enable error reporting for debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connection.php'; // Ensure the connection.php file connects to the database

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.0 200 OK");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'DELETE') {
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

    // Ensure the question_num is passed in the query string
    if (isset($_GET['question_num']) && !empty($_GET['question_num'])) {
        $question_num = mysqli_real_escape_string($conn, $_GET['question_num']);

        // Query to delete the question
        $query = "DELETE FROM question_users WHERE question_num = '$question_num'";

        if (mysqli_query($conn, $query)) {
            // Check if the question was deleted
            if (mysqli_affected_rows($conn) > 0) {
                $data = [
                    'status' => 200,
                    'message' => 'Question deleted successfully',
                ];
                echo json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No question found with that number',
                ];
                header("HTTP/1.0 404 Not Found");
                echo json_encode($data);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Error deleting question',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    } else {
        // Question number not provided
        $data = [
            'status' => 400,
            'message' => 'Question number not provided',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
    }

} else {
    // Respond with 405 Method Not Allowed for non-DELETE requests
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

$conn->close();
?>
