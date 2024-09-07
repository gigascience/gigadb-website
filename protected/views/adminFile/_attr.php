<td>
	<?php echo CHtml::activeHiddenField($attribute, 'id') ?>
	<?php echo CHtml::activeDropDownList($attribute, 'attribute_id', CHtml::listData(Attributes::model()->findAll(), 'id', 'attribute_name'), array('class' => 'attr-form form-control', 'empty' => 'Select name', 'title' => 'Choose the appropriate attribute name from the dropdown menu', 'data-toggle' => 'tooltip')); ?>
</td>
<td>
	<?php echo CHtml::activeTextField($attribute, 'value', array('class' => 'attr-form form-control', 'title' => 'Choose the appropriate attribute name from the dropdown menu', 'data-toggle' => 'tooltip')); ?>
</td>
<td>
	<?php echo CHtml::activeDropDownList($attribute, 'unit_id', CHtml::listData(Unit::model()->findAll(), 'id', 'name'), array('class' => 'attr-form form-control', 'empty' => 'Select unit', 'title' => 'Choose the appropriate attribute name from the dropdown menu', 'data-toggle' => 'tooltip')); ?>
</td>
<td>
	<button type="submit" class="btn background-btn js-save" name="edit_attr">Save Attribute</button>
</td>

<script>
  // run tooltip script when partial is rendered
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>