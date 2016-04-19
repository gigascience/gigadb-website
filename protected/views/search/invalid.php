<div class="row">
    <div class="center">
    <? if( isset($general_search)) {?>
        Your search for "<?echo $keyword ?>" did not match anything in our database, please try a 
        different term.
    <? } else {?>
    The DOI <? echo $keyword ?> cannot be displayed. <br/>
   If you found reference to this DOI in a publication, please <b>Let us know.</b>
   
    <? } ?>
    </div>
</div>
<div class="clear"></div>
<?
// There are search results
$this->renderPartial('_form', array('model' => $model));

?>

<div class="center">
    <a href="/" class="btn-green">GigaDB home</a>
</div>
