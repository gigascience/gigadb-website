<?php

/**
 * Service that provide autocompletion to the forms for controllers
 * it is meant to be called in an AJAX context from a form
 *
 * Currently used by AdminDatasetSampleController and AdminExternalLinkController.
 * It is setup as an application component, so it must have an entry in the main.php config file.
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AutoCompleteService extends CApplicationComponent
{
    /**
     * partial search for the term passed in parameter
     *
     * To be used by AdminExternalLinkController
     * and by AdminDatasetSampleController
     * @param mixed $term search keyword
     * @return array list of matching species terms
    */
    public function findSpeciesLike($term)
    {
        $result = [];

        if (is_numeric($term)) {
            $sql = "select tax_id,common_name,scientific_name from species where cast(tax_id as text) like :name";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindValue(":name", $term . '%', PDO::PARAM_STR);
            $query_resultset = $command->queryAll();
        } else {
            $sql = "select tax_id , common_name ,scientific_name from
                species where common_name ilike :name or scientific_name ilike :name
                order by length(common_name)";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindValue(":name", '%' . $term . '%', PDO::PARAM_STR);
            $query_resultset = $command->queryAll();
        }

        if (!empty($query_resultset)) {
            foreach ($query_resultset as $row) {
                $name = $row['tax_id'] . ":";
                $has_common_name = false;
                if ($row['common_name'] != null) {
                    $has_common_name = true;
                    $name .= $row['common_name'];
                    $name .= ",";
                }

                $name .= $row['scientific_name'];


                $result[] = $name;
            }
        }

        return $result;
    }
}
