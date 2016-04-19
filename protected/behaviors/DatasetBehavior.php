<?php 

class DatasetBehavior extends ActiveRecordLogableBehavior
{ 
    public function afterSave($event)
    {
        if (!$this->Owner->isNewRecord) 
        {

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
                if($oldAttrs['publisher_id'] != $newAttrs['publisher_id']) {
                    # add a log when dataset publisher is updated
                    $publisher = Publisher::model()->findByPk($oldAttrs['publisher_id']);
                    if($publisher) {
                        $this->createLog($this->Owner->id, 'Publisher name updated from : '.$publisher->name);
                    }
                }
                if($oldAttrs['submitter_id'] != $newAttrs['submitter_id']) {
                     $submitter = User::model()->findByPk($oldAttrs['submitter_id']);
                     if($submitter) {
                        $this->createLog($this->Owner->id, 'Submitter updated from : '. $submitter->first_name . ' ' .$submitter->last_name);
                    }
                }
                if($oldAttrs['image_id'] != $newAttrs['image_id']) {
                    # add a log when dataset image is updated
                    $this->createLog($this->Owner->id, 'Update image');
                }
                if($oldAttrs['upload_status'] != 'Published' and $newAttrs['upload_status'] == 'Published') {
                    # add a log when dataset is published
                    $this->createLog($this->Owner->id, 'Dataset publish');
                }
                if(preg_replace('/\s+/', ' ', trim($oldAttrs['description'])) != preg_replace('/\s+/', ' ', trim($newAttrs['description']))) {
                    $this->createLog($this->Owner->id, 'Description updated from : '.$oldAttrs['description']);
                }
                if($oldAttrs['title'] != $newAttrs['title']) {
                    $this->createLog($this->Owner->id, 'Title updated from : '.$oldAttrs['title']);
                }
                if($oldAttrs['dataset_size'] != $newAttrs['dataset_size']) {
                    $this->createLog($this->Owner->id, 'Dataset size updated from : '. $oldAttrs['dataset_size'] . ' bytes');
                }
                if($oldAttrs['modification_date'] != $newAttrs['modification_date']) {
                    $this->createLog($this->Owner->id, 'Modification date added : '. $this->Owner->modification_date);
                }
                if($oldAttrs['fairnuse'] != $newAttrs['fairnuse']) {
                    $this->createLog($this->Owner->id, 'Fair Use date added : ' . $this->Owner->fairnuse);
                }
            }

        } else {
            $this->createLog($this->Owner->id, 'Dataset Created');
        }
    }
}