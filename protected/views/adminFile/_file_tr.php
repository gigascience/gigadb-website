<?php
/** @var int $i */
/** @var Dataset $model */
/** @var File $file */

$samples = $model->samples;
$samples_data = array();
//add none and All , Multiple
$samples_data[''] = '';
$samples_data['none'] = 'none';
$samples_data['All'] = 'All';
$samples_data['Multiple'] = 'Multiple';
foreach($samples as $sample) {
    $samples_data[$sample->name] = $sample->name;
}
?>

<tr class="tr-file">
    <?
    echo CHtml::activeHiddenField($file, '[' . $i . ']name');
    echo CHtml::activeHiddenField($file, '[' . $i . ']size');
    echo CHtml::activeHiddenField($file, '[' . $i . ']extension');
    echo CHtml::activeHiddenField($file, '[' . $i . ']id', array('class' => 'js-id'));
    echo CHtml::activeHiddenField($file, '[' . $i . ']dataset_id');
    echo CHtml::activeHiddenField($file, '[' . $i . ']location');
    ?>
    <td class="left" style="white-space: nowrap;">
        <?php echo $file->name ?>
    </td>
    <td class="left">
        <?= CHtml::activeDropDownList($file, '[' . $i . ']type_id', CHtml::listData(FileType::model()->findAll(), 'id', 'name'), array('class' => 'span2 dropdown-white js-type-id')); ?>
    </td>
    <td>
        <?= CHtml::activeDropDownList($file, '[' . $i . ']format_id', CHtml::listData(FileFormat::model()->findAll(), 'id', 'name'), array('class' => 'span2 dropdown-white')); ?>
    </td>
    <td>
        <?= CHtml::encode($file->getSizeWithFormat()) ?>
    </td>
    <td>
        <?= CHtml::activeTextArea($file, '[' . $i . ']description', array('rows' => 3, 'cols' => 30, 'style' => 'resize:none', 'class' => 'js-description')); ?>
    </td>
    <td class="left">
        <?= CHtml::activeDropDownList($file, '[' . $i . ']code', $samples_data, array('class' => 'span2 dropdown-white')); ?>
    </td>
    <td>
        <?php echo CHtml::button("Update", array('class' => 'btn btn-green js-update-file', 'name' => $i, 'data-id' => $file->id)); ?>
    </td>
</tr>