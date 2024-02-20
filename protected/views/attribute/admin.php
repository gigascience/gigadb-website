<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Attribute',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>
	<div class="mb-10">
		<a href="/attribute/create" class="btn background-btn">Add New Attribute</a>
	</div>
	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>
  <?php $this->widget('CustomGridView', array(
    'id' => 'attribute-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-bordered dataset-table-wide',
    'filter' => $model,
    'template' => '<div class="dataset-table-wide-container">{items}</div>{pager}',
    'columns' => array(
      'id',
      'attribute_name',
      'definition',
      'model',
      'structured_comment_name',
      'value_syntax',
      'allowed_units',
		'occurance',
		'ontology_link',
		'note',
		CustomGridView::getDefaultActionButtonsConfig()
	),
)); ?>
</div>