
<h1>View File #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Files', array('admin')) ?>]
</div>
<? } ?>

<?php
$sample_id = FileSample::model()->find('file_id=:file_id', array(':file_id'=>$model->id));
$attributes = FileAttributes::model()->findAll('file_id=:file_id', array(':file_id'=>$model->id));

if(isset($sample_id))
 {
 $sample_name= Sample::model()->find('id=:id',array(':id'=>$sample_id->sample_id));
 }

 $name="Not Set";
 
 if(isset($sample_id)&&isset($sample_name))
 {
     $name=$sample_name->name;
 }


$attribute_array = array();

 if(!empty($attributes)) {
     foreach ($attributes as $attribute) {

         array_push($attribute_array, $attribute->value);
     }
 }

 ?>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 10.3px;
    }
    th, td {
        border: 1px transparent;
        text-align: left;
        padding: 3px;
    }
    tr:nth-child(odd) {
        background-color: #E5F1F4;
    }
    tr:nth-child(even) {
        background-color: #f8f8f8;
    }
</style>

<table>
    <tr>
        <th><?php echo 'ID'; ?></th>
        <td><?php echo $model->id; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Dataset'; ?> </th>
        <td><?php echo $model->dataset_id; ?></td>
    </tr>
    <tr>
        <th><?php echo 'File Name'; ?></th>
        <td><?php echo $model->name; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Location'; ?></th>
        <td><?php echo $model->location; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Extension'; ?></th>
        <td><?php echo $model->extension; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Size'; ?></th>
        <td><?php echo $model->size; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Description'; ?></th>
        <td><?php echo $model->description; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Release Date'; ?></th>
        <td><?php echo $model->date_stamp; ?></td>
    </tr>
    <tr>
        <th><?php echo 'File Format'; ?></th>
        <td><?php echo $model->format_id; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Data Type'; ?></th>
        <td><?php echo $model->type_id; ?></td>
    </tr>
    <tr>
        <th><?php echo 'Sample'; ?></th>
        <td><?php echo $name; ?></td>
    </tr>
    <?php for ($i = 0; $i < count($attribute_array); $i++) { ?>
        <tr>
            <th><?php echo 'File Attribute'; ?></th>
            <td><?php echo $attribute_array[$i]; ?></td>
        </tr>
    <?php } ?>
</table>