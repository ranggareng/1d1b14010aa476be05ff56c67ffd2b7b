<?php
include_once "Database.php";

class Authentication{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    function login(){
        try{
            $email = $_POST['email'];
            $pass = $_POST['password'];
            
            if(empty($email) || empty($pass)){
                $response = [
                    'success' => false,
                    'message' => 'Lengkapi email dan password!'
                ];	
            }else{		
                $query = $this->conn->prepare("Select * from m_users where email = :email ");
                $query->bindParam(':email', $email);
                $query->execute();
                $user = $query->fetch();
                
                if($query->rowCount() > 0){
                    if(password_verify($pass, $user['password'])){
                        $response = [
                            'success' => true,
                            'message' => 'Login Success',
                            'data' => [
                                'token' => $this->generateToken($user)
                            ]
                        ];
                        http_response_code(200);
                    }else{
                        $response = [
                            'success' => false,
                            'message' => 'Email atau Password salah!'
                        ];	
                        http_response_code(401);
                    }
                }else{
                    $response = [
                        'success' => false,
                        'message' => 'Email atau Password salah!'
                    ];	
                    http_response_code(401);
                }
            }

        }catch(Exception $e){
            http_response_code(500);
            
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];	
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function generateToken($user)
    {
        $date = date('Y-m-d H:i:s', strtotime('+30 day'));
        
        $token = hash('SHA256', uniqid(40));
        $query = $this->conn->prepare("INSERT INTO access_tokens(user_id,token,expire_at) VALUES(:user,:token,:expire)");
        $query->bindParam(':user', $user['id']);
        $query->bindParam(':token', $token);
        $query->bindParam(':expire', $date);
        $query->execute();

        return $token;
    }

    public function cekToken($token)
    {
        $date = date('Y-m-d H:i:s');

        $query = $this->conn->prepare("Select * from access_tokens where token = :token and expire_at >= :datetime");
        $query->bindParam(':token', $token);
        $query->bindParam(':datetime', $date);
        $query->execute();
        $user = $query->fetch();
        
        if($query->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }
}