
    <?php echo MyHtml::beginForm('/search/new','GET',array('class'=>'form','onsubmit'=>'return validateForm(this);')); ?>
    <?php echo MyHtml::errorSummary($model); ?>

   <div class="form-group home-search-bar-group">
       <div class="input-group search-bar-group">
    <?php

        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name'=>'keyword',
            //'source'=>array('ac1', 'ac2', 'ac3'),
            // 'source'=> array_values($dataset->getListTitles()),
            'source'=> array_values(array()),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                 'minLength'=>'2',
             ),
            'htmlOptions'=>array(
                 'class'=>'form-control',
             ),
             'value'=>$model->keyword
        ));
          ?>
           
        <span class="input-group-btn">
       <button class="btn background-btn" type="submit">
                                                <i class="fa fa-search"></i> Search again
                                            </button>
          
             <?
        if(!Yii::app()->user->isGuest) {
            
            echo MyHtml::ajaxButton(Yii::t('app' , 'Save current search criteria'),array("/search/save"),array('type'=>'POST','dataType'=>'json','data'=>array('criteria'=>$model->criteria, 'result'=>$model->query_result) ,'success'=>"function(data){
                if(data.status=='fail'){
                    alert('Failed to save: '+ data.reason);
                }else {
                    alert('Successfully save search query');
                }
            }") ,array('class'=>'btn background-btn'));
        }
    ?>
        </span>   
      
 </div>
</div>

    <?php echo MyHtml::endForm(); ?>

   

<div id="saveSearchStatus" >
    <div id="saveSearchSuccess" style="display:none">
        <h2>Success</h2>
    </div>
    <div id="saveSearchFail" style="display:none">
        <h2>Opps!</h2>
        <h2 id="saveSearchReason">Reason</h2>
    </div>
</div>
<script>
function validateForm(myform){
    if(myform.keyword.value.length==0) {
        alert("Keyword can not be blank");
        return false;
    }

    return true;

}


// function submitForm(myform){
//     var strJson= JSON.stringify($(myform).serializeObject());

//     var searchform=myform['searchform'].value;


//     var url=window.location.protocol+"//"+window.location.hostname+(window.location.port ? ':'+window.location.port: '')+$(myform).attr("action");

//     window.location = url+"?criteria=" + strJson;



//     return false;

// }

// $.fn.serializeObject = function()
// {
//     var o = {};
//     var a = this.serializeArray();
//     $.each(a, function() {
//         if (o[this.name] !== undefined) {
//             if (!o[this.name].push) {
//                 o[this.name] = [o[this.name]];
//             }
//             o[this.name].push(this.value || '');
//         } else {
//             o[this.name] = this.value || '';
//         }
//     });
//     return o;
// };

</script>
