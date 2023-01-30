<?php
ob_start();
require_once 'authentication.php';
require_once "email.php";

/**
 * File index.php ini menjadi gerbang utama dalam
 * mengakses API yang ada diaplikasi ini.
 * File ini bertindak sebagai routing system agar 
 * webserver tidak mengakses langsung class-class
 * sistem
 */

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$base = "/api";

 switch ($url){
    case $base.'/email':

        $email = new Email();

        switch ($method){
            case 'POST':
                checkAuth();
                $email->store();
                break;
            default:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
        }

        break;
    case $base.'/token':
        $auth = new Authentication();
        
        switch ($method){
            case 'POST':
                $auth->login();
                break;
            default:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
        }

        break;
    default:
        header("HTTP/1.0 404 Not Found");
        break;
}

function checkAuth()
{
    $headers = $_SERVER['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'];

    if(empty($headers)){
        setResponse(401);
    }else{
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            $token = $matches[1];
            $auth = new Authentication();

            if($auth->cekToken($token))
                return true;
            else
                setResponse(401);
        }else{
            setResponse(401);
        }
    }
}

function setResponse($code){
    http_response_code($code);

    switch($code){
        case 401:
            $response = [
                'success' => false,
                'message' => 'Unauthenticated'
            ];
            break;
        default:
            $response = [
                'success' => false,
                'message' => ''
            ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit();
}