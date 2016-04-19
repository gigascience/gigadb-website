<?php

/**
 * Super-simple, minimum abstraction MailChimp API v2 wrapper
 * 
 * Uses curl if available, falls back to file_get_contents and HTTP stream.
 * This probably has more comments than code.
 * 
 * Extended to fit the requirements of Waggle project
 *
 * Contributors:
 * Michael Minor <me@pixelbacon.com>
 * Lorna Jane Mitchell, github.com/lornajane
 * Bartosz Wójcik, http://procreative.eu
 * 
 * @author Drew McLellan <drew.mclellan@gmail.com> 
 * @version 1.1.1
 */
class MailChimpBase extends CApplicationComponent
{
    public $apikey;
    public $verifySsl   = false;
    
    private $_apiEndpoint = 'https://<dc>.api.mailchimp.com/2.0';
    private $_errorCode = false;
    private $_errorMessage = '';

    /**
     * Create a new instance
     * @param string $api_key Your MailChimp API key
     */
    function init()
    {
        //$this->apikey = $api_key;
        list(, $datacentre) = explode('-', $this->apikey);
        $this->_apiEndpoint = str_replace('<dc>', $datacentre, $this->_apiEndpoint);
    }

    /**
     * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
     * @param  string $method The API method to call, e.g. 'lists/list'
     * @param  array  $args   An array of arguments to pass to the method. Will be json-encoded for you.
     * @return array          Associative array of json decoded API response.
     */
    public function call($method, $args=array())
    {
        $this->_errorCode = false;
        $this->_errorMessage = '';
        
        $result = $this->makeRequest($method, $args);
        
        if (isset($result['status']) && $result['status'] == 'error') {
            $this->_errorCode = isset($result['code']) ? (int)$result['code'] : 0;
            $this->_errorMessage = isset($result['error']) ? (int)$result['error'] : '';
        }
        
        Yii::log(print_r($result, true), 'debug');
        return $result;
    }

    /**
     * Performs the underlying HTTP request. Not very exciting
     * @param  string $method The API method to be called
     * @param  array  $args   Assoc array of parameters to be passed
     * @return array          Assoc array of decoded result
     */
    private function makeRequest($method, $args=array())
    {      
        $args['apikey'] = $this->apikey;

        $url = $this->_apiEndpoint.'/'.$method.'.json';

        if (function_exists('curl_init') && function_exists('curl_setopt')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');       
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $json_data = json_encode($args);
            $result    = file_get_contents($url, null, stream_context_create(array(
                'http' => array(
                    'protocol_version' => 1.1,
                    'user_agent'       => 'PHP-MCAPI/2.0',
                    'method'           => 'POST',
                    'header'           => "Content-type: application/json\r\n".
                                          "Connection: close\r\n" .
                                          "Content-length: " . strlen($json_data) . "\r\n",
                    'content'          => $json_data,
                ),
            )));
        }

        return $result ? json_decode($result, true) : false;
    }
    
    public function getErrorCode()
    {
        return $this->_errorCode;
    }
    
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }
}