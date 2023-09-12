<?php
?>

<div id="files_settings" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Table settings</h4>
            </div>
            <div class="modal-body">

                <div class="divForForm">
                    <form id="fileSettingsForm" name="myFilesSettingform" method="POST" class="table-settings-form">
                        <input type='hidden' name='setting[]' value="name" />

                        <div class="attribute-setting-item form-group">
                            <label for="selectPageSizeFilesSetting"><strong>Items per page:</strong></label>
                            <select name="pageSize" class="selectPageSize" id="selectPageSizeFilesSetting">
                                <?php $options = [5, 10, 20, 50, 100, 200];
                                foreach ($options as $option) {
                                ?>
                                    <option value="<?= $option ?>" <?= $option == $pageSize ? "selected" : "" ?>><?= $option ?></option>
                                <?php   } ?>
                            </select>
                        </div>

                        <div class="attribute-setting-content">
                            <fieldset class="form-group row">
                                <legend class="table-settings-form-label col-xs-12">Check columns to display</legend>

                                <div class="col-xs-6">
                                    <div class="checkbox">
                                        <input id="description" type="checkbox" name="setting[]" value="description" <?= (in_array("description", $setting)) ? "checked" : "" ?> />
                                        <label for="description"><?= Yii::t('app', 'File Description') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="sample_id" type="checkbox" name="setting[]" value="sample_id" <?= (in_array("sample_id", $setting)) ? "checked" : "" ?> />
                                        <label for="sample_id"><?= Yii::t('app', 'Sample ID') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="type_id" type="checkbox" name="setting[]" value="type_id" <?= (in_array("type_id", $setting)) ? "checked" : "" ?> />
                                        <label for="type_id"><?= Yii::t('app', 'Data Type') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="format_id" type="checkbox" name="setting[]" value="format_id" <?= (in_array("format_id", $setting)) ? "checked" : "" ?> />
                                        <label for="format_id"><?= Yii::t('app', 'File Format') ?></label>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="checkbox">
                                        <input id="size" type="checkbox" name="setting[]" value="size" <?= (in_array("size", $setting)) ? "checked" : "" ?> />
                                        <label for="size"><?= Yii::t('app', 'Size') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="date_stamp" type="checkbox" name="setting[]" value="date_stamp" <?= (in_array("date_stamp", $setting)) ? "checked" : "" ?> />
                                        <label for="date_stamp"><?= Yii::t('app', 'Release Date') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="location" type="checkbox" name="setting[]" value="location" <?= (in_array("location", $setting)) ? "checked" : "" ?> />
                                        <label for="location"><?= Yii::t('app', 'Download Link') ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input id="attribute" type="checkbox" name="setting[]" value="attribute" <?= (in_array("attribute", $setting)) ? "checked" : "" ?> />
                                        <label for="attribute"><?= Yii::t('app', 'File Attributes') ?></label>
                                    </div>
                                </div>

                            </fieldset>
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
        $("#save-files-settings").click(function() {
            $("#fileSettingsForm").submit();
            return false;
        });

    });
</script>