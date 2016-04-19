<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
?>


                   

<h1>Update Files of Dataset: <?= $identifier ?></h1>
<div class="actionBar">
    [<?= MyHtml::link('Manage Files', array('admin')) ?>]
</div>
<br/>


<div class="form-horizontal">
    <div class="form overflow">
        <table class="table table-bordered tablesorter" id="file-table">
            <!--tr-->
            <thead>
                <?php
                    $fsort = $files->getSort();
                    $fsort_map = array(
                        'name' => 'span5',
                        'code' => 'span5',
                        'type_id' => 'span2',
                        'format_id' => 'span2',
                        'size' => 'span2',           
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
                <?php foreach ($fsort_map as $column => $css) { ?>                    
                <th class="<?= $css ?>"><?= $fsort->link($column) ?></th>
                <?php } ?>
                
                <th class="span2"></th>
            </thead>

            <?php

            $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
                    Yii::app()->request->cookies['filePageSize']->value : 10;

            $files->getPagination()->pageSize = $pageSize;

            echo $pageSize." pagesize";

            $i = 0;

            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'file-forms',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'form-horizontal')
                    ));

            ?>
            <?php foreach ($files->getData() as $file) { ?>
                <tr>
                    <?php
                        echo $form->hiddenField($file, '[' . $i . ']extension');
                        echo $form->hiddenField($file, '[' . $i . ']id');
                        echo $form->hiddenField($file, '[' . $i . ']dataset_id');
                        echo $form->hiddenField($file, '[' . $i . ']location');
                        echo $form->hiddenField($file, '[' . $i . ']extension');
                    ?>
                    <td class="left"><?php echo $file->name ?></td>
                    <td class="left"><?= CHtml::activeDropDownList($file, '[' . $i . ']code', $samples_data, array('class' => 'span2')); ?></td>
                    <td class="left"><?= CHtml::activeDropDownList($file, '[' . $i . ']type_id', CHtml::listData(FileType::model()->findAll(), 'id', 'name'),array('class'=>'span2')); ?></td>

                    <td> <?= CHtml::activeDropDownList($file, '[' . $i . ']format_id', CHtml::listData(FileFormat::model()->findAll(), 'id', 'name'),array('class'=>'autowidth')); ?></td>
                    <td><span style="display:none"><?= File::staticGetSizeType($file->size) . ' ' . strlen($file->size) . ' ' . $file->size ?></span><?= MyHtml::encode(File::staticBytesToSize($file->size)) ?></td>
                    
                    <td><?php echo $form->textArea($file, '[' . $i . ']description', array('rows' => 3, 'cols' =>30,'style'=>'resize:none')); ?></td>
                    <td> <?php echo CHtml::submitButton("Update", array('class' => 'update btn', 'name' => $i)); ?> </td>
                </tr>

                <?
                $i++;
            }
            ?>
  
        </table>
    </div>
</div>
    
<div class="span12" style="text-align:center">
    <?php
    echo CHtml::submitButton("Update All", array('class' => 'btn', 'name' => 'files'));

    $this->endWidget();
    ?>
</div>

<div>
    <h4>File information upload</h4>
    <p> Please upload a tab separated table of file information, one row per file with the columns File Name, Sample ID, File Type, File Format, Description.</p>
    <p>e.g.</p>
    <table class="ex-table">
        <tr>
            <td>readme.txt</td> <td>none</td> <td>readme </td><td>txt</td><td>readme file</td>
        </tr>
        <tr>
            <td>Sample_1.fasta</td><td>1</td><td>geomic</td> <td>fasta</td><td>the filtered and clipped reads of sample 1</td>
        </tr>
    </table>
    <form method="POST" enctype="multipart/form-data" action="<?= $this->createUrl('/adminFile/uploadAttr', array('id'=>$identifier))?>">
        <input type="file" name="file_info" id="js-file" style="display:none"/>
        <div class="input-group">
            <input type="text" class="form-control js-ftext">
            <span class="input-group-addon">
                <button class="btn js-btn-browse">Browse</button>
            </span>
        </div>        
        <button type="submit" class="btn js-btn-up">Upload file info</button>
    </form>
    
</div>


<?php

$pagination = $files->getPagination();
$pagination->pageSize = $pageSize;

$this->widget('CLinkPager', array(
    'pages' => $pagination,
    'header' => '',
    'cssFile' => false,
));

?>

<script>
    $('.date').each(function() {
        $(this).datepicker();
    });

    $('#js-file').change(function(e) {
        $('.js-ftext').val(e.target.files[0].name);
    })

    $(".js-btn-browse").click(function(e) {
        e.preventDefault();
        $('#js-file').click();
    });     
</script>