<?php

/**
 * Class to handle the identity of affiliate user and how they authenticate
 *
 */
class AffiliateUserIdentity extends UserIdentity
{
    public $_id;

    /**
     * @var string Oauth provider
     */
    public $provider;

    /**
     * @var string identifier from Oauth provider
     */
    public $uid;

    /**
     * constructor is needed as we don't need ($username, $password), because Oauth is the authentication
     * instead we need to feed in the provider and uid returned form Oauth process
     */
    public function __construct($provider, $uid)
    {
        $this->provider = $provider;
        $this->uid = $uid;
    }

    /**
     * This is the method used to encapsulate the main details of the authentication approach.
     *
     * An identity class may also declare additional identity information that needs
     * to be persistent during the user session. In this case $user->id and $user->role
     *
     * @return boolean whether authentication succeeds. True if successful, False otherwise
     */
    public function authenticate()
    {

        $user = User::findAffiliateUser($this->provider, $this->uid);

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            $this->_id = $user->id;
            $this->errorCode = self::ERROR_NONE;
            $this->setState("_id", $user->id);
            $this->setState('roles', $user->role);
        }

        return !$this->errorCode;
    }

    /**
     * revoke affilate login token
    **/
    public static function revoke_token()
    {
        $provider = null;
        $token = null;

        if (isset($_SESSION['affiliate_login'])) {
            $provider = isset($_SESSION['affiliate_login']['provider']) ? $_SESSION['affiliate_login']['provider'] : null ;
            $token = isset($_SESSION['affiliate_login']['token']) ? $_SESSION['affiliate_login']['token'] : null ;
        }

        if ("Orcid" == $provider) {
            $environment = ("sandbox" == Yii::app()->getModules()['opauth']['opauthParams']["Strategy"]["Orcid"]["environment"] ? "sandbox."  : "") ;
            $service_url = 'https://' . $environment . 'orcid.org/oauth/revoke';  #TODO: make orcid coming form config
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
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
            curl_exec($curl);
            curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        }
        //TODO: add Google, Facebook, Twitter
        //TODO: refactor most of the curl stuff into its own function as likely common to all providers
    }
}
