<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 16:21
 */



if ($error != null) {
    echo '<div class="flash-error">Could not display folder content : ' . $error->getMessage() . "</div>\n";
} else {


    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'file-grid',
        'dataProvider'=>$files,
        'itemsCssClass'=>'table table-bordered',
        'template' => $template,
        'pager' => array('class'=>'SiteLinkPager', 'id'=>'file_table_pager'),
        'pagerCssClass' => '',
        'summaryText' => 'Displaying {start}-{end} of {count} File(s).',
        'htmlOptions' => array('style'=>'padding-top: 0px'),
        'columns' => array(
            array(
                'name' => 'name',
                'type' => 'raw',
                'value' => '$data->nameHtml',
                'visible' => in_array('name', $setting),
            ),
            array(
                'name' => 'description',
                'value' => '$data->description',
                'visible' => in_array('description', $setting),
            ),
            array(
                'name' => 'sample_name',
                'type' => 'raw',
                'value' => '$data->getallsample($data->id)',
                'visible' => in_array('sample_id', $setting),
            ),
            array(
                'name' => 'type_id',
                'value' => '$data->type->name',
                'visible' => in_array("type_id", $setting),
            ),
            array(
                'name' => 'format_id',
                'value' => '$data->format->name',
                'visible' => in_array("format_id", $setting),
            ),
            array(
                'name' => 'size',
                'value' => 'File::staticBytesToSize($data->size)',
                'visible' => in_array("size", $setting),
            ),
            array(
                'name' => 'date_stamp',
                'value' => '$data->date_stamp',
                'visible' => in_array("date_stamp", $setting),
            ),
            array(
                'name' => 'attribute',
                'type' => 'raw',
                'value' => '$data->attrDesc',
                'visible' => in_array("attribute", $setting),
            ),
            array(
                'class'=>'CButtonColumn',
                'template' => '{download}',
                'buttons' => array(
                    'download' => array(
                        'label'=>'',
                        'url' => '$data->location',
                        'imageUrl' => '',
                        'options' => array(
                            'target' => '_blank',
                            'class' => 'download-btn js-download-count',
                        ),
                    )
                ),
                'visible' => in_array("location", $setting),
            ),
            array(
                'class'=>'CCheckBoxColumn',
                'selectableRows'=>2,
                'value'=> '$data->location',
                'cssClassExpression'=>'($data->type->name === "Directory") ? "hidden-checkbox":"" '
            ),

        ),

    ));
}
