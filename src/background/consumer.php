<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once __DIR__.'/../api/Database.php';
include_once __DIR__.'/../api/authentication.php';
include __DIR__ . '/../vendor/autoload.php';

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

$producer = new \Simple\Queue\Producer($transport);
$consumer = new \Simple\Queue\Consumer($transport, $producer);

echo 'Start consuming' . PHP_EOL;

while (true) {

    if ($message = $transport->fetchMessage(['my_queue'])) {

        // Your message handling logic

        $consumer->acknowledge($message);
        $content = unserialize($message->getBody());

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        
            //Recipients
            $mail->setFrom($content['from'], $content['from_name']);
            $mail->addAddress($content['to']);     //Add a recipient
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Email Sender';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            if (!$mail->send()) {
                throw new Exception($mail->ErrorInfo);
            } else {
                $pdoCon = (new Database)->connect();
                $datetime = date('Y-m-d H:i:s');
                $query = $pdoCon->prepare("INSERT INTO email_sent(user_id,sent_at,content) VALUES('1',:sent_at,:content)");
                $query->bindParam(':sent_at', $datetime);
                $query->bindParam(':content', json_encode($content));
                $query->execute();
            }

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        echo sprintf('Received message: %s ', $message->getBody());

        echo PHP_EOL;
    }

}