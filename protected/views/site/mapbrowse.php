<?
$this->pageTitle = 'GigaDB - Map Browse';
?>

<div class="container">

<?php
$this->widget('TitleBreadcrumb', [
  'pageTitle' => 'Map Browse',
  'breadcrumbItems' => [
    ['label' => 'Home', 'href' => '/'],
    ['isActive' => true, 'label' => 'Map Browse'],
  ]
]);
?>

</div>

<?php $this->renderPartial('/shared/_mapbrowse', array('locations' => $locations)); ?>
