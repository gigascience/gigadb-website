<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Update File' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminFile/admin'],
			['isActive' => true, 'label' => 'Update'],
		]
	]);

	?>

	<?php echo $this->renderPartial('_form', array('model' => $model, 'attribute' => $attribute)); ?>
</div>