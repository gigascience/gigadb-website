<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'View News #' . $model->id,
		'pageTitleLevel' => 'h2', // news title seems more appropriate as h1 for SEO purposes
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/news/admin'],
			['isActive' => true, 'label' => 'View'],
		]
	]);
	?>
	<div class="news">
		<h1 class="h3"><?php echo $model->title; ?></h1>
		<p><?php echo $model->body; ?></p>
	</div>
</div>