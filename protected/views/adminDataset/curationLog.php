<?php
$dataset = Dataset::model()->find('id=:dataset_id', [':dataset_id' => $dataset_id]);

?>
<a href="/curationLog/create/id/<?php echo $dataset_id; ?>" class="btn background-btn-o" data-toggle="tooltip" title="Click this to add a new entry to the curation log below">Create New Log</a>
<div class="clear"></div>

<?php
$this->widget(
    'CustomGridView',
    [
        'id'            => 'dataset-grid',
        'dataProvider'  => $model,
        'itemsCssClass' => 'table table-bordered',
        'enableSorting'  => false,
        'columns'       => [
            'creation_date',
            'created_by',
            'action',
            [
                    'name' => 'comments',
                    'type' =>  'text',
                    'value' => function ($data) {
                        if (preg_match('/^<\?xml/', $data->comments)) {
                            return LogCurationFormatter::getDisplayXmlAttr($data->id, $data->comments);
                        }

                        return $data->comments;
                    }
            ],
            'last_modified_date',
            'last_modified_by',
            [
                'class'   => 'CButtonColumn',
                'header' => "Actions",
                'headerHtmlOptions' => array('style' => 'width: 100px'),
                'template' => '{view}{update}{delete}',
                'buttons' => array(
                  'view' => array(
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("curationLog/view", ["id" => $data->id])',
                    'label' => '',
                    'options' => array(
                      "title" => "View",
                      "class" => "fa fa-eye fa-lg icon icon-view",
                      "aria-label" => "View"
                    ),
                  ),
                  'update' => array(
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("curationLog/update", ["id" => $data->id])',
                    'label' => '',
                    'options' => array(
                      "title" => "Update",
                      "class" => "fa fa-pencil fa-lg icon icon-update",
                      "aria-label" => "Update"
                    ),
                  ),
                  'delete' => array(
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("curationLog/delete", ["id" => $data->id])',
                    'label' => '',
                    'options' => array(
                      "title" => "Delete",
                      "class" => "fa fa-trash fa-lg icon icon-delete",
                      "aria-label" => "Delete"
                    ),
                  ),
                ),
            ],
        ],
    ]
);
?>
<div id='modal' class='modal fade' role='dialog'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Dataset as XML</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <pre id='xmlData'></pre>
            </div>

        </div>
    </div>
</div>
<script>
    $('.js-desc').click(function (e) {
        e.preventDefault();
        id = $(this).attr('data');
        const xmlDataContainer = document.getElementById('xmlData');
        const hiddenContent = document.getElementsByClassName('js-long-' + id)
        xmlDataContainer.textContent = formatXML(hiddenContent[0].innerHTML.trim())

        $('#modal').modal('show');
    });

    $('.close').click(function (e) {
        e.preventDefault()

        $('#modal').modal('hide');

    });
</script>
