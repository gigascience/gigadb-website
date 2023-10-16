<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Link Prefixes',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<p>
		To list certain news items that you are looking for, you may search via keyword or value. Type your keyword or value into their respective boxes under the column headers and press the enter key. You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
		or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p>

	<div class="mb-10">
		<a href="/adminLinkPrefix/create" class="btn background-btn">Create A New Link Prefix</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'prefix-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'itemsCssClass' => 'table table-bordered',
		'columns' => array(
			'prefix',
			'source',
			'url',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>