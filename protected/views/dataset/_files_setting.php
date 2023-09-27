<?php
?>

<div id="files_settings" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="h4 modal-title">Table settings</h3>
        </div>
        <div class="modal-body">

            <div class="divForForm">
                <form id="fileSettingsForm" name="myFilesSettingform" method="POST">
                <input type='hidden' name='setting[]' value="name"/>

                    <div class="attribute-setting-item">
                        <label><strong>Items per page:</strong></label>
                        <select name="pageSize" class="selectPageSize">
                        <?php $options = [5,10,20,50,100,200];
                            foreach ($options as $option) {
                        ?>
                                <option value="<?= $option ?>" <?= $option == $pageSize ? "selected":"" ?> ><?= $option ?></option>
                         <?php   } ?>
                        </select>
                    </div>

                    <div class="attribute-setting-content">
                        <div class="row">
                            <div ><h3>Columns:</h3></div>
                            <div class="span4">
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','File Description') ?></div>
                                    <div class="span1"><input id="description" type="checkbox" name="setting[]" value="description"
                                        <?= (in_array("description", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','Sample ID') ?></div>
                                    <div class="span1"><input id="sample_id" type="checkbox" name="setting[]" value="sample_id"
                                        <?= (in_array("sample_id", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','Data Type') ?></div>
                                    <div class="span1"><input id="type_id" type="checkbox" name="setting[]" value="type_id"
                                    <?= (in_array("type_id", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','File Format') ?></div>
                                    <div class="span1"><input id="format_id" type="checkbox" name="setting[]" value="format_id"
                                    <?= (in_array("format_id", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                            </div>

                            <div class="span4">
                                 <div class="row">
                                    <div class="span3"><?= Yii::t('app','Size') ?></div>
                                    <div class="span1"><input id="size" type="checkbox" name="setting[]" value="size"
                                    <?= (in_array("size", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','Release Date') ?></div>
                                    <div class="span1"><input id="date_stamp" type="checkbox" name="setting[]" value="date_stamp"
                                    <?= (in_array("date_stamp", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','Download Link') ?></div>
                                    <div class="span1"><input id="location" type="checkbox" name="setting[]" value="location"
                                    <?= (in_array("location", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="span3"><?= Yii::t('app','File Attributes') ?></div>
                                    <div class="span1"><input id="attribute" type="checkbox" name="setting[]" value="attribute"
                                    <?= (in_array("attribute", $setting))? "checked" : ""?> />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div> <!-- /.modal-body -->
        <div class="modal-footer">
            <a class="btn btn-default" data-dismiss="modal" href="#" title="Close">Close</a>
            <a id="save-files-settings" class="btn btn-primary" href="#" title="Save changes">Save changes</a>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
        $("#save-files-settings").click(function(){
            $("#fileSettingsForm").submit();
            return false;
        });

    });
</script>
