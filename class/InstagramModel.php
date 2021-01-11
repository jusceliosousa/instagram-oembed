<?php
require_once 'Conexao.php';

class InstagramModel
{
    protected $db;
    
    public function __construct() {
        $this->db = Conexao::getInstance();
    }
    
    /**
     * 
     * @param string $face_client_id
     */
    public function getRowByFaceClientId( $face_client_id ) {
        $query = "SELECT
                    face_client_id, redirect_uri, instagram_client_id, accessToken, expiresAt, user_id
                FROM instagram
                WHERE face_client_id = :face_client_id";
       
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':face_client_id', $face_client_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject();
    }
    
    /**
     * 
     * @param array $dados
     */
    public function insert( $dados ){
        try{
            $sql = "INSERT INTO instagram 
                        (face_client_id, 
                        face_secret_id, 
                        redirect_uri, 
                        instagram_client_id, 
                        instagram_secret_id, 
                        accessToken, 
                        expiresAt, 
                        user_id) 
                    VALUES 
                        (:face_client_id, 
                        :face_secret_id, 
                        :redirect_uri, 
                        :instagram_client_id, 
                        :instagram_secret_id, 
                        :accessToken, 
                        :expiresAt, 
                        :user_id)";
            $stmt= $this->db->prepare($sql);
            
            $stmt->execute([
                    ':face_client_id' => $dados['face_client_id'],
                    ':face_secret_id' => $dados['face_secret_id'],
                    ':redirect_uri' => $dados['redirect_uri'],
                    ':instagram_client_id' => $dados['instagram_client_id'],
                    ':instagram_secret_id' => $dados['instagram_secret_id'],
                    ':accessToken' => NULL,
                    ':expiresAt' => NULL,
                    ':user_id' => NULL
                ]);
            return $stmt->rowCount();
        }catch(PDOException $e) {
            echo 'Error: ' . $e->getMessage();exit;
        }
    }
    
    
    /**
     * 
     * @param array $dados
     * @param string $face_client_id
     */
    public function update($dados, $face_client_id){
        $sql = "UPDATE instagram SET 
                    face_secret_id = :face_secret_id, 
                    redirect_uri = :redirect_uri,
                    instagram_client_id = :instagram_client_id, 
                    instagram_secret_id = :instagram_secret_id, 
                    accessToken = :accessToken, 
                    expiresAt = :expiresAt, 
                    user_id = :user_id 
                WHERE face_client_id = :face_client_id";
        $stmt= $this->db->prepare($sql);
        
        $stmt->execute([
            ':face_secret_id' => $dados['face_secret_id'],
            ':redirect_uri' => $dados['redirect_uri'],
            ':instagram_client_id' => $dados['instagram_client_id'],
            ':instagram_secret_id' => $dados['instagram_secret_id'],
            ':accessToken' => $dados['accessToken'],
            ':expiresAt' => $dados['expiresAt'],
            ':user_id' => $dados['user_id'],
            ':face_client_id' => $dados['face_client_id']
        ]);
        return $stmt->rowCount();
    }
    
}