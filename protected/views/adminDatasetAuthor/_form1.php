

<div class="span12 form well">
    <div class="form-horizontal">



        <div class="form">

            <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'author-grid',
                'dataProvider' => $author_model,
                'selectableRows' => 10,
                'itemsCssClass' => 'table table-bordered',
                'columns' => array(
                    'name',
                    'rank',
                    array(
                        'class' => 'CButtonColumn',
                        'template' => '{delete}',
                        'buttons' => array
                            (
                            'delete' => array
                                (
                                'label' => 'delete this row',
                                'url' => 'Yii::app()->createUrl("/adminDatasetAuthor/delete1", array("id"=>$data["id"]))',
                            ),
                        ),
                    ),
                )
            ));
            ?>


            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'dataset-author-form',
                'enableAjaxValidation' => false,
            ));
            ?>




            <p class="note">Fields with <span class="required">*</span> are required.</p>

            <?php echo $form->errorSummary($model); ?>





            <!--
                        <div class="control-group">
            <?php // echo $form->labelEx($model, 'author_id', array('class' => 'control-label')); ?>
                            <div class="controls">
            <? // CHtml::activeDropDownList($model, 'author_id', CHtml::listData(Author::model()->findAll(array('order' => 'name')), 'id', 'fullAuthor')); ?>
            <?php //echo $form->error($model, 'author_id'); ?>
                            </div>
                        </div>-->

            <div class="control-group">
                <?php echo $form->labelEx($model, 'author_name', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please provide all author names in the format <Surname, Initial>; and separate multiple authors by use of semi-colons ; . e.g. Hunter, CI; Li,P;"></a>
                <div class="controls">

                    <?php
//                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
//                        'name' => 'name',
//                        'model' => $model,
//                        'attribute' => 'author_name',
//                        'source' => $this->createUrl('/adminDatasetAuthor/autocomplete'),
//                        'options' => array(
//                            'minLength' => '1',
//                        ),
//                        'htmlOptions' => array(
//                            'placeholder' => 'name',
//                        ),
//                    ));
                    ?>

                    <?php
                    $this->widget('ext.multicomplete.MultiComplete', array(
//                        'name'=>'name',
                        'model' => $model,
                        'attribute' => 'author_name',
                        'splitter' => ';',
                        //'source'=>array('ac1', 'ac2', 'ac3'),
                        'sourceUrl' => $this->createUrl('/adminDatasetAuthor/search'),
                        // additional javascript options for the autocomplete plugin
                        'options' => array(
                            'minLength' => '2',
                        ),
                        'htmlOptions' => array(
                            'size' => '60',
                            'placeholder' =>'one or more names',
                        ),
                    ));
                    
                    ?>


<?php echo $form->error($model, 'author_id'); ?>


                </div>
            </div>


            <div class="control-group">
                    <?php echo $form->labelEx($model, 'rank', array('class' => 'control-label')); ?>
                 <a class="myHint" data-content="Please input the order you wish the author to appear separated by ;. If you have put the authors names in the correct order please leave this field blank."></a>
                <div class="controls">
                    <?= $form->textField($model, 'rank',array('size' => 60, 'maxlength' => 200)); ?>
                    <?php echo $form->error($model, 'rank'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
<?php echo CHtml::submitButton('Add Author', array('class' => 'btn')); ?>
                </div>
            </div>


            <div class="span12" style="text-align:center">


                <!--   <a href="/adminSample/choose" class="btn-green">Next</a> -->
                <a href="/dataset/create1" class="btn-green">Previous</a>
                <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green">Save & Quit</a>
                <a href="/adminDatasetProject/create1" class="btn-green">Next</a>
            </div>

<?php $this->endWidget(); ?>

        </div><!-- form -->

    </div>
</div>
<script>

    $(".myHint").popover();
</script>