<?php

if ($error != null) {
	echo '<div class="flash-error">Could not display folder content : ' . $error->getMessage() . "</div>\n";
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
		'id'=>'page-grid',
		'dataProvider'=>$dp,
		'columns'=>array(
			array (
				'class' =>'CDataColumn',
				'header' => 'File name',
				'value' => 'GFtpUtils::displayFilename($data, "'.$this->navKey.'", "'.$this->baseFolder.'", '.($this->allowNavigation ? 'true' : 'false').')',
				'type' => 'html',
				'filter' => false,
				'name' => 'filename',
				'visible' => in_array('filename', $this->columns, true)
			),
			array (
				'class' =>'CDataColumn',
				'header' => 'Rights',
				'value' => '$data->rights',
				'filter' => false,
				'name' => 'rights',
				'visible' => in_array('rights', $this->columns, true)
			),
			array (
				'class' =>'CDataColumn',
				'header' => 'User',
				'value' => '$data->user',
				'filter' => false,
				'name' => 'user',
				'visible' => in_array('user', $this->columns, true)
			),
			array (
				'class' =>'CDataColumn',
				'header' => 'Group',
				'value' => '$data->group',
				'filter' => false,
				'name' => 'group',
				'visible' => in_array('group', $this->columns, true)
			),
			array (
				'class' =>'CDataColumn',
				'header' => 'Modification time',
				'value' => '$data->mdTime',
				'filter' => false,
				'name' => 'mdTime',
				'visible' => in_array('mdTime', $this->columns, true)
			),
			array (
				'class' =>'CDataColumn',
				'header' => 'Size',
				'value' => 'GFtpUtils::isDir($data) ? "" : $data->size',
				'htmlOptions' => array('style'=>'text-align: right;'),
				'filter' => false,
				'name' => 'size',
				'visible' => in_array('size', $this->columns, true)
			),
		),
	)); 
}
