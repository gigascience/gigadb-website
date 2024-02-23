<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Update Sample ' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminSample/admin'],
			['isActive' => true, 'label' => 'Update'],
		]
	]);
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model, 'specie' => $specie)); ?>
</div>