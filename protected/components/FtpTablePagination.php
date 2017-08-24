<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 21/02/2017
 * Time: 11:44
 */



class FtpTablePagination extends CPagination
{
    public function createPageUrl($controller,$page)
    {
        $params=$this->params===null ? $_GET : $this->params;
        if($page>0) // page 0 is the default
            $params[$this->pageVar]=$page+1;
        else
            unset($params[$this->pageVar]);
        // aditional anchor (hash) parameter
        $params['#'] = 'file_table';
        return $controller->createUrl($this->route,$params);
    }
}