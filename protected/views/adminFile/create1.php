<style>
    select.dropdown-white {
        background-color: white;
    }
    select.dropdown-white:disabled {
        background-color: #eee !important;
    }
</style>
<h2>File details</h2>
<div class="clear"></div>

<?php $this->renderPartial('../datasetSubmission/_tabs_navigation', array('model' => $model)); ?>

<?
$count = count($files->getData());
if($count>0)
    echo $this->renderPartial('_form1', array('files'=>$files,'identifier'=>$identifier,
        'model' => $model, 'dataset_id'=>$model->id));
else{

    ?>
    <div class="span12 form well">
        <div class="form-horizontal">
            <div class="form overflow">
                <p>You can update the files when the administrator upload your files.</p>

                <div class="span12" style="text-align:center">
                    <a href="/datasetSubmission/submit" class="btn-green">Submit</a>
                </div>
            </div>
        </div>
    </div>

    <?
}
?>

