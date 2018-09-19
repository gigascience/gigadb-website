<div class="tab-content">
    <?php foreach($datasets['data'] as $dt) { ?>
    <?php 
        $dataset = Dataset::model()->findByPk($dt); 
        $dsamples = $dataset->getSamplesInIds($samples['data']);
        $dfiles = $dataset->getFilesInIds($files['data']); 
    ?>
    <div class="search-result-container">
        <!--Dataset section-->
        <?php $is_display = in_array('dataset', $display) || (in_array('file', $display) && $dfiles) || (in_array('sample', $display) && $dsamples); ?>
        <?php if($is_display) { ?>
        <div class="row1">
            <div class="span1" style="margin-left: 40px;margin-top: 10px;height: 30px;width: 40px"><div class="text-icon text-icon-sm text-icon-blue" style="margin-right: 0px;">G
            </div></div>
            <div class="span8 main-content" style="float:right">
                <ul class="nav nav-tabs nav-stacked result-cell">
                  <li><a data-content="<?php echo MyHtml::encode($dataset->description) ?>" class="result-main-link left content-popup" href="<?= $dataset->shortUrl ?>"><?= $dataset->title ?></a></li>
                  <li>
                    <strong>
                        <?php echo $dataset->authorNames ?>
                    </strong>
                  </li>
                  <li class="searchID"><?= Yii::t('app', 'DOI') ?>:<?php echo "10.5524/" . $dataset->identifier ?></li>
                </ul>
            </div>
        </div>
        <?php } ?>

        <?php if(in_array('sample', $display)) { 
            foreach($dsamples as $sample) { ?>
            <!--Sample section-->
            <div class="row1">
                <div class="span1" style="margin-left: 40px;margin-top: 10px;height: 30px;width: 40px"><div class="text-icon text-icon-sm text-icon-green" style="margin-right: 0px;">S
            </div></div>
                <div class="span8 main-content" style="float:right">
                    <ul class="nav nav-tabs nav-stacked result-cell">
                      <li><a class="result-main-link" href="<?= $sample->dataset->shortUrl ?>"><?php echo $sample->name ?></a></li>
                      <li>
                        <strong>
                            <?= $sample->species->common_name ?> 
                            NCBI taxonomy : 
                            <a class="result-sub-links" target="_blank" href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=<?php echo $sample->species->tax_id ?>">
                                <?= $sample->species->tax_id ?>
                            </a>
                        </strong>
                      </li>
                      <li class="searchID"><a class="result-sub-links" href="<?= $sample->dataset->shortUrl ?>">DOI:10.5524/<?= $sample->dataset->identifier ?></a></li>
                    </ul>
                </div>
            </div>
            <?php }} ?>
            <!--Download File list-->
            <?php if(in_array('file', $display)) {
                foreach($dfiles as $file) { ?>
            <div class="row1 file-container">
                 <div class="span1" style="margin-left: 40px;margin-top: 10px;height: 30px;width: 40px"><div class="text-icon text-icon-sm text-icon-yellow" style="margin-right: 0px;">F
            </div></div>
                <div class="span3 file-name"><a href="<?php echo $file->location ?>"><?php echo strlen($file->name) > 20 ? substr($file->name, 0, 20). '...' : $file->name ?></a></div>
                <div class="span2 file-type"><?php echo $file->type->name ?></div>
                <div class="span2 file-size"><?php echo MyHtml::encode($file->getSizeWithFormat())?></div>
                <div class="span1 file-checkbox"><input type="checkbox" ></div>
            </div>
            <?php }} ?>
	</div>
		
	<div style="clear:both;"></div>	
	<br/>
	<?php } ?>	
</div>
