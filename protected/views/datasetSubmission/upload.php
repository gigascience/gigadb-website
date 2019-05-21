<div class="clear"></div>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'successful'): ?>
        <div class="row">
            <div class="span8 offset2">
                <div class="form well light-green">
                    Your GigaDB submission has been received and is currently under review. If you do not hear from us within 5 working days please contact <a href="mailto:#"> database@gigasciencejournal.com </a>
                    <br/><br/>
                    <a href="/datasetSubmission/upload" class="btn">Back to upload new dataset</a>
                </div>
            </div>
        </div>
    <?php elseif ($_GET['status'] == 'failed'): ?>
        <div class="row">
            <div class="span8 offset2">
                <div class="form well">
                    <p class="error">
                        Upload failed. Please contact <a href="mailto:#"> database@gigasciencejournal.com </a>
                        <br/><br/>
                        <a href="/datasetSubmission/upload" class="btn">Back to upload new dataset</a>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="row">
        <div class="span12">
            <h2 style="display: inline-block">Upload your dataset metadata from a spreadsheet</h2>
            <a class="myHint" style="float: none;" data-content="You may prepare all the dataset metadata in a special GigaDB Excel submission template file with instructions within the template. Download the empty “Template File” and upload it here after you have completed it. There are also example files you can download to see how it should be completed." data-original-title="" title=""></a>
            <div class="form well">
                You will need to complete the spreadsheet following the instructions provided within.
                <br/>
                Please download the template spreadsheet here
                <br/><br/>
                <a href="/files/GigaDBUploadForm.xlsx" class="btn pull-right">Download template spreadsheet (Excel)</a>
                <div class="clear"></div>
                <a href="/files/GigaDBUploadForm.ods" class="btn pull-right">Download template spreadsheet (Open Office)</a>
                <div class="clear"></div>

                To assist you in completing the information you may wish to see an example of a completed spreadsheet, you may download an example here
                <br/><br/>
                <a href="/files/GigaDBUploadForm-example1.xls" class="btn pull-right">Download Example 1 (Excel)</a>
                <div class="clear"></div>
                <a href="/files/GigaDBUploadForm-example1.ods" class="btn pull-right">Download Example 1 (Open Office)</a>
                <div class="clear"></div>

                <br/><br/>
                After you have completed the spreadsheet please use the upload facility below to send the completed spreadsheet to us.

                <br/><br/>
                <div class="center">
                    <input id="agree-checkbox" type="checkbox" style="margin-right:5px"/><a target="_blank" href="/site/term">I have read GigaDB's Terms and Conditions</a>
                    <br/>
                    <div class="clear"></div>
                    <?php echo CHtml::form(Yii::app()->createUrl('datasetSubmission/upload'), 'post', array('enctype' => 'multipart/form-data')); ?>

                    <?php echo CHtml::hiddenField('userId', Yii::app()->user->id); ?>
                    <?php echo CHtml::label('Excel File', 'xls'); ?>
                    <?php echo CHtml::fileField('xls', null, array('disabled' => 'disabled', 'class' => 'upload-control', 'title' => 'You must agree to the terms and conditions before continuing.')); ?>
                    <?php echo CHtml::submitButton('Upload New Dataset', array('class' => 'btn-green upload-control', 'disabled' => 'disabled', 'title' => 'You must agree to the terms and conditions before continuing.')); ?>

                    <?php echo CHtml::endForm(); ?>
                </div>

                <br/><br/>
                Prefer to use the wizard instead? Click <a href="/datasetSubmission/create1">here</a>.
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#agree-checkbox').click(function() {
                if ($(this).is(':checked')) {
                    $('.upload-control').attr('disabled', false);
                } else {
                    $('.upload-control').attr('disabled', true);
                }
            });
        });
    </script>
<?php endif; ?>
