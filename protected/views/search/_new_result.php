<div class="tab-content">
    <?php if (0 === count($datasets['data'])) {
        echo "No results found for '" . $model->keyword . "'";
    } ?>
    <?php foreach ($datasets['data'] as $dt) { ?>
        <?php
        $filterOnDatasetId = function ($var) use ($dt) {
            return $var['dataset_id'] === $dt['id'];
        };
        $dsamples = array_filter($samples['data'], $filterOnDatasetId);
        $dfiles = array_filter($files['data'], $filterOnDatasetId);
        $is_display = in_array('dataset', $display) || (in_array('file', $display) && $dfiles) || (in_array('sample', $display) && $dsamples);
        ?>
        <?php if ($is_display) { ?>
            <div class="search-result-card">
                <!--Dataset section-->
                <div class="search-result-row">
                    <div class="search-result-icon-container">
                        <div class="text-icon text-icon-sm text-icon-blue search-result-icon" aria-hidden="true">G</div>
                        <span class="sr-only">Dataset</span>
                    </div>
                    <div class="search-result-content">
                        <h3 class="search-result-title">
                            <a data-content="<?php echo CHtml::encode($dt['description']) ?>" class="search-result-link content-popup" href="<?php echo $dt['shorturl'] ?>"><?php echo $dt['title'] ?></a>
                        </h3>
                        <?php if (!empty($dt['authornames'])) : ?>
                            <!-- HACK to render a list of links for authors, since the model directly returns a sequence of links as a string -->
                            <div class="search-result-subcontent">
                                <?php
                                    $authorLinks = explode(';', $dt['authornames']);
                                    $authorLinks = array_map('trim', $authorLinks);

                                    $listItems = '';
                                    $numberOfLinks = count($authorLinks);
                                    foreach ($authorLinks as $index => $link) {
                                        $listItems .= '<li>' . $link;
                                        if ($index !== $numberOfLinks - 1) {
                                            $listItems .= ';&nbsp;';
                                        }
                                        $listItems .= '</li>';
                                    }

                                    $unorderedList = '<ul class="search-result-list search-result-author-list">' . $listItems . '</ul>';

                                    echo $unorderedList;
                                ?>
                            </div>
                        <?php endif; ?>
                        <div><?= Yii::t('app', 'DOI') ?>:<?php echo "10.5524/" . $dt['identifier'] ?></div>
                    </div>
                </div>

                <?php if (in_array('sample', $display)) { ?>
                    <ul class="search-result-list">
                        <?php foreach ($dsamples as $sample) { ?>
                            <!--Sample section-->
                            <li class="search-result-row">
                                <div class="search-result-icon-container">
                                    <div class="text-icon text-icon-sm text-icon-green search-result-icon" aria-hidden="true">S</div>
                                    <span class="sr-only">Sample</span>
                                </div>
                                <div class="search-result-content">
                                    <h3 class="search-result-title">
                                        <a class="search-result-link" href="<?= $dt['shorturl'] ?>"><?php echo $sample['name'] ?></a>
                                    </h3>
                                    <div class="search-result-subcontent">
                                        <?= $sample['species_common_name'] ?>
                                        NCBI taxonomy :
                                        <a class="search-result-link" target="_blank" href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=<?php echo $sample['species_tax_id'] ?>">
                                            <?= $sample['species_tax_id'] ?>
                                        </a>
                                    </div>
                                    <div class="search-result-subcontent">
                                        <a class="search-result-link" href="<?php echo $dt['shorturl'] ?>">DOI:10.5524/<?= $dt['identifier'] ?></a>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <!--Download File list-->
                <?php if (in_array('file', $display)) { ?>
                    <ul class="search-result-list">
                        <?php foreach ($dfiles as $file) { ?>
                            <li class="search-result-row">
                                <div class="search-result-icon-container">
                                    <div class="text-icon text-icon-sm text-icon-yellow search-result-icon" aria-hidden="true">F</div>
                                    <span class="sr-only">File</span>
                                </div>
                                <div class="search-result-content file-content row">
                                    <div class="col-xs-5 file-name truncate-text"><a class="file-link" href="<?php echo $file['location'] ?>" aria-label="<?= $file['name'] . ' file' ?>"><?php echo $file['name'] ?></a></div>
                                    <div class="col-xs-3 file-type"><?php echo $file['file_type'] ?></div>
                                    <div class="col-xs-3 file-size"><?php echo CHtml::encode(UnitHelper::specifySizeUnits($file['size'], null, 2)) ?></div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>