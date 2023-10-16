<div class="container">

	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Rss Messages',
		'breadcrumbItems' => [
			['label' => 'Datasets', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>

	<div class="mb-10">
		<a href="/rssMessage/create" class="btn background-btn">Create an RSS Message</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'rss-message-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'itemsCssClass' => 'table table-bordered',
		'columns' => array(
			'id',
			'message',
			'publication_date',
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

</div>