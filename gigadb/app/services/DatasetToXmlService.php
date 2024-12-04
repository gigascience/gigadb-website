<?php

declare(strict_types=1);

namespace GigaDB\services;

use yii\base\Component;

class DatasetToXmlService
{
    public function convertToXml(
        \Dataset $model,
        ?\Image $image = null,
        bool $isAll = false,
        bool $isDatasetOnly = false,
        bool $isOnlySample = false,
        bool $isOnlyFile = false
    ): string
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><gigadb_entry></gigadb_entry>");

        if ($isAll || $isDatasetOnly) {
            $xml = $this->buildDataset($xml, $model, $image);
        }

        if ($isDatasetOnly) {
            if (!$xml->asXML()) {
                return 'XML is invalid';
            }

            return $xml->asXML();
        }

        if ($isAll || $isOnlySample) {
            $xml = $this->buildSamples($xml, $model);
        }

        if ($isAll) {
            $xml->addChild('experiments');
        }

        if ($isAll || $isOnlyFile) {
            $xml = $this->buildFiles($xml, $model);
        }

        if (!$xml->asXML()) {
            return 'XML is invalid';
        }

        return $xml->asXML();
    }

    private function buildSamples(\SimpleXMLElement $xml, \Dataset $model): \SimpleXMLElement
    {
        $samplesElement = $xml->addChild('samples');
        $samples = $model->samples;

        foreach ($samples as $sample) {
            $sampleElement = $samplesElement->addChild('sample');
            $sampleElement->addAttribute('submission_date', $sample->submission_date);
            $sampleElement->addAttribute('id', (string) $sample->id);

            $sampleElement->addChild('name', $sample->name);
            $species = $sample->species;

            $speciesElement = $sampleElement->addChild('species');
            $speciesElement->addChild('tax_id', (string) $species->tax_id);
            $speciesElement->addChild('common_name', $species->common_name);
            $speciesElement->addChild('genbank_name', $species->genbank_name);
            $speciesElement->addChild('scientific_name', $species->scientific_name);
            $speciesElement->addChild('eol_link', $species->eol_link);

            $sampleElement->addChild('sampling_protocol', $sample->sampling_protocol);
            $sampleElement->addChild('consent_doc', $sample->consent_document);

            $contactAuthor = $sampleElement->addChild('contact_author');
            $contactAuthor->addChild('name', $sample->contact_author_name);
            $contactAuthor->addChild('email', $sample->contact_author_email);

            $relsamples = $sample->sampleRels;
            $relatedSamplesElement = $sampleElement->addChild('related_samples');
            foreach ($relsamples as $relsample) {
                $relSample = $relatedSamplesElement->addChild('related_sample', $sample->name);
                $relSample->addAttribute('relationship_type', $relsample->relationship->name);
            }

            $samplesAttrElement = $sampleElement->addChild('sample_attributes');
            $sa_attributes = $sample->sampleAttributes;
            foreach ($sa_attributes as $sa_attribute) {
                $saattribute = $sa_attribute->attribute;
                $attr = $samplesAttrElement->addChild('attribute');
                $attr->addChild('key', $saattribute->attribute_name);
                $attr->addChild('value', $sa_attribute->value);
                $unit = $attr->addChild('unit', $sample_unit->name ?: NULL);
                $unit->addAttribute('id', (string) $sa_attribute->unit_id);
                $sample_unit = $sa_attribute->unit;
            }
        }

        return $xml;
    }

    private function buildFiles(\SimpleXMLElement $xml, \Dataset $model): \SimpleXMLElement
    {
        $files = $model->files;
        $filesElement = $xml->addChild('files');

        foreach ($files as $file) {
            $fileElement = $filesElement->addChild('file');
            $fileElement->addAttribute('id', (string) $file->id);
            $fileElement->addAttribute('index4blast', (string) $file->index4blast);
            $fileElement->addAttribute('download_count', (string) $file->download_count);
            $fileElement->addChild('name', $file->name);
            $fileElement->addChild('location', $file->location);
            $fdescription = preg_replace('/[<>]/', '', $file->description);
            $fileElement->addChild('description', $fdescription);
            $fileElement->addChild('extension', $file->extension);
            $size = $fileElement->addChild('size', (string) $file->size);
            $size->addAttribute('units', 'bytes');
            $fileElement->addChild('release_date', $file->date_stamp);
            $file_type = $file->type;
            $type = $fileElement->addChild('type', $file_type->name);
            $type->addAttribute('id', (string) $file_type->id);
            $format = $fileElement->addChild('format', $file->format->name);
            $format->addAttribute('id', (string) $file->format_id);

            $linkedSamples = $fileElement->addChild('linked_samples');
            $filesamples = $file->fileSamples;
            foreach ($filesamples as $filesample) {
                $fi_sample = $filesample->sample;

                if (!$fi_sample) {
                    continue;
                }
                $linkedSample = $linkedSamples->addChild('linked_sample', $fi_sample->name);
                $linkedSample->addAttribute('sample_id', (string) $filesample->sample_id);
            }

            $fileAttributes = $fileElement->addChild('file_attributes');
            $fileattributes = $file->fileAttributes;

            foreach ($fileattributes as $fileattribute) {
                $attr = $fileAttributes->addChild('attribute');
                $file_att = $fileattribute->attribute;
                $file_unit = $fileattribute->unit;
                $attr->addChild('key', $file_att->attribute_name);
                $attr->addChild('value', $fileattribute->value);
                $unit = $attr->addChild('unit', $file_unit ? $file_unit->name: NULL);
                $unit->addAttribute('id', $file_unit ? $file_unit->id: '');
            }

            $fileElement->addChild('related_file');
        }

        return $xml;
    }

    private function buildDataset(\SimpleXMLElement $xml, \Dataset $model, ?\Image $image = null): \SimpleXMLElement
    {
        $datasetElement = $xml->addChild('dataset');
        $datasetElement->addAttribute('id', (string) $model->id);
        $datasetElement->addAttribute('doi', $model->identifier);

        $submitterElement = $datasetElement->addChild('submitter');
        $submitterElement->addChild('first_name', $submitter_first_name);
        $submitterElement->addChild('last_name', $submitter_last_name);
        $submitterElement->addChild('affiliation', $submitter_affiliation);
        $submitterElement->addChild('username', $submitter_username);
        $submitterElement->addChild('email', $submitter_email);

        $title = strip_tags($model->title);
        $datasetElement->addChild('title', $title);
        $model->description = htmlspecialchars(str_replace('<br>', '<br />', $model->description), ENT_XML1, 'UTF-8');
        $datasetElement->addChild('description', $model->description);

        $authorsElement = $datasetElement->addChild('authors');
        $authors = $model->authors;
        usort($authors, function ($a, $b) {
            return $a['id'] - $b['id'];
        });
        foreach ($authors as $author) {
            $authorElement = $authorsElement->addChild('author');
            $authorElement->addChild('firstname', $author->first_name);
            $authorElement->addChild('middlename', $author->middle_name);
            $authorElement->addChild('surname', $author->surname);
            $authorElement->addChild('orcid', $author->orcid);
        }

        $dataTypeElement = $datasetElement->addChild('data_types');
        $dataset_types = $model->datasetTypes;
        foreach ($dataset_types as $dataset_type) {
            $type = $dataTypeElement->addChild('dataset_type');
            $type->addChild('type_name', $dataset_type->name);
            $type->addChild('type_id', (string) $dataset_type->id);
        }

        $imageElement = $datasetElement->addChild('image');
        $imageElement->addChild('image_filename', $image->location);
        $imageElement->addChild('tag', $image->tag);
        $imageElement->addChild('license', $image->license);
        $imageElement->addChild('source', $image->source);
        $imageElement->addChild('credit', $image->photographer);

        $size = $datasetElement->addChild('dataset_size', (string) $model->dataset_size);
        $size->addAttribute('units', 'bytes');
        $datasetElement->addChild('ftp_site', $model->ftp_site);

        $publication = $datasetElement->addChild('publication');
        $publication->addAttribute('date', $model->publication_date);
        $publisher = $publication->addChild('publisher');
        $publisher->addAttribute('name', 'GigaScience database');
        $publication->addChild('modification_date', $model->modification_date);
        $fairUse = $publication->addChild('fair_use');
        $fairUse->addAttribute('date', $this->fairnuse ?: '');

        $links = $datasetElement->addChild('links');

        $externalLinks = $links->addChild('external_links');
        $external_links = $model->externalLinks;
        foreach ($external_links as $external_link) {
            $subLink = $externalLinks->addChild('external_link', $external_link->url);
            $subLink->addAttribute('type', $external_link->externalLinkType->name);
        }

        $projectLinks = $links->addChild('project_links');
        $project_links = $model->projects;
        foreach ($project_links as $project) {
            $projectLink = $projectLinks->addChild('project_link');
            $projectLink->addChild('project_name', $project->name);
            $projectLink->addChild('project_url', $project->url);
        }

        $internalLinks = $links->addChild('internal_links');
        $internal_links = $model->relations;
        foreach ($internal_links as $relation) {
            $internalLink = $internalLinks->addChild('related_DOI');
            $internalLink->addAttribute('relationship', $relation->relationship->name);
        }

        $manuscriptLinks = $links->addChild('manuscript_links');
        $manuscripts = $model->manuscripts;
        foreach ($manuscripts as $manuscript) {
            $manuscriptLink = $internalLinks->addChild('manuscript_link');
            $manuscriptLink->addChild('manuscript_DOI', $manuscript->identifier);
            $manuscriptLink->addChild('manuscript_pmid', (string) $manuscript->pmid);
        }

        $alternativeIdentifierLinks = $links->addChild('alternative_identifiers');
        $alternative_identifiers = $model->links;
        foreach ($alternative_identifiers as $link) {
            $linkname = explode(':', $link->link);
            $name = $linkname[0];
            $modelurl = \Prefix::model()->find('lower(prefix) = :p', array(':p' => strtolower($name)));
            $value = $modelurl ? sprintf('%s%s',$modelurl->url, $linkname[1]) : $linkname[1];
            $alternativeIdentifer = $alternativeIdentifierLinks->addChild('alternative_identifier', $value);
            $alternativeIdentifer->addAttribute('is_primary', (string) $link->is_primary);
            $alternativeIdentifer->addAttribute('prefix', $name);
        }

        $fundingLinks = $links->addChild('funding_links');
        $dataset_funders = $model->datasetFunders;
        foreach ($dataset_funders as $dataset_funder) {
            $grant = $fundingLinks->addChild('grant');
            $funder = $dataset_funder->funder;
            $grant->addChild('funder_name', $funder->primary_name_display);
            $grant->addChild('fundref_url', $funder->uri);
            $grant->addChild('award', $dataset_funder->grant_award);
            $grant->addChild('comment', $dataset_funder->comments);
        }

        $attribute = $datasetElement->addChild('ds_attributes');
        $dataset_attributes = $model->datasetAttributes;
        foreach ($dataset_attributes as $dataset_attribute) {
            if (!$dataset_attribute->value) {
                continue;
            }

            $attribute->addChild('attribute');
            $attr = $dataset_attribute->attribute;
            $unit = $dataset_attribute->units;
            $attribute->addChild('key', $attr ? $attr->attribute_name : NULL);
            $attribute->addChild('value', $dataset_attribute->value);
            $unitEl = $attribute->addChild('unit');
            $unitEl->addAttribute('id', $unit ? $unit->id : '');
        }

        return $xml;
    }
}
