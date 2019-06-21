<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Public data archive links</h3>
    <a class="myHint" style="float: none;" data-content="You should include top level accessions only, e.g. If you add the BioProject accession there is no need to add every BioSample accession contained within that BioProject."></a>


    <p class="note">
        Have you already submitted data to a public repository that is directly described as part of this dataset? E.g. raw sequence data submitted to the Sequence Read Archives.
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#"
           data-target="public-links"
           id="public-links-yes"
           class="btn additional-button <?php if ($isPublicLinks === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#"
           data-target="public-links"
           data-next-block="related-doi-block"
           data-url="/adminLink/deleteLinks"
           id="public-links-no"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isPublicLinks === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="public-links"<?php if ($isPublicLinks !== true): ?> style="display: none"<?php endif; ?>>
        <p class="note">Please select the appropriate database from the list (you may repeat process to add different database links).</p>

        <div class="control-group">
            <label class='control-label'>Database</label>
            <a class="myHint" data-html="true" data-content="Please contact <a href=&quot;mailto:database@gigasciencejournal.com&quot; >database@gigasciencejournal.com</a> to request the addition of a new database."></a>
            <div class="controls">
                <?= CHtml::dropDownList('prefix',
                    null,
                    array('' => 'Please select') + CHtml::listData($link_database,'prefix','prefix'),
                    array('class'=>'js-database dropdown-white', 'style'=>'width:250px'));
                ?>
            </div>
        </div>

        <p class="note js-set-database">Please select add accession numbers to your data in the above database</p>

        <div class="control-group js-set-database">
            <label class='control-label'>Accession number</label>
            <a class="myHint" data-content="Please provide unique identifier of linked data, e.g. an SRA accession; SRS012345."></a>
            <div class="controls">
                <?= CHtml::textField('link', '', array('class'=>'js-acc-num', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"Unique identifier of linked data")); ?>
                <a href="#" dataset-id="<?=$model->id?>" class="btn js-not-allowed" style="margin-left: 20px;"/>Add Link</a>
            </div>
        </div>

        <div class="grid-view">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th id="author-grid_c0" width="45%">Link Type</th>
                    <th id="author-grid_c0" width="45%">Link</th>
                    <th id="author-grid_c5" class="button-column" width="10%"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($links as $link): ?>
                    <tr class="odd js-my-item">
                        <td><?= ($link->is_primary)?  "ext_acc_mirror" : "ext_acc_link" ?></td>
                        <td><?= $link->link ?></td>
                        <td class="button-column">
                            <input type="hidden" class="js-my-id" value="<?= $link->id ?>">
                            <a class="js-delete-link delete-title" link-id="<?=$link->id?>" data-id="<?= $model->id ?>" title="delete this row">
                                <img alt="delete this row" src="/images/delete.png">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="js-no-results"<?php if ($links): ?> style="display: none"<?php endif ?>>
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
    var publicLinksDiv = $('#public-links');

    $(document).on('change', '#prefix', function () {
        if ($(this).val()){
            $('.js-set-database').show();
        } else {
            $('.js-set-database').hide();
        }
    });

    $(publicLinksDiv).on('keydown', 'input[name="link"]', function () {
        setTimeout((function(){
            makePublicAddActiveIfCan();
        }), 50);
    });

    $(publicLinksDiv).on('change', '#prefix', function () {
        makePublicAddActiveIfCan();
    });

    function makePublicAddActiveIfCan() {
        if (
            $('#prefix', publicLinksDiv).val()
            && $('input[name="link"]', publicLinksDiv).val()
        ) {
            $('.js-not-allowed', publicLinksDiv).removeClass('js-not-allowed').addClass('js-add-link btn-green');
        } else {
            $('.js-add-link', publicLinksDiv).removeClass('js-add-link btn-green').addClass('js-not-allowed');
        }
    }

    $(publicLinksDiv).on('click', ".js-add-link", function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var database = $(".js-database :selected").text();
        var accNum = $(".js-acc-num").val();

        $.ajax({
            type: 'POST',
            url: '/adminLink/getLink',
            data:{'dataset_id': did, 'database': database, 'acc_num': accNum},
            success: function(response){
                if(response.success) {
                    var exit = false;
                    var trs = $('#public-links').find('.odd');
                    trs.each(function() {
                        let tr = $(this);
                        let link_type = tr.children('td').eq(0).text().trim();
                        let link = tr.children('td').eq(1).text().trim();

                        if (response.link['link_type'] == link_type && link == response.link['link']) {
                            alert('This link has been added already.');
                            exit = true;
                            return false;
                        }
                    });

                    if (exit) {
                        return false;
                    }

                    var tr = '<tr class="odd js-my-item">' +
                        '<td>' + response.link['link_type'] + '</td>' +
                        '<td>' + response.link['link'] + '</td>' +
                        '<td class="button-column">' +
                        '<a class="js-delete-link delete-title" title="delete this row">' +
                        '<img alt="delete this row" src="/images/delete.png">' +
                        '</a>' +
                        '</td>' +
                        '</tr>';

                    $('.js-no-results', publicLinksDiv).before(tr);
                    $('.js-no-results', publicLinksDiv).hide();

                    $('input[name="link"]', publicLinksDiv).val('');
                    $('.js-add-link', publicLinksDiv).removeClass('js-add-link btn-green').addClass('js-not-allowed');

                    $('#related-doi-block').show();

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

    $(publicLinksDiv).on('click', ".js-delete-link", function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();

        $(this).closest('tr').remove();

        if (publicLinksDiv.find('.odd').length === 0) {
            $('.js-no-results', publicLinksDiv).show();
        }

        checkIfCanSave();
    });
</script>
