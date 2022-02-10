<div class="tab-content">
    <?php if (0 === count($datasets['data'])) {
        echo "No results found for '".$model->keyword."'";
    } ?>
    <?php foreach($datasets['data'] as $dt) { ?>
    <?php
        $filterOnDatasetId = function ($var) use ($dt) {
            return $var['dataset_id'] === $dt['id'];
        };
        $dsamples = array_filter($samples['data'], $filterOnDatasetId);
        $dfiles = array_filter($files['data'], $filterOnDatasetId) ;
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
                  <li><a data-content="<?php echo CHtml::encode($dt['description']) ?>" class="result-main-link left content-popup" href="<?php echo $dt['shorturl'] ?>"><?php echo $dt['title'] ?></a></li>
                  <li>
                    <strong>
                        <?php echo $dt['authornames'] ?>
                    </strong>
                  </li>
                  <li class="searchID"><?= Yii::t('app', 'DOI') ?>:<?php echo "10.5524/" . $dt['identifier'] ?></li>
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
                      <li><a class="result-main-link" href="<?= $dt['shorturl'] ?>"><?php echo $sample['name'] ?></a></li>
                      <li>
                        <strong>
                            <?= $sample['species_common_name'] ?>
                            NCBI taxonomy : 
                            <a class="result-sub-links" target="_blank" href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=<?php echo $sample['species_tax_id'] ?>">
                                <?= $sample['species_tax_id'] ?>
                            </a>
                        </strong>
                      </li>
                      <li class="searchID"><a class="result-sub-links" href="<?php echo $dt['shorturl'] ?>">DOI:10.5524/<?= $dt['identifier'] ?></a></li>
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
                <div class="span3 file-name"><a href="<?php echo $file['location'] ?>"><?php echo strlen($file['name']) > 20 ? substr($file['name'], 0, 20). '...' : $file['name'] ?></a></div>
                <div class="span2 file-type"><?php echo $file['file_type'] ?></div>
                <div class="span2 file-size"><?php echo CHtml::encode(File::specifySizeUnits($file['size'],null, 2))?></div>
            </div>
            <?php }} ?>
	</div>
		
	<div style="clear:both;"></div>	
	<br/>
	<?php } ?>	
</div>
