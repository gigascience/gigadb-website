<div class="container">
	<?php
	$this->widget('application.components.TitleBreadcrumb', [
		'pageTitle' => 'Create DatasetAuthor',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminDatasetAuthor/admin'],
			['isActive' => true, 'label' => 'Create'],
		]
	]);
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>