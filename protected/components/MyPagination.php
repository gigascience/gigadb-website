<?
class MyPagination extends CPagination {

    public $tab="";

    public function createPageUrl($controller,$page) 
    { 
        $params=$this->params===null ? $_GET : $this->params; 
        if($page>0) // page 0 is the default 
            $params[$this->pageVar]=$page+1; 
        else 
            unset($params[$this->pageVar]); 
        // aditional anchor (hash) parameter
        $params['#'] = $this->tab;
        return $controller->createUrl($this->route,$params); 
    }
}
?>