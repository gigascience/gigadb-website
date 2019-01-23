<div class="tab-content">
	<?php foreach($datasets as $dataset) { ?>
	<div class="search-result-container">
		<!--Dataset section-->
		<div class="row">
			<div class="span1 logo-container"><img src="/images/icons/g.png"></div>
			<div class="span8 main-content">
				<ul class="nav nav-tabs nav-stacked result-cell">
				  <li><a data-content="<?php echo CHtml::encode($dataset->description) ?>" class="result-main-link left content-popup" href="/dataset/<?= $dataset->identifier?>"><?= $dataset->title ?></a></li>
				  <li>
				  	<strong>
				  		<?= $dataset->authorNames ?>
				  	</strong>
				  </li>
				  <li class="searchID"><?= Yii::t('app', 'DOI') ?>:<?php echo "10.5524/" . $dataset->identifier ?></li>
				</ul>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<br/>
	<?php } ?>
</div>
