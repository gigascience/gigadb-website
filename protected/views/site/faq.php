<?php
$this->pageTitle = 'GigaDB - FAQ';

//echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/about.html');
?>

<div class="clear"></div>
<div class="content">
    <div class="container">
      <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => 'FAQ',
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['isActive' => true, 'label' => 'FAQ'],
          ]
        ]);
        ?>
        <section>
          <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading01">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel01" aria-expanded="false" aria-controls="panel01">
                                What is <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel01" role="region" aria-labelledby="heading01" class="panel-collapse collapse" data-parent="#accordion">
                        <div class="panel-body">
                            <p><em>GigaDB</em> is the home for all data/files/tools/software associated with GigaScience manuscripts. <em>GigaDB</em> curators will ensure the information is complete and appropriately formatted, before cataloging and publishing. Submission of data to <em>GigaDB</em> complements but does not serve as a replacement for community approved public repositories, supporting data and source code should still be made publicly available in a suitable public repository. <em>GigaDB</em> can link any and all publicly deposited data together with additional files/tools that do not have a natural home in any other public repository. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading01-1">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel01-1" aria-expanded="false" aria-controls="panel01-1">
                            What are the benefits of using <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel01-1" role="region" aria-labelledby="heading01-1" class="panel-collapse collapse" data-parent="#accordion">
                        <div class="panel-body">
                            <p>Expert curation by experienced BioCurators ensures that your data can be easily found and reused. All the data associated with a unit of work can be found in a single place, either hosted by <em>GigaDB</em> or with stable links out to other reputable repositories. We ensure the Open sharing policy of GigaScience Press (and other publishers) are met. You receive increased exposure of your research to the community and ensure that other researchers can find and cite your work, so that you get the credit you deserve.</p>
                            <p>The inclusion of extensive metadata about your data increases discovervisibility, in-turn increasing the visibility of your associated journal article.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading02">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel02" aria-expanded="false" aria-controls="panel02">
                                What journals are integrated with <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel02" aria-labelledby="heading02" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Currently, GigaDB works very closely with GigaScience and GigaByte journals. If you have data relating to a publication in another journal, you may wish to consider splitting the paper into a research paper and a data note; the latter can be submitted to GigaByte then we can host your data in GigaDB which can be referenced in any research article using those data.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading03">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel03" aria-expanded="false" aria-controls="panel03">
                                Why use <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel03" aria-labelledby="heading03" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaScience is committed to enabling reproducible science, to do this, readers need to be able to easily find and get hold off all the underlying data, methods, workflows, software and anything else that was used in the research. In the past authors of research articles have made (justified) claims that there is no way of making all their data available, now <em>GigaDB</em> has filled that gap. <em>GigaDB</em> complements but does not serve as a replacement for community approved public repositories, and can link any and all publicly deposited data together with additional files/tools that do not have a natural home in any other public repository. Any and all data/files required for reproducibility of GigaScience manuscripts should be either hosted in, or linked to from, <em>GigaDB</em>.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading04">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel04" aria-expanded="false" aria-controls="panel04">
                                What kinds of data does <em>GigaDB</em> accept?
                            </button>
                        </h2>
                    </div>
                    <div id="panel04" aria-labelledby="heading04" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Anything related to a GigaScience article that does NOT already have a relevant public repository (e.g. sequence data should still be deposited in the INSDC archives and/or the SRA).</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading05">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel05" aria-expanded="false" aria-controls="panel05">
                                My research is on human subjects. Can I archive my data in <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel05" aria-labelledby="heading05" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>As long as the data is fully consented and legally and ethically approved for public release, we encourage complete disclosure including a blank copy of the consent form signed where possible. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading06">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel06" aria-expanded="false" aria-controls="panel06">
                                In what file format(s) should I submit my data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel06" aria-labelledby="heading06" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There are many specific formats depending on the date types, but the rule of thumb is to use non-propitiatory formats and where possible follow the standard for the relevant field, our curators will be on hand to help with any specific questions on this matter.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading_file-name-convention">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel_file-name-convention" aria-expanded="false" aria-controls="panel_file-name-convention">
                                What is the file name convention for GigaDB?
                            </button>
                        </h2>
                    </div>
                    <div id="panel_file-name-convention" aria-labelledby="heading_file-name-convention" class="panel-collapse collapse">
                        <div class="panel-body">

                            <p>The file name includes the full name of the file including the relative file path, e.g. <code>directory_name/file_name.ext</code></p>

                            <ul class="content-text">
                                <li>Ideally the filename should be meaningful in some way, if appropriate it may contain reference to a particular point within the associated manuscript, but usually that reference would be expected in the file descriptions. (e.g. <code>gene-expression-fig1.csv</code> would be better than <code>Fig1.csv</code>)</li>

                                <li>Full file-path names must be unique within the dataset.</li>

                                <li>Filenames should only include the following characters <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code>, <code>-</code>, <code>+</code>, <code>.</code></li>

                                <li>Filenames should not include spaces, we recommend using the underscore (<code>_</code>) in place of spaces.</li>

                                <li>All files should be machine-readable (e.g. No PDF, Excel or Word documents)</li>

                                <li>The file extension should be relevant to the format of the file, e.g. csv tabular data should have the file extension <code>.csv</code>.</li>
                            </ul>

                            <p>See the <a href="/site/help#vocabulary">vocabulary</a> page for a list of file types and regular file extensions.</p>

                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading_expectation-file-compression">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel_expectation-file-compression" aria-expanded="false" aria-controls="panel_expectation-file-compression">
                                What are the expectations on file compression?
                            </button>
                        </h2>
                    </div>
                    <div id="panel_expectation-file-compression" aria-labelledby="heading_expectation-file-compression" class="panel-collapse collapse">
                        <div class="panel-body">

                            <p>It is normal to compress large files to reduce transfer times, and we encourage the use of gzip or bzip2 for this. Zip can be acceptable, but for preference if possible use gzip or bzip2 for individual files. Where a file has been compressed there should be two file extensions consecutively e.g. <code>filename.fasta.gz</code>.</p>

                            <p>In some instances it is appropriate to archive multiple files into a single archive file, for this we strongly recommend the use of tar with or without the addition of gzip. Similarly the file extension will reflect this, e.g. <code>directoryName.tar</code> or <code>directoryName.tar.gz</code></p>

                            <p>For tar files we allow for longer descriptions as there could be more required due to the potential for more varied content. You MUST avoid the use of carriage returns within the description.</p>

                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading07">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel07" aria-expanded="false" aria-controls="panel07">
                                When should I submit my data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel07" aria-labelledby="heading07" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>At the same time or soon after submission of the manuscript. GigaScience reviewers will be looking to see if the underlying data is available and appropriate, so they will need access to it. This can be via your own private servers if you prefer, but we offer a secure staging area to host data under review/pre-release.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading08">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel08" aria-expanded="false" aria-controls="panel08">
                                How can I modify files I have submitted to <em>GigaDB</em> while my article is in review?
                            </button>
                        </h2>
                    </div>
                    <div id="panel08" aria-labelledby="heading08" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>While the dataset is "pre-release" status, any files can be replaced by over-writing the original file with the newly modified one. After publishing the data, no over-writing will be permissible, only the addition of new files, all published files will remain available (unless there is a very good reason to remove them). Versioning is still possible for updates and major changes to the files if they need to be changed post-publication. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading09">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel09" aria-expanded="false" aria-controls="panel09">
                                What should I prepare before submission?
                            </button>
                        </h2>
                    </div>
                    <div id="panel09" aria-labelledby="heading09" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Organise your data into logical directories/folders, name files consistently and without using spaces or special characters. Re-read you methods section to check that everything mentioned there is available, either by links to public repositories or as files you have organised to submit to <em>GigaDB</em>.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading10">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel10" aria-expanded="false" aria-controls="panel10">
                                How can I make my data submission as accessible and reusable as possible?
                            </button>
                        </h2>
                    </div>
                    <div id="panel10" aria-labelledby="heading10" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>By ensuring all files are in non-proprietary formats that anyone can use without the need for expensive software.By making sure data tables are in tables not PDF.By using a CC0 waiver or other suitable public domain licenses for datasets. By using OSI (Open Source Initiative) licenses for software, and linking to versions in code repositories for updates and forking.By including as much metadata about the samples/specimens/files/methods etc... as possible.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading11">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel11" aria-expanded="false" aria-controls="panel11">
                                How do I submit data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel11" aria-labelledby="heading11" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>All data submissions should be approved before being started, please contact <a href="mailto:editorial@gigasciencejournal.com">editorial@gigasciencejournal.com</a> to discuss your article and associated data with our editors.</p>
                            <p>Once approved, the curation team will liaise with you and the editorial team to import relevant information from <em>GigaScience</em> or <em>GigaByte</em> submission systems into GigaDB, and you will be asked to complete the submission of the dataset using the online wizard.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading12">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel12" aria-expanded="false" aria-controls="panel12">
                                How do I write a ReadMe file?
                            </button>
                        </h2>
                    </div>
                    <div id="panel12" aria-labelledby="heading12" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The readme file is an important part of any dataset and as such we will generate it automatically from the information entered into the database. If you are asked to upload data files using FTP a curator may also request you upload a simple readme file listing each of files uploaded with 1 line descriptions of each (these will be used by the curator to add the descriptions to the database).</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading13">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel13" aria-expanded="false" aria-controls="panel13">
                                How do I cite the data in my manuscript?
                            </button>
                        </h2>
                    </div>
                    <div id="panel13" aria-labelledby="heading13" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p><i>GigaScience</i> supports and has signed the <a href="https://www.force11.org/group/joint-declaration-data-citation-principles-final" target="_blank">FORCE 11 Joint Declaration of the Data Citation Principles</a>, feeling strongly that data should be accorded the same importance in the scholarly record as citations of publications. <em>GigaDB</em> datasets can and should be cited in the same manner as any other reference, although the format is journal specific based on their instructions. Following DCC and DataCite guildelines, in GigaScience journal the citation within the references section will be of the form:Author List; publication year: "Dataset title", GigaScience Database. DOI. Example.Peter E Larsen; Yang Dai; (2015): Supporting materials for "Metabolome of Human gut microbiome is predictive of host dysbiosis".; GigaScience Database. http://dx.doi.org/10.5524/100163</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading14">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel14" aria-expanded="false" aria-controls="panel14">
                                Are there any problems with publishing my final research paper AFTER publishing the data in GigaScience?
                            </button>
                        </h2>
                    </div>
                    <div id="panel14" aria-labelledby="heading14" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There shouldn't be, and a major rationale for data publishing is to incentivise earlier release of data in this manner. It is commonly understood throughout the publishing community that publishing data (as a Data Note or in a public archive) is a good thing to be encouraged, and as such, there are no penalties to then subsequently publishing research based on those data. <i>GigaScience</i> has published plenty of data notes and released data sets prior to the analysis papers being published, some examples are: </p>
                            <ol>
                                <li>3,000 Rice Genomes Project (13.4 Tb data).</li>
                                <li>Polar Bear genome - dataset released in <em>GigaDB</em> nearly 3 years before the analysis paper was published in <i>Cell</i>.</li>
                                <li>Deadly 2011 outbreak <a href="http://dx.doi.org/10.5524/100001" target="_blank"><i>E. Coli</i> genome</a> that lead to over 50 deaths in Germany (and eventually published in <i>NEJM</i>).</li>
                            </ol>
                            <p>Our Polar bear genome data was released nearly three years before any official publication came out from the project, and despite being used by at least 5 other studies, the analysis paper made the cover of <i>Cell</i> (see <a href="http://blogs.biomedcentral.com/gigablog/2014/05/14/the-latest-weapon-in-publishing-data-the-polar-bear/" target="_blank">the blog</a> for more). </p>
                            <p>Journals do not consider the publication of a dataset with a DOI and associated protocol information as a 'prior publication' that would preclude subsequent publication of new results obtained from such a dataset. F1000 Research did a useful survey to confirm this with a number of publishers (see: <a href="http://f1000research.com/data-policies" target="_blank"> F1000 policy</a>), and this is only going to become increasingly observed and accepted as most of the publishers are now promoting their own Data Journals.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading15">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel15" aria-expanded="false" aria-controls="panel15">
                                Do I have the option to embargo release of my data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel15" aria-labelledby="heading15" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>As early release as possible is encouraged, although the standard protocol we follow is to maintain data as private to the peer reviewers only until the associated manuscript has been formally accepted, at which point the dataset is released, this is usually several days prior to the manuscript publication due to production times of the BMC publishing system. While we cannot foresee any reason why datasets should be embargoed for extended periods we can discuss this further on a case-by-case basis.If you have major concerns about someone else publishing on your data before you, we can add a Fair Use policy statement on the <em>GigaDB</em> dataset page which looks like this:</p>
                            <img alt="policy" src="../images/fair_use2.gif">
                            <p>These data are made available pre-publication under the recommendations of the <a href="https://www.genome.gov/10506537" target="_blank">Fort Lauderdale</a>/<a href="http://www.nature.com/nature/journal/v461/n7261/full/461168a.html">Toronto</a> meetings. Please respect the rights of the data producers to publish their whole dataset analysis first. The data is being made available so that the research community can make use of them for more focused studies without having to wait for publication of the whole dataset analysis paper. If you wish to perform analyses on this complete dataset, please contact the authors directly so that you can work in collaboration rather than in competition.</p>
                            <p><b>This dataset fair use agreement is in place until </b><b><i>&lt;author can specify a data up to 12 months away&gt;</i></b></p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading16">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel16" aria-expanded="false" aria-controls="panel16">
                                Is there a cost for the service?
                            </button>
                        </h2>
                    </div>
                    <div id="panel16" aria-labelledby="heading16" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There are currently no separate Data Publishing Charges (<abbr>DPCs</abbr>) for submission of data to <em>GigaDB</em>, as we currently do not accept data that is not accompanied by a <em>GigaScience Press</em> manuscript (either <em>GigaScience</em> or <em>GigaByte</em>). All <abbr title="Data Publishing Charges">DPCs</abbr> for <em>GigaScience Press</em> manuscripts are covered by the Article Publishing Charges (<abbr>APCs</abbr>) of that manuscript (up to a terabyte automatically included, but contact us if you need more). For <abbr title="Article Publishing Charges">APCs</abbr> of <em>GigaScience</em> manuscripts please see in <a href="https://academic.oup.com/gigascience/pages/charges_licensing_and_self_archiving" target="_blank"><em>GigaScience</em> journal pricing (opens in a new window)</a>. For <abbr title="Article Publishing Charges">APCs</abbr> of <em>GigaByte</em> manuscripts please see in <a href="https://gigabytejournal.com/faq" target="_blank"><em>GigaByte</em> journal FAQs (opens in a new window)</a>.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading17">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel17" aria-expanded="false" aria-controls="panel17">
                                Do I have to pay to download or use the data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel17" aria-labelledby="heading17" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>No. All data provided by <em>GigaDB</em> is free to download and use. On occasion when datasets are very large and internet connections are slow, some user may request data to be sent by hard disk, <em>GigaDB</em> cannot bare the cost of this but we will assist in the copy of the data onto the disks and help arrange shipment, but the user will be required to cover the cost of the disks and shipment. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading18">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel18" aria-expanded="false" aria-controls="panel18">
                              How do I download a large dataset with my slow internet connection?
                            </button>
                        </h2>
                    </div>
                    <div id="panel18" aria-labelledby="heading18" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There are 2 ways to download data from <em>GigaDB</em>:</p>
                            <ol>
                                <li>FTP. This is the "normal" method, click the download button on any dataset page and this is how your data will be sent.</li>
                                <li>Hard drive shipment. On occasion when datasets are very large and internet connections are slow, some user may request data to be sent by hard disk, <em>GigaDB</em> cannot bare the cost of this but we will assist in the copy of the data onto the disks and help arrange shipment, but the user will be required to cover the cost of the disks and shipment.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading19">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel19" aria-expanded="false" aria-controls="panel19">
                                How do I cite data from <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel19" aria-labelledby="heading19" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p><i>GigaScience</i> supports and has signed the <a href="https://www.force11.org/group/joint-declaration-data-citation-principles-final" target="_blank">FORCE 11 Joint Declaration of the Data Citation Principles</a>, feeling strongly that data should be accorded the same importance in the scholarly record as citations of publications. <em>GigaDB</em> datasets can and should be cited in the same manner as any other reference, although the format is journal specific based on their instructions. Following DCC and DataCite guildelines, in GigaScience journal the citation within the references section will be of the form:Author List; publication year: "Dataset title", GigaScience Database. DOI. Example.Peter E Larsen; Yang Dai; (2015): Supporting materials for "Metabolome of Human gut microbiome is predictive of host dysbiosis".; GigaScience Database. http://dx.doi.org/10.5524/100163</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading20">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel20" aria-expanded="false" aria-controls="panel20">
                                How do I download information to my citation management software?
                            </button>
                        </h2>
                    </div>
                    <div id="panel20" aria-labelledby="heading20" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>On each dataset page there are 3 buttons after the authors names, "RIS", "BIBTEX" and "TEXT" you may use these to download the citation of the dataset in those formats. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading21">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel21" aria-expanded="false" aria-controls="panel21">
                                What is a dataset?
                            </button>
                        </h2>
                    </div>
                    <div id="panel21" aria-labelledby="heading21" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The term dataset in <em>GigaDB</em> refers to a collection of related works, including but not limited to; files, software, workflows, experiments, data, metadata and results. Each dataset has its own webpage which has a DOI (digital object identifier). These datasets are permanent and citable records of research output designed to allow for a modernization of the classical publishing framework while maintaining the familiarity of citations and metrics thereof.While uncommon, it is possible for a dataset to be made-up of several other datasets in a nested fashion, for example the Avian phylogenomics project data dataset (<a href="http://dx.doi.org/10.5524/101000">http://dx.doi.org/10.5524/101000</a>) is a compilation of 48 other datasets, some of those were published before and some at the same time. This allows the original authors to cite just one dataset to cover them all, but also allows future users to cite individual datasets if they require. We will discuss the merits of such procedures on a case-by-case basis with the submitter.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading_whats-included-in-a-dataset">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel_whats-included-in-a-dataset" aria-expanded="false" aria-controls="panel_whats-included-in-a-dataset">
                                What is included in a dataset?
                            </button>
                        </h2>
                    </div>
                    <div id="panel_whats-included-in-a-dataset" aria-labelledby="heading_whats-included-in-a-dataset" class="panel-collapse collapse">
                        <div class="panel-body">

                            <p>A dataset is a collection of files and/or links to externally hosted items, that together are associated with a particular "unit-of-work", usually a manuscript. Since the exact details of which objects are required to make up a dataset is highly dependent on the "unit-of-work" to which they relate, we can only provide generic guidelines, but our curators are always on hand for specific queries that you may have.</p>

                            <p>We would expect to see all data and scripts used in the unit-of-work. This should be sufficient to enable full reproducibility and transparency of the unit-of-work in conjunction with the published methods (either in the manuscript or in <a href="https://www.protocols.io/">protocols.io</a>) together with openly available software tools.</p>

                            <p>In addition, the file metadata will always include md5sum values to enable confirmation of file integrity after download, as well as a brief (upto 200 chars) description of each file.</p>

                            <p>We have prepared submission guidelines for the more common dataset types <a href="/site/guide">here</a>.</p>

                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading22">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel22" aria-expanded="false" aria-controls="panel22">
                                Does my journal work with <em>GigaDB</em> and how?
                            </button>
                        </h2>
                    </div>
                    <div id="panel22" aria-labelledby="heading22" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>While we have no formal agreements with any particular journals, we are happy to work with other journals to ensure timely and coherent joint publications, please discuss with the editors (<a href="mailto:editorial@gigasciencejournal.com">editorial@gigasciencejournal.com</a>).</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading23">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel23" aria-expanded="false" aria-controls="panel23">
                                What is a <em>GigaDB</em> DOI?
                            </button>
                        </h2>
                    </div>
                    <div id="panel23" aria-labelledby="heading23" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>A Digial Object Identifier (DOI) is a stable, citable link to an electronic resource. A <em>GigaDB</em> DOI is a stable and citable link to a dataset hosted by <em>GigaDB</em>. </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading24">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel24" aria-expanded="false" aria-controls="panel24">
                                Why does <em>GigaDB</em> use Creative Commons Zero?
                            </button>
                        </h2>
                    </div>
                    <div id="panel24" aria-labelledby="heading24" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>It is widely recognized that publicly funded research data should be made publicly available for free to be used by anyone. The Creative Commons Zero (CC0) waiver provides the explicit statement of that fact, and it is transparent to all that the data hosted by <em>GigaDB</em> are all freely available for any use case. CC0 is thought to be the most appropriate method for dedicating data to the public domain, but for more on the rationale and practicalities <a href="http://www.biomedcentral.com/1756-0500/5/494" target="_blank">see this <i>BMC Research Notes</i> editorial</a>. Citation of data usage is greatly encouraged in order to provide the recognition to the data producers, both for their efforts in the production and in their foresight and generosity in making the data CC0.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading25">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel25" aria-expanded="false" aria-controls="panel25">
                                Can the <em>GigaDB</em> repository help me prepare a data management plan?
                            </button>
                        </h2>
                    </div>
                    <div id="panel25" aria-labelledby="heading25" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>At the present time <em>GigaDB</em> doesn't have the resources to assist in data management plans, but there are many useful resources available on the internet, including places like the <a href="http://www.dcc.ac.uk/resources/data-management-plans">DCC (UK focus)</a>or <a href="https://www.cic.net/projects/technology/shared-storage-services/data-management-plans">the CIC (US focus)</a>.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading26">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel26" aria-expanded="false" aria-controls="panel26">
                                What are the charges for submitting data?
                            </button>
                        </h2>
                    </div>
                    <div id="panel26" aria-labelledby="heading26" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There are currently no separate Data Publishing Charges (<abbr>DPCs</abbr>) for <em>GigaDB</em> as we currently do not accept data that is not accompanied by a <i>GigaScience </i>manuscript. All <abbr title="Data Publishing Charges">DPCs</abbr> for <i>GigaScience </i>manuscripts are covered by the Article Publishing Charges (<abbr>APCs</abbr>) of that manuscript (up to a terabyte automatically included, but contact us if you need more). For <abbr title="Article Publishing Charges">APCs</abbr> of <i>GigaScience </i>manuscripts please see in <a href="https://academic.oup.com/gigascience/pages/charges_licensing_and_self_archiving">Gigascience journal pricing</a>.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading27">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel27" aria-expanded="false" aria-controls="panel27">
                                Why is submission to <em>GigaDB</em> not closely integrated with submission to GigaScience?
                            </button>
                        </h2>
                    </div>
                    <div id="panel27" aria-labelledby="heading27" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Due to various technical differences in the editorial submission tools and the <em>GigaDB</em> system, unfortunately at this time it is not possible to integrate the submission process, but our editors and curators will do everything they can to make the process as smooth as possible for authors.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading28">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel28" aria-expanded="false" aria-controls="panel28">
                                How are datasets in <em>GigaDB</em> backed up?
                            </button>
                        </h2>
                    </div>
                    <div id="panel28" aria-labelledby="heading28" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We have a regular backup of data, so if you find a corrupt file please let us know and we will replace it with a copy from back-up.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading29">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel29" aria-expanded="false" aria-controls="panel29">
                                What happens to data after it is submitted?
                            </button>
                        </h2>
                    </div>
                    <div id="panel29" aria-labelledby="heading29" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We will host your data on our private <em>GigaDB</em> server giving access to the reviewers, if the manuscript is accepted we will move the data to our public production server. If it is unsuccessful the data will be deleted.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading30">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel30" aria-expanded="false" aria-controls="panel30">
                                Can I see how often my dataset is being used and downloaded?
                            </button>
                        </h2>
                    </div>
                    <div id="panel30" aria-labelledby="heading30" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>urrrm. Sort-of yes. If a user clicks the download button on the website, it is recorded in the database and you can see on the dataset page how many times this has happened. However, if a file is pulled directly from the FTP server it is currently not recorded in the database. This functionality is on our to-do list and will be addressed as soon as we can.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading31">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel31" aria-expanded="false" aria-controls="panel31">
                                How may data from <em>GigaDB</em> be reused?
                            </button>
                        </h2>
                    </div>
                    <div id="panel31" aria-labelledby="heading31" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>It can be used for anything by anyone, most* data is given the licence CC0 specifically to remove any restrictions on reuse. * - on occasion we host some files for convenience of our users that are already covered by other licences (e.g. more appropriate OSI-compliant licenses for software, or multiple (all open) licenses in a workflow or virtual machine), where this happens we make every effort to make users aware of the different licences.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading32">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel32" aria-expanded="false" aria-controls="panel32">
                                What is Hypothes.is?
                            </button>
                        </h2>
                    </div>
                    <div id="panel32" aria-labelledby="heading32" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p><a href="http://hypothes.is/">Hypothes.is</a> is an open source project helping to bring a discussion, annotation and curation layer to the web, we are collaborating with Hypothes.is in order to make all our datasets open to discussion by anyone (with a hypothesis user account). Simply highlight the text of interest on the page and click "New Note" icon that appears. To see previous notes, click the number on the side bar, or open the side bar to see all previous public annotations.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading33">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel33" aria-expanded="false" aria-controls="panel33">
                                How do I report missing values in my metadata?
                            </button>
                        </h2>
                    </div>
                    <div id="panel33" aria-labelledby="heading33" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>There are various reason why certain data values may need to not be included in the sample metadata, but you still want it to be compliant with particular Minimum Information standards such the GSC MIxS. To maintain compliance when there are missing values within the mandatory fields please use the following terms only:</p>
                            <dl>
                                <div class="dl-item-wrapper">
                                    <dt>Term</dt>
                                    <dd>Definition</dd>
                                </div>
                                <div class="dl-item-wrapper">
                                    <dt>not applicable</dt>
                                    <dd>information is inappropriate to report, can indicate that the standard itself fails to model or represent the information appropriately</dd>
                                </div>
                                <div class="dl-item-wrapper">
                                    <dt>restricted access</dt>
                                    <dd>information exists but can not be released openly because of privacy concerns</dd>
                                </div>
                                <div class="dl-item-wrapper">
                                    <dt>not provided</dt>
                                    <dd>information is not available at the time of submission, a value may be provided at the later stage</dd>
                                </div>
                                <div class="dl-item-wrapper">
                                    <dt>not collected</dt>
                                    <dd>information was not collected and will therefore never be available</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading34">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel34" aria-expanded="false" aria-controls="panel34">
                                Why do you request so many sample attributes?
                            </button>
                        </h2>
                    </div>
                    <div id="panel34" aria-labelledby="heading34" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The addition of comprehensive sample metadata will ensure the best possible reach of these data and help users find and filter relevant data.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading35">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel35" aria-expanded="false" aria-controls="panel35">
                                Why is the directory structure missing from the file table view on my dataset page?
                            </button>
                        </h2>
                    </div>
                    <div id="panel35" aria-labelledby="heading35" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>This is simply a display issue. In the longer term, we wish to display the directory structure on the GigaDB dataset pages, however, for the moment the files appear as a flat list. By mousing over a filename in the list you can see the complete filepath which shows the directory structure has been maintained. Additionally you can click the "FTP site" link at the top of any file table to be taken to the FTP server which displays the complete directory structure.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading36">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel36" aria-expanded="false" aria-controls="panel36">
                                What should I do if I accidentally identify an individual from anonymized human (meta)data within a dataset?
                            </button>
                        </h2>
                    </div>
                    <div id="panel36" aria-labelledby="heading36" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>If you inadvertently discover the identity of any patient/individual, then (a) you agree that you will make no use of this knowledge, (b) that you will notify us (<a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a>) of the incident, and (c) that you will inform no one else of the discovered identity.
                                We will assess the specific case and remove/reduce the amount of metadata available for the subjects involved, and inform the data owners/submitters of the situation.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading37">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel37" aria-expanded="false" aria-controls="panel37">
                                What curation do you carry out?
                            </button>
                        </h2>
                    </div>
                    <div id="panel37" aria-labelledby="heading37" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>All datasets are curated to a high standard including but not limited to; the checking and conversion of file formats if required to ensure open (non-proprietary) and stable formats are used whenever possible; Sample metadata to meet appropriate standards and to include ontology terms where possible; creation of specialist display formats like 3D images from STL image stacks and JBrowse genome browser files from genome assemblies and annotation files. All datasets are manually curated with email correspondence to the submitting author to ensure completeness. Where possible our curators follow guidelines provided by international bodies such as the Genomics Standards Consortium (<a href="https://gensc.org/" target="_blank">gensc.org, opens in a new window</a>) for the minimal information about any genomic sequences. Dataset level metadata is also checked and curated to go above and beyond DataCite standards.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading38">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel38" aria-expanded="false" aria-controls="panel38">
                                What procedures are in place to ensure data integrity?
                            </button>
                        </h2>
                    </div>
                    <div id="panel38" aria-labelledby="heading38" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We ensure data files provided are not corrupt in transfer by use of md5 checksums whenever files are received or moved. All changes to the datafiles and or metadata (after publication) are tracked in a history log present on each dataset page. In the event that a major update is requested we would initiate a full new dataset and maintain the previous dataset as the archival record, placing a notice on the archival record informing users that there is a newer version available, with a link.</p>
                            <p>When curation is complete the dataset is registered with a datacite DOI. Each dataset can have multiple links to external repositories / websites, these are manually curated at the time of submission and automatic link resolution checks are performed weekly to try to catch links to sites that move / disappear. We check validity of email addresses from submitting author and ask for identifiers such as ORCID if available for all named authors, but no additional checks are made on those authors.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading39">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel39" aria-expanded="false" aria-controls="panel39">
                                What data storage procedures do you follow?
                            </button>
                        </h2>
                    </div>
                    <div id="panel39" aria-labelledby="heading39" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaDB data currently hosts the data files in a <a href="https://wasabi.com/cloud-object-storage" target="_blank">Wasabi cloud hot storage</a> bucket. Wasabi promises persistent and stable storage with high-speed connections and Anti-DDoS protection to safeguard the data. In addition, regular data backups are made to AWS Glacier to ensure all data in our repository has two copies in different locations and service providers.</p>
                            <p>We ensure data files provided are not corrupt in transfer by using MD5 checksums whenever files are received or moved (see the <a href="#heading38">"What procedures are in place to ensure data integrity"</a> FAQ for more details). As full members of <a href="https://datacite.org/" target="_blank">DataCite</a>, CC0 metadata is sent to them upon public release and is discoverable and searchable via search.datacite.org and other linked data search indexes.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading40">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel40" aria-expanded="false" aria-controls="panel40">
                                What's the relationship between <i>GigaScience Press</i> and <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel40" aria-labelledby="heading40" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaScience Press currently publishes two journals; <em><a href="https://www.gigasciencejournal.com" target="_blank">GigaScience</a></em> and <em><a href="https://www.gigabytejournal.com" target="_blank">GigaByte</a></em>, as well as one data archive; GigaDB. The GigaDB platform has been created with the intention of hosting all the data associated with articles published in GigaScience Press journals to ensure full transparency and reproducibility of those scientific articles and promote data sharing and data reuse in line with the FAIR sharing principles. It should be noted that GigaScience Press is a part of the BGI Group, who provided the start up funding for the journal and GigaDB development.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading41">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel41" aria-expanded="false" aria-controls="panel41">
                                What is the long term preservation plan for <em>GigaDB</em>?
                            </button>
                        </h2>
                    </div>
                    <div id="panel41" aria-labelledby="heading41" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The fact that BGI covers the majority of the costs of running GigaDB means that it will be actively maintained for the foreseeable future. In the longer term, it is envisaged that the article (and/or) data processing charges levied on submitters will cover storage and curation costs to enable the maintenance and sustained growth of GigaDB.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading42">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel42" aria-expanded="false" aria-controls="panel42">
                                What metadata do you collect?
                            </button>
                        </h2>
                    </div>
                    <div id="panel42" aria-labelledby="heading42" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We follow a variety of standards for metadata collection, primarily the DataCite specification (<a href="https://schema.datacite.org/">https://schema.datacite.org/</a>) for the dataset level metadata and the <a href="https://press3.mcs.anl.gov/gensc/mixs/">Genomics Standards Consortium minimal information standards</a> for the sample level metadata. We also follow community norms for file metadata. Additionally all dataset pages are marked up with Schema.org compliant metadata to facilitate discovery by generic web search engines such as Google.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading43">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel43" aria-expanded="false" aria-controls="panel43">
                                Does <em>GigaDB</em> use community-supported Open Source software?
                            </button>
                        </h2>
                    </div>
                    <div id="panel43" aria-labelledby="heading43" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaDBs code is open source (available on GitHub <a href="https://github.com/gigascience/gigadb-website">https://github.com/gigascience/gigadb-website</a>), based on PostgreSQL database and Yii PHP frameworks, and we utilise and integrate with a growing number of open source plugins and widgets. e.g. Jbrowse, a community supported genome browser. All of the servers run on Centos 7, which is also open source.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading44">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel44" aria-expanded="false" aria-controls="panel44">
                                Do you allow comments, moderation or annotation of dataset entries?
                            </button>
                        </h2>
                    </div>
                    <div id="panel44" aria-labelledby="heading44" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>If you spot errors in data or metadata or anywhere in this website please contact the GigaDB curators via <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a>. We also provide a moderation space for more interactive feedback or discussion on our datasets using hypothes.is integration. <a href="https://web.hypothes.is/">Hypothes.is</a> is an open-source open annotation tool to enable users of its website to make comments, highlight important sections of articles and engage with fellow readers online. Anyone who might question any of the information or have additional things to link can do so via this functionality, producing a conversation layer over our datasets. We have integrated a plugin to allow public annotations to be highlighted on the landing pages, and using the hypothes.is icon that hovers over the top-right of GigaDB landing pages you can login and join the discussion. Adding comments and your own annotations.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading45">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel45" aria-expanded="false" aria-controls="panel45">
                                Which BUSCO genome completeness files should I include in my dataset?
                            </button>
                        </h2>
                    </div>
                    <div id="panel45" aria-labelledby="heading45" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>You must include the "full_table.tsv" and the "missing_busco_list.tsv" files, any other output files are optional. It is acceptable to include the entire "output" of a BUSCO run in a tar.gz archive if you prefer. See this website for more details about the various outputs from <a href="https://busco.ezlab.org/busco_userguide.html#outputs">BUSCOv4</a>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading46">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel46" aria-expanded="false" aria-controls="panel46">
                                Which prefix (decimal or binary) is used for file size display?
                            </button>
                        </h2>
                    </div>
                    <div id="panel46" aria-labelledby="heading46" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaDB displays file sizes using the binary prefixes (e.g. 1KB = 1024 byte; 1GB= 1073741824 byte)
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading47">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel47" aria-expanded="false" aria-controls="panel47">
                                What does my dataset status mean?
                            </button>
                        </h2>
                    </div>
                    <div id="panel47" aria-labelledby="heading47" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The submission of data to GigaDB is integrated with the submission of a manuscript to GigaScience, the general workflow followed is outlined in the <a href="/site/guide" target="_blank">GigaDB Submission Guidelines</a>. During the process you may track the progress in "<a href="/site/login#submitted" target="_blank">your datasets</a>" on your personal profile page in GigaDB. Each of your datasets will have one of the statuses listed below.
                            </p>
                            <figure>
                                <img src="../images/status_flow_2.png" alt="Diagram of dataset status lifecycle, see description below">
                                <figcaption>
                                    <p>Green steps are those carried out by Giga-staff, yellow are performed by Authors, Red indicates the end of the line for that dataset.
                                    </p>
                                    <h3 class="h5">Dataset status lifecycle</h3>
                                    <dl>
                                        <dt>1. Import From EM</dt>
                                        <dd>Carried out by Giga staff: Dataset metadata gets imported from manuscript submission system.</dd>

                                        <dt>2. User Started Incomplete</dt>
                                        <dd>Carried out by authors: Manual dataset submission started by user, but not yet submitted.</dd>

                                        <dt>3. Assigning FTP Box</dt>
                                        <dd>Carried out by Giga staff: Curator to provide private FTP login details for user to upload data files to.</dd>

                                        <dt>4. User Uploading Data</dt>
                                        <dd>Carried out by authors: Author is uploading data files to GigaDB private FTP area.</dd>

                                        <dt>5. Data Available For Review</dt>
                                        <dd>Carried out by Giga staff: Pending results of manuscript review.</dd>

                                        <dt>6. Submitted (Dataset)</dt>
                                        <dd>Carried out by Giga staff: Dataset has been submitted for curator review.</dd>

                                        <dt>7. Data Pending</dt>
                                        <dd>Carried out by authors: Authors are updating files and metadata at request of curators.</dd>

                                        <dt>8. Curation</dt>
                                        <dd>Carried out by Giga staff: Final curation checks are being made, and mock-up being made for author review.</dd>

                                        <dt>9. Author Review</dt>
                                        <dd>Carried out by authors: Dataset mock-up is complete, pending review by authors.</dd>

                                        <dt>10. Private</dt>
                                        <dd>Carried out by Giga staff: Dataset is complete and checked by authors, ready to be released.</dd>


                                        <dt>11. Published</dt>
                                        <dd>End of the line for that dataset: The dataset is now published. This status cannot be reversed.</dd>

                                        <dt>Rejected</dt>
                                        <dd>Related manuscript was rejected so no need for this dataset. Things of this status will be purged from the database on a regular basis.</dd>

                                        <dt>Not Required</dt>
                                        <dd>Associated manuscript has no data. Datasets of this status may be purged from the database.</dd>
                                    </dl>
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading48">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel48" aria-expanded="false" aria-controls="panel48">
                                Which version of BUSCO genome completeness analysis should I use?
                            </button>
                        </h2>
                    </div>
                    <div id="panel48" aria-labelledby="heading48" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We strongly recommend that you conduct your analysis using the most recent version of any software tool available, this includes BUSCO analysis. If for any reason you ran BUSCO analysis a long time before the dataset is being uploaded we may suggest that you re-run the analysis with the newest version. Please visit the <a href="https://busco.ezlab.org/">BUSCO webpage</a> for details of the most recent version. In general, the differences between versions have been minimal so will not impact the conclusions drawn from the analysis, but the exact numbers or percentages will be slightly different.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading49">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel49" aria-expanded="false" aria-controls="panel49">
                            What is involved in data curation?
                            </button>
                        </h2>
                    </div>
                    <div id="panel49" aria-labelledby="heading49" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>GigaDB biocurators will enhance metadata for your submitted data files by including a dataset title, author list, description and keywords. The following actions, checks and enhancements are undertaken during the curation process:</p>
                            <ul class="content-text">
                                <li>Check the consistency and completeness of the data provided, with respect to the associated manuscript</li>
                                <li>Carry out file integrity checks</li>
                                <li>Ensure accessibility of data to end users</li>
                                <li>Ensure transparency of data files including file descriptions and where appropriate additional metadata.</li>
                                <li>Extract sample metadata to be hosted in GigaDB</li>
                                <li>Check for the presence of sensitive or identifying information</li>
                                <li>Check that the methodology is complete and discoverable</li>
                                <li>Recommend appropriate external repositories and the appropriate details to include with those data</li>
                                <li>Provide assistance with data upload to the GigaDB repository (with up to 1TB free storage)</li>
                                <li>Organise files into a logical structure and collections</li>
                                <li>Content is assigned to appropriate SRAO categories</li>
                                <li>A Digital Object Identifier (DOI) is generated for each dataset</li>
                                <li>Pre-publication embargoes with private, anonymous access can be enabled</li>
                                <li>Link and synchronize the dataset release with associated publications</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading50">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel50" aria-expanded="false" aria-controls="panel50">
                                Who can use GigaDB's data support services?
                            </button>
                        </h2>
                    </div>
                    <div id="panel50" aria-labelledby="heading50" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>The scope of GigaDB is all of the life sciences, so as long as your research is Open, involves life sciences in some way, and is in a state that forms a complete <i>unit-of-work*</i>, then contact us to discuss how we can help.</p><p>*By <i>unit-of-work</i> we just mean something that could be written up as a scientific paper, that could be a data-note, technical-note or a research article.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="h4 panel-title" id="heading51">
                            <button data-toggle="collapse" data-parent="#accordion" data-target="#panel51" aria-expanded="false" aria-controls="panel51">
                            How do I calculate the MD5 checksum of the files I am uploading for a GigaDB submission?
                            </button>
                        </h2>
                    </div>
                    <div id="panel51" aria-labelledby="heading51" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We ensure data files provided are not corrupt in transfer by use of the MD5 checksums whenever files are received or moved. We also publish the MD5 checksum value of every file we host so that anyone who downloads a file from us can also check its integrity. Each operating system has a variety of tools/apps to calculate the MD5 value, here are some that we are aware of:</p>
                            <ul class="content-text">
                                <li>Windows: <a href="https://apps.microsoft.com/detail/9nblggh4rrr2" target="_blank" rel="noopener noreferrer">Hash Tool</a></li>
                                <li>macOS: <a href="https://osxdaily.com/2009/10/13/check-md5-hash-on-your-mac/" target="_blank" rel="noopener noreferrer">md5</a></li>
                                <li>Linux: <a href="https://en.wikipedia.org/wiki/Md5sum" target="_blank" rel="noopener noreferrer">md5sum</a></li>
                            </ul>
                            <p>Whichever tool you use, please ensure the output of the tool is saved to a single file with a relevant filename and the extension ".md5", e.g., "submitted-files.md5"</p>
                        </div>
                    </div>
                </div>


            </div>
    </div>
    </section>

</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        let idToShow = "#panel01"

        if (location.hash != null && location.hash != "") {
            idToShow = location.hash
        }

        $('.collapse').removeClass('in');
        $(idToShow + '.collapse').collapse('show');
    });
</script>