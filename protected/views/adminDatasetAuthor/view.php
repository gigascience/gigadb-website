<div class="container">
	<?php
	$this->breadcrumbs = array(
		'Dataset Authors' => array('index'),
		$model->id,
	);

	$this->menu = array(
		array('label' => 'List DatasetAuthor', 'url' => array('index')),
		array('label' => 'Create DatasetAuthor', 'url' => array('create')),
		array('label' => 'Update DatasetAuthor', 'url' => array('update', 'id' => $model->id)),
		array('label' => 'Delete DatasetAuthor', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?')),
		array('label' => 'Manage DatasetAuthor', 'url' => array('admin')),
	);

	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View DatasetAuthor #' . $model->id,
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminDatasetAuthor/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	?>

	<section>
		<?php $this->widget('zii.widgets.CDetailView', array(
			'data' => $model,
			'attributes' => array(
				'id',
				'dataset_id',
				'author_id',
			),
			'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
			'itemCssClass' => array('odd', 'even'),
			'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>',
		)); ?>
	</section>

</div>