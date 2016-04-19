<div class="span12 form well">
    <div class="form-horizontal">

        <?php
        $this->widget('ext.selgridview.SelGridView', array(
            'id' => 'author-grid',
            'dataProvider' => $sample_model,
            'selectableRows' => 10,
            'itemsCssClass' => 'table table-bordered',
            'columns' => array(
                array('header' => 'Sample ID', 'name' => 'name'),
                array('header' => 'Species Name', 'name' => 'species'),
                array('header' => 'Attributes', 'name' => 'attrs'),
                array(
                    'class' => 'CButtonColumn',
                    'template' => '{delete}',
                    'buttons' => array
                        (
                        'delete' => array
                            (
                            'label' => 'delete this row',
                            'url' => 'Yii::app()->createUrl("/adminDatasetSample/delete1", array("id"=>$data["id"]))',
                        ),
                    ),
                ),
            ),
        ));
        ?>

        <div class="form">



            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'dataset-sample-form',
                'enableAjaxValidation' => false,
            ));
            ?>


            <p class="note">Fields with <span class="required">*</span> are required.<br/>
                Please provide the details of all samples represented by the data being submitted, 
                this should include their taxonomic identification, and as much information about the sample 
                and its collection as possible.

            </p>

            <?php echo $form->errorSummary($model); ?>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'code', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="The unique identifier used for this sample. 
                   It can be free text or it can be a database accession, e.g. BioSample:SAMN001234"></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'code', array('size' => 50, 'maxlength' => 50)); ?>
                    <?php echo $form->error($model, 'code'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'species', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please provide the Species name or taxonomy id of the organism sampled, if you are unable to find the organism in our database please use &quot;new organism&quot; and 
                   add the name in the attributes section, a curator will contact you."></a>
                <div class="controls">

                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'name',
                        'model' => $model,
                        'attribute' => 'species',
                        'source' => $this->createUrl('/adminDatasetSample/autocomplete'),
                        'options' => array(
                            'minLength' => '4',
                        ),
                        'htmlOptions' => array(
                            'placeholder' => 'name',
                            'size' => 'auto'
                        ),
                    ));
                    ?>

                    <?php echo $form->error($model, 'species'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'attribute', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please provide all sample details in the format 
                   < attribute_name>=&QUOT;< attribute_value>&QUOT;,. You should include details like description, 
                   alternative names/IDs, geographic location, sample volume/weight, sex, age, phenotype etc...e.g. sex=&quot;male&quot;,age=&quot;21&quot;,country=&quot;Hong Kong&quot;,geographic_location=&quot;10.123,-45.678&quot;"></a>
                <div class="controls">
                    <?php echo $form->textArea($model, 'attribute', array('rows' => 6, 'cols' => 10, 'style' => "width:300px")); ?>
                    <?php echo $form->error($model, 'attribute'); ?>
                </div>
            </div>





            <div class="control-group">

                <div class="controls">

                    <?php echo CHtml::submitButton('Add Sample', array('class' => 'btn')); ?>
                </div>
            </div>



            <div class="span12" style="text-align:center">



                <a href="/adminRelation/create1" class="btn-green">Previous</a>
                <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green">Save & Quit</a>

                <? if (isset($_SESSION['filecount']) && $_SESSION['filecount'] > 0) { ?>
                    <a href="/adminFile/create1" class="btn-green"><?= Yii::t('app', 'Next') ?></a>
                <? } else { ?>

                    <a href="/dataset/submit" class="btn-green" title="Click submit to send information to a curator for review."><?= Yii::t('app', 'Submit') ?></a>

                <? } ?>




            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

<script>
    $(".myHint").popover();
</script>



