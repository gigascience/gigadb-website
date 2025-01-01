<?php

class SampleLocationHelper extends CComponent
{
  public static function getLocations($dataset_identifier = null)
  {
    $sql = "SELECT d.identifier, d.title, satt.value, sp.scientific_name as sciname, s.id as sampleid
                FROM dataset as d
                INNER JOIN dataset_sample as dsam on dsam.dataset_id = d.id
                INNER JOIN sample as s on s.id = dsam.sample_id
                INNER JOIN sample_attribute as satt on satt.sample_id=s.id
                INNER JOIN species as sp on sp.id = s.species_id
                WHERE satt.attribute_id = 269 AND d.upload_status='Published'";

    if ($dataset_identifier) {
      $sql .= " AND d.identifier = :dataset_identifier";
    }

    $sql .= " ORDER BY sampleid";

    $command = Yii::app()->db->createCommand($sql);

    if ($dataset_identifier) {
      $command->bindParam(':dataset_identifier', $dataset_identifier, PDO::PARAM_STR);
    }

    $locations = $command->queryAll();

    foreach ($locations as $location) {
      $locationValue = $location["value"];
      $locationValue = preg_replace('/\s+/', '', $locationValue);
      $formatCheck = preg_match('/-?[0-9]*[.][0-9]*[,]-?[0-9]*[.][0-9]*/', $locationValue);

      if (!$formatCheck == 1) {
        continue;
      }
      $val = explode(',', $locationValue);
      if (strpos($val[0], '.') == false || !is_numeric($val[0])) {
        continue;
      }
      if (strpos($val[1], '.') == false || !is_numeric($val[1])) {
        continue;
      }
      $location["sciname"] = str_replace(",", "", $location["sciname"]);
    }
    return $locations;
  }
}