<h2>Update Metadata for Dataset <?php echo $model->identifier; ?></h2>
<div class="clear"></div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="span12 form well">
	<div class="form-horizontal">
		<div class="span10">
			<h3>External Links</h3>
			<h3>Links</h3>
			<h3>Manuscripts</h3>
			<h3>Projects</h3>
			<table >
				<thead id="project_head">
					<tr>
						<th>URL </th> <th>Name </th><th>Image Location</th>
					</tr>
					<tr style="display:none;">
						<td><?php echo $form->textField($model,'projects[new_project_url][]'); ?></td>
						<td><?php echo $form->textField($model,'projects[new_project_name][]'); ?> </td>
						<td><?php echo $form->textField($model,'projects[new_project_image][]'); ?></td>

					</tr>
				</thead>
				<tbody id="project_form">
					<tr>
						<td><?php echo $form->textField($model,'projects[new_project_url][]'); ?></td>
						<td><?php echo $form->textField($model,'projects[new_project_name][]'); ?> </td>
						<td><?php echo $form->textField($model,'projects[new_project_image][]'); ?></td>
					</tr>
				</tbody>
			</table>
			<div class="clear"></div>
			<a onclick="javascript:addMoreProject();" class="btn">Add More Project</a>

			<h3>Samples</h3>
			<table >
				<thead id="sample_head">
					<tr>
						<th>SampleID </th> <th>species </th><th>Common Name </th><th>Sample Attributes </th>
					</tr>
					<tr style="display:none;">
						<td><?php echo $form->textField($model,'samples[new_sample_id][]'); ?></td> <td><?php echo $form->textField($model,'samples[new_species][]'); ?> </td><td><?php echo $form->textField($model,'samples[new_species_common_name][]'); ?></td><td><?php echo $form->textField($model,'samples[new_sample_attributes][]'); ?></td>
					</tr>
				</thead>
				<tbody id="sample_form">
					<tr>
						<td><?php echo $form->textField($model,'samples[new_sample_id][]'); ?></td> <td><?php echo $form->textField($model,'samples[new_species][]'); ?> </td><td><?php echo $form->textField($model,'samples[new_species_common_name][]'); ?></td><td><?php echo $form->textField($model,'samples[new_sample_attributes][]'); ?></td>
					</tr>
				</tbody>
			</table>
			<div class="clear"></div>
			<a onclick="javascript:addMoreSample();" class="btn">Add More Sample</a>

			<h3>Files</h3>
			<table >
				<thead id="file_head">
					<tr>
						<th>Name </th>
						<th>Location </th>
						<th>Type</th>
						<th>Format</th>
						<th>Size</th>
						<th>Description</th>
						<th>Sample ID (AKA file.code)</th>
					</tr>
					<tr style="display:none;">
						<td><?php echo $form->textField($model,'files[new_file_name][]',array('style'=>'width:120px')); ?></td> 
						<td><?php echo $form->textField($model,'files[new_file_location][]'); ?> </td>
						<td><?php echo $form->textField($model,'files[new_file_type][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_format][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_size][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textArea($model,'files[new_file_description][]',array('rows'=>1,'columns'=>50)); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_code][]',array('style'=>'width:70px')); ?></td>
					</tr>
				</thead>
				<tbody id="file_form">
					<tr>
						<td><?php echo $form->textField($model,'files[new_file_name][]',array('style'=>'width:120px')); ?></td> 
						<td><?php echo $form->textField($model,'files[new_file_location][]'); ?> </td>
						<td><?php echo $form->textField($model,'files[new_file_type][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_format][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_size][]',array('style'=>'width:70px')); ?></td>
						<td><?php echo $form->textArea($model,'files[new_file_description][]',array('rows'=>1,'columns'=>50)); ?></td>
						<td><?php echo $form->textField($model,'files[new_file_sample_id][]',array('style'=>'width:70px')); ?></td>
					</tr>
				</tbody>
			</table>
			<div class="clear"></div>
			<a onclick="javascript:addMoreFile();" class="btn">Add More File</a>
			<div class="clear"></div>
		</div>

	</div><!-- form -->
</div>
<?php $this->endWidget(); ?>
<script>
function addMoreSample(){
	var template = $('#sample_head tr:last').html();
	$('#sample_form tr:last').after("<tr>"+template+"</tr>");

	return false;
}

function addMoreFile(){
	var template = $('#file_head tr:last').html();
	$('#file_form tr:last').after("<tr>"+template+"</tr>");

	return false;
}

function addMoreProject(){
	var template = $('#project_head tr:last').html();
	$('#project_form tr:last').after("<tr>"+template+"</tr>");

	return false;
}
</script>