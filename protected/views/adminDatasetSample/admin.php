<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Manage Dataset - Samples',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['isActive' => true, 'label' => 'Manage'],
		]
	]);
	?>
	<div class="mb-10">
	  <a href="/adminDatasetSample/create" class="btn background-btn">Add a Sample to a Dataset</a>
  </div>

	<p>
		Column headers with links are sortable. Cells with a text input are used for filtering.
	</p>

	<?php $this->widget('CustomGridView', array(
		'id' => 'dataset-sample-grid',
		'ajaxUpdate' => false,
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier'),
			'sample_id',
			array('name' => 'sample_name', 'value' => '$data->sample->name'),
			array('header' => 'Sample Attributes', 'type' => 'raw', 'value' => 'FormattedDatasetSamples::getDisplayAttr($data->sample->id,$data->sample->getSampleAttributeArrayMap())'),
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>

	<?php
	$clientScript = Yii::app()->clientScript;
	$register_script = <<<EO_SCRIPT
      jQuery(".js-desc").click(function(e) {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        e.preventDefault();
        id = $(this).attr('data');
        $(this).attr('aria-label', isExpanded ? 'show less' : 'show more');
        $(this).attr('aria-expanded', !isExpanded);
        jQuery(this).hide();
        jQuery('.js-short-'+id).toggle();
        jQuery('.js-long-'+id).toggle();
      })
  EO_SCRIPT;
	$clientScript->registerScript('register_script', $register_script, CClientScript::POS_READY);
	?>

</div>