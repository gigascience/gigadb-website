<?php

declare(strict_types=1);

namespace GigaDB\behaviors;

use GigaDB\models\Dataset;
use GigaDB\models\DatasetLog;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class DatasetRelatedTableBehavior extends Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    private function getMethodName()
    {
        $methods = [
            'relation' => 'relation',
            'manuscript' => 'manuscript',
            'link' => 'link',
            'external_link' => 'externalLink',
            'file' => 'file',
            'experiment' => 'experiment',
            'dataset_author' => 'datasetAuthor',
            'dataset_project' => 'datasetProject',
            'dataset_type' => 'datasetType',
            'dataset_sample' => 'datasetSample',
            'dataset_funder' => 'datasetFunder',
            # related to sample
            'sample' => 'sample',
            'alternative_identifiers' => 'alternativeIdentifiers',
            'sample_attribute' => 'sampleAttribute',
            'sample_rel' => 'sampleRel',
            # related to experiment
            'exp_attributes' => 'expAttributes',
        ];

        $tableName = $this->owner->tableName();

        if (isset($methods[$tableName]) && method_exists($this, $methods[$tableName])) {
            return $methods[$tableName];
        }

        return null;
    }

    public function afterSave($event)
    {
        $method = $this->getMethodName();

        if (!$method) {
            return;
        }

        if ($this->owner->isNewRecord) {
            $this->$method('create');

            return;
        }

        $changedAttributes = $event->changedAttributes;

        $isUpdated = false;
        foreach ($changedAttributes as $name => $oldValue) {
            if ($oldValue != $this->owner->$name) {
                $isUpdated = true;
                break;
            }
        }

        if ($isUpdated) {
            $this->$method('update');
        }
    }

    public function afterDelete($event)
    {
        $method = $this->getMethodName();

        if ($method) {
            $this->$method('delete');
        }
    }

    private function createLog($dataset_id, $message)
    {
        $dataset = Dataset::findOne($dataset_id);

        if ($dataset && $dataset->getIsPublic()) {
            $log = DatasetLog::makeNewInstanceForDatasetLogBy((int)$dataset_id, $message, $this->owner->tableName(), $this->owner->id);

            $log->save(false);
        }
    }

    private function relation($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
         }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Relationship $action : DOI " . $this->owner->related_doi);

    }

    private function manuscript($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Manuscript Link $action: " . $this->owner->identifier);
    }

    private function link($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Link $action: " . $this->owner->link);
    }

    private function externalLink($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "External Link  $action: " . $this->owner->url);
    }

    private function file($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "File Link  $action: " . $this->owner->name);
    }

    private function experiment($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Experiment  $action: " . $this->owner->experiment_name);
    }

    private function datasetAuthor($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Author  $action: " . $this->owner->author->name);
    }

    private function datasetProject($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Project  $action: " . $this->owner->project->name);
    }

    private function datasetType($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Dataset Type  $action: " . $this->owner->type->name);
    }

    private function datasetSample($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Sample  $action: " . $this->owner->sample->name);
    }

    private function datasetFunder($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';
        $this->createLog($this->owner->dataset_id, "Funder  $action: " . $this->owner->funder->primary_name_display);
    }

    private function sample($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $datasets = $this->owner->datasets;
        if ($type === 'create') {
            // nothing on create
            return;
        }

        $action = $type === 'update' ? 'updated' : 'removed';
        foreach ($datasets as $dataset) {
            $this->createLog($dataset->id, "Sample $action : " . $this->owner->name);
        }
    }

    private function alternativeIdentifiers($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }
        $datasets = $this->owner->sample->datasets;
        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';

        foreach ($datasets as $dataset) {
            $this->createLog($dataset->id, "Alternative Identifiers $action : " . $this->owner->id . ' of Sample ' . $this->owner->sample->name);
        }
    }

    private function sampleAttribute($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $datasets = $this->owner->sample->datasets;
        $action = $type === 'create' ? 'added' : $type === 'update' ? 'updated' : 'removed';

        foreach ($datasets as $dataset) {
            $unit = ($this->owner->unit) ? $this->owner->unit->name : '';
            $this->createLog($dataset->id, "Sample Attribute $action : " . $this->owner->value . ' ' . $unit . ' of Sample ' . $this->owner->sample->name);
        }
    }

    private function sampleRel($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $datasets = $this->owner->sample->datasets;
        $action = $type === 'create' ? 'added' : 'removed';
        if ($type == 'update') {
            return;
        }
        foreach ($datasets as $dataset) {
            $relatedSample = Sample::findByPk($this->owner->related_sample_id);
            $this->createLog($dataset->id, "Sample Relationship $action : " . $relatedSample->name . ' of Sample ' . $this->owner->sample->name);
        }
    }

    private function expAttributes($type)
    {
        if (!in_array($type, ['create', 'update', 'delete'])) {
            throw new \InvalidArgumentException('an error occured');
        }

        $dataset = $this->owner->exp->dataset;
        $action = $type === 'create' ? 'added' : 'removed';

        $this->createLog($dataset->id, "Experiment Attribute $action : " . $this->owner->value . ' of Experiment ' . $this->owner->exp->experiment_name);
    }
}
