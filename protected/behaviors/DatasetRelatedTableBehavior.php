<?php 

class DatasetRelatedTableBehavior extends ActiveRecordLogableBehavior
{
    private function getMethodName() {
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

        if(isset($methods[$this->Owner->tableName()])) {
            $method = $methods[$this->Owner->tableName()];
            if(method_exists($this, $method)) {
               return $method;
            }
        }

        return null;
    }

    public function afterSave($event)
    {
        if($this->getMethodName() != null) {
            $method = $this->getMethodName();
            if ($this->Owner->isNewRecord) 
            {
                # on create
                $this->$method("create");
            } else {
                # on update
                $newAttrs = $this->Owner->getAttributes();
                $oldAttrs = $this->getOldAttributes();

                $isUpdated = false;

                foreach($newAttrs as $key => $value) {
                    if(!empty($oldAttrs)) {
                        if($oldAttrs[$key] != $value) {
                            $isUpdated = true;
                        }
                    }
                }

                if($isUpdated) {
                    $this->$method("update");
                }
            }         
        }
    }
 
    public function afterDelete($event)
    {
        if($this->getMethodName() != null) {
            $method = $this->getMethodName();
            $this->$method("delete");
        }
    }

    private function relation($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Relationship added : DOI ".$this->Owner->related_doi);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "Relationship updated : DOI ".$this->Owner->related_doi);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Relationship removed : DOI ".$this->Owner->related_doi);
        } 
    }

    private function manuscript($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link added : ".$this->Owner->identifier);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link updated : ".$this->Owner->identifier);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Manuscript Link removed : ".$this->Owner->identifier);
        } 
    }

    private function link($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Link added : ".$this->Owner->link);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "Link updated : ".$this->Owner->link);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Link removed : ".$this->Owner->link);
        } 
    }

    private function externalLink($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "External Link added : ".$this->Owner->url);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "External Link updated : ".$this->Owner->url);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "External Link removed : ".$this->Owner->url);
        } 
    }

    private function file($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "File added : ".$this->Owner->name);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "File updated : ".$this->Owner->name);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "File removed : ".$this->Owner->name);
        } 
    }

    private function experiment($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Experiment added : ".$this->Owner->experiment_name);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "Experiment updated : ".$this->Owner->experiment_name);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id,"Experiment removed : ".$this->Owner->experiment_name);
        } 
    }

    private function datasetAuthor($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Author added : ".$this->Owner->author->name);
        } else if ($type == 'update') {
            // nothing
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Author removed : ".$this->Owner->author->name);
        } 
    }

    private function datasetProject($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Project added : ".$this->Owner->project->name);
        } else if ($type == 'update') {
            // nothing
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Project removed : ".$this->Owner->project->name);
        } 
    }

    private function datasetType($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Dataset Type added : ".$this->Owner->type->name);
        } else if ($type == 'update') {
            // nothing
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Dataset Type removed : ".$this->Owner->type->name);
        } 
    }

    private function datasetSample($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Sample added : ".$this->Owner->sample->name);
        } else if ($type == 'update') {
            // nothing
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Sample removed : ".$this->Owner->sample->name);
        } 
    }

    private function datasetFunder($type) {
        if($type == "create") {
            $this->createLog($this->Owner->dataset_id, "Funder added : ".$this->Owner->funder->primary_name_display);
        } else if ($type == 'update') {
            $this->createLog($this->Owner->dataset_id, "Funder updated : ".$this->Owner->funder->primary_name_display);
        } else if ($type == 'delete') {
            $this->createLog($this->Owner->dataset_id, "Funder removed : ".$this->Owner->funder->primary_name_display);
        } 
    }

    private function sample($type) {
        $datasets = $this->Owner->datasets;
        if($type == "create") {
           // nothing on create
        } else if ($type == 'update') {
            foreach($datasets as $dataset) {
                $this->createLog($dataset->id, "Sample updated : ".$this->Owner->name);
            }
        } else if ($type == 'delete') {
            foreach($datasets as $dataset) {
                $this->createLog($dataset->id, "Sample removed : ".$this->Owner->name);
            }
        } 
    }

    private function alternativeIdentifiers($type) {
        $datasets = $this->Owner->sample->datasets;
        if($type == "create") {
           foreach($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers added : ".$this->Owner->id." of Sample ".$this->Owner->sample->name);
            }
        } else if ($type == 'update') {
            foreach($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers updated : ".$this->Owner->id." of Sample ".$this->Owner->sample->name);
            }
        } else if ($type == 'delete') {
            foreach($datasets as $dataset) {
                $this->createLog($dataset->id, "Alternative Identifiers removed : ".$this->Owner->id." of Sample ".$this->Owner->sample->name);
            }
        } 
    }
     
    private function sampleAttribute($type) {
        $datasets = $this->Owner->sample->datasets;
        if($type == "create") {
           foreach($datasets as $dataset) {
                $unit = ($this->Owner->unit)? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute added : ".$this->Owner->value." ".$unit." of Sample ".$this->Owner->sample->name);
            }
        } else if ($type == 'update') {
            foreach($datasets as $dataset) {
                $unit = ($this->Owner->unit)? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute updated : ".$this->Owner->value." ".$unit." of Sample ".$this->Owner->sample->name);
            }
        } else if ($type == 'delete') {
            foreach($datasets as $dataset) {
                $unit = ($this->Owner->unit)? $this->Owner->unit->name : "";
                $this->createLog($dataset->id, "Sample Attribute removed : ".$this->Owner->value." ".$unit." of Sample ".$this->Owner->sample->name);
            }
        } 
    }
    
    private function sampleRel($type) {
        $datasets = $this->Owner->sample->datasets;
        if($type == "create") {
           foreach($datasets as $dataset) {
                $relatedSample = Sample::findByPk($this->Owner->related_sample_id);
                $this->createLog($dataset->id, "Sample Relationship added : ".$relatedSample->name." of Sample ".$this->Owner->sample->name);
            }
        } else if ($type == 'update') {
            // nothing
        } else if ($type == 'delete') {
            foreach($datasets as $dataset) {
                $relatedSample = Sample::findByPk($this->Owner->related_sample_id);
                $this->createLog($dataset->id, "Sample Relationship removed : ".$relatedSample->name." of Sample ".$this->Owner->sample->name);
            }
        } 
    }

    private function expAttributes($type) {
        $dataset = $this->Owner->exp->dataset;
        if($type == "create") {
            $this->createLog($dataset->id, "Experiment Attribute added : ".$this->Owner->value." of Experiment ".$this->Owner->exp->experiment_name);
        } else if ($type == 'update') {
            // updated
        } else if ($type == 'delete') {
            $this->createLog($dataset->id, "Experiment Attribute removed : ".$this->Owner->value." of Experiment ".$this->Owner->exp->experiment_name);
        } 
    }

}