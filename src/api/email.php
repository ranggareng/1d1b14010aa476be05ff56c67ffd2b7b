<?php
include __DIR__ . '/../vendor/autoload.php';

class Email{
    public $koneksiDB;

    public function __construct()
    {
        
    }

    public function store()
    {
        try{
            $connection = \Doctrine\DBAL\DriverManager::getConnection([
                'dbname' => 'devdb',
                'user' => 'devuser',
                'password' => 'devsecret',
                'host' => 'db',
                'port' => '5432',
                'driver' => 'pdo_pgsql',
            ]);
            
            $transport = new \Simple\Queue\Transport\DoctrineDbalTransport($connection);

            // create table for queue messages
            $transport->init();

            $producer = new \Simple\Queue\Producer($transport, null);
            $message = $producer->createMessage('my_queue', [
                'to'=> $_POST['to'],
                'from' => $_POST['from'],
                'from_name' => $_POST['from_name'],
                'cc' => $_POST['cc'],
                'bcc' => $_POST['bcc'],
                'body' => $_POST['body']
            ]);
            $producer->send($message);

            http_response_code(200);
            $response = [
                'success' => true,
                'message' => 'Success'
            ];
        }catch(Exception $e){
            http_response_code(500);
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }
}