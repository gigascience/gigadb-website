<?php
/** @var Dataset $model */
/** @var Unit[] $units */
/** @var Sample[] $samples */
/** @var SampleAttribute[] $sas */
/** @var TemplateName[] $sts */
/** @var TemplateName $template */
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

<h2>Add Samples</h2>
<div class="clear"></div>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <div class="form-horizontal">
        <div id="sample-grid" class="grid-view">
            <p class="note">
                Please supply all the information about the experimental samples included in this dataset. This should include samples used only for example purposes even when you have only performed in-silco analysis.
                <a class="myHint" style="float: none;" data-html="true" data-content="For datasets that include biological sample-related data we would expect the sample metadata to be included in the GigaDB dataset. We understand that the level of sample metadata made available is often limited by sample collection restrictions, but authors should make every effort to provide as comprehensive metadata about samples as is possible. For a checklist of metadata fields please see <a href='http://gigadb.org/site/guide'>http://gigadb.org/site/guide</a>."></a>
            </p>

            <p class="note">
                You should add as many attributes as you have details for, as a guide we provided a small number of templates for common dataset types (genomic, metagenomic and imaging) with a suggestion of possible attributes.
                <a class="myHint" style="float: none;" data-content="Dont know what to add here."></a>
            </p>

            <div class="clear"></div>

            <div class="span6">
                <div class="control-group" id="set-template-div">
                    <label class='control-label'>Choose a template</label>
                    <div class="controls">
                        <?= CHtml::dropDownList('template',
                            $template ? $template->id : null,
                            CHtml::listData($sts,'id','template_name'),
                            array('empty'=> 'Please select', 'class'=>'js-database dropdown-white', 'style'=>'width:200px'));
                        ?>
                        <a href="#" class="btn <?php if (!$template): ?>js-not-allowed<?php else: ?> btn-green js-set-template<?php endif ?>" style="margin-left: 20px;"/>Apply</a>
                    </div>
                </div>
            </div>

            <div class="span5">
                <span>Note- applying a new template will delete any attributes already inserted below</span>
                <a class="myHint" style="float: none;" data-content="Dont know what to add here."></a>
            </div>

            <div class="clear"></div>

            <p class="note">
                For very few samples you may type the information directly into the web-form below.
            </p>

            <div class="additional-bordered" style="overflow-x: auto;margin: 15px 0;">
                <table class="table table-bordered sample-tab-table" id="samples-table">
                    <thead>
                    <tr>
                        <th style="white-space: nowrap;">Sample ID</th>
                        <th style="white-space: nowrap;">Species name</th>
                        <th style="white-space: nowrap;" class="sample-attribute-column">Description</th>
                        <?php if ($rows): ?>
                            <?php for ($j = 3, $k = count($rows[0]); $j < $k; $j++): ?>
                                <?php
                                $attributeName = isset($rows[0][$j]) ? $rows[0][$j] : '';
                                $attribute = Attribute::model()->findByAttributes(array('attribute_name' => $attributeName));
                                ?>
                                <th class="sample-attribute-column">
                                <a class="js-delete-column delete-title" title="delete this column">
                                    <img alt="delete this column" src="/images/delete.png">
                                </a>

                                <input type="text" class="js-attribute-name-autocomplete" placeholder='Attribute name' value="<?= $attributeName ?>">

                                <?= CHtml::dropDownList(
                                    'units[]',
                                    $attribute ? $attribute->allowed_units : null,
                                    CHtml::listData($units, 'id', 'name'),
                                    array('empty'=> 'N/A','style'=>'width:70px;margin-right:20px;', 'class' => 'dropdown-white')
                                ); ?>
                            <?php endfor ?>
                        <?php elseif (!$template): ?>
                            <?php foreach ($sas as $sa): ?>
                                <?php if ($sa->attribute->attribute_name == 'description') continue ?>
                                <th class="sample-attribute-column">
                                    <a class="js-delete-column delete-title" title="delete this column">
                                        <img alt="delete this column" src="/images/delete.png">
                                    </a>

                                    <input type="text" class="js-attribute-name-autocomplete" placeholder='Attribute name' value="<?= $sa->attribute->attribute_name ?>">

                                    <?= CHtml::dropDownList('units[]', $sa->unit_id, CHtml::listData($units, 'id', 'name'),array('empty'=> 'N/A','style'=>'width:70px;margin-right:20px;', 'class' => 'dropdown-white')); ?>
                                </th>
                            <?php endforeach ?>
                        <?php else: ?>
                            <?php foreach ($template->attributes as $attribute): ?>
                                <th class="sample-attribute-column">
                                    <a class="js-delete-column delete-title" title="delete this column">
                                        <img alt="delete this column" src="/images/delete.png">
                                    </a>

                                    <input type="text" class="js-attribute-name-autocomplete" placeholder='Attribute name' value="<?= $attribute->attribute_name ?>">

                                    <?= CHtml::dropDownList('units[]', $attribute->allowed_units, CHtml::listData($units, 'id', 'name'),array('empty'=> 'N/A','style'=>'width:70px;margin-right:20px;', 'class' => 'dropdown-white')); ?>
                                </th>
                            <?php endforeach ?>
                        <?php endif ?>
                        <th class="button-column"><a href="#" class="btn btn-green btn-sample js-add-column" style="max-width: 100px;">Add Column</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows): ?>
                        <?php for ($i = 1, $n = count($rows); $i < $n; $i++): ?>
                            <tr class="item">
                                <td style="white-space: nowrap;">
                                    <a class="js-delete-row delete-title" title="delete this sample">
                                        <img alt="delete this row" src="/images/delete.png">
                                    </a>
                                    <input type="text" placeholder='Sample ID' value="<?= isset($rows[$i][0]) ? $rows[$i][0] : '' ?>" style="margin-right: 18px;">
                                </td>
                                <td>
                                    <input type="text" class="js-species-autocomplete" placeholder='Species name' value="<?= isset($rows[$i][1]) ? $rows[$i][1] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" placeholder='Short description of sample' value="<?= isset($rows[$i][2]) ? $rows[$i][2] : '' ?>" style="width:250px;">
                                </td>
                                <?php for ($j = 3, $k = count($rows[0]); $j < $k; $j++): ?>
                                    <td>
                                        <input type="text" placeholder='Attribute value' value="<?= isset($rows[$i][$j]) ? $rows[$i][$j] : '' ?>" style="width: 250px;">
                                    </td>
                                <?php endfor ?>
                                <td class="button-column">
                                </td>
                            </tr>
                        <?php endfor ?>
                    <?php elseif (!$template): ?>
                        <?php foreach($samples as $sample): ?>
                            <tr class="item">
                                <td style="white-space: nowrap;">
                                    <a class="js-delete-row delete-title" title="delete this sample">
                                        <img alt="delete this row" src="/images/delete.png">
                                    </a>
                                    <input type="text" placeholder='Sample ID' value="<?= $sample->name ?>" style="margin-right: 18px;">
                                </td>
                                <td>
                                    <input type="text" class="js-species-autocomplete" placeholder='Species name' value="<?= $sample->species->common_name ?>">
                                </td>
                                <td>
                                    <?php $mySa = $sample->getSampleAttributeByAttributeName('description') ?>
                                    <input type="text" placeholder='Short description of sample' value="<?= $mySa ? $mySa->value : '' ?>" style="width: 250px;">
                                </td>
                                <?php foreach ($sas as $sa): ?>
                                    <?php if ($sa->attribute->attribute_name == 'description') continue ?>
                                    <td>
                                        <?php $mySa = $sample->getSampleAttributeByAttributeIdAndUnitId($sa->attribute_id, $sa->unit_id) ?>
                                        <input type="text" placeholder='Attribute value' value="<?= $mySa ? $mySa->value : '' ?>" style="width: 250px;">
                                    </td>
                                <?php endforeach ?>
                                <td class="button-column">
                                    <input type="hidden" class="js-sample-id" value="<?= $sample->id ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr id="js-no-results"<?php if ((!$template && $samples) || $rows): ?> style="display: none;"<?php endif ?>>
                        <td colspan="4">
                            <span class="empty">No results found.</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="button-column" style="border-top:none;text-align: left;">
                            <a href="#" class="btn btn-green btn-sample js-add-row" style="margin-left: 30px;">Add Row</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <p class="note">
                Alternatively, if you have many samples you may wish to prepare the information in a spreadsheet and upload that to this page, the uploader will only parse CSV or TSV files.
            </p>

            <p class="note">
                Note – ALL samples must include Sample ID, Species name and  sample description as mandatory information.
                Units should be included in header row in parentheses e.g. depth (m)
            </p>

            <div class="clear"></div>

            <div class="span6">
                <form action="/datasetSubmission/validateSamples" data-action="<?= '/datasetSubmission/sampleManagement/id/'. $model->id ?>" method="POST" id="upload-samples" enctype="multipart/form-data">
                    <div class="control-group" id="add-samples-div">
                        <label class='control-label'>Upload sample metadata</label>
                        <div class="controls">
                            <input type="file" id="samples" name="samples">
                            <input type="hidden" name="upload" value="true">
                            <a href="#" class="btn js-not-allowed" style="margin-left: 20px;"/>Upload</a>
                            <a class="btn btn-green" id="js-add-samples" style="margin-left: 20px;display: none;" value="Upload"/>Upload</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="span5">
                <span>Note- uploading metadata file will overwrite any values already inserted above</span>
            </div>

            <div class="clear"></div>
            <div style="text-align:center" id="samples-save">
                <a href="/datasetSubmission/fundingManagement/id/<?= $model->id ?>" class="btn-green">Previous</a>
                <a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>"
                   class="btn btn-green js-save-samples">Save</a>
                <a href="/datasetSubmission/end/id/<?= $model->id ?>"
                   class="btn btn-green js-save-samples">Next</a>
            </div>
        </div>
    </div>
