<div class="container">
	<?php
	$isAdmin = Yii::app()->user->checkAccess('manageUsers');

	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Update User ' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/user/admin'],
			['isActive' => true, 'label' => 'Update'],
		]
	]);
	?>

	<?= $this->renderPartial('_form', array(
		'model' => $model,
		'scenario' => 'update',
		'update' => true
	)) ?>
</div>