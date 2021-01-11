<?php 
/*
 * Constantes de parâmetros para configuração da autenticação OAUTH2
 */
define('FACE_CLIENT_ID', '/* CLIENT_ID */');
define('FACE_SECRET_ID', '/* SECRET_ID */');
define('REDIRECT_URI', '/* REDIRECT_URI */');
define('INSTAGRAM_CLIENT_ID', '/* INSTAGRAM_CLIENT_ID */');
define('INSTAGRAM_SECRET_ID', '/* INSTAGRAM_SECRET_ID */');

class InstagramOauth2 {

    private static $instance;
    private $face_client_id;
    private $face_secret_id;
    private $redirect_uri;
    private $instagram_client_id;
    private $instagram_secret_id;
    private $accessToken;
    private $expiresAt;
    private $user_id;
    
    
    private function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    private function __wakeup()
    {
    }
  

    public static function instance() {
      if(self::$instance === null){
          self::$instance = new self;

          self::$instance->setFaceClientId( FACE_CLIENT_ID );
          self::$instance->setFaceSecretId( FACE_SECRET_ID );
          self::$instance->setFaceRedirectURI( REDIRECT_URI );
          self::$instance->setInstragramClientId( INSTAGRAM_CLIENT_ID );
          self::$instance->setInstragramSecretId( INSTAGRAM_SECRET_ID );
      }
      return self::$instance;
    }
    
   
    public function setFaceClientId($face_client_id){
        $this->face_client_id = $face_client_id;
    }
    
    public function getFaceClientId(){
        return $this->face_client_id;
    }
    
    public function setFaceSecretId($face_secret_id){
        $this->face_secret_id = $face_secret_id;
    }
    
    
    public function setFaceRedirectURI($redirect_uri){
        $this->redirect_uri = $redirect_uri;
    }
    
    
    public function setInstragramClientId($instagram_client_id){
        $this->instagram_client_id = $instagram_client_id;
    }
    
    
    public function setInstragramSecretId($instagram_secret_id){
        $this->instagram_secret_id = $instagram_secret_id;
    }
    
    public function setAccessToken($token){
        $this->accessToken = $token;
    }
    
    public function setExpiresAt($expires_in){
        $this->expiresAt = $expires_in;
    }
    
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    public function setUserId($id){
        $this->user_id = $id;
    }
    
    public function getAccessToken(){
        return $this->accessToken;
    }
    
    
    /**
     * Redireciona para autenticação no instagram
     * 
     * @param array $scope
     * @return string
     */
    public function getLoginUrl($scope = array('user_profile','user_media'))
    {
        $params = array(
              'client_id' => $this->face_client_id,
              'redirect_uri' => $this->redirect_uri,
              'scope' => implode(',', $scope),
              'response_type' => 'code'
        );
        return 'https://api.instagram.com/oauth/authorize?' .
              http_build_query($params, null, '&');
    }
    
    
    /** 
    *   curl -k -X POST \
    *     https://api.instagram.com/oauth/access_token \
    *   -F client_id=451961462486666 \
    *   -F client_secret=8cc024bebea10b1a442dac54812568e7 \
    *   -F grant_type=authorization_code \
    *   -F redirect_uri=https://canaleducacao.tv/login/instagram \
    *   -F code=AQC3lVJ3mp10u6rqMz6qGZ4NeXHbJOOF0dRGrcMSwse46vjV55TZRV63bLIfaVos9nDcGVbtrXSR8oKK8KOC6VdVwCi6VcYQ9pp83gRJwrFlzSRbWUwDBRyujuQNo8f2CkJ-iwiLOM0iOegMmgWrf_66A8SdrtPIDrIB12IWAz_BSCFwMlvuGJoADZ8Nuxv68S4zw-DPhI8Zq3zm6quE7Mt1VsFFrGG2E8YoXxLjTFkdiA
    *   
    */
    public function getAccessTokenByCode($code)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/oauth/access_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        $fields = "client_id={$this->face_client_id}&";
        $fields .= "client_secret={$this->face_secret_id}&";
        $fields .= "grant_type=authorization_code&";
        $fields .= "redirect_uri={$this->redirect_uri}&";
        $fields .= "code={$code}";
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($ch);
        $obj_access_token = json_decode($response);
        
        $this->setAccessToken($obj_access_token->access_token);
        $this->setUserId($obj_access_token->user_id);
        
        curl_close($ch);
        return $response;
    }
    
    /**
    * https://graph.instagram.com/access_token
    *     ?grant_type=ig_exchange_token
    *     &client_secret={instagram-app-secret}
    *     &access_token=
    */
    public function getAccessTokenLongaDuracao()
    {
        $params = array(
          'grant_type' => 'ig_exchange_token',
          'client_secret' => $this->face_secret_id,
          'access_token' => $this->accessToken
        );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://graph.instagram.com/access_token?' . http_build_query($params, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        $obj_access_token = json_decode($response);
        
        $this->setAccessToken($obj_access_token->access_token);
        $this->setExpiresAt($obj_access_token->expires_in);
        
        curl_close($ch);
        
        return $response;
    }
    
    
    
    /**
     * curl -i -X GET "https://graph.instagram.com/refresh_access_token
     *    ?grant_type=ig_refresh_token
     *    &access_token={long-lived-access-token}"
     */
    public function RefreshAccessToken()
    {
        $params = array(
            'grant_type' => 'ig_refresh_token',
            'access_token' => $this->accessToken
        );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://graph.instagram.com/refresh_access_token?' . http_build_query($params, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        $obj_access_token = json_decode($response);
        
        $this->setAccessToken($obj_access_token->access_token);
        $this->setExpiresAt($obj_access_token->expires_in);
        
        curl_close($ch);
        
        return $response;
    }
    
    
    /**
    * https://graph.instagram.com/me?fields=account_type,media_count,username,media&access_token=IGQVJWeGV4QU1VemhSckMt..
    * @return mixed
    */
    public function getMeUrl($fields = array('id', 'account_type','media_count','username','media'))
    {
        $params = array(
          'fields' => implode(',', $fields),
          'access_token' => $this->accessToken
        );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://graph.instagram.com/me?' . http_build_query($params, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    
    /**
    * 
    * https://graph.instagram.com/17841445262812471/media?access_token=IGQVJWeGV4QU1VemhSckMtM19DMF9vWUcxMGVvZ..
    */
    public function getMyMediaUrl()
    {
        $params = array(
          'access_token' => $this->accessToken,
          'fields'=> 'id,caption,media_type,media_url,permalink,username,timestamp'
        );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://graph.instagram.com/{$this->user_id}/media?" . http_build_query($params, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    
    /**
    *
    * curl -X GET \ "https://graph.facebook.com/v9.0/instagram_oembed?url=https://www.instagram.com/p/fA9uwTtkSN/&access_token=IGQVJ..."
    */
    public function getOembedPost($post)
    {
        $params = array(
          'url'=> $post,
          'access_token' => "{$this->instagram_client_id}|{$this->instagram_secret_id}",
          'maxwidth' => 560,
          'omitscript' => false,
          'hidecaption' => false
        );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v9.0/instagram_oembed?" . http_build_query($params, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}