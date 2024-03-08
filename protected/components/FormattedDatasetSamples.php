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

    public function __construct(int $pager, DatasetSamplesInterface $datasetSamples)
    {
        parent::__construct();
        $this->_cachedDatasetSamples = $datasetSamples;
        $this->_pageSize = $pager;
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
    public function getDatasetSamples(?string $limit = "ALL", ?int $offset = 0): array
    {
        $formatted_samples = [];
        $samples =   array_filter($this->_cachedDatasetSamples->getDatasetSamples($limit, $offset));
        foreach ($samples as &$sample) {
            $sample['taxonomy_link'] = "<a href=\"http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=" . $sample['tax_id'] . "\">" . $sample['tax_id'] . "</a>";
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
        $samples = $this->getDatasetSamples();
        $dataProvider = new CArrayDataProvider($samples, array(
            'sort' => array('defaultOrder' => 't.name ASC',
                            'attributes' => array(
                                    'name',
                                    'common_name',
                                    'genbank_name',
                                    'scientific_name',
                                    'tax_id',
                                )),
            'pagination' => null
            ));
        $samples_pagination = new CPagination(count($samples));
        $samples_pagination->setPageSize($this->_pageSize);
        $samples_pagination->pageVar = "Samples_page";
        $dataProvider->setPagination($samples_pagination);
        return $dataProvider;
    }

    public static function shortAttrDesc(array $sample_attributes)
    {
        $desc = "";
        foreach ($sample_attributes as $idx => $nameValue) {
            $attr = ucfirst(implode(array_keys($nameValue))) . ":" . implode(array_values($nameValue));
            $short = strlen($attr) > 50 ? substr($attr, 0, 50) . "...<br/>" : $attr . "<br/>";
            $desc .= $short;
            if ($idx > 1) {
                break;
            }
        }
        return $desc . "...";
    }

    public static function fullAttrDesc(array $sample_attributes)
    {
        $desc = "";
        foreach ($sample_attributes as $nameValue) {
            $name = implode(array_keys($nameValue));
            $attr = ucfirst($name) . ":" . implode(array_values($nameValue)) . "<br/>";
            $desc .= $attr;
        }
        return $desc;
    }

    public static function getDisplayAttr(int $sample_id, array $sample_attributes)
    {
        $num = count($sample_attributes);
        $shortDesc = self::shortAttrDesc($sample_attributes) ;
        $fullDesc = self::fullAttrDesc($sample_attributes) ;
        $display = "";
        if ($num > 3) {
            $display = "<span class=\"js-short-$sample_id\">$shortDesc</span>
        		<span class=\"js-long-$sample_id\" style=\"display: none;\">$fullDesc</span>";
            if ($shortDesc) {
                    $display .= "<button class='js-desc btn btn-link' data='$sample_id' aria-label='show more' aria-expanded='false' aria-controls='js-long-$sample_id'>+</button>";
            }
        } elseif ($num <= 3 && $num > 0) {
            $display = "<span class=\"js-long-$sample_id\">$fullDesc</span>";
        }
        return $display;
    }

    /**
     * count number of samples associated to a dataset
     *
     * @return int how many samples are associated with the dataset
     */
    public function countDatasetSamples(): int
    {
        return $this->_cachedDatasetSamples->countDatasetSamples();
    }
}
