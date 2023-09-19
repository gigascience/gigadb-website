<?php
$this->pageTitle = 'GigaDB - Submission Guidelines';

//echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/about.html');
?>
<style>
table {
    width: 1138px;
}
th {
    background-color: #D3D3D3;
}
th, td {
  padding: 5px;
  line-height: 2;
}
</style>
<div class="content">
    <div class="container">
        <div class="section page-title-section guides-title-section">
            <div class="page-title">
                <nav aria-label="breadcrumbs">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">Guidelines</li>
                    </ol>
                </nav>
                <h4>GigaDB - Submission Guidelines</h4>
            </div>
        </div>
        <section>

            <section style="margin-bottom: 5px;">
                <div style="display:inline-block;">
                    <ul class="nav nav-tabs nav-border-tabs" style="margin-top: 1px; margin-bottom: 1px">
                            <li class="active"><a href="/site/guide">General Submission Guidelines</a></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    Datasets Checklists&nbsp;<i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" style="margin-top: 5px;">
                                    <li><a href="/site/guidegenomic">Genomic Dataset Checklist</a></li>
                                    <li><a href="/site/guideimaging">Imaging Dataset Checklist</a></li>
                                    <li><a href="/site/guidemetabolomic">Metabolomic and Lipidomic Dataset Checklist</a></li>
                                    <li><a href="/site/guideepigenomic">Epigenomic Dataset Checklist</a></li>
                                    <li><a href="/site/guidemetagenomic">Metagenomic Dataset Checklist</a></li>
                                    <li><a href="/site/guidesoftware">Software Dataset Checklist</a></li>
                                </ul>
                            </li>
                </div>
            </section>
            <section>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="general">
                        <h4 class="page-subtitle">General Submission Guidelines </h4>
                        <div class="subsection">
                            <p>GigaDB is a <a target="_blank" href="https://www.cngb.org/aboutUs.html?i18nlang=en_US">China National GeneBank</a> supported repository used to host data and tools associated with articles in <i>GigaScience</i>. As part of your manuscript submission and in line with the <a target="_blank" href="https://academic.oup.com/gigascience/pages/editorial_policies_and_reporting_standards">Reporting Standards</a> and <a target="_blank" href="http://doi.org/10.25504/fairsharing.prdtva">FAIRsharing guidelines for data deposition and formatting for papers submitted to <i>GigaScience</i></a> we will provide an associated GigaDB dataset to host the data and files required for transparency and reproducibility. GigaDB is an open-access database. As such, all data submitted to GigaDB must be fully consented for public release (for more information about our data policies, please see our <a href="/site/term">Terms of use</a> page).
                            </p>
                        </div>

                        <h4 class="page-subtitle">Workflow</h4>
                        <div class="subsection">
                            <p>The workflow diagram below details a standard submission process:</p>
                            <img src="/images/workflow.png" alt="Workflow"></img>
                            <p>When contacted by curators to process the GigaDB dataset you will be invited to:<br>
                                -	Create a GigaDB user account<br>
                                -	Upload your prepared data files* (if not already public)<br>
                                *- see checklists below<br>
                                -	Supply the appropriate metadata<br>
                                -	Proofread and approve the GigaDB pre-publication dataset page<br>
                            </p>
                        </div>

                        <h4 class="page-subtitle">Required metadata</h4>
                        <div class="subsection">
                            <p>For all datasets the following information will be required. Most of the details will be imported directly from the <i>GigaScience</i> manuscript submission, other details will be requested by the curators.</p>
                            <br>
                            <div id="table_guide_submission" class="scrollbar">
                                <table border="1" style="text-align: center;">
                                    <tr>
                                        <th style="text-align: center; width: 20%">
                                            Item
                                        </th>
                                        <th style="text-align: center; width: 20%">
                                            Imported directly from manuscript (y/n)
                                        </th>
                                        <th style="text-align: center; width: 60%">
                                            Description
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Submitting author
                                        </td>
                                        <td>
                                            y
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
                                            y
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
                                            y
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
                                            y
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
                                            y
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
                                            n
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
                                            n
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
                                            n
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
                                            n
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
                                            n
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
                                            n
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
                                <table border="1" style="text-align: center;">
                                    <tr>
                                        <th style="text-align: center; width: 30%">
                                            Attribute
                                        </th>
                                        <th style="text-align: center; width: 10%">
                                            Requirement <sup>*<sup>
                                        </th>
                                        <th style="text-align: center; width: 60%">
                                            Description
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample name<sup>^</sup>
                                        </td>
                                        <td>
                                            R
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
                                            R
                                        </td>
                                        <td>
                                            Please enter the <a target="_blank" href="http://www.ncbi.nlm.nih.gov/Taxonomy">NCBI Taxonomy ID</a> for the species used in your study. NB this is mandatory for any sequenced samples.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Species name<sup>^</sup>
                                        </td>
                                        <td>
                                            R
                                        </td>
                                        <td>
                                            Please enter the bionomial (Genus species) name for the species of this sample
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Description<sup>^</sup>
                                        </td>
                                        <td>
                                            R
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
                                            R
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
                                            R
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
                                            R
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
                                            R
                                        </td>
                                        <td>
                                            Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the local environment in which sampling occurred as a semicolon separated list, e.g. digestive tract environment [ENVO:01001033]
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <br>
                            <p>* - Requirements are listed as R= Recommended, O= Optional. Note ^ denotes absolutely mandatory fields.</p>
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
                                <table border="1" style="text-align: center;">
                                    <tr>
                                        <th style="text-align: center; width: 20%">
                                            Item
                                        </th>
                                        <th style="text-align: center; width: 10%">
                                            Mandatory (y/n)
                                        </th>
                                        <th style="text-align: center; width: 70%">
                                            Description
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            File name
                                        </td>
                                        <td>
                                            y
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
                                            y
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
                                            y
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
                                            y
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
                                            y
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
                                            n
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
                                            n
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
                            <p><a href="/site/guidegenomic">Genomic Dataset checklists</a></p>
                            <p><a href="/site/guideimaging">Imaging Dataset checklists</a></p>
                            <p><a href="/site/guidemetabolomic">Metabolomic and Lipidomic Dataset checklists</a></p>
                            <p><a href="/site/guideepigenomic">Epigenomic Dataset checklists</a></p>
                            <p><a href="/site/guidemetagenomic">Metagenomic Dataset checklists</a></p>
                            <p><a href="/site/guidesoftware">Software Dataset checklists</a></p>
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
