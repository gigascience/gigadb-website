<?php

class ModularFileRenderer extends CApplicationComponent {
    public $previewServer ;
    public $supportedExtensions;

    public function is_preview_server_ok() {
		$ch= curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->get_preview_server_url("status") );
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$curl_response = curl_exec($ch);
        if (false === $curl_response) {
            error_log("is_preview_server_ok: curl_exec returns false for url: " . $this->get_preview_server_url("status") , 0) ;
            return false;
        }
		$server_status = json_decode($curl_response, true);
		curl_close ($ch) ;

        if ('up' === $server_status['status']) {
            return true ;
        }
        return false ;
    }

    public function get_preview_server_url($pathinfo) {
        return "http://" . $this->previewServer . "/" . $pathinfo ;
    }

    public function getSupportedExtensions() {
        return $this->supportedExtensions;
    }



}
 ?>
