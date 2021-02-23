<?php

/**
 * Method template for previewing dataset links
 * Interface DatasetLinksPreviewInterface
 *
 * @see StoredDatasetLinksPreview.php
 * @see CachedDatasetLinksPreview.php
 */
interface DatasetLinksPreviewInterface
{
    /**
     * return the dataset id
     * @return int
     */
    public function getDatasetId(): int;

    /**
     * return the dataset identifier
     * @return string
     */
    public function getDatasetDOI(): string;

    /**
     * return the image url
     * @return array
     */
    public function getImageUrl(): array;

    /**
     * extract short doi, url, title, description and image url
     * @return array of string for previewing the links
     */
    public function getPreviewDataForLinks(): array;

}