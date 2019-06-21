<?

class AttributesCommand extends CConsoleCommand {
    public function actionAdd() {
        $neededAttrs = array(
            'analyte type',
            'alternative accession-BioSample',
            'alternative accession-BioProject',
            'Geographic location (country and/or sea,region)',
            'geographic location (latitude)',
            'geographic location (longitude)',
            'Broad-scale environmental context',
            'Local environmental context',
            'Life stage',
            'Age',
            'Sample source',
            'Collection date',
            'Ploidy',
            'Tissue',
            'Collected by',
            'alternative names',
            'alternative names',
            'Sample collection device or method',
            'Sample material processing',
            'Amount or size of sample collected',
            'Estimated genome size',
            'sequencing method',
            'assembly software',
        );

        foreach ($neededAttrs as $attributeName) {
            $attribute = Attribute::findByAttrName($attributeName);
            if (!$attribute) {
                $attribute = new Attribute();
                $attribute->attribute_name = $attributeName;
                $attribute->save();
            }
        }

    } 
}
?>

