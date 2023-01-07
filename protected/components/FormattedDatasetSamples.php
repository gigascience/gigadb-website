<?php
/**
 * Adapter class to present the samples associated to a dataset for a dataset view
 *
 * @param int $pageSize how many sample items should be displayed on one page
 * @param DatasetSamplesInterface $datasetSamples the adaptee class to fall back to
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetSamples extends DatasetComponents implements DatasetSamplesInterface
{
	private $_cachedDatasetSamples;
	private $_pageSize;

	public function __construct (int $pageSize, DatasetSamplesInterface $datasetSamples)
	{
		parent::__construct();
		$this->_cachedDatasetSamples = $datasetSamples;
		$this->_pageSize = $pageSize;
	}

	/**
	 * return the dataset id
	 *
	 * @return int
	 */
	public function getDatasetId(): int
	{
		return $this->_cachedDatasetSamples->getDatasetId();
	}

	/**
	 * return the dataset identifier (DOI)
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string
	{
		return  $this->_cachedDatasetSamples->getDatasetDOI();
	}

	/**
	 * retrieve samples associated to a dataset
	 *
	 * @return array of samples array map
	 */
	public function getDatasetSamples(): array
	{
		$formatted_samples = [];
		$samples =   array_filter($this->_cachedDatasetSamples->getDatasetSamples());
		foreach ($samples as &$sample) {
			$sample['taxonomy_link'] = "<a href=\"http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=".$sample['tax_id']."\">".$sample['tax_id']."</a>";
			$sample['displayAttr'] = self::getDisplayAttr($sample['sample_id'], $sample['sample_attributes']);
		}
		return $samples;
	}

	/**
	 * Wrap the list of samples associated to a dataset in a CArrayProvider so we get pagination and sorting
	 *
	 * @return CArrayDataProvider the DataProvider to be used in view templates for managing the list of samples to display
	 */
	public function getDataProvider(): CArrayDataProvider
	{
//		$samples = $this->getDatasetSamples();
        $objectToHash =  function ($sample) {

            $toNameValueHash = function ($sample_attribute) {
                return array( $sample_attribute->attribute->attribute_name => $sample_attribute->value);
            };

            return array(
                'sample_id' => $sample->id,
                'linkName' => $sample->getLinkName(),
                'dataset_id' => $this->getDatasetId(),
                'species_id' => $sample->species_id,
                'tax_id' => $sample->species->tax_id,
                'common_name'=> $sample->species->common_name,
                'scientific_name'=> $sample->species->scientific_name,
                'genbank_name' => $sample->species->genbank_name,
                'name' => $sample->name,
                'consent_document' => $sample->consent_document,
                'submitted_id' => $sample->submitted_id,
                'submission_date' => $sample->submission_date,
                'contact_author_name' => $sample->contact_author_name,
                'contact_author_email' => $sample->contact_author_email,
                'sampling_protocol' => $sample->sampling_protocol,
                'sample_attributes' => array_map($toNameValueHash, SampleAttribute::model()->findAllByAttributes( array('sample_id' => $sample->id) )),
            );
        };
        $sql = "select
		ds.sample_id as id, s.name, s.species_id, s.consent_document, s.submitted_id, s.submission_date, s.contact_author_name, s.contact_author_email, s.sampling_protocol
		from sample s, dataset_sample ds
		where ds.sample_id = s.id and ds.dataset_id=:id" ;

        $samples_pagination = new CPagination(count($samples));
        $samples_pagination->setPageSize($this->_pageSize);
        $samples_pagination->pageVar = "Samples_page";

        $criteria = new CDbCriteria;
        $criteria->select = "ds.sample_id as id, t.name, t.species_id, t.consent_document, t.submitted_id, t.submission_date, t.contact_author_name, t.contact_author_email, t.sampling_protocol";
        $criteria->join = "join dataset_sample ds on t.id = ds.sample_id";
        $criteria->condition = "ds.dataset_id=:id";
        $criteria->params = [":id" => $this->getDatasetId()];
        $samples_pagination->applyLimit($criteria);
        $samples = Sample::model()->findAll($criteria);
        $result = array_map($objectToHash, $samples);


        $dataProvider= new CArrayDataProvider( $result , array(
		    'sort' => array('defaultOrder'=>'t.name ASC',
                            'attributes' => array(
                                    'name',
                                    'common_name',
                                    'genbank_name',
                                    'scientific_name',
                                    'tax_id',
                                )),
		    'pagination' => null
		    )
		);
        $dataProvider->setPagination($samples_pagination);
        Yii::log("sample count:" . count($result));
        Yii::log("pagination limit:" . $samples_pagination->getLimit());
        Yii::log("pagination offset:" . $samples_pagination->getOffset());
        return $dataProvider;
	}

	public static function shortAttrDesc(array $sample_attributes) {
		$desc = "";
        foreach($sample_attributes as $idx => $nameValue){
            $attr = ucfirst(implode(array_keys($nameValue))). ":".implode(array_values($nameValue));
            $short = strlen($attr) > 50 ? substr($attr, 0, 50). "...<br/>":$attr ."<br/>";
            $desc .= $short;
            if($idx > 1)
            	break;
        }
        return $desc."...";
	}

	public static function fullAttrDesc(array $sample_attributes) {
		$desc = "";
        foreach($sample_attributes as $nameValue){
            $name = implode(array_keys($nameValue));
            $attr = ucfirst($name). ":".implode(array_values($nameValue))."<br/>";
            $desc .= $attr;
        }
        return $desc;
	}

	public static function getDisplayAttr(int $sample_id, array $sample_attributes) {
		$num = count($sample_attributes);
		$shortDesc = self::shortAttrDesc($sample_attributes) ;
		$fullDesc = self::fullAttrDesc($sample_attributes) ;
		$display = "";
		if($num > 3) {
			$display ="<span class=\"js-short-$sample_id\">$shortDesc</span>
        		<span class=\"js-long-$sample_id\" style=\"display: none;\">$fullDesc</span>";
		    if($shortDesc)
		            $display .= "<a href='#' class='js-desc' data='$sample_id'>+</a>";
  		}
  		elseif ( $num <= 3 && $num > 0) {
	  		$display = "<span class=\"js-long-$sample_id\">$fullDesc</span>";
  		}
        return $display;
	}

}

?>