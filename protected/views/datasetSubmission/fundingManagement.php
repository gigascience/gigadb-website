<?php
/** @var Dataset $model */
/** @var Funder[] $funders */
/** @var DatasetFunder[] $fundings */

$disabled = $model->getFunding() === null || ($model->getFunding() === true && !$fundings);
$fundersList = array('Please select…');
foreach ($funders as $funder) {
    $fundersList[$funder->id] = $funder->primary_name_display;
}
?>

<h2>Add Fundings</h2>
<div class="clear"></div>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <div class="form-horizontal">
        <div id="funding-grid" class="grid-view">
            <p class="note">
                Would you like to acknowledge any funding bodies that have provided resources to generate these data?
                <a class="myHint" style="float: none;" data-content="We encourage the addition of funding information to acknowledge the support from your funders, we require that funding information is highly structured to ensure that it can be machine readable. If your funding body is not already included in our database please contact us (database@gigasciencejournal.com) with the name, country and a URL of the funder and we can add it to the list."></a>
            </p>

            <div style="text-align: center; margin-bottom: 15px;">
                <a href="#"
                   id="funding-yes-button"
                   class="btn additional-button <?php if ($model->getFunding() === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
                <a href="#"
                   id="funding-no-button"
                   data-id="<?= $model->id ?>"
                   class="btn additional-button <?php if ($model->getFunding() === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
            </div>

            <div id="funding"<?php if ($model->getFunding() !== true): ?> style="display: none;"<?php endif; ?>>
                <p class="note">Please select the appropriate funding body from the dropdown list. This list is from FundRef  and should be used if possible. If your funding body is not present you may either contact FundRef directly to ask for its addition (please allow some time for the updates to propagate to this page) or use the Funding body “Other” from the list.</p>

                <div class="control-group">
                    <div style="text-align: center">
                        <?= CHtml::dropDownList('funder_id',
                            null,
                            $fundersList,
                            array('class'=>'js-database dropdown-white', 'style'=>'width:250px'));
                        ?> * required
                    </div>
                </div>

                <p class="note">Often Funding bodies provide funds to different “programs”, if appropriate you may type the funding bodies Program name here:</p>

                <div class="control-group">
                    <div style="text-align: center">
                        <?= CHtml::textField('program_name', '', array('size' => 60, 'maxlength' => 100, 'style'=>'width:240px;margin-right:60px;', 'placeholder'=>"Program name, e.g. FP7 framework")); ?>
                    </div>
                </div>

                <p class="note">Please now provide the unique reference to the grant/funding received</p>

                <div class="control-group">
                    <div style="text-align: center">
                        <?= CHtml::textField('grant', '', array('size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"Grant reference", 'class' => 'js-funding-required')); ?> * required
                    </div>
                </div>

                <p class="note">Now add the last name and first initial of the Principal investigator names on the grant application</p>

                <div class="control-group">
                    <div style="text-align: center">
                        <?= CHtml::textField('pi_name', '', array('size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"PI name, e.g. Bloggs J", 'class' => 'js-funding-required')); ?> * required
                    </div>
                </div>

                <div class="control-group">
                    <div style="text-align: center">
                        <a href="#" class="btn js-not-allowed"/>Add Link</a>
                    </div>
                </div>

                <div class="grid-view">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th id="author-grid_c0" width="22%">Funding body</th>
                            <th id="author-grid_c1" width="23%">Program Name</th>
                            <th id="author-grid_c2" width="22%">Grant Number</th>
                            <th id="author-grid_c3" width="23%">PI name</th>
                            <th id="author-grid_c4" class="button-column" width="10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($fundings as $funding): ?>
                            <tr class="odd">
                                <td><?= $funding->funder->primary_name_display ?></td>
                                <td><?= $funding->comments ?></td>
                                <td><?= $funding->grant_award ?></td>
                                <td><?= $funding->awardee ?></td>
                                <td class="button-column">
                                    <a class="js-delete-funding delete-title" title="delete this row">
                                        <img alt="delete this row" src="/images/delete.png">
                                    </a>
                                    <input type="hidden" class="js-funding-id" value="<?= $funding->id ?>">
                                    <input type="hidden" class="js-funder-id" value="<?= $funding->funder_id ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="js-no-results"<?php if ($fundings): ?> style="display: none"<?php endif ?>>
                            <td colspan="5">
                                <span class="empty">No results found.</span>
                            </td>
                        </tr>
                        <tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clear"></div>
            <div style="text-align:center" id="funding-save">
                <a href="/datasetSubmission/additionalManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
                <a href="/datasetSubmission/fundingManagement/id/<?= $model->id ?>"
                   class="btn <?php if ($disabled): ?>js-not-allowed<?php else: ?>btn-green js-save-funding<?php endif; ?>">Save</a>
                <a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>"
                   class="btn <?php if ($disabled): ?>js-not-allowed<?php else: ?>btn-green js-save-funding<?php endif; ?>">Next</a>
            </div>
        </div>
    </div>
</div>

<script>
    var datasetId = <?= $model->id ?>;
    var fundingDiv = $('#funding');

    $(".delete-title").tooltip({'placement':'top'});

    $(document).on('click', '.js-not-allowed', function() {
        return false;
    });

    function makeSaveActiveIfCan() {
        if ($('#funding-no-button').hasClass('btn-green') || $('#funding').find('.odd').length) {
            $('#funding-save').find('.js-not-allowed').removeClass('js-not-allowed').addClass('btn-green js-save-funding');
        } else {
            $('#funding-save').find('.js-save-funding').removeClass('btn-green js-save-funding').addClass('js-not-allowed');
        }
    }

    function makeAddActiveIfCan() {
        if (
            $('#funder_id').val() !== '0'
            && $('#grant').val()
            && $('#pi_name').val()
        ) {
            $(fundingDiv).find('.js-not-allowed').removeClass('js-not-allowed').addClass('btn-green js-add-funding');
        } else {
            $(fundingDiv).find('.js-add-funding').removeClass('btn-green js-add-funding').addClass('js-not-allowed');
        }
    }

    function cleanFundingForm()
    {
        $('#funder_id').val('0');
        $('#grant').val('');
        $('#pi_name').val('');
        $('#program_name').val('');
    }

    $(document).on('click', '.js-no-button', function(e) {
        var $this = $(this);

        var items = fundingDiv.find('.odd');
        if (items.length > 0) {
            if (!confirm('Are you sure you want to delete all items?')) {
                return false;
            }
        }

        $this.addClass('btn-green btn-disabled');
        $this.removeClass('js-no-button');
        $this.siblings().removeClass('btn-green btn-disabled').addClass('js-yes-button');

        items.remove();
        $('.js-no-results', fundingDiv).show();

        fundingDiv.hide();

        makeSaveActiveIfCan();

        return false;
    });

    $(".js-yes-button").click(function() {
        var $this = $(this);

        $this.addClass('btn-green btn-disabled');
        $this.removeClass('js-yes-button');
        $this.siblings().removeClass('btn-green btn-disabled').addClass('js-no-button');

        fundingDiv.show();

        makeSaveActiveIfCan();

        return false;
    });

    function saveFunding(url) {
        var fundings = [];

        let trs = fundingDiv.find('.odd');
        trs.each(function() {
            let tr = $(this);

            let id = tr.find('.js-funding-id').val();
            if (!id) {id = 0;}
            let funder_id = tr.find('.js-funder-id').val();
            let program_name = tr.children('td').eq(1).text();
            let grant = tr.children('td').eq(2).text();
            let pi_name = tr.children('td').eq(3).text();

            fundings.push({
                id: id,
                dataset_id: datasetId,
                funder_id: funder_id,
                program_name: program_name,
                grant: grant,
                pi_name: pi_name,
            });
        });

        $.ajax({
            type: 'POST',
            url: '/datasetSubmission/saveFundings',
            data:{
                fundings: fundings,
                dataset_id: datasetId,
            },
            success: function(response){
                if(!response.success) {
                    alert(response.message);
                } else {
                    window.location.href = url;
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    }

    $(document).on('click', ".js-save-funding", function() {
        saveFunding($(this).attr('href'));

        return false;
    });

    $(fundingDiv).on('change', '#funder_id', function () {
        makeAddActiveIfCan();
    });

    $(fundingDiv).on('keydown', '.js-funding-required', function () {
        setTimeout((function(){
            makeAddActiveIfCan();
        }), 50);
    });

    $(fundingDiv).on('click', ".js-add-funding", function() {
        $.ajax({
            type: 'POST',
            url: '/datasetSubmission/validateFunding',
            data:{
                'dataset_id': datasetId,
                'funder_id': $('#funder_id').val(),
                'grant': $('#grant').val(),
                'pi_name': $('#pi_name').val(),
                'program_name': $('#program_name').val()
            },
            success: function(response){
                if(response.success) {
                    var tr =
                        '<tr class="odd">' +
                            '<td>' + response.funding['funder_name'] + '</td>' +
                            '<td>' + response.funding['program_name'] + '</td>' +
                            '<td>' + response.funding['grant'] + '</td>' +
                            '<td>' + response.funding['pi_name'] + '</td>' +
                            '<td class="button-column">' +
                                '<a class="js-delete-funding delete-title" title="delete this row">' +
                                '<img alt="delete this row" src="/images/delete.png">' +
                                '</a>' +
                                '<input type="hidden" class="js-funder-id" value="' + response.funding['funder_id'] + '">' +
                            '</td>' +
                        '</tr>';

                    $('.js-no-results', fundingDiv).before(tr);
                    $('.js-no-results', fundingDiv).hide();

                    cleanFundingForm();
                    makeAddActiveIfCan();
                    makeSaveActiveIfCan();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });

        return false;
    });

    $(fundingDiv).on('click', ".js-delete-funding", function() {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;

        $(this).closest('tr').remove();

        if (fundingDiv.find('.odd').length === 0) {
            $('.js-no-results', fundingDiv).show();
        }

        makeSaveActiveIfCan();
    });
</script>
