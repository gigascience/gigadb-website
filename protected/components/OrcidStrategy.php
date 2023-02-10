<?php

class OrcidStrategy extends OpauthStrategy
{
    /**
     * Compulsory config keys, listed as unassociative arrays
     */
    public $expects = array('client_id', 'client_secret');

    /**
     * Optional config keys, without predefining any default values.
     */
    public $optionals = array('redirect_uri', 'scope', 'state', 'access_type', 'approval_prompt');

    /**
     * Optional config keys with respective default values, listed as associative arrays
     * eg. array('scope' => 'email');
     */
    public $defaults = array(
        'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
        'scope' => '/authenticate',
    );

    /**
     * Auth request
     * example: https://sandbox.orcid.org/oauth/authorize?client_id=0000-0003-2736-8061&response_type=code&scope=/orcid-profile/read-limited&redirect_uri=https://developers.google.com/oauthplayground
     */
    public function request()
    {
        $environment = ("sandbox" == $this->strategy['environment'] ? "sandbox."  : "") ;
        $url = 'https://' . $environment . 'orcid.org/oauth/authorize';
        $params = array(
            'client_id' => $this->strategy['client_id'],
            'response_type' => 'code',
            'scope' => $this->strategy['scope'],
            'redirect_uri' => $this->strategy['redirect_uri'],
        );

        foreach ($this->optionals as $key) {
            if (!empty($this->strategy[$key])) {
                $params[$key] = $this->strategy[$key];
            }
        }

        $this->clientGet($url, $params);
    }

    /**
     * Internal callback, after OAuth
     * https://api.sandbox.orcid.org/oauth/token
     */
    public function oauth2callback()
    {
            $environment = ("sandbox" == $this->strategy['environment'] ? "sandbox."  : "") ;
        if (array_key_exists('code', $_GET) && !empty($_GET['code'])) {
            $code = $_GET['code'];
            $url = 'https://' . $environment . 'orcid.org/oauth/token';
            $params = array(
                'code' => $code,
                'client_id' => $this->strategy['client_id'],
                'client_secret' => $this->strategy['client_secret'],
                'redirect_uri' => $this->strategy['redirect_uri'],
                'grant_type' => 'authorization_code'
            );
            $response = $this->serverPost($url, $params, null, $headers);

            $results = json_decode($response);

            if (!empty($results) && !empty($results->access_token) && !empty($results->orcid)) {
                // $userinfo = $this->userinfo($results->orcid);

                $this->auth = array(
                    'provider' => 'Orcid',
                    'uid' => $results->orcid,
                    'info' => array(),
                    'credentials' => array(
                        'token' => $results->access_token,
                        'expires' => date('c', time() + $results->expires_in)
                    ),
                    // 'raw' => $userinfo,
                );

                if (!empty($results->refresh_token)) {
                    $this->auth['credentials']['refresh_token'] = $results->refresh_token;
                }

                if (!empty($results->name)) {
                    $this->auth['info']['name'] = $results->name;
                }

                $this->auth['info']['email'] = null;

                $this->callback();
            } else {
                $error = array(
                    'code' => 'access_token_error',
                    'message' => 'Failed when attempting to obtain access token',
                    'raw' => array(
                        'response' => $response,
                        'headers' => $headers
                    )
                );

                $this->errorCallback($error);
            }
        } else {
            $error = array(
                'code' => 'oauth2callback_error',
                'raw' => $_GET
            );

            $this->errorCallback($error);
        }
    }

    /**
     * Queries Orcid API for user info
     *
     * @param string $uid
     * @return array Parsed JSON results
     */
    // private function userinfo($uid){
    //  $userinfo = $this->serverPost("https://sandbox.orcid.org/v2.1/$uid/person",
    //      array('access_token' => $this->strategy['public_access_token']),
    //      null,
    //      $headers);



    //  if (!empty($userinfo)){
    //      return $userinfo;
    //      //return $this->recursiveGetObjectVars(json_decode($userinfo));
    //  }
    //  else{
    //      $error = array(
    //          'code' => 'userinfo_error',
    //          'message' => 'Failed when attempting to query for user information',
    //          'raw' => array(
    //              'response' => $userinfo,
    //              'headers' => $headers
    //          )
    //      );

    //      $this->errorCallback($error);
    //  }
    // }
}
