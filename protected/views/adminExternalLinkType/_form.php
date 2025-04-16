<div class='section form row'>
    <div class='col-md-offset-3 col-md-6'>
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id'                   => 'external-link-type-form',
            'enableAjaxValidation' => false,
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if ($model->hasErrors()) : ?>
            <div class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <?php
        $this->widget('application.components.controls.TextField', [
            'form'          => $form,
            'model'         => $model,
            'attributeName' => 'name',
            'inputOptions'  => [
                'required' => true,
            ],
        ]);
        $this->widget('application.components.controls.TextField', [
            'form'          => $form,
            'model'         => $model,
            'attributeName' => 'description',
            'inputOptions'  => [],
        ]);
        $this->widget('application.components.controls.TextField', [
            'form'          => $form,
            'model'         => $model,
            'attributeName' => 'prefix',
            'inputOptions'  => [],
        ]);
        $options = array(
            'tab' => 'As tab',
            'link' => 'As link',
        );

        $listDataOptions = [
            'id' => 'displayed-as',
            'style' => 'width:200px;',
        ];

        echo $form->labelEx($model, 'displayed_as', ['style' => 'margin-right: 10px;']);
        echo $form->dropDownList($model, 'displayed_as', $options, $listDataOptions);

        $this->widget('application.components.controls.DropdownField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'relationship_id',
            'listDataOptions' => [
                'data' => Relationship::model()->findAll(),
                'valueField' => 'id',
                'textField' => 'name',
            ],
            'labelOptions' => ['class' => 'mt-10'],

            'inputOptions' => [
                'required' => true,
            ],
            'tooltip' => 'Specify the relationship the values entered in this field will have with the dataset(s) they are being linked to, e.g. a protocol link would be related to the dataset with the relationshipType references',
        ]);
        $this->widget('application.components.controls.CheckBoxField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'multiple',
            'label' => 'Can be multiple instances of that external_link per dataset'
        ]);

        $this->widget('application.components.controls.CheckBoxField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'can_self_referred',
            'label' => 'Can be linked to another external link url'
        ]);
        ?>

        <div class="pull-right btns-row">
            <a href="/adminExternalLink/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>
