<?php

return array(
	array(
		'id'=>1,
		'dataset_id'=>3, //101001
		'related_doi'=>"101000", // dataset_id 4
		'relationship_id'=>3, //IsSupplementTo
	),
	array(
		'id'=>2,
		'dataset_id'=>4, //101000
		'related_doi'=>"101001", // dataset_id 3
		'relationship_id'=>4, //IsSupplementedBy
	),
	array(
		'id'=>3,
		'dataset_id'=>5, //100038
		'related_doi'=>"100044", // dataset_id 6
		'relationship_id'=>17, //IsCompiledBy
	),
	array(
		'id'=>4,
		'dataset_id'=>6, //100044
		'related_doi'=>"100038", // dataset_id 5
		'relationship_id'=>18, //Compiles
	),
	array(
		'id'=>5,
		'dataset_id'=>6, //100044
		'related_doi'=>"100148", // dataset_id 7
		'relationship_id'=>10, //IsPreviousVersionOf
	),
	array(
		'id'=>6,
		'dataset_id'=>7, //100148
		'related_doi'=>"100044", // dataset_id 6
		'relationship_id'=>9, //IsNewVersionOf
	),
);
?>