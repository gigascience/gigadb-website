<?php
$this->pageTitle = 'GigaDB - Metabolomic Dataset checklists';

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
                <h1 class="h4">Metabolomic and Lipidomic Dataset checklists</h1>
            </div>
        </section>
        <?php
        $this->widget('application.components.GuideNavigation');
        ?>
        <section>
            <div class="tab-content">
                <div class="tab-pane active">
                    <h2 class="h4 page-subtitle">Metabolomic and Lipidomic Dataset Checklist </h2>
                    <div class="subsection">
                        <p>All Metabolomics and Lipomics data must be deposited in a public repository that is part of the <a target="_blank" href="http://www.metabolomexchange.org">MetabolomeXchange</a> (we recommend <a target="_blank" href="https://www.ebi.ac.uk/metabolights">Metabolights</a> database at <a target="_blank" href="http://www.ebi.ac.uk/">EBI</a>), before you submit to <i>GigaScience</i> or GigaDB. </p>
                        <p>In cases where sample metadata* are not fully consented for public release, e.g. patient information, you must first submit the non-public data/metadata to the Genome-Phenome Archive (<a href="http://ega-archive.org/">EGA</a>) to enable controlled access.</p>
                        <p>*Note- we do expect the anonymised data to be made publicly available via Metabolights as this is not traceable back to individuals.</p>
                        <p>For datasets with metabolomics data we would expect all relevant data to be made available in Metabolights, and therefore GigaDB would only host other data files that are created/used in the analysis/project (if any). Where there are no additional files or data to be hosted in GigaDB it is possible we will not generate a dataset in those cases.</p>
                        <p>The basic outline of data that is to be included in a Metabolomics dataset are summarised below</p>
                        <p>(Information taken from https://www.metabolomicsworkbench.org/data/datasharing.php 18-Dec-2018):</p>
                        <div id='table_metabolomic_data' class="scrollbar">
                            <table border="1" class="guide-table">
                                <thead>
                                    <tr>
                                        <th class="col-30">
                                            Item
                                        </th>
                                        <th class="col-70">
                                            Description
                                        </th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                        Raw metabolomics data
                                    </td>
                                    <td>
                                        The spectrometric, spectrographic and chromatographic data as created by the instrument software.
                                        A description of the platform and vendor software version used to generate and analyze raw data files.
                                        An open exchange format submission is encouraged, as long as the raw data and exchange format contain the same level of information. File names should use identifiers that can be linked to the final result matrix of an experiment.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Analytical metadata
                                    </td>
                                    <td>
                                        Details on how samples were obtained at the biological or clinical laboratory
                                        Sample storage conditions.
                                        Sample preparation and extraction protocols, analytical methods including the instrument and analytical methods with enough detail to allow for an independent replication of the experiment.
                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        Biological and clinical sample metadata
                                    </td>
                                    <td>
                                        The taxonomic definition of species, organs, cell types or cell line information that was used in in-vivo and in-vitro experiments. All clinical metadata should adhere to HIPPA regulations ensuring patient confidentiality
                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        Final result matrix
                                    </td>
                                    <td>
                                        Metabolites x sample ID matrix, with quantitative or semi-quantitative metabolite values and appropriate substance identifiers, including a list of all known and, where appropriate, unknown metabolites for each given experimental sample. The results matrix must contain the units of measurement (pmol/ml, ng/sample, MS peak height, MS peak area, etc).
                                    </td>
                            </table>
                        </div>
                        <br>
                        <br>
                        <p>Where we do host a dataset for metabolomics data, we may include the sample metadata in GigaDB to further assist discovery and reuse of these data. Metabolomic sample metadata is expected to be at least as comprehensive as that of transcriptomic datasets, below are some of the more common sample metadata attributes that we would expect to be made available for most metabolomics samples. The complete list of pre-defined sample attributes are available in the <a href="/">GigaDB home page</a>, and it is possible to include bespoke attributes by prior communication with us.</p>
                        <br>
                        <br>
                        <div id='table_metabolomic_meta' class="scrollbar">
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
                                        Analyte type
                                    </td>
                                    <td>
                                        <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                    </td>
                                    <td>
                                        The analyte being assayed from the sample (e.g. DNA for sequencing, peptide for MS, etc.)
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
                                        Please provide details of the type of tissue sampled, where possible values should be from an ontology such as <a target="_blank" href="http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=UBERON">UBERON</a> e.g."lung [UBERON:0002048]"
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
                                        Cell line
                                    </td>
                                    <td>
                                        <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                    </td>
                                    <td>
                                        Please provide the cell line name and supplier of the immortalised cell line used in your experiments, e.g. "HEK-293:Addex Bio"
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
                                        Sample source
                                    </td>
                                    <td>
                                        <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                    </td>
                                    <td>
                                        Additional information about where the sample originated from, e.g. the particular zoo/avery/lab/company name
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
                                        Environmental medium
                                    </td>
                                    <td>
                                        <span aria-hidden="true">R</span><span class="sr-only">recommended</span>
                                    </td>
                                    <td>
                                        Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the environmental medium of the sample e.g. fecal material [ENVO:00002003]
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
                                        Body mass index
                                    </td>
                                    <td>
                                        <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                    </td>
                                    <td>
                                        Body mass index, calculated as weight/(height)squared
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Diet
                                    </td>
                                    <td>
                                        <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                    </td>
                                    <td>
                                        Type of diet depending on the host, for animals omnivore, herbivore etc., for humans high-fat, meditteranean etc.; can include multiple diet types
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Medication
                                    </td>
                                    <td>
                                        <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                    </td>
                                    <td>
                                        List all medication currently being taken by subject, where possible use the IHMC medication codes
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Blood glucose
                                    </td>
                                    <td>
                                        <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                    </td>
                                    <td>
                                        Fasting blood glucose measurement
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Food name
                                    </td>
                                    <td>
                                        <span aria-hidden="true">O</span><span class="sr-only">optional</span>
                                    </td>
                                    <td>
                                        The name of the feed used to grow/maintain the host/subject, (usually laboratory reared animals). Often used in conjunction with Food supplier attribute.
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
    $(document).ready(function() {
        if (location.hash != null && location.hash != "") {
            $('ul li').removeClass('active');
            $('div' + '.tab-pane').removeClass('active');
            var variableli = location.hash;
            $(location.hash).addClass('active');
            $(variableli.replace('#', '#li')).addClass('active');
        }

    });
</script>