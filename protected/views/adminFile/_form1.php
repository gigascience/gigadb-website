<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

<div class="span12 form well">
    <div class="form-horizontal">
        <div class="form overflow">
            <p class="note">
                You should have been contacted by a curator with details of the private FTP user upload area, if not please email
                <a href="mailto:support@gigasciencejournal.com">support@gigasciencejournal.com</a> to ask about this, please include the manuscript submission ID in your email subject.
            </p>

            <p class="note">
                Please check your files have met the requirements outlined <a href="/site/guide">here</a>
            </p>

            <p class="note">
                Once you have uploaded all the files to our private FTP server you can populate the file table below by entering your FTP server credentials here and clicking “get file names”. This will poll your user area on the private sever and return all file names for you to check and complete the metadata for.
            </p>

            <div class="control-group">
                <label class='control-label'>FTP username =</label>
                <div class="controls">
                    <?= CHtml::textField('username', '', array('class' => 'js-check-can-get', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"userNNN")); ?>
                </div>
            </div>

            <div class="control-group">
                <label class='control-label'>FTP password =</label>
                <div class="controls">
                    <?= CHtml::textField('password', '', array('class' => 'js-check-can-get', 'size' => 60, 'maxlength' => 100, 'style'=>'width:240px', 'placeholder'=>"XyZ")); ?>
                    <a href="#" class="btn js-not-allowed" style="margin-left: 20px;" id="js-get-files"/>Get File Names</a>
                </div>
            </div>

            <div style="overflow-x: auto;margin: 15px 0;border: 3px solid #BFBFBF;padding: 10px;">
                <form class="form-horizontal" id="file-forms" action="/adminFile/updateFiles" method="post">
                    <input type="hidden" name="dataset_id" value="<?= $model->id ?>">
                    <input type="hidden" name="file_id" id="file_id" value="">
                    <table class="table table-bordered tablesorter" id="file-table">
                        <!--tr-->
                        <thead>
                        <!--th class="span2"><a href='#' onClick="setCookie('dataset.identifier')">DOI</a></th-->
                        <?
                        //TODO: This part is also dupicated
                        $fsort = $files->getSort();
                        $fsort_map = array(
                            'name' => 'span1',
                            'type_id' => 'span1',
                            'format_id' => 'span1',
                            'size' => 'span1',
//                        'date_stamp' => 'span1',
                            'description' => 'span2',
                            'code' => 'span2',
                        );

                        foreach ($fsort->directions as $key => $value) {
                            if (!array_key_exists($key, $fsort_map)) {
                                continue;
                            }
                            $direction = ($value == 1) ? ' sorted-down' : ' sorted-up';
                            $fsort_map[$key] .= $direction;
                        }
                        ?>
                        <?
                        foreach ($fsort_map as $column => $css) {
                            ?>
                            <th class="<?= $css ?>">
                                <?= $fsort->link($column) ?>
                            </th>
                        <? }
                        ?>
                        <th class="span2"></th>
                        </thead>

                        <?
                        $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
                            Yii::app()->request->cookies['filePageSize']->value : 10;

                        $files->getPagination()->pageSize = $pageSize;

                        $i = 0;
                        $page = $files->getPagination()->getCurrentPage() + 1;
                        $pageCount = (int) (($files->getTotalItemCount() + $pageSize - 1) / $pageSize);
                        ?>
                        <input type="hidden" name="page" value=<?=$page ?>>
                        <input type="hidden" name="pageCount" value=<?=$pageCount ?>>
                        <?php
                        foreach ($files->getData() as $file) {
                            $this->renderPartial('_file_tr', array(
                                'model' => $model,
                                'file' => $file,
                                'i' => $i,
                            ));
                            $i++;
                        }
                        ?>
                    </table>
                </form>
            </div>

            <p class="note">
                If you have many files you may wish to prepare the information in a spreadsheet and upload that to this page, the uploader will only parse CSV or TSV files, do NOT try to upload an Excel spreadsheet. Note – the columns should be in the same order as shown in table above.
            </p>

            <div class="clear"></div>

            <div class="span6">
                <form action="/adminFile/uploadFiles" method="POST" enctype="multipart/form-data" id="upload-files">
                    <div class="control-group" id="add-files-div">
                        <label class='control-label'>Upload file metadata</label>
                        <div class="controls">
                            <input type="file" id="files" name="files">
                            <input type="hidden" name="upload" value="true">
                            <a href="#" class="btn js-not-allowed" style="margin-left: 20px;"/>Upload</a>
                            <input type="submit" class="btn btn-green" id="js-upload-files" style="margin-left: 20px;display: none;" value="Upload"/>
                        </div>
                    </div>
                </form>
            </div>

            <div class="span5">
                <span>Note- uploading metadata file will overwrite any values already inserted above</span>
            </div>
        </div>
        <div class="span12" style="text-align:center">
            <a href="/datasetSubmission/sample/id/<?= $dataset_id ?>" class="btn-green">Previous</a>
            <?php
            //            echo $files->getPagination()->getCurrentPage()." eexx";
            ////            echo $files->getPagination()->getItemCount()."x dd";
            //              $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
            //                        Yii::app()->request->cookies['filePageSize']->value : 10;
            $page = $files->getPagination()->getCurrentPage() + 1;
            $pageCount = (int) (($files->getTotalItemCount() + $pageSize - 1) / $pageSize);
            //            echo $page." ".$pageCount;
            if ($page == $pageCount) {
                echo CHtml::submitButton("Save", array('class' => 'btn btn-green js-save-files', 'name' => 'files', 'title' => 'Save the updates to these files'));
                ?>
                <input type="hidden" name="file" value="file">
                <input type="submit" value="Complete submission" class="btn-green js-complete-submission" title="Submit changes to file details." />
                <?
                ?>
                <?
            } else {
                echo CHtml::submitButton("Next", array('class' => 'btn btn-green', 'name' => 'files', 'title' => 'Save the updates to these 10 and show next 10 files'));
                $this->endWidget();
            }
            ?>
            <!--<a href="/datasetSubmission/submit" class="btn-green" title="Click submit to send information to a curator for review.">Submit</a>-->
        </div>
        <?php
        //  $pageSize = isset(Yii::app()->request->cookies['filePageSize']) ?
        //  Yii::app()->request->cookies['filePageSize']->value : 10;
        $pagination = $files->getPagination();
        $pagination->pageSize = $pageSize;

        $this->widget('CLinkPager', array(
            'pages' => $pagination,
            'header' => '',
            'cssFile' => false,
        ));
        ?>
    </div>
