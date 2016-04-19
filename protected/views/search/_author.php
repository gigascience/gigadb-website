<div class="tab-content">
	<?php foreach($datasets as $dataset) { ?>
	<div class="search-result-container">
		<!--Dataset section-->		
		<div class="row">
			<div class="span1 logo-container"><img src="/images/icons/g.png"></div>
			<div class="span8 main-content">
				<ul class="nav nav-tabs nav-stacked result-cell">
				  <li><a data-content="<?php echo MyHtml::encode($dataset->description) ?>" class="result-main-link left content-popup" href="/dataset/<?= $dataset->identifier?>"><?= $dataset->title ?></a></li>
				  <li>
				  	<strong>
				  		<?= $dataset->authorNames ?>
				  	</strong>
				  </li>
				  <li class="searchID"><?= Yii::t('app', 'DOI') ?>:<?php echo "10.5524/" . $dataset->identifier ?></li>
				</ul>
			</div>
		</div>
		<?php /* foreach($dataset->samples as $sample) { ?>
		<!--Sample section-->
		<div class="row">
			<div class="span1 logo-container"><img src="/images/icons/s.png"></div>
			<div class="span8 main-content">
				<ul class="nav nav-tabs nav-stacked result-cell">
				  <li><a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=<?php echo $sample->species->tax_id ?>"><?php echo $sample->name ?></a></li>
				  <li>
				  	<strong>
						<a class="result-sub-links" href="#"><?php echo $sample->species->common_name ?></a> 
						NCBI taxonomy : <?php echo $sample->species->tax_id ?>
				  	</strong>
				  </li>
				  <li class="searchID">DOI:10.5524/<?= $sample->dataset->identifier ?></li>
				</ul>
			</div>
		</div>
		<?php } ?>
		<!--Download File list-->
		<?php foreach($dataset->files as $file) { ?>
		<div class="row file-container">
			<div class="span1 logo-container-file"><img src="/images/icons/f.png"> </div>
			<div class="span3 file-name"><a href="<?php echo $file->location ?>"><?php echo strlen($file->name) > 20 ? substr($file->name, 0, 20). '...' : $file->name ?></a></div>
			<div class="span2 file-type"><?php echo $file->type->name ?></div>
			<div class="span2 file-size"><?php echo MyHtml::encode(File::staticBytesToSize($file->size))?></div>
			<div class="span1 file-checkbox"><input type="checkbox" ></div>
		</div>
		<?php } */?>
	</div>		
	<div style="clear:both;"></div>	
	<br/>
	<?php } ?>	
</div>
