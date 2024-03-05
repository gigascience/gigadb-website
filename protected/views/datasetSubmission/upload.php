<div class="container">

<?php
$this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Dataset Upload',
    'breadcrumbItems' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Your Profile', 'href' => '/user/view_profile'],
        ['isActive' => true, 'label' => 'Dataset Upload']
    ]
]);
?>

    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'successful') {
            ?>
                    <div class="row">
                        <div class="col-xs-8 col-xs-offset-2">
                            <div class="form well light-green">
                                Your GigaDB submission has been received and is currently under review. If you do not hear from us within 5 working days please contact <a href="mailto:#"> database@gigasciencejournal.com </a>
                                <br/><br/>
                                <a href="/datasetSubmission/upload" class="btn background-btn">Back to upload new dataset</a>
                            </div>
                        </div>
                    </div>
            <?php } elseif ($_GET['status'] == 'failed') { ?>
                    <div class="row">
                        <div class="col-xs-8 col-xs-offset-2">
                            <div class="form well">
                                <p class="error">
                                    Upload failed. Please contact <a href="mailto:#"> database@gigasciencejournal.com </a>
                                    <br/><br/>
                                    <a href="/datasetSubmission/upload" class="btn background-btn">Back to upload new dataset</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
        }
    } elseif (isset($study)) {

        ?>
            <h2 class="h4">Submission request accepted</h2>
            <div class="col-xs-12 form well">
                <div class="form-horizontal">
                    <?= $this->renderPartial('/user/uploadedDatasets', array('uploadedDatasets' => $uploadedDatasets, 'selected' => $study)); ?>
                    <div class="row">
                        <p class="col-xs-12">
                        Congratulations, you have completed the submission of data to GigaDB.
                        A curator will make a final review of the information and contact you
                        shortly with the DOI and citation details of your data.
                        </p>
                        <br/><br/>
                        <div class="col-xs-12">
                            <div class="pull-right btns-row">
                                <a href="/user/view_profile" class="btn background-btn">&nbsp&nbsp;Your Profile&nbsp;&nbsp;</a>
                                <a href="/" class="btn background-btn">&nbsp;&nbsp;&nbspGigaDB home&nbsp;&nbsp;&nbsp; </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
    } else {
        ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="well">
                    <p>
                        GigaDB primarily serves as a repository to host data and tools associated with articles in <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a>, as well as certain <a href="http://www.genomics.cn" target="_blank">BGI</a> data not associated with GigaScience articles. Datasets that are not affiliated with a GigaScience article are approved for inclusion by the Editors of GigaScience. Inclusion of particularly interesting, previously unpublished datasets may be considered, especially if they meet the criteria for inclusion as a Data Note article in the journal (<a href="http://www.gigasciencejournal.com/authors/instructions/datanote" target="_blank">see author instructions</a>).
                        You may send a short summary of your data to the <a href="mailto:editorial@gigasciencejournal.com">Editors</a> for review prior to submission.
                    </p>
                </div>
            </div>

            <div class="col-xs-6 col-xs-offset-3">
                <div class="form well">
                    <p>
                    You will need to fill out a template file and then give it a new file name.
                    <br/>
                    Click 'Download Template Excel File' to get a copy:
                    <br />
                    <a href="/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx">Download Template Excel File</a>
                    </p>

                    <p>
                    When filling out your dataset file, you may refer to the files below as examples.
                    <br />
                    <a href="/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx">Download Example Excel File 1</a>
                    <br/>
                    <a href="/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx">Download Example Excel File 2</a>
                    </p>

                    <br />
                    <br />

                    <?php echo CHtml::form(Yii::app()->createUrl('datasetSubmission/upload'), 'post', array('enctype' => 'multipart/form-data')); ?>

                        <div class="form-group row js-agree-form-group">
                            <div class="col-xs-3">
                                <input id="agree-checkbox" name="agree-checkbox" type="checkbox" required aria-required="true" class="pull-right" aria-describedby="agreeError"/>
                            </div>
                            <label class="col-xs-9" for="agree-checkbox"><a target="_blank" href="/site/term">I have read GigaDB's Terms and Conditions</a></label>
                            <div class="row">
                                <div id="agreeError" class="col-xs-offset-3 col-xs-9 help-block" role="alert">
                                    <span class="js-agree-error" style="display: none;">
                                        You must agree to the terms and conditions before continuing
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php echo CHtml::hiddenField('userId', Yii::app()->user->id); ?>

                        <div class="form-group js-file-form-group row">
                            <div class="col-xs-3">
                                <?php echo CHtml::label('Excel File', 'xls', array('class' => 'control-label pull-right')); ?>
                            </div>
                            <div class="col-xs-9">
                                <?php echo CHtml::fileField('xls', null, array(
                                    'required' => true,
                                    'aria-required' => 'true',
                                    'class' => 'form-control js-file-upload-control',
                                    )); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                     <?php echo CHtml::submitButton('Upload New Dataset', array(
                                         'class' => 'btn background-btn js-submit-button-control',
                                         'aria-required' => 'true',
                                     )); ?>
                                </div>
                            </div>
                        </div>

                        <?php echo CHtml::endForm(); ?>
                </div>
            </div>
        </div>

            <script>
                $(function() {
                    const fileInput = $('.js-file-upload-control');
                    const submitBtn = $('.js-submit-button-control');
                    const agreeError = $('.js-agree-error');
                    const agreeFormGroup = $('.js-agree-form-group');

                    // Function to enable or disable the fileField and submit button based on the checkbox state
                    function toggleFileFieldState() {
                        if ($('#agree-checkbox').is(':checked')) {
                            fileInput.attr('aria-disabled', false);
                            fileInput.prop('disabled', false);
                            submitBtn.attr('aria-disabled', false);
                            submitBtn.prop('disabled', false);
                            agreeFormGroup.removeClass('has-error');
                            agreeError.hide();
                        } else {
                            fileInput.attr('aria-disabled', true);
                            fileInput.prop('disabled', true);
                            submitBtn.attr('aria-disabled', true);
                            submitBtn.prop('disabled', true);
                            agreeFormGroup.addClass('has-error');
                            agreeError.show();
                        }
                    }

                    // Call the function when the page loads
                    toggleFileFieldState();

                    // Bind the function to the checkbox change event
                    $('#agree-checkbox').change(toggleFileFieldState);

                    // Bind the function to the fileField change event
                    $('#xls').change(function() {
                        submitBtn.prop('disabled', false);
                    });
                });
            </script>
    <? } ?>

</div>