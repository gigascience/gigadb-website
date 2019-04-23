<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Related GigaDB Datasets</h3>
    <a class="myHint" style="float: none;" data-content="Dont know what to add here."></a>


    <p class="note">
        Does this dataset use or relate to any other GigaDB dataset?
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#" data-target="related-doi" class="btn additional-button <?php if ($isRelatedDoi === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#"
           data-target="related-doi"
           data-url="/adminRelation/deleteRelations"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isRelatedDoi === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="related-doi"<?php if ($isRelatedDoi !== true): ?> style="display: none"<?php endif; ?>>
        <div class="control-group" style="text-align: right;">
            <label>The dataset I am now uploading</label>
            <?= CHtml::dropDownList('relation', null, CHtml::listData(Relationship::model()->findAll(), 'id', 'name'),array('class'=>'js-relation-relationship dropdown-white','style'=>'width:250px')); ?>
            <label>dataset (DOI)</label>
            <?= CHtml::dropDownList('relation', null, CHtml::listData(Util::getDois(), 'identifier', 'identifier'),array('class'=>'js-relation-doi dropdown-white','style'=>'width:250px')); ?>
            <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-relation"/>Add Related Doi</a>
        </div>

        <div id="author-grid" class="grid-view">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th id="author-grid_c0" width="45%">Related DOI</th>
                    <th id="author-grid_c0" width="45%">Relationship</th>
                    <th id="author-grid_c5" class="button-column" width="10%"></th>
                </tr>
                </thead>
                <tbody>
                <?php if($relations) { ?>
                    <?php foreach($relations as $relation) { ?>
                        <tr class="odd js-my-item" id="js-relation-<?=$relation->id?>">
                            <td><?= $relation->related_doi ?></td>
                            <td><?= $relation->relationship->name ?></td>
                            <td class="button-column">
                                <a class="js-delete-relation delete-title" relation-id="<?=$relation->id?>" data-id="<?= $model->id ?>" title="delete this row">
                                    <img alt="delete this row" src="/images/delete.png">
                                </a>
                            </td>
                        </tr>
                    <? } ?>
                <? } else  { ?>
                <tr>
                    <td colspan="4">
                        <span class="empty">No results found.</span>
                    </td>
                </tr>
                <tr>
                    <? } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(".js-add-relation").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var doi = $('.js-relation-doi').val();
        var relationship = $('.js-relation-relationship').val();

        $.ajax({
            type: 'POST',
            url: '/adminRelation/addRelation',
            data:{'dataset_id': did, 'doi': doi, 'relationship': relationship},
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

    $(".js-delete-relation").click(function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();
        var  relationid = $(this).attr('relation-id');
        var  datasetId = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/adminRelation/deleteRelation',
            data:{'relation_id': relationid, 'dataset_id': datasetId},
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
