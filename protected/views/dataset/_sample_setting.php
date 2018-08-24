<?php
?>

<div id="samples_settings" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Table settings</h4>
        </div>
        <div class="modal-body">

            <div class="divForForm">
                <form id="sampleSettingsForm" name="mySamplesSettingform" method="POST">
                    <input type='hidden' name='columns[]' value="name"/>

                    <div class="attribute-setting-item">
                        <label><strong>Items per page:</strong></label>
                        <select name="perPage" class="selectPageSize">
                        <?php $options = [5,10,20,50,100,200];
                            foreach ($options as $option) {
                        ?>
                                <option value="<?= $option ?>" <?= $option == $pageSize ? "selected":"" ?> ><?= $option ?></option>
                         <?php   } ?>
                        </select>
                    </div>

                    <div class="modal-body" style="padding: 30px;">

                        <div class="form form-inline" style="margin-bottom: 60px;">


                                    <input type="checkbox" name="columns[]" value="common_name"
                                    <?= (in_array("common_name", $columns))? "checked" : ""?>/><label for="sample_col1"><?= Yii::t('app','Common Name') ?></label>



                                    <input type="checkbox" name="columns[]" value="scientific_name"
                                    <?= (in_array("scientific_name", $columns))? "checked" : ""?>/><label for="sample_col2"><?= Yii::t('app','Scienfic Name') ?></label>



                                    <input type="checkbox" name="columns[]" value="attribute"
                                    <?= (in_array("attribute", $columns))? "checked" : ""?>/><label for="sample_col3"><?= Yii::t('app','Sample Attributes') ?></label>



                                    <input type="checkbox" name="columns[]" value="taxonomic_id"
                                    <?= (in_array("taxonomic_id", $columns))? "checked" : ""?>/><label for="sample_col4"><?= Yii::t('app','Taxonomic ID') ?></label>


                                    <input type="checkbox" name="columns[]" value="genbank_name"
                                    <?= (in_array("genbank_name", $columns))? "checked" : ""?>/><label for="sample_col5"><?= Yii::t('app','Genbank Name') ?></label>

                            </div>


                        </div>


                </form>
            </div>

        </div> <!-- /.modal-body -->
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button id="save-samples-settings" type="button" class="btn btn-primary">Save changes</button>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
    Yii::app()->clientScript->registerScript("samples_settings_button", '$("#save-samples-settings").click(function(){
    $("#sampleSettingsForm").submit();
    return false;
});
');
?>