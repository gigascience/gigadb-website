<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Collaboration links</h3>
    <a class="myHint" style="float: none;" data-content="If your dataset is part of an international collaborative project you may provide the details here."></a>


    <p class="note">
        Is this data part of a large recognised (international) project ? E.g. G10K
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#"
           data-target="projects"
           id="projects-yes"
           class="btn additional-button <?php if ($isProjects === true): ?>btn-green btn-disabled<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#"
           data-target="projects"
           data-next-block="others-block"
           id="projects-no"
           data-url="/adminDatasetProject/deleteProjects"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isProjects === false): ?>btn-green btn-disabled<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="projects"<?php if ($isProjects !== true): ?> style="display: none"<?php endif; ?>>
        <p class="note">
            Please select the appropriate project from the dropdown list and click “Add Project”, you may add multiple projects if appropriate.
            <br>
            If the project you wish to add is not in the list, please complete the submission without it and inform us by email so we may add it to your dataset and update this list.
        </p>

        <div class="control-group" style="text-align: center">
            <?= CHtml::dropDownList('project', null,
                array('' => 'Please select') + CHtml::listData(Project::model()->findAll(), 'id', 'name'),
                array('class'=>'js-project dropdown-white','style'=>'width:auto')); ?>
            <a href="#" dataset-id="<?=$model->id?>" class="btn js-not-allowed" style="margin-left: 20px;"/>Add Project</a>
        </div>

        <div id="author-grid" class="grid-view">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th id="author-grid_c0" width="80%">Project Name</th>
                    <th id="author-grid_c5" class="button-column" width="20%"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($dps as $dp) { ?>
                    <tr class="odd js-my-item">
                        <td>
                            <?=$dp->project->name?>
                            <input type="hidden" class="js-project-id" value="<?= $dp->project->id ?>">
                        </td>
                        <td class="button-column">
                            <input type="hidden" class="js-my-id" value="<?= $dp->id ?>">
                            <a class="js-delete-project delete-title" dp-id="<?=$dp->id?>" data-id="<?= $model->id ?>" title="delete this row">
                                <img alt="delete this row" src="/images/delete.png">
                            </a>
                        </td>
                    </tr>
                <? } ?>
                <tr class="js-no-results"<?php if ($dps): ?> style="display: none"<?php endif ?>>
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
    var projectsDiv = $('#projects');

    $(document).on('change', '#project', function () {
        if ($('#project').val()){
            $('.js-not-allowed', projectsDiv).removeClass('js-not-allowed').addClass('js-add-project btn-green');
        } else {
            $('.js-add-project', projectsDiv).removeClass('js-add-project btn-green').addClass('js-not-allowed');
        }
    });

    $(projectsDiv).on('click', ".js-add-project", function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var pid = $('.js-project').val();

        $.ajax({
            type: 'POST',
            url: '/adminDatasetProject/getProject',
            data:{'dataset_id': did, 'project_id':pid},
            success: function(response){
                if(response.success) {
                    var exit = false;
                    var trs = projectsDiv.find('.odd');
                    trs.each(function() {
                        let tr = $(this);
                        let project_name = tr.children('td').eq(0).text().trim();

                        if (response.project['name'] == project_name) {
                            alert('This project has been added already.');
                            exit = true;
                            return false;
                        }
                    });

                    if (exit) {
                        return false;
                    }

                    var tr = '<tr class="odd js-my-item">' +
                        '<input type="hidden" class="js-project-id" value="' + response.project['id'] + '">' +
                        '<td>' + response.project['name'] + '</td>' +
                        '<td class="button-column">' +
                        '<a class="js-delete-project delete-title" title="delete this row">' +
                        '<img alt="delete this row" src="/images/delete.png">' +
                        '</a>' +
                        '</td>' +
                        '</tr>';

                    $('.js-no-results', projectsDiv).before(tr);
                    $('.js-no-results', projectsDiv).hide();

                    $('#others-block').show();

                    checkIfCanSave();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(projectsDiv).on('click', ".js-delete-project", function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();

        $(this).closest('tr').remove();

        if (projectsDiv.find('.odd').length === 0) {
            $('.js-no-results', projectsDiv).show();
        }

        checkIfCanSave();
    });
</script>
