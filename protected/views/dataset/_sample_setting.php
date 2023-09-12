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
                    <form id="sampleSettingsForm" name="mySamplesSettingform" method="POST" class="table-settings-form">
                        <input type='hidden' name='columns[]' value="name" />
                        <div class="attribute-setting-item form-group">
                            <label for="selectPageSizeSampleSetting">
                                <strong>Items per page:</strong>
                            </label>
                            <select name="samplePageSize" class="selectPageSize" id="selectPageSizeSampleSetting">
                                <?php $options = [5, 10, 20, 50, 100, 200];
                                foreach ($options as $option) {
                                ?>
                                    <option value="<?= $option ?>" <?= $option == $pageSize ? "selected" : "" ?>>
                                        <?= $option ?>
                                    </option>
                                <?php   } ?>
                            </select>
                        </div>
                        <div>
                            <fieldset class="form-group">
                                <legend class="table-settings-form-label">Columns to display</legend>

                                <div class="checkbox">
                                    <input id="common_name" type="checkbox" name="columns[]" value="common_name" <?= (in_array("common_name", $columns)) ? "checked" : "" ?> /><label for="common_name"><?= Yii::t('app', 'Common Name') ?></label>
                                </div>

                                <div class="checkbox">
                                    <input id="scientific_name" type="checkbox" name="columns[]" value="scientific_name" <?= (in_array("scientific_name", $columns)) ? "checked" : "" ?> /><label for="scientific_name"><?= Yii::t('app', 'Scientific Name') ?></label>
                                </div>

                                <div class="checkbox">
                                    <input id="sample_attribute" type="checkbox" name="columns[]" value="attribute" <?= (in_array("attribute", $columns)) ? "checked" : "" ?> /><label for="sample_attribute"><?= Yii::t('app', 'Sample Attributes') ?></label>
                                </div>

                                <div class="checkbox">
                                    <input id="taxonomic_id" type="checkbox" name="columns[]" value="taxonomic_id" <?= (in_array("taxonomic_id", $columns)) ? "checked" : "" ?> /><label for="taxonomic_id"><?= Yii::t('app', 'Taxonomic ID') ?></label>
                                </div>

                                <div class="checkbox">
                                    <input id="genbank_name" type="checkbox" name="columns[]" value="genbank_name" <?= (in_array("genbank_name", $columns)) ? "checked" : "" ?> /><label for="genbank_name"><?= Yii::t('app', 'Genbank Name') ?></label>
                                </div>
                            </fieldset>
                        </div>
                    </form>
                </div>

            </div> <!-- /.modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="save-samples-settings" href="#" class="btn btn-primary">Save changes</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
        $("#save-samples-settings").click(function() {
            $("#sampleSettingsForm").submit();
            return false;
        });

    });
</script>