<?php
$this->pageTitle = 'GigaDB - Submission Guidelines';

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
                <h1 class="h4">GigaDB - Submission Guidelines</h1>
            </div>
        </section>
        <section>

        <?php
            $this->widget('application.components.GuideNavigation');
        ?>
            <section>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="general">
                        <h2 class="page-subtitle h4">General Submission Guidelines </h2>
                        <div class="subsection">
                            <p>GigaDB is a <a target="_blank" href="https://www.cngb.org/aboutUs.html?i18nlang=en_US">China National GeneBank</a> supported repository used to host data and tools associated with articles in <i>GigaScience</i>. As part of your manuscript submission and in line with the <a target="_blank" href="https://academic.oup.com/gigascience/pages/editorial_policies_and_reporting_standards">Reporting Standards</a> and <a target="_blank" href="http://doi.org/10.25504/fairsharing.prdtva">FAIRsharing guidelines for data deposition and formatting for papers submitted to <i>GigaScience</i></a> we will provide an associated GigaDB dataset to host the data and files required for transparency and reproducibility. GigaDB is an open-access database. As such, all data submitted to GigaDB must be fully consented for public release (for more information about our data policies, please see our <a href="/site/term">Terms of use</a> page).
                            </p>
                        </div>

                        <h2 class="page-subtitle h4">Workflow</h2>
                        <div class="subsection">
                            <p>The workflow diagram below details a standard submission process:</p>
                            <figure role="group">
                                <img src="/images/workflow.png" alt="Workflow diagram outlining the manuscript and data submission process for GigaScience, from initial submission to publication, described in detail below"></img>
                                <figcaption>
                                    <h3 class="h4">Workflow overview</h3>
                                    <p>This workflow diagram outlines the manuscript and data submission process for GigaScience. It covers the steps from initial manuscript submission to the eventual publication of the dataset.</p>
                                    <h3 class="h4">Workflow Steps</h3>
                                    <ol class="number-spacing">
                                        <li>Authors submit manuscript</li>
                                        <li>Is it in scope for GigaScience?</li>
                                        <li>Decision: If no, reject. If yes, continue.</li>
                                        <li>Does manuscript include data?</li>
                                        <li>Decision: If no, no further GigaDB involvement. If yes, continue.</li>
                                        <li>Is data available to peer reviewers?</li>
                                        <li>Decision: If no, provide authors with private FTP login, then authors upload all data files to GigaDB private FTP area and continue. If yes, continue.</li>
                                        <li>Does manuscript pass review?</li>
                                        <li>Decision: If no, either reject or author makes revisions to manuscript and/or data in FTP server, and continue. If yes, continue.</li>
                                        <li>Is all data available?</li>
                                        <li>Decision: If no, gather all required data. If yes, continue.</li>
                                        <li>Is all metadata available?</li>
                                        <li>Decision: If no, gather all required metadata. If yes, continue.</li>
                                        <li>Curator uploads metadata to GigaDB</li>
                                        <li>Did authors confirm dataset page?</li>
                                        <li>Decision: If no, authors liaise with curators to ensure dataset page is complete and correct, then again curators upload metadata to GigaDB and generate dataset page. If yes, publish dataset.</li>
                                    </ol>
                                </figcaption>
                            </figure>
                            <p>When contacted by curators to process the GigaDB dataset you will be invited to:</p>
                            <ul class="content-text">
                                <li>Create a GigaDB user account</li>
                                <li>Upload your prepared data files if not already public (see checklists below)</li>
                                <li>Supply the appropriate metadata</li>
                                <li>Proofread and approve the GigaDB pre-publication dataset page</li>
                            </ul>
                        </div>

                        <h2 class="page-subtitle h4">Required metadata</h2>
                        <div class="subsection">
                            <p>For all datasets the following information will be required. Most of the details will be imported directly from the <i>GigaScience</i> manuscript submission, other details will be requested by the curators.</p>
                            <br>
                            <div id="table_guide_submission" class="scrollbar">
                                <table border="1" class="guide-table">
                                    <thead>
                                        <tr>
                                            <th class="col-20">
                                                Item
                                            </th>
                                            <th class="col-20">
                                                Imported directly from manuscript <span aria-hidden="true">(y/n)</span>
                                            </th>
                                            <th class="col-60">
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>
                                            Submitting author
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            First Name, Last Name, Email, Institution/Company, ORCID.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Author list
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            First Name, Last Name, ORCID
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Dataset title
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            Manuscript title prefixed with “Supporting data for”
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Dataset description
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            Manuscript abstract
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Funding information
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            Funding body, program, award ID and awardee
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Dataset type
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            Selected from <a href="http://gigadb.org/site/help#vocabulary">controlled vocabulary</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Keywords
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            Please list upto 5 keywords, separated by semicolons. All keywords are converted to lowercase.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Additional information links
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            Any URLs to FTP servers or webpages associated with your dataset as semicolon separated lists
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Thumbnail image
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            An appropriate image to represent the dataset. Title, Credit, Source and License (CC0 or public domain only) details will be required.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            External accessions
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            If any data that you wish to publish in GigaDB has been submitted to to an external resource such as EBI or NCBI, please provide the accession(s) as a semicolon separated list in the format 'SRA:SRPXXXXXX' ; BioProject:PRJNAXXXXXX'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Protocols.io link
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            Where authors provide their methods via <a target="_blank" href="https://protocols.io/">protocols.io</a> we can embed these in GigaDB datasets, please provide the published widget URL or DOI
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <p>For datasets that include biological sample-related data we would expect the sample metadata to be included in the GigaDB dataset. We understand that the level of sample metadata  made available is often limited by sample collection restrictions, but authors should make every effort to provide as comprehensive metadata about samples as is possible. </p>
                            <p>Below is the list of attributes commonly associated with any biological sample. In addition to these we strongly encourage the inclusion of ALL appropriate attributes, and for specific types of data there are a number of standards that we encourage our users to adopt. Please see the Dataset Type specific checklists for recommendations.</p>
                            <br>
                            <div id="table_guide_attribute" class="scrollbar">
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
                                            Sample name<sup aria-hidden="true">^</sup><span class="sr-only"> absolutely mandatory field</span>
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
                                            Species name<sup aria-hidden="true">^</sup><span class="sr-only"> absolutely mandatory field</span>
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
                                            Description<sup aria-hidden="true">^</sup><span class="sr-only"> absolutely mandatory field</span>
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

                                </table>
                            </div>
                            <br>
                            <p aria-hidden="true">* - Requirements are listed as R= Recommended, O= Optional. Note ^ denotes absolutely mandatory fields.</p>
                            <br>
                            <br>
                            <p>For all datasets we expect all data to be available from a stable public open access source and where appropriate we will link directly to external sources rather than duplicate data files. </p>
                            <p>However if there is no established suitable repository for a particular file/data-type we will host it on our servers.</p>
                            <p>Where possible, all files should be machine readable without the need for proprietary software (e.g. No PDF, Excel or Word documents).</p>
                            <br>
                            <p>For all files we host, we expect the following details:</p>
                            <br>
                            <br>
                            <div id="table_guide_details" class="scrollbar">
                                <table border="1" class="guide-table">
                                    <thead>
                                        <tr>
                                            <th class="col-20">
                                                Item
                                            </th>
                                            <th class="col-10">
                                                Mandatory <span aria-hidden="true">(y/n)</span>
                                            </th>
                                            <th class="col-70">
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>
                                            File name
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            The exact name of the file including relative file path. Ideally it should be unique within the dataset. Filenames should only include the following characters a-z,A-Z,0-9,_,-,+,. Filenames should not include spaces, we recommend using the underscore (_) in place of spaces.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Description
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            Short human readable description of the file and its contents
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Data type
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            The type of data in the file, selected from a <a href="http://gigadb.org/site/help#vocabulary">controlled vocabulary</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Format
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            Most common formats are automatically assigned by file extension, but can be updated manually if required.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            MD5 #value
                                        </td>
                                        <td>
                                            <span aria-hidden="true">y</span><span class="sr-only">yes</span>
                                        </td>
                                        <td>
                                            These are calculated automatically on our server and added to the database on submitters behalf.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            File-Sample association
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            If the sample is derived from a particular sample (in GigaDB) an explicit link can be made between sample(s) and file(s) by adding the Sample ID to the file attributes.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Additional attributes
                                        </td>
                                        <td>
                                            <span aria-hidden="true">n</span><span class="sr-only">no</span>
                                        </td>
                                        <td>
                                            If files have metadata that should be included with them they can be added as attributes, the most common example is Licenses
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <br>
                            <br>
                            <p>Due to the nature of scientific publications the files that need to be provided are usually unique to the individual manuscript, however there are some commonalities that we have attempted to capture in a set of minimal checklists for the most common dataset types that we receive. These lists are to be treated as a guide only and there may be changes to them over time. </p>
                            <p>Please see the Dataset Type specific checklists for recommendations:</p>
                            <ul class="content-text">
                                <li><a href="/site/guidegenomic">Genomic Dataset checklists</a></li>
                                <li><a href="/site/guideimaging">Imaging Dataset checklists</a></li>
                                <li><a href="/site/guidemetabolomic">Metabolomic and Lipidomic Dataset checklists</a></li>
                                <li><a href="/site/guideepigenomic">Epigenomic Dataset checklists</a></li>
                                <li><a href="/site/guidemetagenomic">Metagenomic Dataset checklists</a></li>
                                <li><a href="/site/guidesoftware">Software Dataset checklists</a></li>
                            </ul>
                        </div>
                        <p>If you have any questions, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a>.</p>

                    </div>


                </div>
            </section>






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
