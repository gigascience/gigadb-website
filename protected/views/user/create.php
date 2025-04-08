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

    <?php if (isset($from) && $from === 'site/faq'): ?>
        <div class="alert alert-gigadb-info">
            <p>
                Thank you for submitting your question, someone will get back to you as soon as possible. Why not create an account and subscribe to the GigaDB mailing list?
            </p>
        </div>
    <?php endif; ?>

	<?= $this->renderPartial('_form', array(
		'model' => $model,
		'scenario' => 'create',
		'update' => false,
	)) ?>


</div>