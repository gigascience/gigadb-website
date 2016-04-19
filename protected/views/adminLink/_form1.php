<div class="span12 form well">
    <div class="form-horizontal">

        <?php
        $this->widget('ext.selgridview.SelGridView', array(
            'id' => 'author-grid',
            'dataProvider' => $link_model,
            'selectableRows' => 10,
            'itemsCssClass' => 'table table-bordered',
            'columns' => array(
                array('header' => 'Link Type', 'name' => 'link_type'),
                array('header' => 'Link', 'name' => 'link'),
                array(
                    'class' => 'CButtonColumn',
                    'template' => '{delete}',
                    'buttons' => array
                        (
                        'delete' => array
                            (
                            'label' => 'delete this row',
                            'url' => 'Yii::app()->createUrl("/adminLink/delete1", array("id"=>$data["id"]))',
                        ),
                    ),
                ),
            ),
        ));
        ?>
        <div class="form">




            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'link-form',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'form-horizontal')
            ));
            ?>



            <p class="note">Please add the database 
                and accession numbers of any data in this
                dataset that is also stored in other repositories,
                e.g. sequences in the Sequence Read Archive (SRA).</p>
            <?php echo $form->errorSummary($model); ?>


            <div class="control-group">

                <?php echo $form->labelEx($model, 'database', array('class' => 'control-label')); ?>
                <a class="myHintLink" data-content="Please contact <a href=&quot;mailto:database@gigasciencejournal.com&quot; >database@gigasciencejournal.com</a> to request the addition of a new database."></a>
                <div class="controls">
                    <?= CHtml::activeDropDownList($model, 'database', $link_database) ?>
                    <?php echo $form->error($model, 'databse'); ?>
                </div>
            </div>



            <div class="control-group">

                <?php echo $form->labelEx($model, 'acc_num', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please provide unique identifier of linked data, e.g. an SRA accession; SRS012345."></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'acc_num', array('size' => 60, 'maxlength' => 100)); ?>
                    <?php echo $form->error($model, 'acc_num'); ?>
                </div>
            </div>

            <div class="control-group">

                <div class="controls">
                    <?php echo CHtml::submitButton('Add Link', array('class' => 'btn')); ?>
                </div>
            </div>

            <div class="span12" style="text-align:center">


                <a href="/adminDatasetProject/create1" class="btn-green">Previous</a>
                <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard." class="btn-green">Save & Quit</a>
                <a href="/adminExternalLink/create1" class="btn-green">Next</a>
            </div>

            <?php $this->endWidget(); ?>

        </div><!-- form -->
    </div>
</div>
<script>
    $(".hint").popover();
    $(".myHint").popover();
    $(".myHintLink").popover({trigger: 'manual'}).hover(function(e) {
        $(this).popover('show');
        e.preventDefault();
    });


    $('.myHintLink').on('mouseleave', function() {
        var v = $(this);
        setTimeout(
                function() {
                    v.popover('hide');
                }, 2000);
    });
    
</script>