<?php
Class CommandLineBehavior extends CBehavior {
    public $message ;
    public $options ;


    public function parseArguments($args, $short_long_map) {
        $command_args = implode(" ", $args) ;
        preg_match_all("/-{1,2}([-_a-zA-Z0-9]+)\s*={0,1}\s*([-_a-zA-Z0-9]*)/", $command_args, $matches, PREG_PATTERN_ORDER) ;
        $options = array_combine($matches[1], $matches[2]) ;

        foreach ($short_long_map as $key => $value) {
            if( isset($options[$key]) ) {
                $options[$value] = $options[$key] ;
                unset($options[$key]) ;
            }
        }

        if (YII_DEBUG) {
            var_dump($options) ;
        }

        $this->options = $options ;
        return $options;
    }

    public function setHelpMessage($message) {
        $this->message = $message ;
    }
    public function printHelpMessage($status="") {
        if (isset($status))
            echo $status. PHP_EOL ;
        foreach ($this->message as $line) {
            echo $line .PHP_EOL ;
        }
    }

    public function validateMandatoryOptions($mandatory_keys) {
        if (count($this->options) === 1 && in_array("help", array_keys($this->options)) ) {
            $this->printHelpMessage() ;
            exit(0);
        }

        $present = 0 ;
        foreach ($mandatory_keys as $key) {
            if ( array_key_exists($key, $this->options) ) {
                $present++ ;
            }
        }

        if (count($mandatory_keys) > $present) {
            $this->printHelpMessage("Mandatory argument missing") ;
            exit(1);
        }

    }

    public function getOption($key) {
        if (!isset($this->options[$key])) {
            return false ;
        }
        else if ("" === $this->options[$key]) {
            return true ;
        }
        else {
            return $this->options[$key] ;
        }
    }

    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

    public function optionsCount() {
        return count(array_keys($this->options));
    }

}

 ?>