</div>

<script>
    $('#samples-table').resizable();

    var species = JSON.parse('<?= json_encode(array_values(CHtml::listData($species, 'id', 'common_name'))) ?>');

    $( ".js-species-autocomplete" ).autocomplete({
        source: species
    });

    var attrs = JSON.parse('<?= json_encode(array_values(CHtml::listData($attrs, 'id', 'attribute_name'))) ?>');

    $( ".js-attribute-name-autocomplete" ).autocomplete({
        source: attrs
    });

    var baseUrl = '<?= '/datasetSubmission/sampleManagement/id/'. $model->id ?>';
    var units = JSON.parse('<?= json_encode(CHtml::listData($units, 'id', 'name')) ?>');
    var datasetId = <?= $model->id ?>;
    var samplesTable = $('#samples-table');

    $(samplesTable).on('click', ".js-delete-row", function() {
        if (!confirm('Are you sure you want to delete this row?')) {
            return false;
        }


        $(this).closest('tr').remove();

        if (samplesTable.find('.item').length === 0) {
            $('#js-no-results').show();
        }

        return false;
    });

    $(samplesTable).on('click', ".js-delete-column", function() {
        if (!confirm('Are you sure you want to delete this column?')) {
            return false;
        }


        var th = $(this).closest('th');
        var index = $(this).closest('th').index();

        samplesTable.find('.item').each(function () {
            $(this).find('td').eq(index).remove();
        });

        th.remove();

        return false;
    });

    var newTd = '<td>' +
        '<input type="text" placeholder=\'Attribute value\' style="width: 250px;">' +
        '</td>';

    var unitsSelect = '<select style="width:70px;margin-right:20px;" class="dropdown-white" name="units[]">' +
        '<option value="">N/A</option>';

    for (var i in units) {
        unitsSelect += '<option value="'+i+'">'+units[i]+'</option>';
    }

    unitsSelect +='</select>';

    var newTh = '<th class="sample-attribute-column">' +
        '<a class="js-delete-column delete-title" title="delete this column">' +
        '<img alt="delete this column" src="/images/delete.png">' +
        '</a>' +

        '<input type="text" placeholder=\'Attribute name\' class="js-attribute-name-autocomplete">' +

        unitsSelect +

        '</td>';

    $(samplesTable).on('click', ".js-add-column", function() {
        var th = $(this).closest('th');
        var index = $(this).closest('th').index();

        samplesTable.find('.item').each(function () {
            $(this).find('td').eq(index).before(newTd);
        });

        th.before(newTh);

        samplesTable.find('.sample-attribute-column').last().find(".js-attribute-name-autocomplete").autocomplete({
            source: attrs
        });

        return false;
    });

    $(samplesTable).on('click', ".js-add-row", function() {
        var newTr = '<tr class="item">';

        newTr += '<td style="white-space: nowrap;">' +
            '<a class="js-delete-row delete-title" title="delete this sample">' +
            '<img alt="delete this row" src="/images/delete.png">' +
            '</a>' +
            '<input type="text" placeholder=\'Sample ID\' style="margin-right: 18px;">' +
            '</td>';

        newTr += '<td>' +
            '<input type="text" class="js-species-autocomplete" placeholder=\'Species name\'>' +
            '</td>';

        newTr += '<td>' +
            '<input type="text" placeholder=\'Short description of sample\' style="width:250px;">' +
            '</td>';

        for (var i = 0, n = samplesTable.find('.sample-attribute-column').length; i < n; i++) {
            newTr += newTd;
        }

        newTr += '<td class="button-column"></td>' +
            '</tr>';

        $('#js-no-results').before(newTr);

        $('#js-no-results').hide();

        samplesTable.find('.item').last().find('.js-species-autocomplete').autocomplete({
            source: species
        });

        return false;
    });

    $(document).on('click', ".js-save-samples", function() {
        saveSamples($(this).attr('href'));

        return false;
    });

    $(document).on('click', '#js-add-samples', function() {
        var form = $('#upload-samples');

        form.ajaxSubmit({
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function (response) {
                if (response.success) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'rows',
                        value: JSON.stringify(response.rows)
                    }).appendTo(form);

                    if (response.matches) {
                        var question = 'Do you want:';
                        for (var i in response.matches) {
                            question += ' ' + response.matches[i] + ' instead ' + i + ',';
                        }

                        question = question.slice(0,-1) + '?';

                        if (confirm(question)) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'matches',
                                value: JSON.stringify(response.matches)
                            }).appendTo(form);
                        }
                    }

                    form.attr('action', form.data('action'));
                    form.submit();
                } else {
                    alert(response.message)
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });

        return false;
    });

    function saveSamples(url) {
        var samples = [];
        var sample_attrs = [];

        let trs = samplesTable.find('.item');
        let attr_tds = samplesTable.find('.sample-attribute-column');

        trs.each(function() {
            let tr = $(this);

            let id = tr.find('.js-sample-id').val();
            if (!id) {id = 0;}
            let sample_id = tr.children('td').eq(0).find('input').val();
            let species_name = tr.children('td').eq(1).find('input').val();


            let attr_values = [];
            for (var i = 0, n = attr_tds.length; i < n; i++) {
                let attr_value = tr.children('td').eq(2 + i).find('input').val();
                attr_values.push(attr_value);
            }

            samples.push({
                id: id,
                sample_id: sample_id,
                species_name: species_name,
                attr_values: attr_values,
            });
        });

        attr_tds.each(function() {
            let td = $(this);

            let id = td.find('.js-sample-attr-id').val();
            if (!id) {id = 0;}
            let attr_name = td.find('input').val();
            if (!attr_name) {attr_name = 'description';}
            let unit_id = td.find('select').val();
            if (!unit_id) {unit_id = 0;}


            sample_attrs.push({
                id: id,
                attr_name: attr_name,
                unit_id: unit_id,
            });
        });

        $.ajax({
            type: 'POST',
            url: '/datasetSubmission/saveSamples',
            data:{
                dataset_id: datasetId,
                samples: samples,
                sample_attrs: sample_attrs,
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

    $(document).on('change', '.js-attribute-name-autocomplete', function () {
        var $this = $(this);

        $.ajax({
            type: 'GET',
            url: '/datasetSubmission/checkUnit',
            data:{
                attr_name: $this.val(),
            },
            success: function(response){
                var select = $this.closest('th').find('select');
                if(response.success) {
                    select.val(response.unitId)
                } else {
                    select.val('');
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(document).on('change', '#template', function () {
        if ($(this).val()){
            $('.js-not-allowed', '#set-template-div').removeClass('js-not-allowed').addClass('js-set-template btn-green');
        } else {
            $('.js-set-template').removeClass('js-set-template btn-green').addClass('js-not-allowed');
        }
    });

    $(document).on('change', '#samples', function () {
        if ($(this).val()){
            $('.js-not-allowed', '#add-samples-div').hide();
            $('#js-add-samples').show();
        } else {
            $('.js-not-allowed', '#add-samples-div').show();
            $('#js-add-samples').hide();
        }
    });

    $(document).on('click', '.js-set-template', function () {
        if (samplesTable.find('.item').length) {
            if (!confirm('Please note that all data in table will be overwritten! Are you sure?')) {
                return false;
            }
        }

        window.location.href = baseUrl + '/template/' + $('#template').val();

        return false;
    });

    function ajaxIndicatorStart(text)
    {
        if($('body').find('#resultLoading').attr('id') != 'resultLoading'){
            $('body').append('<div id="resultLoading" style="display:none"><div><img width="30" src="/images/ajax-loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
        }

        $('#resultLoading').css({
            'width':'100%',
            'height':'100%',
            'position':'fixed',
            'z-index':'10000000',
            'top':'0',
            'left':'0',
            'right':'0',
            'bottom':'0',
            'margin':'auto'
        });

        $('#resultLoading .bg').css({
            'background':'#000000',
            'opacity':'0.7',
            'width':'100%',
            'height':'100%',
            'position':'absolute',
            'top':'0'
        });

        $('#resultLoading>div:first').css({
            'width': '250px',
            'height':'75px',
            'text-align': 'center',
            'position': 'fixed',
            'top':'0',
            'left':'0',
            'right':'0',
            'bottom':'0',
            'margin':'auto',
            'font-size':'16px',
            'z-index':'10',
            'color':'#ffffff'

        });

        $('#resultLoading .bg').height('100%');
        $('#resultLoading').fadeIn(300);
        $('body').css('cursor', 'wait');
    }

    function ajaxIndicatorStop()
    {
        $('#resultLoading .bg').height('100%');
        $('#resultLoading').fadeOut(300);
        $('body').css('cursor', 'default');
    }

    $(document).ajaxStop(function () {
        //hide ajax indicator
        ajaxIndicatorStop();
    });
</script>
