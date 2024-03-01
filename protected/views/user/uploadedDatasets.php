<section>
    <div class="table-responsive">
      <div class="mb-20">
        <ul class="list-unstyled content-text p-0">
          <li><span class="text-bold">Update dataset</span>: Update the metadata of the dataset.</li>
          <li><span class="text-bold">Delete dataset</span>: Delete the dataset and all its files.</li>
          <li><span class="text-bold">Upload files</span>: Upload files to the dataset, delete the existing files or edit their metadata.</li>
        </ul>
      </div>
    <table class="table table-bordered submitted-table" id="list">
        <thead>
            <tr>
                <th style="width: 1%;">
                    <?= Yii::t('app', 'DOI') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Title') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Subject') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Dataset Type') ?>
                </th>
                <th>
                    <?= Yii::t('app', 'Status') ?>
                </th>
                <th style="width: 1%;">
                    <?= Yii::t('app', 'Publication Date') ?>
                </th>
                <th style="width: 1%;">
                    <?= Yii::t('app', 'Modification Date') ?>
                </th>
                <th style="width: 1%;">
                    <?= Yii::t('app','File Count') ?>
                </th>
                <th style="width: 1%;">
                    <?= Yii::t('app', 'Operation') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $data = $uploadedDatasets; ?>
            <?php
        for ($i = 0; $i < count($uploadedDatasets); $i++) {
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
                                    <? echo $data[$i]->commonNames; ?>
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
                                <td>
                                    <? if ($data[$i]->upload_status !='Published' && $data[$i]->upload_status!='AuthorReview' && $data[$i]->upload_status!='Private'){ ?>
                                        <div>
                                            <a class="update btn btn-transparent tooltip-trigger" data-toggle="tooltip" title="Update the metadata of the dataset" href=<? echo "/datasetSubmission/datasetManagement/id/" . $data[$i]->id ?>>Update<br />dataset</a>
                                            <button class="js-delete-dataset btn btn-transparent" data-toggle="tooltip" title="Delete the dataset and all its files" did="<?=$data[$i]->id?>">
                                       Delete<br />dataset</button>
                                            <?php if ($data[$i]->upload_status === "UserUploadingData" || $data[$i]->upload_status === "DataPending") {
                                              ?>
                                              <a class="upload btn btn-transparent" href="/authorisedDataset/uploadFiles/id/<?php echo $data[$i]->identifier; ?>" data-toggle="tooltip" title="Upload files to the dataset, delete the existing files or edit their metadata">Upload<br />Files</a>
                                              <?php
                                            } ?>
                                        </div>
                                    <? } ?>
                                </td></tr>
                <? } ?>
        </tbody>
    </table>
    </div>
</section>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>