<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Other links</h3>
    <a class="myHint" style="float: none;" data-content="Dont know what to add here."></a>


    <p class="note">
        Do you wish to add links to any of the following:
    </p>

    <?php $this->renderPartial('_manuscripts', array('model' => $model, 'isManuscripts' => $isManuscripts)); ?>
    <?php $this->renderPartial('_protocols', array('model' => $model, 'isProtocols' => $isProtocols)); ?>
    <?php $this->renderPartial('_3d_images', array('model' => $model, 'is3dImages' => $is3dImages)); ?>
    <?php $this->renderPartial('_codes', array('model' => $model, 'isCodes' => $isCodes)); ?>
    <?php $this->renderPartial('_sources', array('model' => $model, 'isSources' => $isSources)); ?>

    <div class="clear"></div>
    <div id="others-grid" class="grid-view"<?php if (!$exLinks): ?> style="display: none;"<?php endif ?>>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th id="author-grid_c0" width="35%">Url</th>
                <th id="author-grid_c0" width="35%">Link Description</th>
                <th id="author-grid_c0" width="20%">External Link Type</th>
                <th id="author-grid_c5" class="button-column" width="10%"></th>
            </tr>
            </thead>
            <tbody>
            <?php if($exLinks): ?>
                <?php foreach($exLinks as $exLink): ?>
                    <tr class="odd js-my-item-<?= $exLink->type ?>">
                        <td><?= \yii\helpers\Html::encode($exLink->url) ?></td>
                        <td><?= $exLink->description ?></td>
                        <td><?= $exLink->getTypeName() ?></td>
                        <td class="button-column">
                            <a class="js-delete-exLink delete-title" exLink-id="<?=$exLink->id?>" data-id="<?= $model->id ?>" title="delete this row">
                                <img alt="delete this row" src="/images/delete.png">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">
                    <span class="empty">No results found.</span>
                </td>
            </tr>
            <tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(".js-add-exLink").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var  did = $this.attr('dataset-id');
        var url = $this.closest('.row').find('.js-ex-link').val();
        var externalLinkType = $this.data('type');
        var externalLinkDescription = '';
        var textArea = $this.closest('.row').find('.js-ex-description');
        if (textArea.length) {
            externalLinkDescription = textArea.val();
        }

        $.ajax({
            type: 'POST',
            url: '/adminExternalLink/addExLink',
            data:{'dataset_id': did, 'url': url,  'externalLinkType': externalLinkType, 'externalLinkDescription': externalLinkDescription},
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function(response){
                if(response.success) {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(".js-delete-exLink").click(function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();
        var  exlinkid = $(this).attr('exLink-id');
        var  datasetId = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/adminExternalLink/deleteExLink',
            data:{'exLink_id': exlinkid, 'dataset_id': datasetId},
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function(response){
                if(response.success) {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });
</script>
