<td>
	<?php echo CHtml::activeHiddenField($attribute, 'id') ?>
	<?php echo CHtml::activeDropDownList($attribute, 'attribute_id',CHtml::listData(Attribute::model()->findAll(),'id','attribute_name'), array('class'=>'attr-form', 'style'=>'width: 100%;', 'empty'=>'Select name')); ?>
</td>
<td>
	<?php echo CHtml::activeTextField($attribute, 'value',array('class'=>'attr-form', 'style'=>'width: 100%;'));?>
</td>
<td>
	<?php echo CHtml::activeDropDownList($attribute, 'unit_id',CHtml::listData(Unit::model()->findAll(),'id','name'), array('class'=>'attr-form', 'style'=>'width: 100%;', 'empty'=>'Select unit')); ?>
</td>
<td>
    <div>
	<input type="submit" class="btn" name="edit_attr" style="margin: auto;" value="Save"/>
    </div>
</td>