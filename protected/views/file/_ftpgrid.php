<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 18:36
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

 $previewjs = "
    $('.previewbtn').click(function(){

        $.ajax({
            type: 'POST',
            url: '/file/preview',
            data:{'location': $(this).attr('href') },
            success: function(output){
                var response = JSON.parse(output);
                 if(response.preview_url) {
                     document.getElementById('preview').innerHTML='<iframe src=\"http://127.0.0.1:8000/mfr?'+ data.preview_url +  '\"></iframe>';
                     console.log('success with ' + response.lastop + ' !');
                 } else {
                     document.getElementById('preview').innerHTML='<iframe src=\"\">a preview for this file is not ready, try again later,</p></iframe>' ;
                     console.log(response);
                 }
            },
            error:function(){
                console.log('error!');
            }

        });

        return false;


    });
";

 Yii::app()->clientScript->registerScript('file-grid', $js);
 Yii::app()->clientScript->registerScript('preview-button', $previewjs);

if ($error != null) {
    //echo '<div class="flash-error">Could not display folder content : ' . $error->getMessage() . "</div>\n";
    echo '<div class="flash-error">Could not display folder content : ' . "</div>\n";
} else {

    $this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Dataset files', array('dataset/view', 'id'=>$model->identifier, '#'=>'file_table')),
        'links'=>$breadcrumbs,
    ));


    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'file-grid',
        'dataProvider'=>$files,
        'itemsCssClass'=>'table table-bordered',
        'template' => $template,
        'pager' => array('class'=>'SiteLinkPager', 'id'=>'ftp_table_pager'),
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
                'header'=>'Preview',
                'template'=>'{preview}',
                'class'=>'bootstrap.widgets.BootButtonColumn',
                //'class'=>'CButtonColumn',
                'buttons' => array(
                    'preview' => array(
                        'icon'=>'eye-open',
                        // 'label'=>'P',
                        'url'=>'$data->location',
                        'options'=>array(
                            'class'=>'btn btn-mini previewbtn'
                        )
                    ),
                ),
                'htmlOptions'=>array('style'=>'width: 50px'),
                'visible' => in_array("location", $setting),
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
                'selectableRows'=>2,
                'value'=> 'serialize(array("location" => $data->location, "filename" => $data->filename, "type" => ($data->isDirectory) ? "Directory": "File" ))',
                'checked'=>'( isset(unserialize(Yii::app()->session["bundle"])[$data->location]) ) ? true : false',
            ),

        ),

    ));


}
