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
			array('header' => 'Sample Attributes', 'type' => 'raw', 'value' => 'FormattedDatasetSamples::getDisplayAttr($data->sample->id,$data->sample->getSampleAttributeArrayMap())'),
			CustomGridView::getDefaultActionButtonsConfig()
		),
	)); ?>
</div>

<script>
function toggleShowMore(btnEl) {
  const isExpanded = btnEl.attr('aria-expanded') === 'true';
  id = btnEl.attr('data');
  btnEl.attr('aria-label', isExpanded ? 'show less' : 'show more');
  btnEl.attr('aria-expanded', !isExpanded);
  btnEl.hide();
  $('.js-short-'+id).toggle();
  $('.js-long-'+id).toggle();
}

function handleClick(e) {
  const target = $(e.target);

  if (!target.hasClass('js-desc')) {
    return;
  }

  e.preventDefault();
  toggleShowMore(target);
}

$(document).ready(function() {
  // NOTE targeting container because on filter, content gets rerendered and any event listeners are destroyed
  $("#adminDatasetContainer").on("click", handleClick)
})
</script>