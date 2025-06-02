<div id="adminDatasetContainer" class="container">
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
		'dataProvider' => $model->search(),
		'itemsCssClass' => 'table table-bordered',
		'filter' => $model,
		'columns' => array(
			array('name' => 'doi_search', 'value' => '$data->dataset->identifier'),
			'sample_id',
			array('name' => 'sample_name', 'value' => '$data->sample->name'),
			array(
                'header' => 'Sample Attributes',
                'type' => 'raw',
                'value' => function($data) {
                    return $this->renderPartial(
                        '//shared/_longTextToggler',
                        array(
                            'id' => 'sample_attr_value_' . $data->sample->id,
                            'text' => HtmlStringHelper::autoLinkUrls(FormattedDatasetSamples::fullAttrDesc($data->sample->getSampleAttributeArrayMap())),
                            'maxLines' => 3
                        ),
                        true
                    );
                }
            ),
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>
</div>
