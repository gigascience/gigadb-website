<div class="span12 form well">
    <div class="form-horizontal">


        <?php
        $this->widget('ext.selgridview.SelGridView', array(
            'id' => 'author-grid',
            'dataProvider' => $relation_model,
            'selectableRows' => 10,
            'itemsCssClass' => 'table table-bordered',
            'columns' => array(
                array('name' => 'related_doi', 'header' => 'Related Doi'),
                array('name' => 'relationship', 'header' => 'Relationship'),
                array(
                    'class' => 'CButtonColumn',
                    'template' => '{delete}',
                    'buttons' => array
                        (
                        'delete' => array
                            (
                            'label' => 'delete this row',
                            'url' => 'Yii::app()->createUrl("/adminRelation/delete1", array("id"=>$data["id"]))',
                        ),
                    ),
                ),
            ),
        ));
        ?>
        <div class="form">

            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'relation-form',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'form-horizontal')
            ));
            ?>

            <p class="note">If your dataset is directly related to any other GigaDB published DOI,
                please add the DOI number here and the type of the relationship.</p>
            <?php echo $form->errorSummary($model); ?>



            <div class="control-group">
                <?php echo $form->labelEx($model, 'related_doi', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Use the six digit GigaDB identifier e.g. 100023"></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'related_doi', array('size' => 15, 'maxlength' => 15)); ?>
                    <?php echo $form->error($model, 'related_doi'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'relationship', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please select relationship type from drop down menu where:<br/>
                   IsNewVersionOf = your submission is a new version of that DOIs<br/>
                   IsSupplentedBy = Your submission is supplemented by existing DOI<br/>
                   IsSupplementTo = Your submission is supplemental to an existing DOI<br/>
                   Compiles = your submission is used to create the data in existing DOI<br/>
                   IsCompiledBy = your data was created by using software in existing DOI"></a>
                <div class="controls">
                    <?= CHtml::activeDropDownList($model, 'relationship', $relation_type) ?> 

                    <?php echo $form->error($model, 'relationship'); ?>
                </div>
            </div>

            <div class="control-group">

                <div class="controls">
                    <?php echo CHtml::submitButton('Add Related Doi', array('class' => 'btn')); ?>

                </div>
            </div>

            <div class="span12" style="text-align:center">


                <a href="/adminExternalLink/create1" class="btn-green">Previous</a>
                  <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green">Save & Quit</a>
                <a href="/adminDatasetSample/create1" class="btn-green">Next</a>
            </div>

            <?php $this->endWidget(); ?>

        </div><!-- form -->
    </div>
</div>
<script>
    $(".myHint").popover();
</script>