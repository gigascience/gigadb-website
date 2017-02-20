<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 18:36
 */

if ($error != null) {
    //echo '<div class="flash-error">Could not display folder content : ' . $error->getMessage() . "</div>\n";
    echo '<div class="flash-error">Could not display folder content : ' . "</div>\n";
} else {
    $dp = new CArrayDataProvider (
        $files,
        array(
            'id'=>'filename',
            'keyField'=>'filename',
            'pagination'=>array(
                'pageSize'=>10,
            )
        )
    );



    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'file-grid',
        'dataProvider'=>$dp,
        'itemsCssClass'=>'table table-bordered',
        'template' => $template,
        'pager' => 'SiteLinkPager',
        'pagerCssClass' => '',
        'summaryText' => 'Displaying {start}-{end} of {count} File(s).',
        'htmlOptions' => array('style'=>'padding-top: 0px'),
        'columns' => array(
            array(
                'name' => 'name',
                'type' => 'raw',
                'value' => '$data->isDirectory?CHtml::link(CHtml::encode($data->filename), CHtml::encode(Yii::app()->request->getBaseUrl(true) . "/" . Yii::app()->request->pathInfo . "?location=".$data->location . "#file_table")):CHtml::link(CHtml::encode($data->filename), CHtml::encode($data->location))',
                'visible' => in_array('name', $setting),
            ),
            array(
                'name' => 'description',
                'value' => 'n/a',
                'visible' => in_array('description', $setting),
            ),
            array(
                'name' => 'sample_name',
                'type' => 'raw',
                'value' => '',
                'visible' => in_array('sample_id', $setting),
            ),
            array(
                'name' => 'type_id',
                'value' => '',
                'visible' => in_array("type_id", $setting),
            ),
            array(
                'name' => 'format_id',
                'value' => '',
                'visible' => in_array("format_id", $setting),
            ),
            array(
                'name' => 'size',
                'value' => 'File::staticBytesToSize($data->size)',
                'visible' => in_array("size", $setting),
            ),
            array(
                'name' => 'date_stamp',
                'value' => '$data->mdTime',
                'visible' => in_array("date_stamp", $setting),
            ),
            array(
                'name' => 'attribute',
                'type' => 'raw',
                'value' => '',
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

        ),

    ));


}