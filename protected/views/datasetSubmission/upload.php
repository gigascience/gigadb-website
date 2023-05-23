
<div class="clear"></div>
<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'successful') {
        ?>
        <div class="row">
            <div class="span8 offset2">
                <div class="form well light-green">
                    Your GigaDB submission has been received and is currently under review. If you do not hear from us within 5 working days please contact <a href="mailto:#"> database@gigasciencejournal.com </a>
                    <br/><br/>
                    <a href="/datasetSubmission/upload" class="btn">Back to upload new dataset</a>
                </div>
            </div>
        </div>
    <?php } elseif ($_GET['status'] == 'failed') { ?>
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
    <?php
    }
} else if (isset($study)) {
   
        ?>
        <h2>Submission request accepted</h2>
        <div class="clear"></div>
        <div class="span12 form well">
            <div class="form-horizontal">
                <?= $this->renderPartial('/user/uploadedDatasets', array('uploadedDatasets' => $uploadedDatasets,'selected'=>$study)); ?>
                <div class="row">
                    <!--            <div class="span8 offset2">
                                    <div class="form well light-green">-->
                    <div class="span12" style="text-align:center">
                    <b>
                   Congratulations, you have completed the submission of data to GigaDB.
                    A curator will make a final review of the information and contact you 
                    shortly with the DOI and citation details of your data.

                    </b>
                    </div>
                    <br/><br/>
                    <div class="span12" style="text-align:center">
                        <a href="/user/view_profile" class="btn-green">&nbsp&nbsp;Your home page&nbsp;&nbsp;</a>
                        <a href="/" class="btn-green">&nbsp;&nbsp;&nbspGigaDB home&nbsp;&nbsp;&nbsp; </a>
                    </div>
                </div>
            </div>
        </div>

        <?
    
} else {
    ?>

    <div class="row form well">
        GigaDB primarily serves as a repository to host data and tools associated with articles in <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a>, as well as certain <a href="http://www.genomics.cn" target="_blank">BGI</a> data not associated with GigaScience articles. Datasets that are not affiliated with a GigaScience article are approved for inclusion by the Editors of GigaScience. Inclusion of particularly interesting, previously unpublished datasets may be considered, especially if they meet the criteria for inclusion as a Data Note article in the journal (see author instructions <a href="http://www.gigasciencejournal.com/authors/instructions/datanote" target="_blank">here</a>).
        You may send a short summary of your data to the <a href="mailto:editorial@gigasciencejournal.com">Editors</a> for review prior to submission.

    </div>


    <table>
        <tr>
            <td style="vertical-align: top;width: 500px">
                <div class="row">
                    <div class="span6">
                        <h2>Dataset Upload</h2>
                        <div class="form well" style="height:340px;width:415px">
                            You will need to fill out a template file and then give it a new file name. 
                            <br/>
                            Click 'Download Template File' to get a copy:
                            <br/><br/>
                            <a href="/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx" class="btn pull-right">Download Template File</a>
                            <div class="clear"></div>

                            When filling out your dataset file, you may refer to the files below as examples.
                            <br/><br/>
                            <a href="/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx" class="btn pull-right">Download Example File 1</a>
                            <br/><br/>
                            <div class="clear"></div>
                            <a href="/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx" class="btn pull-right">Download Example File 2</a>
                            <div class="clear"></div>

                            <?php echo CHtml::form(Yii::app()->createUrl('datasetSubmission/upload'), 'post', array('enctype' => 'multipart/form-data')); ?>
                            <input id="agree-checkbox" name="agree-checkbox" type="checkbox" style="margin-right:5px"/><a target="_blank" href="/site/term">I have read GigaDB's Terms and Conditions</a>
                            <br/>
                            <div class="clear"></div>
                            <div class="pull-right">
                                <?php echo CHtml::submitButton('Upload New Dataset', array('class' => 'btn-green submit-button-control', 'disabled' => 'disabled', 'title' => 'You must agree to the terms and conditions before continuing.')); ?>
                            </div>
                            <?php echo CHtml::hiddenField('userId', Yii::app()->user->id); ?>
                            <?php echo CHtml::label('Excel File', 'xls'); ?>
                            <?php echo CHtml::fileField('xls', null, array('disabled' => 'disabled', 'class' => 'file-upload-control', 'title' => 'You must agree to the terms and conditions before continuing.')); ?>
                            <?php echo CHtml::endForm(); ?>

                        </div>
                    </div>
            </td>
            <td style="vertical-align: top; padding-left: 0px">          



            </td>
        </tr>
    </table>

    <script>
        $(function() {
            $('#agree-checkbox').click(function() {
                if ($(this).is(':checked')) {
                    $('.file-upload-control').attr('disabled', false);
                } else {
                    $('.file-upload-control').attr('disabled', true);
                    $('.submit-button-control').attr('disabled', true);
                }
            });
            $('#xls').change(function (){
                $('.submit-button-control').attr('disabled', false);
            });
            $('#agree-checkbox1').click(function() {
                if ($(this).is(':checked')) {
                    $('#online').attr('disabled', false);
                } else {
                    $('#online').attr('disabled', true);
                }
            });
        });
    </script>
<? } ?>
