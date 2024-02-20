<div class="container">
	<?php
	$isAdmin = Yii::app()->user->checkAccess('manageUsers');

	$this->widget('TitleBreadcrumb', [
		'pageTitle' => $isAdmin ? 'Create User' : 'Registration',
		'breadcrumbItems' => $isAdmin ? [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => 'admin'],
			['isActive' => true, 'label' => 'Create'],
		] : [
			['label' => 'Home', 'href' => '/'],
			['isActive' => true, 'label' => 'Personal Details'],
		]
	]);
	?>

	<?= $this->renderPartial('_form', array(
		'model' => $model,
		'scenario' => 'create',
		'update' => false,
	)) ?>


</div>