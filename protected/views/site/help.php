<?php
$this->pageTitle = 'GigaDB - Help';
?>

<div class="content">
    <div class="container">
        <section class="page-title-section">
            <div class="page-title">
                <ol class="breadcrumb pull-right">
                    <li><a href="/">Home</a></li>
                    <li><a href="about">About</a></li>
                    <li class="active">Help</li>
                </ol>
                <h1 class="h4">Help</h1>
            </div>
        </section>
        <div class="subsection">
            <p>The <a href="http://gigadb.org/" target="_blank"><span class="text-italic">GigaDB</span></a> website allows any user to browse, search, view datasets and access data files. If you want to submit a dataset, save searches or be alerted of new content of interest we request that you <a href="/user/create" target="_blank">create an account</a>.</p>
            <p>A 'Latest news' section will be visible to announce any updates or new features to the database and the RSS feed automatically announces each new dataset release.</p>
            <p>The <a href="http://gigadb.org/" target="_blank"><span class="text-italic">GigaDB</span></a> homepage allows you to browse datasets by type eg Genomic, Metagenomic, Transcriptomic. Clicking on the DOI (digital object identifier) or image will take you directly to the webpage for the dataset of interest.</p>
            <p>Alternatively you can use the search functions to find datasets, samples or files of interest.</p>
        </div>
        <div class="section">
            <ul class="nav nav-tabs nav-border-tabs" role="tablist" id="alltabs" aria-label="GigaDB help">
                <li id="lisearch" role="presentation" class="active"><a href="#search" aria-controls="search" role="tab" data-toggle="tab" aria-selected="true"><span class="text-italic">GigaDB</span> search</a></li>
                <li id="liguideline" role="presentation"><a href="#guidelines" aria-controls="guidelines" role="tab" data-toggle="tab" aria-selected="false">Submission guidelines</a></li>
                <li id="livocabulary" role="presentation"><a href="#vocabulary" aria-controls="vocabulary" role="tab" data-toggle="tab" aria-selected="false">Controlled vocabulary</a></li>
                <li id="liapi" role="presentation"><a href="#interface" aria-controls="interface" role="tab" data-toggle="tab" aria-selected="false">Application programming interface</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="search" aria-labelledby="lisearch">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Search operation</h2>
                    <p>To search across all Dataset, Sample and File records in <span class="text-italic">GigaDB</span>, simply enter a search term in the search bar found at the top of all <span class="text-italic">GigaDB</span> pages.</p>
                    <p>The search is case insensitive which means both uppercase and lowercase keywords will have the same result.</p>
                    <hr style="border-top-style: dashed;">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Search result</h2>
                    <p>The search results are grouped by <span class="text-italic">GigaDB</span> Datasets (G), Samples (S) and Files (F).</p>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="text-icon text-icon-sm text-icon-blue" aria-hidden="true">G</div>
                                    <span class="sr-only">Datasets</span>
                                </td>
                                <td>
                                    <p>For each dataset result, author names and DOI are displayed. Hovering over dataset name provides the description of dataset. Dataset and sample names are linked to the specific DOI page for those data, as well as file links are provided to download.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-icon text-icon-sm text-icon-green" aria-hidden="true">S</div>
                                    <span class="sr-only">Samples</span>
                                </td>
                                <td>
                                    <p>For each sample result, the sample name, species name and species ID are displayed with links to the NCBI taxonomy page for the species and to the <em>GigaDB</em> dataset page.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-icon text-icon-sm text-icon-yellow" aria-hidden="true">F</div>
                                    <span class="sr-only">Files</span>
                                </td>
                                <td>
                                    <p>For each file result, the file name, file type and file size are displayed with a direct link to the FTP server location of that file.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>Only those objects that have direct matches are displayed in the search results, i.e. the only Files to be displayed in the search results will be those with matches to the search term, all other files within the same dataset will NOT be displayed.</p>
                    <p>For example, searching for the term "Potato" will return the dataset with the title "Genomic data from the potato" which contains 17 files, however, the search results table will only display 3 of those 17 files because only 3 contain the search term “potato”. To find all data associated with a dataset you must follow the link to the dataset page.</p>
                    <hr class="dashed">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Filtering result</h2>
                    <p style="margin-bottom: 20px;">On the left of the search results you have the option to further refine the results by using the filters. By default all filters are disabled, allowing you to see all search results for your keyword. If you want to hide some results based on some criteria, choose the filter for your criteria, and select the options that match what you want to see.</p>
                    <h3 class="tabpanel-subtitle">TFilter options for Datasets:</h3>
                    <ol>
                        <li>Dataset Type (<a href="#datasettypes" onclick="DatasetFunction()">Dataset Type</a> controlled vocabulary eg 'Genomic', 'Proteomic')</li>
                        <li>Project (eg 'Genome 10K', '1000 Genomes'</li>
                        <li>External Link Types (Controlled vocabulary: 'Genome Browser' or 'Additional Data')</li>
                        <li>Publication Date (From and To. Format: dd-mm-yyyy)</li>
                    </ol>
                    <h3 class="tabpanel-subtitle">Filter options for Samples:</h3>
                    <ol>
                        <li>Common Name (Internally controlled eg 'Human', 'Mouse')</li>
                    </ol>
                    <h3 class="tabpanel-subtitle">Filter options for Files:</h3>
                    <ol>
                        <li>File Type (<a href="#filetypes" onclick="DatasetFunction()">File Type</a> controlled vocabulary eg 'Alignments', 'Genome sequence', 'SNPs')</li>
                        <li>File Format (<a href="#fileformats" onclick="DatasetFunction()">File format</a> controlled vocabulary eg 'BIGWIG', 'FASTQ', 'VCF')</li>
                        <li>File Size (From and To: Format KB, MB, GB, TB)</li>
                    </ol>
                </div>
                <div role="tabpanel" class="tab-pane" id="guidelines" aria-labelledby="liguideline">
                    <p><a href="http://gigadb.org/"><span class="text-italic">GigaDB</span></a> is an open-access database. As such, all data submitted to <span class="text-italic">GigaDB</span> must be fully consented for public release (for more information about our data policies, please see our <a href="http://gigadb.org/site/term/" target="_blank">Terms of use page</a>).</p>
                    <p>All sequence, assembly, variation, and microarray data must be deposited in a public database at <a href="http://www.ncbi.nlm.nih.gov/" target="_blank">NCBI</a>, <a href="http://www.ebi.ac.uk/" target="_blank">EBI</a>, or <a href="http://www.ddbj.nig.ac.jp/" target="_blank">DDBJ</a> before you submit them to <a href="http://gigadb.org/" target="_blank"><span class="text-italic">GigaDB</span></a>. In the cases where you would like <span class="text-italic">Giga</span>DB to host files associated with genomic data not fully consented for public release, you must first submit the non-public data to <a href="http://www.ncbi.nlm.nih.gov/gap/" target="_blank">dbGaP</a> or <a href="http://ega-archive.org" target="_blank">EGA</a>.</p>
                    <p><strong>Step 1</strong> - <a href="http://gigadb.org/user/create" target="_blank">Create an account</a> or <a href="http://gigadb.org/site/login" target="_blank">log in</a> to <a href="http://gigadb.org/" target="_blank"><span class="text-italic">Giga</span>DB</a></p>
                    <p><strong>Step 2</strong> - Download and complete the <a href="/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx" target="_blank">Excel template file</a>. Completed example files for the <a href="http://gigadb.org/files/GigaDBUploadForm-example1.xls">E. coli</a> (<a href="http://gigadb.org/dataset/100001" target="_blank">10.5524/100001</a>) and <a href="http://gigadb.org/files/GigaDBUploadForm-example2.xls">Sorghum</a> (<a href="http://gigadb.org/dataset/100012" target="_blank">10.5524/100012</a>) datasets are available.</p>
                    <p>The template file contains:</p>
                    <ol>
                        <li>3 tabs which must all be completed [Study, Samples, Files]</li>
                        <li>4 informational tabs [Samples (info), Files (info), CV, Links]</li>
                    </ol>
                    <p>Mandatory fields are highlighted in yellow.</p>
                    <h2 class="h5">Study</h2>
                    <p>Required information includes submitter name, email and affiliation, upload status [can we publish this dataset immediately after review (Publish) or should it be held until publication (HUP)], author list, <a href="http://gigadb.org/site/help#datasettype" target="_blank">dataset type(s)</a> (selected from a controlled vocabulary list), dataset title and description, estimated total size of the files that will be submitted and dataset image information.</p>
                    <p>Optional information includes links to additional resources and related manuscripts, accessions for data in other databases (prefixes are found in the Links tab), and <a href="http://gigadb.org/site/help#relation" target="_blank">relationship</a> (if any) to a previously published <em>Giga</em>DB dataset (selected from a controlled vocabulary list).</p>
                    <h2 class="h5">Samples</h2>
                    <p>Required information includes a sample ID or name (please use an <a href="http://www.ncbi.nlm.nih.gov/biosample" target="_blank">NCBI BioSample ID</a> when possible), species <a href="http://www.ncbi.nlm.nih.gov/Taxonomy" target="_blank">NCBI taxonomy ID</a>, and species common name.</p>
                    <p>Optional information includes sample attributes (these are automatically populated in <em>Giga</em>DB if an <a href="http://www.ncbi.nlm.nih.gov/biosample" target="_blank">NCBI BioSample ID</a> is provided).</p>
                    <h2 class="h5">Files</h2>
                    <p>Required information includes a file name or path relative to your home directory and <a href="http://gigadb.org/site/help#filetype" target="_blank">file type</a> (selected from a controlled vocabulary list). A readme file must be provided.</p>
                    <p>Please note;<br>
                        -Filenames should be unique. <br>
                        -Filenames should not include spaces. We recommend using the underscore (_) in place of spaces in the filenames.<br>
                        -Filenames should only include the following characters a-z,A-Z,0-9,_,-,+,. </p>
                    <p>Optional information includes a file description and a sample ID or name.</p>
                    <p><strong>Step 3</strong> - confirm you have read our <a href="http://gigadb.org/site/term/" target="_blank">Terms of use</a> page and upload the completed Excel template file.</p>
                    <p>You can expect a response from the <a href="http://gigadb.org/" target="_blank"><em>Giga</em>DB</a> team within 5 days to verify the information in your submission and to arrange upload of your files to our FTP site.</p>
                    <p>If you have any questions, please contact us at <a href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a>.</p>
                </div>
                <div role="tabpanel" class="tab-pane" id="vocabulary" aria-labelledby="livocabulary">
                    <h2 class="h4" id="datasettypes" style="color: #099242; margin-bottom: 10px;">Dataset types</h2>
                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt>Genomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all genetic and genomic data eg sequence, assemblies, alignments, genotypes, variation and annotation. Minimal requirements: DNA sequence data eg next-gen raw reads (fastq files) OR assembled DNA sequences (fasta files).</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Transcriptomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all data relating to mRNA. Minimal requirements: RNA sequence data eg next-gen raw reads (fastq files) OR transcript statistics eg RNA coverage/depth.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Epigenomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes methylation and histone modification data. Minimal requirements: Details on methylation sites/status eg qmap files OR details on histone modification sites/status.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Metagenomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all genetic and genomic data eg sequence, assemblies, alignments, genotypes, variation and annotation from environmental samples. Minimal requirements: Environmental DNA sequence data eg next-gen raw reads (fastq files) OR assembled DNA sequences (fasta files).</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Metatranscriptomic</dt><span aria-hidden="true"> - </span>
                            <dd>RNA sequences analysis data from environmental samples, e.g. assemblies, expression profiles, variations, annotations etc.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Genome mapping</dt><span aria-hidden="true"> - </span>
                            <dd>Datasets containing sequence analysis of genes/conserved sequences mapped to genome(s), and/or optical maps of entire genomes.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Imaging</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all imaging data, e.g. light microscopy, 3D imaging, high-resolution images, camera-trap images etc...</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Software</dt><span aria-hidden="true"> - </span>
                            <dd>Includes datasets that package code together into a useful bioinformatics tool. Note, datasets that contain short scripts are not labelled as software.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Virtual-Machine</dt><span aria-hidden="true"> - </span>
                            <dd>Includes software that has been packaged into a virtual machine environment.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Workflow</dt><span aria-hidden="true"> - </span>
                            <dd>Datasets that include tools that have been pieced together into a workflow using CWL or other workflow languages.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Metabolomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes analysis of specific metabolights across multiple samples and/or multiple metabolights in fewer samples, usually LC-MS data.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Proteomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all mass spec data. Minimal requirements: Peptide/protein data eg mass spec.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Lipidomic</dt><span aria-hidden="true"> - </span>
                            <dd>Includes datasets with focus on lipid analysis, usually using mass spectrometry.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Metabarcoding</dt><span aria-hidden="true"> - </span>
                            <dd>Datasets using barcode sequences for environmental analysis and/or monitoring studies e.g. biodiversity assessment.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Metadata</dt><span aria-hidden="true"> - </span>
                            <dd>Denotes datasets where there is a focus on collection of metadata e.g. ontologies or metadata standards.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Network-Analysis</dt><span aria-hidden="true"> - </span>
                            <dd>Datasets containing analysis of biological networks, either species interactions or at the molecular level.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Neuroscience</dt><span aria-hidden="true"> - </span>
                            <dd>Includes all datasets that hold data about brains/neurons, can be imaging, molecular and/or software/tools.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Electro-encephalography (EEG)</dt><span aria-hidden="true"> - </span>
                            <dd>Datasets containing or using EEG data.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Phenotyping</dt><span aria-hidden="true"> - </span>
                            <dd>Includes datasets with extensive phenotypic information about the samples/specimens.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Ecology</dt><span aria-hidden="true"> - </span>
                            <dd>Data used/collected for ecological studies.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Climate</dt><span aria-hidden="true"> - </span>
                            <dd>Data used/collected for climate studies.</dd>
                        </div>
                    </dl>
                    <p>Additional dataset types can be added, upon review, as new submissions are received.</p>
                    <h2 class="h4" id="filetypes" style="color: #099242; margin-bottom: 10px;">File types</h2>
                    <p>File types and examples of associated file extensions:</p>
                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt>Alignments:</dt>
                            <dd>.bam, .chain, .maf, .net, .sam</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Allele frequencies:</dt>
                            <dd>.frq</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Annotation:</dt>
                            <dd>.gff, .ipr, .kegg, .wego</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Coding sequence:</dt>
                            <dd>.cds, .fa</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>InDels:</dt>
                            <dd>.gff, .txt, .vcf</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>ISA-Tab:</dt>
                            <dd>see <a href="http://isa-tools.org/format/specification.html" target="_blank">ISA tools</a></dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Genome assembly:</dt>
                            <dd>.agp, .contig, .depth, .fa, .length, .scafseq</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Genome sequence:</dt>
                            <dd>.fastq, .fq</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Haplotypes:</dt>
                            <dd>.haplotype</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Methylome data:</dt>
                            <dd>.fa, .qmap, .rpm, .txt</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Protein sequence:</dt>
                            <dd>.fa, .pep</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Readme:</dt>
                            <dd>.pdf, .txt</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>SNPs:</dt>
                            <dd>.annotation, .gff, .txt, .vcf</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>SVs:</dt>
                            <dd>.gff, .txt, .vcf</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Transcriptome data:</dt>
                            <dd>.depth, .rpkm, .wig</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>Other:</dt>
                            <dd>.xls, .pdf, .txt</dd>
                        </div>
                    </dl>
                    <p>Additional file types can be added, upon review, as new submissions are received.</p>
                    <hr style="border-top-style: dashed;">
                    <h2 class="h4" id="fileformats" style="color: #099242; margin-bottom: 10px;">File formats</h2>
                    <ol style="padding-left: 22px;">
                        <li><a href="#agp">AGP</a></li>
                        <li><a href="#bam">BAM</a></li>
                        <li><a href="#bigwig">BIGWIG</a></li>
                        <li><a href="#chain">CHAIN</a></li>
                        <li><a href="#contig">CONTIG</a></li>
                        <li><a href="#excel">EXCEL</a></li>
                        <li><a href="#fasta">FASTA</a></li>
                        <li><a href="#fastq">FASTQ</a></li>
                        <li><a href="#gff">GFF</a></li>
                        <li><a href="#ipr">IPR</a></li>
                        <li><a href="#kegg">KEGG</a></li>
                        <li><a href="#maf">MAF</a></li>
                        <li><a href="#net">NET</a></li>
                        <li><a href="#pdf">PDF</a></li>
                        <li><a href="#png">PNG</a></li>
                        <li><a href="#qmap">QMAP</a></li>
                        <li><a href="#qual">QUAL</a></li>
                        <li><a href="#rpkm">RPKM</a></li>
                        <li><a href="#sam">SAM</a></li>
                        <li><a href="#tar">TAR</a></li>
                        <li><a href="#text">TEXT</a></li>
                        <li><a href="#vcf">VCF</a></li>
                        <li><a href="#wego">WEGO</a></li>
                        <li><a href="#wig">WIG</a></li>
                        <li><a href="#unknown">UNKNOWN</a></li>
                        <li><a href="#xml">XML</a></li>
                    </ol>

                    <!-- FILE FORMATS DL -->

                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt id="agp">AGP <span class="dt-sidenote">(.agp)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the Accessioned Golden Path (AGP) file describes the assembly of a
                                    larger sequence object from smaller objects:
                                </p>
                                <pre>
chr1 1       1972671 0 W scaffold43  1 1972671 m
chr1 1972672 3061819 1 W scaffold8   1 1089148 p
chr1 3061820 3181505 2 W scaffold548 1 119686  m
chr1 3181506 4176151 3 W scaffold313 1 994646  m</pre>
                                <p>
                                    The large object can be a contig, a scaffold (supercontig), or a
                                    chromosome. See
                                    <a href="http://www.ncbi.nlm.nih.gov/projects/genome/assembly/agp/AGP_Specification.shtml" target="_blank">AGP Specification v2.0</a>
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="bam">BAM <span class="dt-sidenote">(.bam)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.sph.umich.edu/wiki/BAM" target="_blank">Binary Alignment/Map (BAM) format</a>
                                    is the compressed binary version of the Sequence Alignment/Map (SAM)
                                    format, a compact and index-able representation of nucleotide sequence
                                    alignments.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="bigwig">BIGWIG <span class="dt-sidenote">(.bw)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.ucsc.edu/goldenPath/help/bigWig.html" target="_blank">BIGWIG format</a>
                                    is for storing dense, continuous data (such as GC percent, probability
                                    scores, and transcriptome data) that will be displayed in the UCSC
                                    Genome Browser as a graph. BIGWIG files are created initially from
                                    wiggle (WIG) type files, using the program wigToBigWig.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="chain">CHAIN <span class="dt-sidenote">(.chain)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.ucsc.edu/goldenPath/help/chain.html" target="_blank">CHAIN format</a>
                                    describes a pairwise alignment that allow gaps in both sequences
                                    simultaneously and is used by the UCSC Genome Browser.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="contig">CONTIG <span class="dt-sidenote">(.contig)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://soap.genomics.org.cn/soapdenovo.html" target="_blank">CONTIG format</a>
                                    is a direct output from the SOAPdenovo alignment program:
                                </p>
                                <pre>
&gt;1 length 32 cvg_0.0_tip_0
GAGAACGGCGAAGCCTGCTCGGGCCCGTTATA
&gt;3 length 32 cvg_23.0_tip_0
TAGCAGCGATTTGATCAAACTCAATCTTACCG
&gt;5 length 32 cvg_40.0_tip_0
GGTAAGATTGAGTTTGATCAAATCGCTGCTAT</pre>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="excel">EXCEL <span class="dt-sidenote">(.xls, .xlsx)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">Microsoft office spreadsheet files</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="fasta">
                                FASTA
                                <span class="dt-sidenote">(.fasta, .fa, .seq, .cds, .pep, .scafseq [SOAPdenovo output file -
                                    sequence of each scaffold])</span>
                            </dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    <a href="http://www.ebi.ac.uk/help/formats.html#fasta" target="_blank">FASTA</a>
                                    is a text-based format for representing either nucleotide sequences or
                                    peptide sequences.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="fastq">FASTQ <span class="dt-sidenote">(.fq, .fastq)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://maq.sourceforge.net/fastq.shtml" target="_blank">FASTQ format</a>
                                    stores sequences (usually nucleotide sequence) and Phred qualities in a
                                    single file.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="gff">GFF <span class="dt-sidenote">(.gff)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    The
                                    <a href="http://www.sanger.ac.uk/resources/software/gff/" target="_blank">General Feature Format (GFF)</a>
                                    is used for describing genes and other features of DNA, RNA and protein
                                    sequences.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="ipr">IPR <span class="dt-sidenote">(.ipr)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a>
                                    consists of the protein ID, followed by column(s) that are the IPR (<a href="http://www.ebi.ac.uk/interpro/" target="_blank">InterPro</a>) ID(s):
                                </p>
                                <pre>
CR_ENSP00000334840
CR_ENSMMUP00000018123 IPR000504 IPR003954
CR_ENSP00000333725    IPR001781 IPR015880 IPR007087 IPR001909</pre>
                                <p>
                                    See
                                    <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a>
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="kegg">KEGG <span class="dt-sidenote">(.kegg)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a>
                                    consists of the protein ID, followed by column(s) that are the
                                    <a href="http://www.genome.jp/kegg/" target="_blank">KEGG</a> (Kyoto
                                    Encyclopedia of Genes and Genomes) ID(s):
                                </p>
                                <pre>
CR_ENSMMUP00000031408 ko03010
CR_ENSP00000364815    ko00970 ko00290
CR_ENSP00000414605    ko05146 ko04510 ko04512</pre>
                                <p>
                                    See
                                    <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a>
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="maf">MAF <span class="dt-sidenote">(.maf)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.ucsc.edu/FAQ/FAQformat.html#format5" target="_blank">Multiple Alignment Format (MAF)</a>
                                    stores a series of multiple alignments at the DNA level between entire
                                    genomes.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="net">NET <span class="dt-sidenote">(.net)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.ucsc.edu/goldenPath/help/net.html" target="_blank">NET file format</a>
                                    is used to describe the axtNet data that underlie the net alignment
                                    annotations in the UCSC Genome Browser.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="pdf">PDF <span class="dt-sidenote">(.pdf)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">portable document format</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="png">PNG <span class="dt-sidenote">(.png)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">portable network graphics</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="qmap">QMAP <span class="dt-sidenote">(.qmap)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    <a href="http://yh.genomics.org.cn/Format%20of%20qmap%20file.txt" target="_blank">QMAP</a>
                                    files are generated for methylation data from an internal
                                    <a href="http://www.genomics.cn/en" target="_blank">BGI</a> pipeline.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="qual">QUAL <span class="dt-sidenote">(.qual)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://www.broadinstitute.org/crd/wiki/index.php/Qual" target="_blank">QUAL file format</a>
                                    represents base quality score file for NextGen data (similar in format
                                    to fasta).
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="rpkm">RPKM <span class="dt-sidenote">(.rpkm)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    Gene expression levels are calculated by
                                    <a href="http://sourceforge.net/apps/mediawiki/seqgene/index.php?title=SeqGene#.rpkm:_expression_estimates_generated_by_rpkm.py" target="_blank">Reads Per Kilobase per Million (RPKM) mapped reads</a>
                                    eg 1kb transcript with 1000 alignments in a sample of 10 million reads
                                    (out of which 8 million reads can be mapped) will have RPKM = 1000/(1 *
                                    8) = 125:
                                </p>
                                <pre>
ENSP00000379387 15.5651433366423 6002951 289 3093
ENSP00000349977 24.7483107230444 6002951 398 2679
ENSP00000368887 24.6477413647837 6002951 174 1176</pre>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="sam">SAM <span class="dt-sidenote">(.sam)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://genome.sph.umich.edu/wiki/SAM" target="_blank">Sequence Alignment/Map (SAM) format</a>
                                    is a TAB-delimited text format consisting of a header section, which is
                                    optional, and an alignment section. Most often it is generated as a
                                    human readable version of its sister
                                    <a href="http://gigadb.org/site/help#bam" target="_blank">BAM</a>
                                    format, which stores the same data in a compressed, indexed, binary
                                    form. Currently, most SAM format data is output from aligners that read
                                    <a href="http://gigadb.org/site/help#fastq" target="_blank">FASTQ</a>
                                    files and assign the sequences to a position with respect to a known
                                    reference genome. In the future, SAM will also be used to archive
                                    unaligned sequence data generated directly from sequencing machines. See
                                    <a href="http://www.ncbi.nlm.nih.gov/pmc/articles/PMC2723002/" target="_blank">The Sequence Alignment/Map format and SAMtools</a>
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="tar">TAR <span class="dt-sidenote">(.tar)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">an archive containing other files</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="text">
                                TEXT <span class="dt-sidenote">(.doc, .readme, .text, .txt)</span>
                            </dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">a text file</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="vcf">VCF <span class="dt-sidenote">(.vcf)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://www.1000genomes.org/wiki/Analysis/Variant%20Call%20Format/vcf-variant-call-format-version-40" target="_blank">Variant Call Format (VCF)</a>
                                    is a text file format for representing eg SNPs, InDels, CNVs, SVs,
                                    microsatellites, genotypes.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="wego">WEGO <span class="dt-sidenote">(.wego)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the
                                    <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a>
                                    consists of the protein ID, followed by column(s) that are the
                                    <a href="http://www.geneontology.org/" target="_blank">GO</a> ID(s):
                                </p>
                                <pre>
Bmb015379_2_IPR001092
Bmb003749_1_IPR006329 GO:0009168 GO:0003876
Bmb006173_1_IPR000909 GO:0007165 GO:0004629 GO:0007242&lt;</pre>
                                <p>
                                    See
                                    <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a>
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="wig">WIG <span class="dt-sidenote">(.wig)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    the output file from
                                    <a href="http://tophat.cbcb.umd.edu/" target="_blank">TopHat</a> is a
                                    <a href="http://genome.ucsc.edu/goldenPath/help/wiggle.html" target="_blank">UCSC wigglegram</a>
                                    of alignment coverage.
                                </p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="unknown">UNKNOWN</dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">any file format not in this list</p>
                            </dd>
                        </div>
                        <div class="help-definition-container">
                            <dt id="xml">XML <span class="dt-sidenote">(.xml)</span></dt>
                            <dd>
                                <span aria-hidden="true"> - </span>
                                <p class="inline">
                                    <a href="http://www.w3schools.com/xml/" target="_blank">eXtensible Markup Language</a>
                                </p>
                            </dd>
                        </div>
                    </dl>


                    <!-- ENDOF FILE FORMATS DL -->

                    <hr style="border-top-style: dashed;">

                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Upload status</h2>
                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt>Publish:</dt>
                            <dd>this dataset is fully consented for immediate release upon <span class="text-italic">Giga</span>DB approval</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>HUP:</dt>
                            <dd>this dataset should be Held Until Publication (HUP)</dd>
                        </div>
                    </dl>


                    <hr style="border-top-style: dashed;">

                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">DOI relationship</h2>

                    <p>The DOI relationship vocabulary is taken from the <a href="http://schema.datacite.org/meta/kernel-2.2/doc/DataCite-MetadataKernel_v2.2.pdf" target="_blank">DataCite</a> 'relationType' schema property (ID=12.2).</p>
                    <p>Definition: Description of the relationship of the resource being registered (A) and the related resource (B).</p>

                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt>IsSupplementTo:</dt>
                            <dd>indicates that A is a supplement to B</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>IsSupplementedBy:</dt>
                            <dd>indicates that B is a supplement to A</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>IsNewVersionOf:</dt>
                            <dd>indicates A is a new edition of B, where the new edition has been modified or updated</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>IsPreviousVersionOf:</dt>
                            <dd>indicates A is a previous edition of B</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>IsPartOf:</dt>
                            <dd>indicates A is a portion of B; may be used for elements of a series</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>HasPart:</dt>
                            <dd>indicates A includes the part B</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>References:</dt>
                            <dd>indicates B is used as a source of information for A</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>IsReferencedBy:</dt>
                            <dd>indicates A is used as a source of information by B</dd>
                        </div>
                    </dl>

                    <hr style="border-top-style: dashed;">

                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Missing Value reporting</h2>

                    <p>For attributes (sample, dataset or files) that have some or all values missing please use the following controlled value terms to describe the exact reason for the missing value.</p>

                    <dl class="help-description-list">
                        <div class="help-definition-container">
                            <dt>not applicable:</dt>
                            <dd>information is inappropriate to report, often this attribute can be removed entirely.</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>restricted access:</dt>
                            <dd>information exists but cannot be released openly because of privacy concerns</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>not provided:</dt>
                            <dd>information is not available at the time of submission, a value may be provided at the later stage</dd>
                        </div>
                        <div class="help-definition-container">
                            <dt>not collected:</dt>
                            <dd>information was not collected and will therefore never be available</dd>
                        </div>
                    </dl>

                </div>
                <div role="tabpanel" class="tab-pane" id="interface" aria-labelledby="liapi">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Availability</h2>
                    <p>The current API version is available on our main production database. This version will be periodically updated with new additional functionality and we will whenever possible maintain backwards compatibility. Occasionally this may not be possible and for this reason we recommend regularly checking and updating your usage of our API. </p>
                    <p>The basic functionality of the API is to retrieve dataset metadata held in <em>GigaDB</em>. The actual data files will still need to be pulled by FTP, but you can gather the exact FTP locations from the metadata using the API, then use that to pull only the files you actually need/want.</p>
                    <p>Search function is based on the web-search function and will therefore give the same results.</p>
                    <hr style="border-top-style: dashed;">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Comments and Bug reporting</h2>
                    <p>The GigaScience github issue for the API works is here:</p>
                    <p><a href="https://github.com/gigascience/gigadb-website/issues/27" target="_blank">https://github.com/gigascience/gigadb-website/issues/27</a></p>
                    <p>Please add feedback / comments/ questions to that issue.</p>
                    <hr style="border-top-style: dashed;">
                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Summary</h2>
                    <p>It is currently possible to search "all" fields, or to specify one of a select few fields to search.</p>
                    <p>It is possible to have results return all metadata for each dataset with "hits" to the search term, or to specify a particular portion of the metadata, these portions are currently "dataset", "sample" and "file", which is in alignment with the same functionality on the web-search tool. The default is to return results as <a href="https://sites.google.com/a/gigasciencejournal.com/gigascience/public-pages/xml-schema">GigaDB v3 XML</a></p>
                    <p>It is planned that we will have the option to specify the format to be GigaDBv3-JSON or ISA2.0-JSON in the future, but that has not been implemented yet.</p>

                    <hr style="border-top-style: dashed;">


                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Terminology</h2>
                    <p>To specify exact fields to return data from, use terms; <span>dataset?=</span>, <span>sample?=</span>, <span>file?=</span>, (or <span>experiment?=</span><span aria-hidden="true">*</span>)</p>
                    <p><span aria-hidden="true">* - </span><em>experiment will be implemented in the future</em></p>
                    <p>To search for datasets without the ID's, use the term <span>search?keyword=</span></p>
                    <p>To search by specific attributes use <span>search?&lt;attribute_name&gt;=</span></p>
                    <p>Available <strong>attribute_name</strong> to search include:</p>
                    <ul class="help-terminology-list">
                        <li><strong>taxno</strong> = Taxonomic ID (NCBI)</li>
                        <li><strong>taxname</strong> = species name (nb must exact spelling, no synonyms searched)</li>
                        <li><strong>author</strong> = restricts search to the author table</li>
                        <li><strong>datasettype</strong> = restricts search to the types of datasets, e.g. metagenomic, genomic, transcriptomic etc.</li>
                        <li><strong>manuscript</strong> = restricts search to the manuscript ID associated with <em>GigaDB</em> dataset(s) e.g. <span>search?manuscript=10.1186/2047-217X-3-21</span></li>
                        <li><strong>project</strong> = restricts search to the project name, e.g. Genome 10K</li>
                    </ul>
                    <p>eg. <span>..../search?taxno=9606</span></p>
                    <p>To specify results to be returned are ONLY a particular level of data, add the phrase <strong><span>&amp;results=dataset</span></strong>, or file or sample:<br />e.g. <a href="http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample" target="_blank">http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample</a></p>
                    <p>NB - the search still looks everywhere, but the results returned are only those samples that are in datasets that are found by the search.</p>
                    <p><strong>Default results are "dataset" only.</strong></p>

                    <hr style="border-top-style: dashed;">

                    <h2 class="h4" style="color: #099242; margin-bottom: 10px;">Examples</h2>

                    <ol class="help-examples-list">
                        <li>
                            Retrieve known datasets by doi<br />
                            <a href="http://gigadb.org/api/dataset?doi=100051" target="_blank">http://gigadb.org/api/dataset?doi=100051</a>
                        </li>
                        <li>
                            Retrieve samples from a known DOI<br />
                            <a href="http://gigadb.org/api/sample?doi=100051" target="_blank">http://gigadb.org/api/sample?doi=100051</a>
                        </li>
                        <li>
                            Retrieve file information from a known DOI<br />
                            <a href="http://gigadb.org/api/file?doi=100051" target="_blank">http://gigadb.org/api/file?doi=100051</a>
                        </li>
                        <li>
                            Search all <span class="text-italic">GigaDB</span> by keyword, return only the top level dataset
                            metadata<br />
                            <a href="http://gigadb.org/api/search?keyword=chimp&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=dataset</a>
                        </li>
                        <li>
                            Search all <span class="text-italic">GigaDB</span> by keyword, return only the sample level
                            metadata<br />
                            <a href="http://gigadb.org/api/search?keyword=chimp&amp;result=sample" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=sample</a>
                        </li>
                        <li>
                            Search all <span class="text-italic">GigaDB</span> by keyword, return only the file level
                            metadata<br />
                            <a href="http://gigadb.org/api/search?keyword=chimp&amp;result=file" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=file</a>
                        </li>
                        <li>
                            Refine search to just the title of the dataset<br />
                            <a href="http://gigadb.org/api/search?keyword=title:human&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=title:human&amp;result=dataset</a>
                        </li>
                        <li>
                            Refine search to the descriptions of datasets<br />
                            <a href="http://gigadb.org/api/search?keyword=description:human&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=description:human&amp;result=dataset</a>
                        </li>
                        <li>
                            Refine search to NCBI taxonomic ID<br />
                            <a href="http://gigadb.org/api/search?taxno=9606&amp;result=dataset" target="_blank">http://gigadb.org/api/search?taxno=9606&amp;result=dataset</a>
                        </li>
                        <li>
                            Refine search to taxonomic names<br />
                            <a href="http://gigadb.org/api/search?taxname=Homo%20sapiens&amp;result=dataset" target="_blank">http://gigadb.org/api/search?taxname=Homo%20sapiens&amp;result=dataset</a>
                        </li>
                        <li>
                            Refine search to Authors<br />
                            <a href="http://gigadb.org/api/search?author=Wang%20Jun" target="_blank">http://gigadb.org/api/search?author=Wang%20Jun</a>
                        </li>
                        <li>
                            Refine search to linked manuscript IDs<br />
                            <a href="http://gigadb.org/api/search?manuscript=10.1371/journal.pone.0005795" target="_blank">http://gigadb.org/api/search?manuscript=10.1371/journal.pone.0005795</a>
                        </li>
                        <li>
                            Refine search to dataset types<br />
                            <a href="http://gigadb.org/api/search?datasettype=Genomic" target="_blank">http://gigadb.org/api/search?datasettype=Genomic</a>
                        </li>
                        <li>
                            Refine search to project names<br />
                            <a href="http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample" target="_blank">http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample</a>
                        </li>
                        <li>
                            List all published dataset DOIs (listed in publication date order)<br />
                            <a href="http://gigadb.org/api/list" target="_blank">http://gigadb.org/api/list</a>
                        </li>
                        <li>
                            Dump the database<br />
                            <a href="http://gigadb.org/api/dump" target="_blank">http://gigadb.org/api/dump</a>
                        </li>
                        <li>
                            List all dataset DOI's published in a date range (results ordered by
                            publication date)<br />
                            <a href="http://gigadb.org/api/list?start_date=2018-01-01&amp;end_date=2018-01-30" target="_blank">http://gigadb.org/api/list?start_date=2018-01-01&amp;end_date=2018-01-30</a>
                        </li>
                    </ol>

                    <hr style="border-top-style: dashed;">
                    <h2 class="h4">Command line usage</h2>
                    <p>You can also use the curl commands on the command line to retrieve metadata, e.g.:</p>
                    <pre><code>curl <a href="http://gigadb.org/api/dataset?doi=100051" target="_blank" rel="noopener noreferrer">http://gigadb.org/api/dataset?doi=100051</a></code></pre>
                    <p>If you want to check whether a search will work you can use the <code>-I</code> flag:</p>
                    <pre><code>curl -I <a href="http://gigadb.org/api/dataset?doi=100051" target="_blank" rel="noopener noreferrer">http://gigadb.org/api/dataset?doi=100051</a>
# HTTP/1.1 200 OK
# or
# HTTP/1.1 404 Not Found / HTTP/1.1 500 Internal server error</code></pre>
                </div>
            </div>
            </section>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            if (location.hash != '' && location.hash != null) {
                $("a[href='" + location.hash + "']").click();
            }
        });

        function DatasetFunction() {
            $('ul li').removeClass('active');
            $('#livocabulary').addClass('active');
            $('#search').removeClass('active');
            $('#vocabulary').addClass('active');
            var e = document.getElementById('datasettypes');
            if (!!e && e.scrollIntoView) {
                e.scrollIntoView();
            }
        }
    </script>