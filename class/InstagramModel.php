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
        return $this->db->query("SELECT 
                                    face_client_id, redirect_uri, instagram_client_id, accessToken, expiresAt, user_id 
                                FROM instagram 
                                WHERE face_client_id = ?", [$face_client_id]);
        
    }
    
    /**
     * 
     * @param array $dados
     */
    public function insert( $dados ){
        $sql = "INSERT INTO users 
                    (face_client_id, face_secret_id, redirect_uri, instagram_client_id, instagram_secret_id, accessToken, expiresAt, user_id) 
                VALUES 
                    (?,?,?,?,?,?,?,?)";
        $stmt= $this->db->prepare($sql);
        return $stmt->execute([$dados['face_client_id'], $dados['face_secret_id'], $dados['redirect_uri'],
                               $dados['instagram_client_id'], $dados['instagram_secret_id'], $dados['accessToken'], 
                               $dados['expiresAt'], $dados['user_id']]);
    }
    
    
    /**
     * 
     * @param array $dados
     * @param string $face_client_id
     */
    public function update($dados, $face_client_id){
        $sql = "UPDATE users SET face_client_id=?, face_secret_id=?, redirect_uri=?,instagram_client_id=?, instagram_secret_id=?, accessToken=?, expiresAt=?, user_id=? WHERE face_client_id=?";
        $stmt= $pdo->prepare($sql);
        return $stmt->execute([$dados['face_secret_id'], $dados['redirect_uri'],
                        $dados['instagram_client_id'], $dados['instagram_secret_id'], $dados['accessToken'],
                        $dados['expiresAt'], $dados['user_id'], $dados['face_client_id']]);
    }
    
}