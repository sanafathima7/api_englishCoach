<?php
// Use require_once or include_once
error_reporting(0);
include('prilimsanswer.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'POST'){

$inputData = json_decode(file_get_contents("php://input"),true);

if(empty($inputData)){
   
    $storeprelimst = storeprelims($_POST);
}else{

    $storeprelims = storeprelims($inputData);
}

echo $storeprelims;

}

else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method not allowed");
    echo json_encode($data);
}

?>