 <div class="content">
      <section class="image-background">
    <div class="container">
    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1 text-center">
                            
                            <br>
                            <br>
                            <br>
                            <br>
                              <? if( isset($general_search)) {?>
                             <p class="home-search-bar-subtitle"> Your search for "<?echo $keyword ?>" did not match anything in our database, please try a 
                                 different term. </p>
                                    <? } else {?>
                             <p class="home-search-bar-subtitle">    The DOI <? echo $keyword ?> cannot be displayed. <br/> 
                                     If you found reference to this DOI in a publication, please <b>Let us know.</b></p>
   
                                     <? } ?>                           
                            <? $this->renderPartial('/search/_form',array('model'=>$model,'search_result'=>null)); ?>
                            
                            <br>
                            <br>
                              <a href="/" class="btn background-btn">GigaDB home</a>
                            <br>  
                            <br>  
                            
                                  
                        </div>
                    </div>  
    </div> 
  

     



  

</div>
 </div>     