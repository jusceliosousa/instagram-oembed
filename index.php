<?php 
    require_once 'class/InstagramOauth2.php';
    require_once 'class/InstagramModel.php';

    $instagram_oauth = InstagramOauth2::instance();
    $instagran_model = new InstagramModel();
    
    try {
        
        $data_instagram = $instagran_model->getRowByFaceClientId($instagram_oauth->getFaceClientId());
        
        
        if (!$data_instagram) {
            
            $dados = array(
               'face_client_id' => FACE_CLIENT_ID,
               'face_secret_id' => FACE_SECRET_ID,
               'redirect_uri' => REDIRECT_URI,
               'instagram_client_id' => INSTAGRAM_CLIENT_ID,
               'instagram_secret_id' => INSTAGRAM_SECRET_ID
            );
            $instagran_model->insert($dados);
            
            $data_instagram = $instagran_model->getRowByFaceClientId($instagram_oauth->getFaceClientId());
        }
        
        if (is_null($data_instagram->accessToken) && !$_GET['code']){
            header("location: {$instagram_oauth->getLoginUrl()}");
        }
        
        if( $_GET['code'] ){
           
            /** troca o code pelo access token */
            $code = $_GET['code'];
            $response_access_token = $instagram_oauth->getAccessTokenByCode($code);
            $obj_access_token = json_decode($response_access_token);
           
            /** troca o access token de 2 hrs por um tokes de longa duração de 60 dias */
            $response_access_token_longa_duracao = $instagram_oauth->getAccessTokenLongaDuracao();
            $obj_access_token_longa_duracao = json_decode($response_access_token_longa_duracao);
            
            
            $dados = $data_instagram->toArray();
            $dados['accessToken'] = $obj_access_token_longa_duracao->access_token;
            $dados['user_id'] = $obj_access_token->user_id;
            $data_expiracao = time() + $instagram_oauth->getExpiresAt();
            $dados['expiresAt'] = date('Y-m-d H:i:s',$data_expiracao);
            
            $instagran_model->update($dados, $instagram_oauth->getFaceClientId());
            header('location: index.php?access_token=' . $obj_access_token_longa_duracao->access_token);
        } 

        if ( $_GET['access_token'] ){
            
            $instagram_oauth->setAccessToken( $_GET['access_token'] );
            $instagram_oauth->RefreshAccessToken();
            
            $response_me = $instagram_oauth->getMeUrl();
            $obj_profile = json_decode($response_me);
            $instagram_oauth->setUserId($obj_profile->id);
            
            $response_media = $instagram_oauth->getMyMediaUrl();
            $obj_media = json_decode($response_media);
            
            $dadosMidia = $obj_media->data;
            $midia0 = $dadosMidia[0];
            $midia1 = $dadosMidia[1];
            $midia2 = $dadosMidia[2];
            
            $response_oembed0 = $instagram_oauth->getOembedPost($midia0->permalink);
            $obj_media_oembed0 = json_decode($response_oembed0);
            echo $obj_media_oembed0->html;

            $response_oembed1 = $instagram_oauth->getOembedPost($midia1->permalink);
            $obj_media_oembed1 = json_decode($response_oembed1);
            echo $obj_media_oembed1->html;
            
            $response_oembed2 = $instagram_oauth->getOembedPost($midia2->permalink);
            $obj_media_oembed2 = json_decode($response_oembed2);
            echo $obj_media_oembed2->html;
            
        }
        
    } catch(\Exception $ex) {
        echo "ERRO: " . $ex->getMessage();
    }