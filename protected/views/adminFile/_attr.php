<td>
	<?php echo CHtml::activeHiddenField($attribute, 'id') ?>
	<?php echo CHtml::activeDropDownList($attribute, 'attribute_id',CHtml::listData(Attribute::model()->findAll(),'id','attribute_name'), array('class'=>'attr-form', 'empty'=>'Select name')); ?>
</td>
<td>
	<?php echo CHtml::activeTextField($attribute, 'value',array('class'=>'attr-form'));?> 
</td>
<td>
	<?php echo CHtml::activeDropDownList($attribute, 'unit_id',CHtml::listData(Unit::model()->findAll(),'id','name'), array('class'=>'attr-form', 'empty'=>'Select unit')); ?>
</td>
<td>
	<input type="submit" class="btn" name="edit_attr" value="Save"/>
</td>