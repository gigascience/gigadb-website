<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
?>



<div class="span12 form well">
    <div class="form-horizontal">
        <div class="form overflow">
            <table class="table table-bordered tablesorter" id="file-table">
                <!--tr-->
                <thead>
                    <!--th class="span2"><a href='#' onClick="setCookie('dataset.identifier')">DOI</a></th-->
                    <?
                    //TODO: This part is also dupicated
                    $fsort = $files->getSort();
                    $fsort_map = array(
                        'name' => 'span1',
                        'code' => 'span2',
                        'type_id' => 'span1',
                        'format_id' => 'span1',
                        'size' => 'span1',
//                        'date_stamp' => 'span1',
                        'description' => 'span2',
                    );

                    foreach ($fsort->directions as $key => $value) {
                        if (!array_key_exists($key, $fsort_map)) {
                            continue;
                        }
                        $direction = ($value == 1) ? ' sorted-down' : ' sorted-up';
                        $fsort_map[$key] .= $direction;
                    }
                    ?>
                    <?
                    foreach ($fsort_map as $column => $css) {
                        ?>
                    <th class="<?= $css ?>"><?= $fsort->link($column) ?></th>
                <? }
                ?>
                <th class="span2"></th>
                </thead>

                <?
                $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
                        Yii::app()->request->cookies['filePageSize']->value : 10;

                $files->getPagination()->pageSize = $pageSize;

                $i = 0;
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'file-forms',
                    
                    'enableAjaxValidation' => true,
                    'htmlOptions' => array('class' => 'form-horizontal')
                ));
                $page = $files->getPagination()->getCurrentPage() + 1;
                $pageCount = (int) (($files->getTotalItemCount() + $pageSize - 1) / $pageSize);
                ?>
                <input type="hidden" name="page" value=<?= $page ?>>
                <input type="hidden" name="pageCount" value=<?= $pageCount ?>>
                <?
                foreach ($files->getData() as $file) {
                    ?>
                    <tr>
                        <?
                        echo $form->hiddenField($file, '[' . $i . ']extension');
                        echo $form->hiddenField($file, '[' . $i . ']id');
                        echo $form->hiddenField($file, '[' . $i . ']dataset_id');
                        echo $form->hiddenField($file, '[' . $i . ']location');
                        echo $form->hiddenField($file, '[' . $i . ']extension');
                        ?>

                        <td class="left"><?php echo $file->name ?></td>
                        <td class="left"><?= CHtml::activeDropDownList($file, '[' . $i . ']code', $samples_data, array('class' => 'span2')); ?></td>

                        <td class="left"><?= CHtml::activeDropDownList($file, '[' . $i . ']type_id', CHtml::listData(FileType::model()->findAll(), 'id', 'name'), array('class' => 'span2')); ?></td>

                        <td> <?= CHtml::activeDropDownList($file, '[' . $i . ']format_id', CHtml::listData(FileFormat::model()->findAll(), 'id', 'name'), array('class' => 'autowidth')); ?></td>
                        <td><span style="display:none"><?= File::staticGetSizeType($file->size) . ' ' . strlen($file->size) . ' ' . $file->size ?></span><?= MyHtml::encode(File::staticBytesToSize($file->size)) ?></td>

                        <td><?php echo $form->textArea($file, '[' . $i . ']description', array('rows' => 3, 'cols' => 30, 'style' => 'resize:none')); ?></td>

                        <td> <?php echo CHtml::submitButton("Update", array('class' => 'update btn', 'name' => $i)); ?> </td>
                    </tr>

                    <?
                    $i++;
                }
                ?>

            </table>
        </div>
        <div class="span12" style="text-align:center">
            <?php
            ?>
            <?php
//            echo $files->getPagination()->getCurrentPage()." eexx";
////            echo $files->getPagination()->getItemCount()."x dd";
//              $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
//                        Yii::app()->request->cookies['filePageSize']->value : 10;
            $page = $files->getPagination()->getCurrentPage() + 1;
            $pageCount = (int) (($files->getTotalItemCount() + $pageSize - 1) / $pageSize);
//            echo $page." ".$pageCount;
            if ($page == $pageCount) {
                echo CHtml::submitButton("Save", array('onclick'=>"process('save')",'class' => 'btn', 'name' => 'files', 'title' => 'Save the updates to these files'));
                ?>
                  <input type="hidden" name="file" value="file">
                  <input onclick="process('complete')" type="submit" value="Complete submission" class="btn-green" title="Submit changes to file details."/>
                <?
                $this->endWidget();
                ?>
               
                  
               
<?
} else {
    echo CHtml::submitButton("Next", array('class' => 'btn', 'name' => 'files', 'title' => 'Save the updates to these 10 and show next 10 files'));
    $this->endWidget();
}
?>

            <!--<a href="/dataset/submit" class="btn-green" title="Click submit to send information to a curator for review.">Submit</a>-->

        </div>

        <?php
//  $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
//  Yii::app()->request->cookies['filePageSize']->value : 10;
        $pagination = $files->getPagination();
        $pagination->pageSize = $pageSize;

        $this->widget('CLinkPager', array(
            'pages' => $pagination,
            'header' => '',
            'cssFile' => false,
        ));
        ?>

    </div>
</div>
<script>
    $('.date').each(function() {
        $(this).datepicker();
    }
    );
        
  function   process(v){  
  if(v=="save"){  
        document.getElementById('file-forms').action="/adminFile/create1/id/<?=$dataset_id?>";  
  }else{  
        document.getElementById('file-forms').action="/dataset/submit/id/<?=$dataset_id?>";  
  }  
     document.theForm.submit();  
  }     

</script>
