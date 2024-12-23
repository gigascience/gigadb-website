<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Create DatasetSample',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminDatasetSample/admin'],
			['isActive' => true, 'label' => 'Create'],
		]
	]);
  foreach (Yii::app()->user->getFlashes() as $key => $message) {
		echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	}
	?>

	<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>