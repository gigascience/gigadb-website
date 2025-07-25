<?php

declare(strict_types=1);

class DatasetRelatedTableBehavior extends ActiveRecordLogableBehavior
{
    private function getMethodName(): ?string
    {
        $methods = array(
            # related to dataset
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
        );

        if (isset($methods[$this->Owner->tableName()])) {
            $method = $methods[$this->Owner->tableName()];
            if (method_exists($this, $method)) {
                return $method;
            }
        }

        return null;
    }

    /**
     * @param CEvent $event event parameter
     *
     * @return void
     */
    protected function afterSave($event): void
    {
        if ($this->getMethodName() != null) {
            $method = $this->getMethodName();
            if ($this->Owner->isNewRecord) {
                # on create
                $this->$method("create");
            } else {
                # on update
                $newAttrs = $this->Owner->getAttributes();
                $oldAttrs = $this->getOldAttributes();

                $isUpdated = false;

                foreach ($newAttrs as $key => $value) {
                    if (!empty($oldAttrs)) {
                        if ($oldAttrs[$key] != $value) {
                            $isUpdated = true;
                        }
                    }
                }

                if ($isUpdated) {
                    $this->$method("update");
                }
            }
        }
    }

    /**
     * @param CEvent $event event parameter
     *
     * @return void
     */
    protected function afterDelete($event): void
    {
        if ($this->getMethodName() != null) {
            $method = $this->getMethodName();
            $this->$method("delete");
        }
    }

    private function relation(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Relationship added : DOI " . $this->Owner->related_doi);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "Relationship updated : DOI " . $this->Owner->related_doi);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Relationship removed : DOI " . $this->Owner->related_doi);
        }
    }

    private function manuscript(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link added : " . $this->Owner->identifier);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link updated : " . $this->Owner->identifier);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link removed : " . $this->Owner->identifier);
        }
    }

    private function link(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Link added : " . $this->Owner->link);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "Link updated : " . $this->Owner->link);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Link removed : " . $this->Owner->link);
        }
    }

    private function externalLink(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "External Link added : " . $this->Owner->url);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "External Link updated : " . $this->Owner->url);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "External Link removed : " . $this->Owner->url);
        }
    }

    private function file(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "File added : " . $this->Owner->name);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "File updated : " . $this->Owner->name);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "File removed : " . $this->Owner->name);
        }
    }

    private function experiment(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Experiment added : " . $this->Owner->experiment_name);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "Experiment updated : " . $this->Owner->experiment_name);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Experiment removed : " . $this->Owner->experiment_name);
        }
    }

    private function datasetAuthor(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Author added : " . $this->Owner->author->name);
        } elseif ($type === 'update') {
            // nothing
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Author removed : " . $this->Owner->author->name);
        }
    }

    private function datasetProject(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Project added : " . $this->Owner->project->name);
        } elseif ($type === 'update') {
            // nothing
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Project removed : " . $this->Owner->project->name);
        }
    }

    private function datasetType(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Dataset Type added : " . $this->Owner->type->name);
        } elseif ($type === 'update') {
            // nothing
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Dataset Type removed : " . $this->Owner->type->name);
        }
    }

    private function datasetSample(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Sample added : " . $this->Owner->sample->name);
        } elseif ($type === 'update') {
            // nothing
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Sample removed : " . $this->Owner->sample->name);
        }
    }

    private function datasetFunder(string $type): void
    {
        if ($type === "create") {
            $this->createLog($this->Owner->dataset_id, "Funder added : " . $this->Owner->funder->primary_name_display);
        } elseif ($type === 'update') {
            $this->createLog($this->Owner->dataset_id, "Funder updated : " . $this->Owner->funder->primary_name_display);
        } elseif ($type === 'delete') {
            $this->createLog($this->Owner->dataset_id, "Funder removed : " . $this->Owner->funder->primary_name_display);
        }
    }

    private function sample(string $type): void
    {
        $datasets = $this->Owner->datasets;
        if ($type === "create") {
           // nothing on create
        } elseif ($type === 'update') {
            foreach ($datasets as $dataset) {
                $this->createLog($dataset->id, "Sample updated : " . $this->Owner->name);
            }
        } elseif ($type === 'delete') {
            foreach ($datasets as $dataset) {
                $this->createLog($dataset->id, "Sample removed : " . $this->Owner->name);
            }
        }
    }

    private function alternativeIdentifiers(string $type): void
    {
        $datasets = $this->Owner->sample->datasets;
        if ($type === "create") {
            foreach ($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers added : " . $this->Owner->id . " of Sample " . $this->Owner->sample->name);
            }
        } elseif ($type === 'update') {
            foreach ($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers updated : " . $this->Owner->id . " of Sample " . $this->Owner->sample->name);
            }
        } elseif ($type === 'delete') {
            foreach ($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers removed : " . $this->Owner->id . " of Sample " . $this->Owner->sample->name);
            }
        }
    }

    private function sampleAttribute(string $type): void
    {
        $datasets = $this->Owner->sample->datasets;
        if ($type === "create") {
            foreach ($datasets as $dataset) {
                $unit = ($this->Owner->unit) ? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute added : " . $this->Owner->value . " " . $unit . " of Sample " . $this->Owner->sample->name);
            }
        } elseif ($type === 'update') {
            foreach ($datasets as $dataset) {
                $unit = ($this->Owner->unit) ? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute updated : " . $this->Owner->value . " " . $unit . " of Sample " . $this->Owner->sample->name);
            }
        } elseif ($type === 'delete') {
            foreach ($datasets as $dataset) {
                $unit = ($this->Owner->unit) ? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute removed : " . $this->Owner->value . " " . $unit . " of Sample " . $this->Owner->sample->name);
            }
        }
    }

    private function sampleRel(string $type): void
    {
        $datasets = $this->Owner->sample->datasets;
        if ($type === "create") {
            foreach ($datasets as $dataset) {
                $relatedSample = Sample::model()->findByPk($this->Owner->related_sample_id);
                $this->createLog($dataset->id, "Sample Relationship added : " . $relatedSample->name . " of Sample " . $this->Owner->sample->name);
            }
        } elseif ($type === 'update') {
            // nothing
        } elseif ($type === 'delete') {
            foreach ($datasets as $dataset) {
                $relatedSample = Sample::model()->findByPk($this->Owner->related_sample_id);
                $this->createLog($dataset->id, "Sample Relationship removed : " . $relatedSample->name . " of Sample " . $this->Owner->sample->name);
            }
        }
    }

    private function expAttributes(string $type): void
    {
        $dataset = $this->Owner->exp->dataset;
        if ($type === "create") {
            $this->createLog($dataset->id, "Experiment Attribute added : " . $this->Owner->value . " of Experiment " . $this->Owner->exp->experiment_name);
        } elseif ($type === 'update') {
            // updated
        } elseif ($type === 'delete') {
            $this->createLog($dataset->id, "Experiment Attribute removed : " . $this->Owner->value . " of Experiment " . $this->Owner->exp->experiment_name);
        }
    }
}
