<?php

Class BundleBehavior extends CBehavior { //owning class needs to implements the DatasetFilesInterface



        public function is_in_bundle($raw_bundle) { //used in CCheckboxColumn  to control wether the checkbox is ticked or not
            $bundle = unserialize($raw_bundle) ;
            //error_log($raw_bundle , 0);

            if ( isset($bundle[ $this->owner->getDatasetIdentifier()][$this->owner->getLocationUrl()]) ) {
                //error_log("MATCH Dataset: ". $this->owner->getDatasetIdentifier() . PHP_EOL . "Location: ".$this->owner->getLocationUrl() . PHP_EOL, 0);
                return true;
            }
            else {
                //error_log("Dataset: ".  $this->owner->getDatasetIdentifier() . PHP_EOL . "Location: ". $this->owner->getLocationUrl(). PHP_EOL , 0) ;
                return false;
            }
        }
}
 ?>
