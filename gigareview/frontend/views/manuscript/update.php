<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Manuscript $model */

$this->title = 'Update Manuscript: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Manuscripts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="manuscript-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
