<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 16:21
 */

$url1 = $this->createUrl('/file/addToBundle');
$url2 = $this->createUrl('/file/removeFromBundle');

$js = "
         $('#file-grid :checkbox').change(function(){
             if( $(this).is(':checked') ) {
                $.ajax({
                   type: 'GET',
                   url: '$url1',
                   data:{'fileinfo': $(this).val()},
                   success: function(output){
                       var response = JSON.parse(output);
                        if(response.status == 'OK') {
                            console.log('success with ' + response.lastop + ' !');
                        } else {
                            console.log(response);
                        }
                      },
                    error:function(){
                      console.log('error!');
                    }
                });
             }
             else {
                 $.ajax({
                    type: 'GET',
                    url: '$url2',
                    data:{'fileinfo': $(this).val()},
                    success: function(output){
                        var response = JSON.parse(output);
                         if(response.status == 'OK') {
                             console.log('success with removeFromBundle!');
                         } else {
                             console.log(response.status);
                         }
                       },
                     error:function(){
                       console.log('error!');
                     }
                 });
             }

         });
        ";

Yii::app()->clientScript->registerScript('file-grid', $js);

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
                'header'=>'Preview',
                'template'=>'{preview}',
                'class'=>'bootstrap.widgets.BootButtonColumn',
                'buttons' => array(
                    'preview' => array(
                        'icon'=>'eye-open',
                        'url'=>'$data->location',
                        'options'=>array(
                            'class'=>'btn btn-mini previewbtn'
                        )
                    ),
                ),
                'htmlOptions'=>array('style'=>'width: 50px'),
                'visible' => in_array("preview", $setting),
            ),
            array(
                'header'=>'Download',
                'class'=>'bootstrap.widgets.BootButtonColumn',
                'template' => '{download}',
                'buttons' => array(
                        'download' => array(
                            'icon'=>'icon-download-alt',
                            'url' => '$data->location',
                            'options' => array(
                                'target' => '_blank',
                                'class' => 'btn btn-mini js-download-count',
                            ),
                    )
                ),
                'visible' => in_array("location", $setting),
            ),
            array(
                'class'=>'CCheckBoxColumn',
                'headerTemplate'=> 'Select',
                'id'=>'selecttodownload',
                'selectableRows'=>2,
                'value'=> 'serialize(array("location" => $data->location, "filename" => $data->name, "type" => $data->type->name, "dataset" => $data->dataset->identifier ))',
                'checked'=>'$data->is_in_bundle(Yii::app()->session["bundle"])',
                'visible' => $multidownload,
            ),

        ),

    ));
}
