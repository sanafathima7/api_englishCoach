<?php

require 'connection.php';

//error message 

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

//delete



function deleteblog($data) {
    if (!isset($data['question_num'])) {
        return json_encode(['status' => 400, 'message' => 'Question number not provided']);
    }

    $questionNum = $data['question_num'];

    // Database connection
    $conn = new mysqli('localhost', 'username', 'password', 'database');

    if ($conn->connect_error) {
        return json_encode(['status' => 500, 'message' => 'Database connection failed']);
    }

    // Delete the question
    $stmt = $conn->prepare("DELETE FROM question_users WHERE question_num = ?");
    $stmt->bind_param("i", $questionNum);

    if ($stmt->execute()) {
        return json_encode(['status' => 200, 'message' => 'Question deleted successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Error deleting question']);
    }

    $stmt->close();
    $conn->close();
}



function updateblog($data) {
    if (!isset($data['question_num']) || !isset($data['user_question'])) {
        return json_encode(['status' => 400, 'message' => 'Question number or text not provided']);
    }

    $questionNum = $data['question_num'];
    $userQuestion = $data['user_question'];

    // Database connection
    $conn = new mysqli('localhost', 'username', 'password', 'database');

    if ($conn->connect_error) {
        return json_encode(['status' => 500, 'message' => 'Database connection failed']);
    }

    $stmt = $conn->prepare("UPDATE question_users SET user_question = ? WHERE question_num = ?");
    $stmt->bind_param("si", $userQuestion, $questionNum);

    if ($stmt->execute()) {
        return json_encode(['status' => 200, 'message' => 'Question updated successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Error updating question: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}



//post

function storeblog($moduleInput) {
    global $conn;

    // Prepare the SQL query
    $query = "INSERT INTO `question_users` (`question_num`, `mod_id`, `t_num`, `user_question`, `ispublished`, `user_id`) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'iisssi', 
        $moduleInput['question_num'], 
        $moduleInput['mod_id'], 
        $moduleInput['t_num'], 
        $moduleInput['user_question'], 
        $moduleInput['ispublished'], 
        $moduleInput['user_id']
    );

    // Execute the query
    $result = mysqli_stmt_execute($stmt);

    // Check the result
    if ($result) {
        $data = [
            'status' => 201,
            'message' => 'Question created successfully',
        ];
        header("HTTP/1.0 201 Created");
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}




//get

function getblog() {
    global $conn;

    $query = "SELECT * FROM `question_users`";
    $query_run = mysqli_query($conn,$query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
        
            $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);


            // Return a JSON array of Customer objects
            $data = array();
            foreach ($res as $row) {
                $customer = array(
                    'question_num' => $row['question_num'],
                    'mod_id' => $row['mod_id'],
                    't_num' => $row['t_num'],
                    'user_question' => $row['user_question'],
                    'ispublished' => $row['ispublished'],
                    'user_id' => $row['user_id'],

                );
                array_push($data, $customer);
            }
            header("Content-Type: application/json");
            return json_encode($data);
            
        }else{
            $data = array(
                'status' => 404,
                'message' => 'No module Found',
            );
            header("HTTP/1.0 404  No module Found");
            return json_encode($data);
        }
        
    }else{
        $data = array(
            'status' => 500,
            'message' => 'Internal Server Error',
        );
        header("HTTP/1.0 500  Internal Server Error");
        return json_encode($data);
    }
}




//1 to 1 data fetching

// function getmodulesexcersicesList(){

// global $conn;



// $query = "SELECT * FROM `edu_module_exercises`";
// $result = mysqli_query($conn,$query);

// if($result){

//  if(mysqli_num_rows($result) > 0){
 
//     $res = mysqli_fetch_assoc($result);
//     $data =$res;
//     header("HTTP/1.0 200  Success");
//     return json_encode($data);


//  }else{
//  $data = [
//         'status' => 404,
//         'message' => 'No edu_preliminary_trans_questions Found',
//     ];
//     header("HTTP/1.0 404  Not found");
//     return json_encode($data);
//  }

// }else{
//     $data = [
//         'status' => 500,
//         'message' => 'Internal Server Error',
//     ];
//     header("HTTP/1.0 500  Internal Server Error");
//     return json_encode($data);
// }

// }

?>
