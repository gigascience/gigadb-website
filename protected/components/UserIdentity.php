<?php

class UserIdentity extends CUserIdentity {

    public $_id;
    const ERROR_USER_NOT_ACTIVATED=3;

    public function authenticate() {
        if (!isset($_SESSION['affiliate_login'])){
            $user = User::model()->findByAttributes(array('email'=>$this->username));
        }else{
            $user = User::findAffiliateUser($_SESSION['affiliate_login']['provider'], $_SESSION['affiliate_login']['uid']);
        }

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
         #exist this user
            if (!isset($_SESSION['affiliate_login'])){
                #normal login!
                if(md5($this->password) !== $user->password)
                    $this->errorCode=self::ERROR_PASSWORD_INVALID;
                else if(!$user->is_activated)
                    $this->errorCode=self::ERROR_USER_NOT_ACTIVATED;
                else {
                    $this->_id = $user->id;
                    $this->errorCode = self::ERROR_NONE;
                    $this->setState("_id", $user->id);
                    $this->setState('roles',$user->role);
                 }
            } else {
                #has session affiliate login
                $this->_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
                $this->setState("_id", $user->id);
                $this->setState('roles',$user->role);
            }
        }

        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

    public static function revoke_token() {  # revoke affilate login token for Google and ORCID
        $provider = null;
        $token = null;

        if(isset($_SESSION['affiliate_login'])) {
            $provider = isset($_SESSION['affiliate_login']['provider']) ? $_SESSION['affiliate_login']['provider'] : null ;
            $token = isset($_SESSION['affiliate_login']['token']) ? $_SESSION['affiliate_login']['token'] : null ;
        }

        if ("Orcid" == $provider) {
            $service_url = 'https://sandbox.orcid.org/oauth/revoke';  #TODO: make orcid coming form config
            $curl = curl_init($service_url);
            $curl_post_data = array(
                "client_id" => Yii::app()->getModules()['opauth']['opauthParams']["Strategy"]["Orcid"]["client_id"],
                "client_secret" => Yii::app()->getModules()['opauth']['opauthParams']["Strategy"]["Orcid"]["client_secret"],
                "token" => $token,
            );
            $headers = array(
                'Accept: application/json',
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data) );
            $http_response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
            curl_close($curl);

        }
    }
    

}
