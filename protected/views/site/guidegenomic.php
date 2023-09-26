<?php
$this->pageTitle = 'GigaDB - Genomic Dataset checklists';

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
                <h1 class="h4">Genomic Dataset checklists</h1>
            </div>
        </section>
        <?php
            $this->widget('application.components.GuideNavigation');
        ?>
        <section>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <h2 class="h4 page-subtitle">Genomic Dataset Checklist </h2>
                        <div class="subsection">
                            <p>All sequence, assembly, variation, and RNA-seq data must be deposited in a public database at <a target="_blank" href="http://www.ncbi.nlm.nih.gov/">NCBI</a>, <a target="_blank" href="http://www.ebi.ac.uk/">EBI</a>, or <a target="_blank" href="http://www.ddbj.nig.ac.jp/">DDBJ</a> before you submit to <i>GigaScience</i> or GigaDB. In the cases where you would like GigaDB to host files associated with genomic data not fully consented for public release, you must first submit the non-public data to <a target="_blank" href="http://www.ncbi.nlm.nih.gov/gap/">dbGaP</a> or <a target="_blank" href="http://ega-archive.org/">EGA</a>.</p>
                            <p>For Genomic datasets we would expect to see many of the files listed in the table below, please note this list is not comprehensive and curators/reviewers may ask for additional/different files depending on the specific content of the manuscript.</p>
                            <div id="table_genomic_format" class="scrollbar">
                                <table border="1" class="guide-table">
                                    <tr>
                                        <th style="text-align: center; width: 70%">
                                            Item
                                        </th>
                                        <th style="text-align: center; width: 20%">
                                            Suggested format
                                        </th>
                                        <th style="text-align: center; width: 10%">
                                            Check
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Genome assembly
                                        </td>
                                        <td>
                                            fasta
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Coding gene annotations
                                        </td>
                                        <td>
                                            GFF
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Coding gene nucleotide sequences
                                        </td>
                                        <td>
                                            fasta
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Coding gene translated sequences
                                        </td>
                                        <td>
                                            protein fasta
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Repeats/transposable elements/ncRNAs /other annotations
                                        </td>
                                        <td>
                                            GFF
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Gene family alignments (multi-fasta)
                                        </td>
                                        <td>
                                            multi-fasta
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Phylogenetic tree files (newick)
                                        </td>
                                        <td>
                                            newick
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Full (not summary) BUSCO results output files (text)
                                        </td>
                                        <td>
                                            text
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            SNP annotations (VCF)
                                        </td>
                                        <td>
                                            VCF
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Any perl/python scripts created for analysis process
                                        </td>
                                        <td>
                                            py, pl, etc
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            readme.txt including all file names with a brief description of each
                                        </td>
                                        <td>
                                            text
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <p>In addition these might be included for Transcriptomic datasets;</p>
                            <br>
                            <br>
                            <div id='table_transcriptomic' class="scrollbar">
                                <table border="1" class="guide-table">
                                    <tr>
                                        <th style="text-align: center; width: 70%">
                                            Item
                                        </th>
                                        <th style="text-align: center; width: 20%">
                                            Suggested format
                                        </th>
                                        <th style="text-align: center; width: 10%">
                                            Check
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            De novo transcriptome assembly
                                        </td>
                                        <td>
                                            fasta
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Aligned reads
                                        </td>
                                        <td>
                                            bam
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Expression levels
                                        </td>
                                        <td>
                                            fpkm table
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <p>For genomic and transcriptomic datasets we would expect to see sample metadata that complies with the <a target="_blank" href="http://gensc.org/">Genomic Standards Consortium</a> MIxS checklists, the most common features of which are summarised below.</p>
                            <p>For transcriptomic datasets we we would expect users to follow the well established <a target="_blank" href="https://doi.org/10.1038/ng1201-365">MIAME</a> (Minimum Information About a Microarray Experiment) and <a target="_blank" href="http://fged.org/projects/minseqe/">MINSEQE</a> (Minimum Information About a Next-generation Sequencing Experiment) guidelines outlining the minimum information that should be included when describing a microarray or sequencing studies.</p>
                            <p>The complete list of pre-defined sample attributes are available <a href="/">here</a>, and it is possible to include bespoke attributes by communication with us.</p>
                            <br>
                            <br>
                            <div id='table_genomic_meta' class="scrollbar">
                                <table border="1" class="guide-table">
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
                                            Analyte type
                                        </td>
                                        <td>
                                            R
                                        </td>
                                        <td>
                                            The analyte being assayed from the sample (e.g. DNA for sequencing, peptide for MS, etc.)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            BioSample ID
                                        </td>
                                        <td>
                                            R
                                        </td>
                                        <td>
                                            Accession number given to the same sample in the BioSamples database at either <a target="_blank" href="http://www.ncbi.nlm.nih.gov/biosample/?term=SAMNnnnnnnn">NCBI</a> or <a target="_blank" href="http://www.ebi.ac.uk/ena/data/view/SAMEAnnnnnnn">EBI</a> or DDBJ
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            BioProject ID
                                        </td>
                                        <td>
                                            R
                                        </td>
                                        <td>
                                            Accession number given to the Project containing the BioSample by the BioProjects database at either <a target="_blank" href="http://www.ncbi.nlm.nih.gov/">NCBI</a> or <a target="_blank" href="http://www.ebi.ac.uk/ena/data/view/">EBI</a> or DDBJ
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
                                    <tr>
                                        <td>
                                            Environmental medium
                                        </td>
                                        <td>
                                            R
                                        </td>
                                        <td>
                                            Please add one or more <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/envo">ENVO terms</a> to describe the environmental medium of the sample e.g. fecal material [ENVO:00002003]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Life stage
                                        </td>
                                        <td>
                                            O
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
                                            O
                                        </td>
                                        <td>
                                            Age of host or specimen at the time of sampling; relevant scale depends on species and study, e.g. could be seconds for amoebae or centuries for trees. Please include units. e.g. 5 days
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample source
                                        </td>
                                        <td>
                                            O
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
                                            O
                                        </td>
                                        <td>
                                            The time of sampling, either as an instance (single point in time) or interval. In case no exact time is available, the date/time can be right truncated i.e. all of these are valid times: 2008-01-23T19:23:10+00:00; 2008-01-23T19:23:10; 2008-01-23; 2008-01; 2008; Except: 2008-01; 2008 all are ISO8601 compliant.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Ploidy
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            The ploidy level of the genome (e.g. allopolyploid, haploid, diploid, triploid, tetraploid). It has implications for the downstream study of duplicated gene and regions of the genomes (and perhaps for difficulties in assembly). Please select terms listed under class ploidy (PATO:001374) within the <a target="_blank" href="https://www.ebi.ac.uk/ols/ontologies/pato/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FPATO_0001374">PATO browser</a> (Phenotypic Quality Ontology v1.269)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Tissue
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            Please provide details of the type of tissue sampled, where possible values should be from an ontology such as <a target="_blank" href="http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=UBERON">UBERON</a> e.g."lung  [UBERON:0002048]"
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Collected by
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            The name of the person(s) attributed with the collection of the wild specimen, uppercase the surname, e.g. CI HUNTER.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Isolate
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            ID of individual isolate from which the sample was obtained
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Cell line
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            Please provide the cell line name and supplier of the immortalised cell line used in your experiments, e.g. “HEK-293:Addex Bio”
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sample collection device or method
                                        </td>
                                        <td>
                                            O
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
                                            O
                                        </td>
                                        <td>
                                            A brief description of any processing applied to the sample during or after retrieving the sample from environment, or a link to the relevant protocol(s) performed. Where possible please use OBI (<a target="_blank" href="http://obi-ontology.org/">Ontology for Biomedical Investigations</a>) terms e.g. H&E slide staining [OBI:0002124]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Amount or size of sample collected
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            Amount or size of sample (volume, mass or area) that was collected. Please include units.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Estimated genome size
                                        </td>
                                        <td>
                                            O
                                        </td>
                                        <td>
                                            The estimated size of the genome prior to sequencing. Of particular importance in the sequencing of (eukaryotic) genome which could remain in draft form for a long or unspecified period.
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <p>* - Requirements are listed as R= Recommended, O= Optional. Note ^ denotes absolutely mandatory fields.</p>
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