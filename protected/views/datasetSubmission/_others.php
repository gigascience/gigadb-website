<a name="project"></a>
<div class="form-horizontal additional-bordered">
    <h3 style="display: inline-block">Other links</h3>
    <a class="myHint" style="float: none;" data-content="Dont know what to add here."></a>


    <p class="note">
        Do you wish to add links to any of the following :
    </p>

    <div style="text-align: center; margin-bottom: 15px;">
        <a href="#project" data-target="projects" class="btn additional-button <?php if ($isProjects === true): ?>btn-green<?php else: ?>js-yes-button<?php endif; ?>"/>Yes</a>
        <a href="#project"
           data-target="projects"
           data-url="/adminDatasetProject/deleteProjects"
           data-id="<?= $model->id ?>"
           class="btn additional-button <?php if ($isProjects === false): ?>btn-green<?php else: ?>js-no-button<?php endif; ?>"/>No</a>
    </div>

    <div id="projects"<?php if ($isProjects !== true): ?> style="display: none"<?php endif; ?>>
        <p class="note">
            Please select the appropriate project from the dropdown list and click “Add Project”, you may add multiple projects if appropriate.
            <br>
            If the project you wish to add is not in the list, please complete the submission without it and inform us by email so we may add it to your dataset and update this list.
        </p>

        <div class="control-group" style="text-align: center">
            <?= CHtml::dropDownList('project', null, CHtml::listData(Project::model()->findAll(), 'id', 'name'),array('class'=>'js-project dropdown-white','style'=>'width:auto')); ?>
            <a href="#" dataset-id="<?=$model->id?>" class="btn js-add-project" style="margin-left: 20px;"/>Add Project</a>
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
                <?php if($dps) { ?>
                    <?php foreach($dps as $dp) { ?>
                        <tr class="odd js-my-item">
                            <td><?=$dp->project->name?></td>
                            <td class="button-column">
                                <a class="js-delete-project delete-title" dp-id="<?=$dp->id?>" data-id="<?= $model->id ?>" title="delete this row">
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
    $(".js-add-project").click(function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var pid = $('.js-project').val();

        $.ajax({
            type: 'POST',
            url: '/adminDatasetProject/addProject',
            data:{'dataset_id': did, 'project_id':pid},
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

    $(".js-delete-project").click(function(e) {
        if (!confirm('Are you sure you want to delete this item?'))
            return false;
        e.preventDefault();
        var  dpid = $(this).attr('dp-id');
        var  datasetId = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/adminDatasetProject/deleteProject',
            data:{'dp_id': dpid, 'dataset_id': datasetId},
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
