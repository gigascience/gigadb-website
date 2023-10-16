<div class="container">

	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Species',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>
	<div class="mb-10">
		<a href="/adminSpecies/create" class="btn background-btn">Create New Species</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>


	<?php $this->widget('CustomGridView', array(
		'id' => 'species-grid',
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			'tax_id',
			'common_name',
			'genbank_name',
			'scientific_name',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>