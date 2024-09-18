<?php
     header('Access-Control-Allow-Origin:*');
     header('Content-Type: Application/json');
     header('Access-Control-Allow-Method:GET');
     header('Access-Control-Allow-Headers:Content-Type,Access-Control-Allow-Headers,Authorization,X-Request-With');

     include('functionreply.php');
     $requestMethod = $_SERVER["REQUEST_METHOD"];

     if($requestMethod == 'GET') {
        
        if(isset($_GET['exe_num'])) {
          $customer = getCustomer($_GET);
          echo $customer;
        }

        else {
          $modulereply = modulereply();
        echo $modulereply;
        }        
      }       
      else {
        $data = [
            'status' => 405,
            'message' => $requestMethod . 'Method Not Allowed',
        ];
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($data);
     }
?>