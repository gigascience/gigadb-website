<?php if (isset($from) && $from === 'site/faq'): ?>
    <div class="flash-success alert alert-success">
        Thank you for submitting your question, someone will get back to you as soon as possible. Why not create an account and subscribe to the GigaDB mailing list?
    </div>
<?php endif; ?>

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