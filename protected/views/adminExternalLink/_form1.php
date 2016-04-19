<div class="span12 form well">
    <div class="form-horizontal">


        <?php
        $this->widget('ext.selgridview.SelGridView', array(
            'id' => 'author-grid',
            'dataProvider' => $externalLink_model,
            'selectableRows' => 10,
            'itemsCssClass' => 'table table-bordered',
            'columns' => array(
                array('header' => 'Url', 'name' => 'url'),
                array('header' => 'External Link Type',
                    'name' => 'type_info',
                ),
                // 'value'=> 'externalLink_model.id'),
                array(
                    'class' => 'CButtonColumn',
                    'template' => '{delete}',
                    'buttons' => array
                        (
                        'delete' => array
                            (
                            'label' => 'delete this row',
                            'url' => 'Yii::app()->createUrl("/adminExternalLink/delete1", array("id"=>$data["id"]))',
                        ),
                    ),
                ),
            ),
        ));
        ?>

        <div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'external-link-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('class' => 'form-horizontal')
        ));
?>


            <p class="note">If your data is a genomic assembly that 
                is represented in a public genome browser, please add the direct URL here.</p>
<?php echo $form->errorSummary($model); ?>


            <div class="control-group">
<?php echo $form->labelEx($model, 'url', array('class' => 'control-label')); ?>
                <div class="controls">
                <?php echo $form->textField($model, 'url', array('size' => 60, 'maxlength' => 128)); ?>
                    <?php echo $form->error($model, 'url'); ?>
                </div>
            </div>

            <!--            <div class="control-group">
<?php //echo $form->labelEx($model, 'external_link_type_id', array('class' => 'control-label'));  ?>
                            <div class="controls">
            <? //CHtml::activeDropDownList($model, 'external_link_type_id', CHtml::listData(ExternalLinkType::model()->findAll(), 'id', 'name')); 
            ?>
            <?php //echo $form->error($model, 'external_link_type_id'); ?>
                            </div>
                        </div>-->
            <div class="control-group">
                <div class="controls">
<?php echo CHtml::submitButton('Add External Link', array('class' => 'btn')); ?>
                </div>
            </div>


            <div class="span12" style="text-align:center">

                <a href="/adminLink/create1/" class="btn-green">Previous</a>
                <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green">Save & Quit</a>

                <a href="/adminRelation/create1/" class="btn-green">Next</a>
            </div>

<?php $this->endWidget(); ?>

        </div><!-- form -->
    </div>
</div>
