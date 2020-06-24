<?php
$this->pageTitle = 'GigaDB - Software Dataset checklists';

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
    <div class ="container">
        <section class="page-title-section" style="margin-bottom: 10px;">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a> </li>
                    <li class="active">Guidelines</li>
                </ol>
                <h4>General Submission Guidelines</h4>
            </div>
        </section>
        <section style="margin-bottom: 5px;">
            <div style="display:inline-block;">
                <ul class="nav nav-tabs nav-border-tabs" role="tablist" style="margin-top: 1px; margin-bottom: 1px">
                    <li><a href="/site/guide" style="padding-left: 0px">General Submission Guidelines</a></li>
                    <li class="dropdown active">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Datasets Checklists&nbsp;<i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu" style="margin-top: 5px;">
                            <li><a href="/site/guidegenomic">Genomic Dataset Checklist</a></li>
                            <li><a href="/site/guideimaging">Imaging Dataset Checklist</a></li>
                            <li><a href="/site/guidemetabolomic">Metabolomic and Lipidomic Dataset Checklist</a></li>
                            <li><a href="/site/guideepigenomic">Epigenomic Dataset Checklist</a></li>
                            <li><a href="/site/guidemetagenomic">Metagenomic Dataset Checklist</a></li>
                            <li><a href="/site/guidesoftware">Software Dataset Checklist</a> </li>
                        </ul>
                    </li>
            </div>
        </section>
        <section>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="general">
                        <h4 style="color: #099242; margin-bottom: 10px;">Software Dataset Checklist </h4>
                        <div class="subsection">
                            <h5 style="padding-left: 1px">Minimal requirements</h5>
                            <p>The minimal requirement for a GigaDB dataset associated with a manuscript describing software is that the source-code be made openly available under an <a href="https://web.archive.org/web/20160412003944/https://opensource.org/licenses">Open Source Initiative</a> approved licence. Most authors host their open-source projects in a GitHub repository, and as a standard procedure we would take a snapshot of the GitHub repository at the point of publication as a version of record to ensure the version as published is always available even when the GitHub repositories are updated. Note- All archival GitHub files are labeled with download date and a description suggesting users visit the current GitHub repository for the most recent updates.</p>
                            <p>In addition we encourage submission of your code to <a href="https://codeocean.com/">Code Ocean</a>, a cloud-based computational reproducibility platform. Once your code is published in Code Ocean, they will issue a DOI for it, which should be included in your GigaDB dataset.</p>
                            <p>GigaScience journal expects all new software tools to be registered at <a href="https://scicrunch.org/">SciCrunch.org</a> - a database to register new software applications. You will be assigned an <a href="https://scicrunch.org/resources">RRID</a> which provides a persistent and unique identifier for referencing your research resource, which in turn will facilitate tracking, reproducibility and re-use of your tool, and should be included in the manuscript and dataset.</p>
                            <p>Where authors have demonstrated the utility of software/tools with example data, we would expect those data to be fully open and accessible in a stable international database with permanent IDs (PIDs). If the authors have generated the data themselves/own the data we can host it in GigaDB if required, otherwise links to the PIDs should be included in the dataset. The metadata to accompany example data would be expected to comply with the regular checklists for that particular data type (see other dataset type checklists).</p>
                        </div>
                        <table border="1" style="text-align: center;">
                            <tr>
                            <th style="text-align: center; width: 60%">
                             Item
                            </th>
                            <th style="text-align: center; width: 30%">
                            File format
                            </th>
                            <th style="text-align: center; width: 20%">
                            Check
                            </th>
                            </tr>
                            <tr>
                                <td>
                                    Archive of software code (with OSI license)
                                </td>
                                <td>
                                    zip, gz, tar (archive)
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Input files / raw data
                                </td>
                                <td>
                                    Any open format as appropriate
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Output files / results
                                </td>
                                <td>
                                    Any open format as appropriate
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Intermediary files or data that would be costly or impossible  to reproduce.
                                </td>
                                <td>
                                    Any open format as appropriate
                                </td>
                                <td>

                                </td>
                            </tr>
                        </table>
                        <br>
                        <div>
                            <p>An example of a software dataset is appended below.</p>
                            <p>There are a wide number of resources (some of which are listed below), which may also be used to host software-related code and data. GigaDB encourages authors to make use of these specialist resources and to provide us with a link or DOI, which can be included in your GigaDB record.</p>
                            <p><a href="https://codeocean.com/">Code Ocean</a> - is a cloud-based computational reproducibility platform that provides researchers and developers an easy way to share, discover and run code published in academic journals and conferences.</p>
                            <p><a href="https://www.docker.com/">Docker</a> - a tool designed to make it easier to create, deploy, and run applications by using containers. Containers allow a developer to package up an application with all of the parts it needs, such as libraries and other dependencies, and ship it all out as one package. Please send the link to your Docker repository.</p>
                            <p><a href="https://galaxyproject.org/">Galaxy</a> - makes data-intensive research more accessible, transparent and reproducible by providing a Web-based environment in which users can perform computational analyses and have all of the details automatically tracked for later inspection, publication, or reuse. Please provide a link to your Galaxy resource.</p>
                        </div>
                    </div>
                </div>
        </section>
        <section>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general">
                    <h5 style="padding-left: 1px">Example Software dataset</h5>
                    <div class="subsection">
                        <p>Below is an example software dataset, you can see the final dataset of this example <a href="http://dx.doi.org/10.5524/100753">here</a>.</p>
                    </div>
                    <table border="1" style="text-align: center;">
                        <tr>
                            <th style="text-align: center; width: 12.5%">
                                Item
                            </th>
                            <th style="text-align: center; width: 12.5%">
                                File Name
                            </th>
                            <th style="text-align: center; width: 50%">
                                Description
                            </th>
                            <th style="text-align: center; width: 12.5%">
                                Data Type
                            </th>
                            <th style="text-align: center; width: 12.5%">
                                File Format
                            </th>
                        </tr>
                        <tr>
                            <td>
                                Software Code Archive
                            </td>
                            <td>
                                CandiMeth-master.zip
                            </td>
                            <td>
                                Archival copy of the GitHub repository https://github.com/sjthursby/CandiMeth, downloaded 01-May-2020. CandiMeth allows rapid, quantitative analysis of methylation at user-specified features without the need for coding. Licensed under the GNU-SPL license. Please refer to the GitHub repo for most recent updates.
                            </td>
                            <td>
                                GitHub archive
                            </td>
                            <td>
                                zip
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Input file
                            </td>
                            <td>
                                Supp.Table2.csv
                            </td>
                            <td>
                                List of microRNA (MIR) genes (not analysed in the original paper) used as input here to produce results in Supp.Table 3
                            </td>
                            <td>
                                Tabular Data
                            </td>
                            <td>
                                CSV
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Output file
                            </td>
                            <td>
                                Supp.Table3__mean_beta_D8_Track.csv
                            </td>
                            <td>
                                Full tabular output for the set of miRs in D8 cell line (partial results shown in Fig.2B of associated manuscript)
                            </td>
                            <td>
                                Tabular Data
                            </td>
                            <td>
                                CSV
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Output file
                            </td>
                            <td>
                                Supp.Table3__mean_beta_WT_of_D8.csv
                            </td>
                            <td>
                                Full tabular output for the set of miRs in WT cell line (partial results shown in Fig.2B of associated manuscript)
                            </td>
                            <td>
                                Tabular Data
                            </td>
                            <td>
                                CSV
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Output file
                            </td>
                            <td>
                                Supp.Table5.csv
                            </td>
                            <td>
                                An example ChAMP output from the CandiMeth History.
                            </td>
                            <td>
                                Tabular Data
                            </td>
                            <td>
                                CSV
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Other
                            </td>
                            <td>
                                Supp.Table1.csv
                            </td>
                            <td>
                                Tabular results of NCBI Gene Expression Omnibus (GEO) GSE90012, used as example data for Figures in manuscript.
                            </td>
                            <td>
                                Tabular Data
                            </td>
                            <td>
                                CSV
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    if (location.hash != null && location.hash != "") {
        $('ul li').removeClass('active');
        $('div' + '.tab-pane').removeClass('active');
        var variableli = location.hash;
        $(location.hash).addClass('active');
        $(variableli.replace('#', '#li')).addClass('active');
    }
})
</script>