</div>
<script>
    var dataset_id = <?= $model->id ?>;

    $(document).on('change', '#files', function () {
        if ($(this).val()){
            $('.js-not-allowed', '#add-files-div').hide();
            $('#js-upload-files').show();
        } else {
            $('.js-not-allowed', '#add-files-div').show();
            $('#js-upload-files').hide();
        }
    });

    $(document).on('change', '.js-check-can-get', function () {
        if ($('#username').val() && $('#password').val()){
            $('#js-get-files').removeClass('js-not-allowed').addClass('btn-green js-get-files');
        } else {
            $('#js-get-files').removeClass('btn-green js-get-file-names').addClass('js-not-allowed');
        }
    });

    $(document).on('click', ".js-get-files", function(e) {
        $.ajax({
            type: 'POST',
            url: '/adminFile/getFiles',
            data:{
                username: $('#username').val(),
                password : $('#password').val(),
                dataset_id: dataset_id
            },
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function(response){
                if(response.success) {
                    $('.tr-file').remove();
                    $('#file-table').append(response.html);
                } else {
                    alert(response.message);

                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.js-save-files', function() {
        $('.errorMessage').remove();

        $('#file_id').val('');
        saveFiles('/adminFile/create1/id/' + dataset_id);

        return false;
    });

    $(document).on('click', '.js-update-file', function() {
        var content = $(this).closest('tr');
        content.find('.errorMessage').remove();

        $('#file_id').val($(this).data('id'));
        saveFiles('', content);

        return false;
    });

    $(document).on('click', '.js-complete-submission', function() {
        $('.errorMessage').remove();

        $('#file_id').val('');
        saveFiles('/datasetSubmission/submit/id/' + dataset_id);

        return false;
    });

    $(document).on('click', '#js-upload-files', function() {
        $('#upload-files').ajaxSubmit({
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function (response) {
                if (response.success) {
                    for (var i in response.rows) {
                        var clear = true;

                        var input = $('[value="'+response.rows[i][0]+'"]');
                        if (!input.length) {
                            alert('Cant find file: ' + response.rows[i][0]);
                            clear = false;
                        } else {
                            var tr = input.closest('tr');
                            tr.find('.js-description').text(response.rows[i][2]);
                            tr.find('.js-description').val(response.rows[i][2]);
                            tr.find('.js-type-id').val(response.rows[i][1]);
                        }

                        if (clear) {
                            $("#files").val('');
                            $('.js-not-allowed', '#add-files-div').show();
                            $('#js-upload-files').hide();
                        }
                    }
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

    function saveFiles(url, content) {
        $('#file-forms').ajaxSubmit({
            beforeSend:function(){
                ajaxIndicatorStart('loading data.. please wait..');
            },
            success: function (response) {
                if (response.success) {
                    if (url) {
                        document.getElementById('file-forms').action = url;
                        $('#file-forms').submit();
                    } else {
                        content.find('.js-id').val(response.file_id);
                        content.find('.js-update-file').attr('data-id', response.file_id);
                    }
                } else {
                    for (var i in response.errors) {
                        for (var j in response.errors[i])
                            $('[name="File[' + i + '][' + j + ']"').after('<div class="errorMessage">' + response.errors[i][j][0] + '</div>');
                    }
                }
            }
        });
    }

    $(document).on('click', '.js-not-allowed', function() {
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