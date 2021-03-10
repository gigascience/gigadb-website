<?php


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
        // TODO: Implement getPreviewDataForLinks() method.
    }
}