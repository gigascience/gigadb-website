<?php

class DatasetAttributesFactory
{
	private $da;

	public function create()
	{
		$da = new DatasetAttributes();
	}

	public function setAttributeId($attribute_id)
	{
		$this->da->attribute_id = $attribute_id;
	}

	public function setDatasetId($dataset_id)
	{
		$this->da->dataset_id = $dataset_id;
	}

	public function setValue($value)
	{
		$this->da->value = $value;
	}

	public function save()
	{
		$this->da->save();
	}
}