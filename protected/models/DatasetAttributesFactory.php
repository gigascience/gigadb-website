<?php

declare(strict_types=1);

class DatasetAttributesFactory
{
    protected ?DatasetAttributes $da = null;

    public function create(): DatasetAttributes
    {
        return $this->da = new DatasetAttributes();
    }

    public function setAttributeId(?int $attribute_id = null): void
    {
        $this->da->attribute_id = $attribute_id;
    }

    public function setDatasetId(?int $dataset_id = null): void
    {
        $this->da->dataset_id = $dataset_id;
    }

    public function setValue(?string $value = null): void
    {
        $this->da->value = $value;
    }

    public function save(): void
    {
        $this->da->save();
    }
}
