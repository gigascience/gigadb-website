<?
class MySort extends CSort {

    public $tab="";

    protected function createLink($attribute,$label,$url,$htmlOptions) 
	{ 
	    return CHtml::link($label,array($url,'#'=>$this->tab),$htmlOptions); 
	}
}
?>