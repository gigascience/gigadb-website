<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Manuscript $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="manuscript-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'doi')->textInput() ?>

    <?= $form->field($model, 'manuscript_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'article_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publication_date')->textInput() ?>

    <?= $form->field($model, 'editorial_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'editorial_status_date')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
