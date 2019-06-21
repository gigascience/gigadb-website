<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Related GigaDB Datasets</h3>
    <a class="myHint" style="float: none;" data-content="If this dataset is directly related to any other previously published GigaDB dataset, for example an update or extension to, please provide the dataset ID and the relationship to complete the sentence below."></a>


    <p class="note">
        Does this dataset use or relate to any other GigaDB dataset?
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#"
           data-target="related-doi"
           id="related-doi-yes"
           class="btn additional-button <?php if ($isRelatedDoi === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#"
           data-target="related-doi"
           data-next-block="projects-block"
           id="related-doi-no"
           data-url="/adminRelation/deleteRelations"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isRelatedDoi === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="related-doi"<?php if ($isRelatedDoi !== true): ?> style="display: none"<?php endif; ?>>
        <div class="control-group" style="text-align: right;">
            <label>The dataset I am now uploading</label>
            <?= CHtml::dropDownList('relation', null,
                array('' => 'Please select') + CHtml::listData(Relationship::model()->findAll(), 'id', 'name'),
                array('class'=>'js-relation-relationship dropdown-white','style'=>'width:250px')); ?>
            <label>dataset (DOI)</label>
            <?= CHtml::dropDownList('dataset_doi', null,
                array('' => 'Please select') + CHtml::listData(Util::getDois(), 'identifier', 'identifier'),
                array('class'=>'js-relation-doi dropdown-white','style'=>'width:250px')); ?>
            <a href="#" dataset-id="<?=$model->id?>" class="btn js-not-allowed"/>Add Related Doi</a>
        </div>

        <div class="grid-view">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th id="author-grid_c0" width="45%">Related DOI</th>
                    <th id="author-grid_c0" width="45%">Relationship</th>
                    <th id="author-grid_c5" class="button-column" width="10%"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($relations as $relation): ?>
                    <tr class="odd js-my-item" id="js-relation-<?=$relation->id?>">
                        <td><?= $relation->related_doi ?></td>
                        <td>
                            <?= $relation->relationship->name ?>
                        </td>
                        <td class="button-column">
                            <input type="hidden" class="js-relationship-id" value="<?= $relation->relationship->id ?>">
                            <input type="hidden" class="js-my-id" value="<?= $relation->id ?>">
                            <a class="js-delete-relation delete-title" relation-id="<?=$relation->id?>" data-id="<?= $model->id ?>" title="delete this row">
                                <img alt="delete this row" src="/images/delete.png">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr class="js-no-results"<?php if ($relations): ?> style="display: none"<?php endif ?>>
                    <td colspan="4">
                        <span class="empty">No results found.</span>
                    </td>
                </tr>
                <tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var relatedDoiDiv = $('#related-doi');

    $(document).on('change', '#relation, #dataset_doi', function () {
        if ($('#relation').val() && $('#dataset_doi').val()){
            $('.js-not-allowed', relatedDoiDiv).removeClass('js-not-allowed').addClass('js-add-relation btn-green');
        } else {
            $('.js-add-relation', relatedDoiDiv).removeClass('js-add-relation btn-green').addClass('js-not-allowed');
        }
    });

    $(relatedDoiDiv).on('click', ".js-add-relation", function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var doi = $('.js-relation-doi').val();
        var relationship = $('.js-relation-relationship').val();

        $.ajax({
            type: 'POST',
            url: '/adminRelation/getRelation',
            data:{'dataset_id': did, 'doi': doi, 'relationship': relationship},
            success: function(response){
                if(response.success) {
                    var exit = false;
                    var trs = relatedDoiDiv.find('.odd');
                    trs.each(function() {
                        let tr = $(this);
                        let related_doi = tr.children('td').eq(0).text().trim();
                        let relationship_name = tr.children('td').eq(1).text().trim();

                        if (response.relation['related_doi'] == related_doi && relationship_name == response.relation['relationship_name']) {
                            alert('This relationship has been added already.');
                            exit = true;
                            return false;
                        }
                    });

                    if (exit) {
                        return false;
                    }

                    var tr = '<tr class="odd js-my-item">' +
                            '<input type="hidden" class="js-relationship-id" value="' + response.relation['relationship_id'] + '">' +
                            '<td>' + response.relation['related_doi'] + '</td>' +
                            '<td>' + response.relation['relationship_name'] + '</td>' +
                            '<td class="button-column">' +
                            '<a class="js-delete-relation delete-title" title="delete this row">' +
                            '<img alt="delete this row" src="/images/delete.png">' +
                            '</a>' +
                            '</td>' +
                            '</tr>';

                    $('.js-no-results', relatedDoiDiv).before(tr);
                    $('.js-no-results', relatedDoiDiv).hide();

                    $('#projects-block').show();

                    checkIfCanSave();

                    $(".delete-title").tooltip({'placement':'left'});
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });


    $(relatedDoiDiv).on('click', ".js-delete-relation", function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();

        $(this).closest('tr').remove();

        if (relatedDoiDiv.find('.odd').length === 0) {
            $('.js-no-results', relatedDoiDiv).show();
        }

        checkIfCanSave();
    });
</script>
