<?php
/**
 * Format the cached information that can be displayed in the view page.
 * Class FormattedDatasetLinksPreview
 */

class FormattedDatasetLinksPreview extends DatasetComponents implements DatasetLinksPreviewInterface
{
    private $_cachedDatasetLinksPreview;
    private $_controller;

    public function __construct(CController $controller, DatasetLinksPreviewInterface $datasetLinksPreview)
    {
        parent::__construct();
        $this->_cachedDatasetLinksPreview = $datasetLinksPreview;
        $this->_controller = $controller;
    }


    public function getDatasetId(): int
    {
        return $this->_cachedDatasetLinksPreview->getDatasetId();
    }

    public function getDatasetDOI(): string
    {
        return $this->_cachedDatasetLinksPreview->getDatasetDOI();
    }

    public function getPreviewDataForLinks(): array
    {
        $formattedPreviewData = [];
        $previewData = $this->_cachedDatasetLinksPreview->getPreviewDataForLinks();
        foreach ($previewData as $data) {
            $data['preview_title'] = '<a href="'.$data['external_url'].'">'.$data['external_title'].'</a>';
            $data['preview_description'] = '<p>'.$data['external_description'].'</p>';
            $data['preview_imageUrl'] = '<img src="'.$data['external_imageUrl'].'alt="Image">';
            $formattedPreviewData[] = $data;
        }
        return $formattedPreviewData;
    }
}