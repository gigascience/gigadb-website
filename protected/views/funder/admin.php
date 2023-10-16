<div class="container">

	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Funders',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/funder/create" class="btn background-btn">Add New Funder</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'funder-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			'id',
			'uri',
			'primary_name_display',
			'country',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>