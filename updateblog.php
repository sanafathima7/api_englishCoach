<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'connection.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'PUT') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['question_num']) && isset($inputData['user_question'])) {
        $questionNum = $inputData['question_num'];
        $userQuestion = $inputData['user_question'];

        // Validate that question_num is a positive integer
        if (!empty($questionNum) && is_numeric($questionNum) && $questionNum > 0) {
            $stmt = $conn->prepare("UPDATE question_users SET user_question = ? WHERE question_num = ?");
            $stmt->bind_param('si', $userQuestion, $questionNum);

            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'Question updated successfully']);
            } else {
                echo json_encode(['status' => 400, 'message' => 'Failed to update question']);
            }

            $stmt->close();
        } else {
            // Invalid question number
            echo json_encode(['status' => 400, 'message' => 'Invalid question number']);
        }
    } else {
        // Missing required data
        echo json_encode(['status' => 400, 'message' => 'Invalid input data']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 405, 'message' => 'Method not allowed']);
}
?>
