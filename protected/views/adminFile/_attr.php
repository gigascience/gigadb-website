<div>
  <?php echo CHtml::activeHiddenField($attribute, '[edit]id') ?>
	<div class="form-container">
		<div class="form-group">
			<label for="FileAttributes_edit_attribute_id" class="control-label">Attribute Name</label>
			<?php echo CHtml::activeDropDownList($attribute, '[edit]attribute_id', CHtml::listData(Attributes::model()->findAll(), 'id', 'attribute_name'), array('class' => 'attr-form form-control', 'empty' => 'Select name', 'title' => 'Choose the appropriate attribute name from the dropdown menu', 'data-toggle' => 'tooltip')); ?>
		</div>
		<div class="form-group">
			<label for="FileAttributes_edit_value" class="control-label">Value</label>
			<?php echo CHtml::activeTextArea($attribute, '[edit]value', array('class' => 'attr-form form-control', 'title' => 'Enter the value of the attribute', 'data-toggle' => 'tooltip', 'rows' => 2)); ?>
		</div>
		<div class="form-group">
			<label for="FileAttributes_edit_unit_id" class="control-label">Unit</label>
			<?php echo CHtml::activeDropDownList($attribute, '[edit]unit_id', CHtml::listData(Unit::model()->findAll(), 'id', 'name'), array('class' => 'attr-form form-control', 'empty' => 'Select unit', 'title' => 'Choose the appropriate unit from the dropdown menu', 'data-toggle' => 'tooltip')); ?>
		</div>
	</div>
</div>

<script>
  // run tooltip script when partial is rendered
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
