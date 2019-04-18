<?php
/** @var Dataset $model */

$additionalInfo = $model->getAdditionalInformation();
$isPublicLinks = isset($additionalInfo[Dataset::ADD_INFO_PUBLIC_LINKS]) ? !!$additionalInfo[Dataset::ADD_INFO_PUBLIC_LINKS] : null;
$isRelatedDoi = isset($additionalInfo[Dataset::ADD_INFO_RELATED_DOI]) ? !!$additionalInfo[Dataset::ADD_INFO_RELATED_DOI] : null;
$isProjects = isset($additionalInfo[Dataset::ADD_INFO_PROJECTS]) ? !!$additionalInfo[Dataset::ADD_INFO_PROJECTS] : null;
?>

<h2>Add Additional Information</h2>
<div class="clear"></div>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <?php $this->renderPartial('_public_links', array('model' => $model, 'links' => $links, 'link_database' => $link_database, 'isPublicLinks' => $isPublicLinks)); ?>

    <?php if ($isPublicLinks !== null): ?>
        <div class="clear"></div>
        <?php $this->renderPartial('_related_doi', array('model' => $model, 'relations' => $relations, 'isRelatedDoi' => $isRelatedDoi)); ?>
    <?php endif ?>

    <?php if ($isRelatedDoi !== null): ?>
        <div class="clear"></div>
        <?php $this->renderPartial('_projects', array('model' => $model, 'dps' => $dps, 'isProjects' => $isProjects)); ?>
    <?php endif ?>

    <?php if ($isProjects !== null): ?>
        <div class="clear"></div>
        <?php $this->renderPartial('_projects', array('model' => $model, 'dps' => $dps, 'isProjects' => $isProjects)); ?>
    <?php endif ?>

    <div class="clear"></div>
    <div style="text-align:center">
        <a href="/datasetSubmission/author/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/user/view_profile" class="btn-green">Save</a>
        <a href="#" class="btn-green">Next</a>
    </div>
</div>

<script>
    $(".delete-title").tooltip({'placement':'top'});

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

    $(document).on('click', '.js-no-button', function(e) {
        var $this = $(this);
        var datasetId = $this.data('id');
        var url = $this.data('url');
        var targetId = $this.data('target');
        var target = $('#' + targetId);

        var items = target.find('.js-my-item');
        if (items.length > 0) {
            if (!confirm('Are you sure you want to delete all items?')) {
                return false;
            }
        }

        $.ajax({
            type: 'POST',
            url: url,
            data:{'dataset_id': datasetId},
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

    $(".js-yes-button").click(function(e) {
        var $this = $(this);
        var target = $this.data('target');

        $this.addClass('btn-green');
        $this.siblings().removeClass('btn-green').addClass('js-no-button');

        $('#' + target).show();
    });
</script>
