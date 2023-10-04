<div class="container">
	<?php
	$this->breadcrumbs=array(
		'Dataset Authors'=>array('index'),
		'Create',
	);

	$this->menu=array(
		array('label'=>'List DatasetAuthor', 'url'=>array('index')),
		array('label'=>'Manage DatasetAuthor', 'url'=>array('admin')),
	);

	$this->widget('application.components.TitleBreadcrumb', [
		'pageTitle' => 'Create DatasetAuthor',
		'breadcrumbItems' => [
			['label' => 'Dashboard', 'href' => '/site/admin'],
			['label' => 'Dataset Authors', 'href' => '/adminDatasetAuthor/admin'],
			['isActive' => true, 'label' => 'Create'],
		]
	]);
	?>

	<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
</div>