<a name="links"></a>
<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Public data archive links</h3>
    <a class="myHint" style="float: none;" data-content="You should include top level accessions only, e.g. If you add the BioProject accession there is no need to add every BioSample accession contained within that BioProject."></a>


    <p class="note">
        Have you already submitted data to a public repository that is directly described as part of this dataset? E.g. raw sequence data submitted to the Sequence Read Archives.
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#links" data-target="public-links" class="btn additional-button <?php if ($isPublicLinks === true): ?>btn-green<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#links"
           data-target="public-links"
           data-url="/adminLink/deleteLinks"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isPublicLinks === false): ?>btn-green<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="public-links"<?php if ($isPublicLinks !== true): ?> style="display: none"<?php endif; ?>>
        <p class="note">Please select the appropriate database from the list (you may repeat process to add different database links).</p>

        <div class="control-group">
            <label class='control-label'>Database</label>
            <a class="myHint" data-html="true" data-content="Please contact <a href=&quot;mailto:database@gigasciencejournal.com&quot; >database@gigasciencejournal.com</a> to request the addition of a new database."></a>
            <div class="controls">
                <?= CHtml::dropDownList('link',
                    null,
                    CHtml::listData($link_database,'prefix','prefix'),
                    array('class'=>'js-database dropdown-white', 'style'=>'width:250px'));
                ?>
            </div>
        </div>

        <p class="note">Please select add accession numbers to your data in the above database</p>

        <div class="control-group">
            <label class='control-label'>Accession number</label>
            <a class="myHint" data-content="Please provide unique identifier of linked data, e.g. an SRA accession; SRS012345."></a>
            <div class="controls">
                <?= CHtml::textField('link', '', array('class'=>'js-acc-num', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"Unique identifier of linked data")); ?>
                <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-link" style="margin-left: 20px;"/>Add Link</a>
            </div>
        </div>

        <div id="author-grid" class="grid-view">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th id="author-grid_c0" width="45%">Link Type</th>
                    <th id="author-grid_c0" width="45%">Link</th>
                    <th id="author-grid_c5" class="button-column" width="10%"></th>
                </tr>
                </thead>
                <tbody>
                <?php if($links) { ?>
                    <?php foreach($links as $link) { ?>
                        <tr class="odd js-my-item">
                            <td><?= ($link->is_primary)?  "ext_acc_mirror" : "ext_acc_link" ?></td>
                            <td><?= $link->link ?></td>
                            <td class="button-column">
                                <a class="js-delete-link delete-title" link-id="<?=$link->id?>" data-id="<?= $model->id ?>" title="delete this row">
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
    $(".js-add-link").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var database = $(".js-database :selected").text();
        var accNum = $(".js-acc-num").val();

        $.ajax({
            type: 'POST',
            url: '/adminLink/addLink',
            data:{'dataset_id': did, 'database': database, 'acc_num': accNum},
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

    $(".js-delete-link").click(function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();
        var  linkid = $(this).attr('link-id');
        var  datasetId = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/adminLink/deleteLink',
            data:{'link_id': linkid, 'dataset_id': datasetId},
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
