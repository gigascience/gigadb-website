<h2 class="filters-title">Filter your result</h2>

<form id="filter_form" method="GET">
    <input type="hidden" name="keyword" value="<?= $model->keyword ?>" />

    <!-- FILTERS FOR DATASETS -->
    <div id="dataset_filter">
        <div class="filter">
            <button class='h4 heading toggle-btn js-toggle-btn' type="button" aria-expanded="true" aria-controls="result_type"><?= Yii::t('app', 'Type') ?> <i class="fa fa-caret-up js-caret-type" aria-hidden="true"></i></button>
            <div id="result_type" class='filter-content js-filter-content' style="<?= $model->type ? 'display:block;' : 'display:none;' ?>">
                <button class="btn btn-default btn-filter js-btn-filter" id="btn_type"><? if (empty($model->type)) echo Yii::t('app', 'Enable All');
                                                                else echo Yii::t('app', 'Disable'); ?></button>
                <div class="options <? if (empty($model->type)) echo 'disabled'; ?> ">
                    <? echo CHtml::checkBoxList("type", $model->type, array('dataset' => 'Dataset', 'sample' => 'Sample', 'file' => 'File'), array('class' => 'type')); ?>
                </div>
            </div>
        </div>

    </div>

    <!-- FILTERS FOR FILES -->

    <div id="file_filter">
    </div>

    <?php
    echo CHtml::submitButton(Yii::t('app', 'Apply Filter'), array('class' => 'btn background-btn filter apply-filter-btn'));
    echo CHtml::endForm();
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
            $(function() {
                $("#result_type").show();
                //toggle the componenet with class msg_body
                $(".js-toggle-btn").click(function() {
                    const caret = $(this).children(".js-caret-type");
                    if ($(this).attr("aria-expanded") == "true") {
                        $(this).attr("aria-expanded", "false");
                        caret.removeClass("fa-caret-up");
                        caret.addClass("fa-caret-down");
                    } else {
                        $(this).attr("aria-expanded", "true");
                        caret.removeClass("fa-caret-down");
                        caret.addClass("fa-caret-up");
                    }

                    $(this).next(".js-filter-content").slideToggle(300);
                });
            });

            submitFilter = function() {
                var action = $(this).attr("action");
                var tab = $("#filter_tab").val();
                if (tab != "" && action.indexOf("#") == -1) {
                    action = action + tab;
                }
                $(this).attr("action", action);
                $(this).submit();
                return false;
            };

            $(function() {

                $('.js-btn-filter').click(function() {
                    var action = $(this).html();
                    var alt = "";
                    if ($(this).next().has("input:text").length == 0) {
                        alt = "Enable All";
                    } else {
                        alt = "Enable";
                    }

                    var status;
                    if (action == '<?= Yii::t('app', 'Disable') ?>') {
                        $(this).html(alt);
                        status = false;
                        $(this).next().addClass('disabled');
                    } else {
                        $(this).html('<?= Yii::t('app', 'Disable') ?>');
                        status = true;
                        $(this).next().removeClass('disabled');
                    }
                    $(this).next().find(':checkbox').attr('checked', status);
                    $(this).next().find(':text').attr('value', "");
                    document.forms['filter_form'].submit(submitFilter);
                    return false;
                });

                $('input:checkbox').click(function(e) {
                    var disable = true;
                    $(this).parent().children("input:checkbox").each(function(index, ele) {
                        if ($(ele).attr("checked")) {
                            $(ele).parent().parent().parent().children("button").html('<?= Yii::t('app', 'Disable') ?>');
                            $(ele).parent().parent().removeClass('disabled');
                            disable = false;
                        }
                    });
                    if (disable) {
                        $(this).parent().parent().parent().children("button").html("Enable All");
                        $(this).parent().parent().addClass('disabled');
                    }
                });
            });

            $('#filter_form').submit(submitFilter);
        });
    </script>