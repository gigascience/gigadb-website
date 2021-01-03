<section>
    <div class="table-responsive">
    <table class="table table-bordered" id="list">
        <thead>
            <tr>
                <th>
                    <?= Yii::t('app', 'DOI') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Title') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Dataset Type') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Status') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Publication Date') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Modification Date') ?>
                </th>
                <th>
                    <?= Yii::t('app','File Count') ?>
                </th>
            </tr>
        </thead>
        <?php $data = $authoredDatasets; ?>
        <?php
        for ($i = 0; $i < count($authoredDatasets); $i++) {
            $class = $i % 2 == 0 ? 'even' : 'odd';
            if(isset($selected) && $data[$i]->id==$selected) {
                $class = 'submit-selected';
            }
            ?>
            <tr class="<?php echo $class; ?>" id="js-dataset-row-<?=$data[$i]->id?>">
                <?
                $upload_status = $data[$i]->upload_status;

                if ( $upload_status != 'Published' && $upload_status!='Private' ) { ?>
                    <td class="content-popup" data-content="<? echo CHtml::encode($data[$i]->description); ?>">
                        unknown
                    </td>
                    <? } else { ?>
                        <td class="content-popup" data-content="<? echo CHtml::encode($data[$i]->description); ?>">
                            <? echo CHtml::link("10.5524/" . $data[$i]->identifier, "/dataset/" . $data[$i]->identifier, array('target' => '_blank')); ?>
                        </td>
                        <? } ?>
                            <td class="left content-popup" data-content="<? echo CHtml::encode($data[$i]->description); ?>">
                                <? echo $data[$i]->title; ?>
                            </td>
                            <td>
                                <? foreach ($data[$i]->datasetTypes as $type) { ?>
                                    <?= $type->name ?>
                                        <? } ?>
                            </td>
                            <td>
                                <?= CHtml::encode($data[$i]->upload_status) ?>
                            </td>
                            <td>
                                <? echo CHtml::encode($data[$i]->publication_date); ?>
                            </td>
                            <td>
                                <? echo CHtml::encode($data[$i]->modification_date); ?>
                            </td>
                            <td>
                                <? echo count($data[$i]->files); ?>
                            </td>
            </tr>
            <? } ?>
    </table>
    </div>
</section>