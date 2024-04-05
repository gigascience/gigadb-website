<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Update RSS Message ' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/rssMessage/admin'],
			['isActive' => true, 'label' => 'Update'],
		]
	]);
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>