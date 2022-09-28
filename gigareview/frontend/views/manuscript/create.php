<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Manuscript $model */

$this->title = 'Create Manuscript';
$this->params['breadcrumbs'][] = ['label' => 'Manuscripts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manuscript-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
