<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{

    /**
     * Calculate md5 checksum of response content of current page
     *
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function checksumOfResponse(): string
    {
        return md5($this->getModule('PhpBrowser')->_getResponseContent());
    }

}
