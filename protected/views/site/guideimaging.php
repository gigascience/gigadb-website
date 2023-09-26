<?php
$this->pageTitle = 'GigaDB - Imaging Dataset checklists';

//echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/about.html');
?>
<div class="content">
    <div class="container">
        <section class="page-title-section" style="margin-bottom: 10px">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a></li>
                    <li class="active">Guidelines</li>
                </ol>
                <h1 class="h4">Imaging Dataset checklists</h1>
            </div>
        </section>
        <?php
            $this->widget('application.components.GuideNavigation');
        ?>
        <section>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <h2 class="h4 page-subtitle">Imaging Dataset Checklist </h2>
                        <div class="subsection">
                            <p>All clinical imaging data must be fully anonymised including removal of any identifiable <a target="_blank" href="https://www.dicomlibrary.com/dicom/">DICOM</a> metadata from the image library files (there are several free tools designed to do this).
                            </p>
                            <p>For imaging datasets we would expect to see many of the files listed in the table below, please note this list is not comprehensive and curators/reviewers may ask for additional/different files depending on the specific content of the manuscript.</p>
                            <div id='table_imaging_format' class="scrollbar">
                                <table border="1" class="guide-table">
                                    <thead>
                                        <tr>
                                            <th class="col-70">
                                                Item
                                            </th>
                                            <th class="col-20">
                                                Suggested format
                                            </th>
                                            <th class="col-10">
                                                Check
                                            </th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>
                                            3D volumetric image data
                                        </td>
                                        <td>
                                            TIF stack, NIFTI, DICOM
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            3D mask (e.g. neuroimaging mask files)
                                        </td>
                                        <td>
                                            NIFTI
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            3D surface rendered image / segmented image
                                        </td>
                                        <td>
                                            STL
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            3D Confocal image data
                                        </td>
                                        <td>
                                            TIF stack
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            2D image data (e.g. histology images)
                                        </td>
                                        <td>
                                            TIF, BMP
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            2D mask
                                        </td>
                                        <td>
                                            TIF, BMP
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Video
                                        </td>
                                        <td>
                                            MP4
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            GitHub archive of software used to analyse image data
                                        </td>
                                        <td>
                                            ZIP
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Scripts used to analyse image data
                                        </td>
                                        <td>
                                            Python, MATLAB, Shell script
                                        </td>
                                        <td>

                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <br>
                            <br>
                            <p>In addition these File Attributes may also be included for Imaging datasets:</p>
                            <br>
                            <br>
                            <div id='table_imaging_attribute' class="scrollbar">
                                <table border="1" class="guide-table">
                                    <thead>
                                        <tr>
                                            <th class="col-70">
                                                Item
                                            </th>
                                            <th class="col-20">
                                                Suggested format
                                            </th>
                                            <th class="col-10">
                                                Check
                                            </th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>
                                            Bit-depth
                                        </td>
                                        <td>
                                            8-bit, 12-bit, 16-bit
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Pixel resolution
                                        </td>
                                        <td>
                                            e.g. 0.34x0.34 microns
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Voxel resolution
                                        </td>
                                        <td>
                                            e.g. 2.5 mm (isotropic)
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <p>While there is no specific body providing guidelines for sampling metadata for Imaging datasets we would expect to see sample metadata that is as comprehensive as that of sequencing datasets, the most common attributes that we might expect are summarised below</p>
                            <p>The complete list of pre-defined sample attributes are available in the <a href="/">GigaDB home page</a>, and it is possible to include bespoke attributes by communication with us.</p>
                            <br>
                            <br>
                            <div id='table_imaging_meta' class="scrollbar">
                                <table border="1" class="guide-table">
                                    <thead>
                                        <tr>
                                            <th class="col-30">
                                                Attribute
                                            </th>
                                            <th class="col-10">
                                                Requirement <sup aria-hidden="true">*<sup>
                                            </th>
                                            <th class="col-60">
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>
                                            Sample name<sup aria-hidden="true">^</sup><span class="sr-only">, absolutely mandatory field</span>
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Use an alphanumeric string to uniquely identify each sample used in your study, you may use BioSample IDs if you have them.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Species tax ID
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please enter the <a target="_blank" href="http://www.ncbi.nlm.nih.gov/Taxonomy">NCBI Taxonomy ID</a> for the species used in your study. NB this is mandatory for any sequenced samples.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Species name<sup aria-hidden="true">^</sup><span class="sr-only">, absolutely mandatory field</span>
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please enter the bionomial (Genus species) name for the species of this sample
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Description<sup aria-hidden="true">^</sup><span class="sr-only">, absolutely mandatory field</span>
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Human readable description of sample, it should be unique within a dataset i.e. no two samples are identical so the description should reflect that.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Geographic location (country and/or sea,region)
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            The geographical origin of the sample as defined by the country or sea name followed by specific region name. Country or sea names should be chosen from the <a target="_blank" href="http://www.insdc.org/country">INSDC country list</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Geographic location (latitude and longitude)
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            The geographical origin of the sample as defined by latitude and longitude. The values should be reported in decimal degrees and on WGS84 system e.g. -69.576435, 91.883948
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Tissue type
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please provide details of the type of tissue sampled, where possible values should be from an ontology such as <a target="_blank" href="http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=UBERON">UBERON</a> e.g."lung  [UBERON:0002048]"
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Cell type
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Cell type from which the sequence was obtained, where possible value(s) should be from an ontology such as <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/bto">BREDNA</a>, e.g. "lung epithelium [BTO:0001653]"
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Isolate
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            ID of individual isolate from which the sample was obtained
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Life stage
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please provide one or more ontology terms to describe the life or developmental stage of the organism sampled. e.g. pupa [UBERON:0003143]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Age
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Age of host or specimen at the time of sampling; relevant scale depends on species and study, e.g. could be seconds for amoebae or centuries for trees. Please include units. e.g. 5 days
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sex
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Physical sex of the specimen sampled (or host), controlled vocabulary [male|female|neuter|hermaphrodite|not determined]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Disease status
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            List of diseases with which the host has been diagnosed, can include multiple diagnoses. The value of the field depends on host, for humans the terms should be chosen from DO (Disease Ontology), other hosts are free text. For DO terms, please see https://www.ebi.ac.uk/ols/ontologies/doid
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample source
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Additional information about where the sample originated from, e.g. CAMELYON16
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample storage location
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Location at which sample was stored, usually includes name of a specific freezer/room e.g. University Medical Center Utrecht, room B101
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Experiment type
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            The name of the type of experiment performed, e.g. MS/MS, sequencing, DNA extraction, imaging, CT imaging etc.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Experiment scanner
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Imaging experiment details, scanner name
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Experiment scan method
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Imaging experiment details, scan method
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Experiment scan parameters
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Imaging experiment details, scan parameters
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Experiment scan resolution
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Imaging experiment details, scan resolution (i.e. pixel / voxel resolution)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Collection date
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            The time of sampling, either as an instance (single point in time) or interval. In case no exact time is available, the date/time can be right truncated i.e. all of these are valid times: 2008-01-23T19:23:10+00:00; 2008-01-23T19:23:10; 2008-01-23; 2008-01; 2008; Except: 2008-01; 2008 all are ISO8601 compliant.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Broad-scale environmental context
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the broad environment in which sampling occurred e.g. cliff [ENVO:00000087]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Local environmental context
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the local environment in which sampling occurred as a semicolon separated list, e.g. digestive tract environment [ENVO:01001033]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Amount or size of sample collected
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            Amount or size of sample (volume, mass or area) that was collected. Please include units.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Anatomical plane
                                        </td>
                                        <td>
                                            <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                        </td>
                                        <td>
                                            For section data and/or image stacks, please provide details of the anatomical plane (e.g. transverse, sagittal, coronal)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Collected by
                                        </td>
                                        <td>
                                            <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                        </td>
                                        <td>
                                            The name of the person(s) attributed with the collection of the wild specimen, uppercase the surname, e.g. CI HUNTER.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Environmental medium
                                        </td>
                                        <td>
                                            <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                        </td>
                                        <td>
                                            Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the environmental medium of the sample e.g. fecal material [ENVO:00002003]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample collection device or method
                                        </td>
                                        <td>
                                            <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                        </td>
                                        <td>
                                            The method and/or device employed for collecting the sample.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample material processing
                                        </td>
                                        <td>
                                            <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                        </td>
                                        <td>
                                            A brief description of any processing applied to the sample during or after retrieving the sample from environment, or a link to the relevant protocol(s) performed. Where possible please use OBI (<a target="_blank" href="http://obi-ontology.org/">Ontology for Biomedical Investigations</a>) terms e.g. H&E slide staining [OBI:0002124]
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <p aria-hidden="true">* - Requirements are listed as R= Recommended, O= Optional. Note ^ denotes absolutely mandatory fields.</p>
                        </div>
                    </div>
                </div>
        </section>



    </div>
</div>
<script type="text/javascript">

$(document).ready(function () {
    if(location.hash != null && location.hash != ""){
        $('ul li').removeClass('active');
        $('div'+ '.tab-pane').removeClass('active');
        var variableli = location.hash;
        $(location.hash).addClass('active');
        $(variableli.replace('#','#li')).addClass('active');
    }

});

</script>