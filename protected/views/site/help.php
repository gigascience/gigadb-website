<?php
$this->pageTitle='GigaDB - Help';
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
                        <h4>Help</h4>
                    </div>
                </section>
                <div class="subsection">
                    <p>The <a href="http://gigadb.org/" target="_blank"><em>GigaDB</em></a> website allows any user to browse, search, view datasets and access data files. If you want to submit a dataset, save searches or be alerted of new content of interest we request that you <a href="/user/create" target="_blank">create an account</a>.</p>
                    <p>A 'Latest news' section will be visible to announce any updates or new features to the database and the RSS feed automatically announces each new dataset release.</p>
                    <p>The <a href="http://gigadb.org/" target="_blank"><em>GigaDB</em></a> homepage allows you to browse datasets by type eg Genomic, Metagenomic, Transcriptomic. Clicking on the DOI (digital object identifier) or image will take you directly to the webpage for the dataset of interest.</p>
                    <p>Alternatively you can use the search functions to find datasets, samples or files of interest.</p>
                </div>
                <section>
                    <ul class="nav nav-tabs nav-border-tabs" role="tablist" id="alltabs">
                        <li id="lisearch" role="presentation" class="active"><a href="#search" aria-controls="search" role="tab" data-toggle="tab"><em>GigaDB</em> search</a></li>
                        <li id="liguideline" role="presentation"><a href="#guidelines" aria-controls="guidelines" role="tab" data-toggle="tab">Submission guidelines</a></li>
                        <li id="livocabulary" role="presentation"><a href="#vocabulary" aria-controls="vocabulary" role="tab" data-toggle="tab">Controlled vocabulary</a></li>
                        <li id="liapi" role="presentation"><a href="#interface" aria-controls="interface" role="tab" data-toggle="tab">Application programming interface</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="search">
                            <h4 style="color: #099242; margin-bottom: 10px;">Search operation</h4>
                            <p>To search across all Dataset, Sample and File records in <em>GigaDB</em>, simply enter a search term in the search bar found at the top of all <em>GigaDB</em> pages.</p>
                            <p>The search is case insensitive which means both uppercase and lowercase keywords will have the same result.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Search result</h4>
                            <p>The search results are grouped by <em>GigaDB</em> Datasets (G), Samples (S) and Files (F).</p>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><div class="text-icon text-icon-sm text-icon-blue">G</div></td>
                                        <td><p>For each dataset result, author names and DOI are displayed. Hovering over dataset name provides the description of dataset. Dataset and sample names are linked to the specific DOI page for those data, as well as file links are provided to download.</p></td>
                                    </tr>
                                    <tr>
                                        <td><div class="text-icon text-icon-sm text-icon-green">S</div></td>
                                        <td><p>For each sample result, the sample name, species name and species ID are displayed with links to the NCBI taxonomy page for the species and to the <em>GigaDB</em> dataset page.</p></td>
                                    </tr>
                                    <tr>
                                        <td><div class="text-icon text-icon-sm text-icon-yellow">F</div></td>
                                        <td><p>For each file result, the file name, file type and file size are displayed with a direct link to the FTP server location of that file.</p></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p>Only those objects that have direct matches are displayed in the search results, i.e. the only Files to be displayed in the search results will be those with matches to the search term, all other files within the same dataset will NOT be displayed.</p>
                            <p>For example, searching for the term "Potato" will return the dataset with the title "Genomic data from the potato" which contains 17 files, however, the search results table will only display 3 of those 17 files because only 3 contain the search term “potato”. To find all data associated with a dataset you must follow the link to the dataset page.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Filtering result</h4>
                            <p style="margin-bottom: 20px;">On the left of the search results you have the option to further refine the results by using the filters. By default all filters are disabled, allowing you to see all search results for your keyword. If you want to hide some results based on some criteria, choose the filter for your criteria, and select the options that match what you want to see.</p>
                            <p style="margin-bottom: 0px;">TFilter options for Datasets:</p>
                            <ol>
                                <li>Dataset Type (<a href="#datasettypes" onclick="DatasetFunction()">Dataset Type</a> controlled vocabulary eg 'Genomic', 'Proteomic')</li>
                                <li>Project (eg 'Genome 10K', '1000 Genomes'</li>
                                <li>External Link Types (Controlled vocabulary: 'Genome Browser' or 'Additional Data')</li>
                                <li>Publication Date (From and To. Format: dd-mm-yyyy)</li>
                            </ol>
                            <p style="margin-bottom: 0px;">Filter options for Samples:</p>
                            <ol><li>Common Name (Internally controlled eg 'Human', 'Mouse')</li></ol>
                            <p style="margin-bottom: 0px;">Filter options for Files:</p>
                            <ol>
                                <li>File Type (<a href="#filetypes" onclick="DatasetFunction()">File Type</a> controlled vocabulary eg 'Alignments', 'Genome sequence', 'SNPs')</li>
                                <li>File Format (<a href="#fileformats" onclick="DatasetFunction()">File format</a> controlled vocabulary eg 'BIGWIG', 'FASTQ', 'VCF')</li>
                                <li>File Size (From and To: Format KB, MB, GB, TB)</li>
                            </ol>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="guidelines">
                            <p><a href="http://gigadb.org/"><em>GigaDB</em></a> is an open-access database. As such, all data submitted to <em>GigaDB</em> must be fully consented for public release (for more information about our data policies, please see our <a href="http://gigadb.org/site/term/" target="_blank">Terms of use page</a>).</p>
                            <p>All sequence, assembly, variation, and microarray data must be deposited in a public database at <a href="http://www.ncbi.nlm.nih.gov/" target="_blank">NCBI</a>, <a href="http://www.ebi.ac.uk/" target="_blank">EBI</a>, or <a href="http://www.ddbj.nig.ac.jp/" target="_blank">DDBJ</a> before you submit them to <a href="http://gigadb.org/" target="_blank"><em>GigaDB</em></a>. In the cases where you would like <em>Giga</em>DB to host files associated with genomic data not fully consented for public release, you must first submit the non-public data to <a href="http://www.ncbi.nlm.nih.gov/gap/" target="_blank">dbGaP</a> or <a href="http://ega-archive.org" target="_blank">EGA</a>.</p>
                            <p><strong>Step 1</strong> - <a href="http://gigadb.org/user/create" target="_blank">Create an account</a> or <a href="http://gigadb.org/site/login" target="_blank">log in</a> to <a href="http://gigadb.org/" target="_blank"><em>Giga</em>DB</a></p>
                            <p><strong>Step 2</strong> - Download and complete the <a href="/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx" target="_blank">Excel template file</a>. Completed example files for the <a href="http://gigadb.org/files/GigaDBUploadForm-example1.xls">E. coli</a> (<a href="http://gigadb.org/dataset/100001" target="_blank">10.5524/100001</a>) and <a href="http://gigadb.org/files/GigaDBUploadForm-example2.xls">Sorghum</a> (<a href="http://gigadb.org/dataset/100012" target="_blank">10.5524/100012</a>) datasets are available.</p>
                            <p>The template file contains:</p>
                            <ol>
                                <li>3 tabs which must all be completed [Study, Samples, Files]</li>
                                <li>4 informational tabs [Samples (info), Files (info), CV, Links]</li>
                            </ol>
                            <p>Mandatory fields are highlighted in yellow.</p>
                            <h5>Study</h5>
                            <p>Required information includes submitter name, email and affiliation, upload status [can we publish this dataset immediately after review (Publish) or should it be held until publication (HUP)], author list, <a href="http://gigadb.org/site/help#datasettype" target="_blank">dataset type(s)</a> (selected from a controlled vocabulary list), dataset title and description, estimated total size of the files that will be submitted and dataset image information.</p>
                            <p>Optional information includes links to additional resources and related manuscripts, accessions for data in other databases (prefixes are found in the Links tab), and <a href="http://gigadb.org/site/help#relation" target="_blank">relationship</a> (if any) to a previously published <em>Giga</em>DB dataset (selected from a controlled vocabulary list).</p>
                            <h5>Samples</h5>
                            <p>Required information includes a sample ID or name (please use an <a href="http://www.ncbi.nlm.nih.gov/biosample" target="_blank">NCBI BioSample ID</a> when possible), species <a href="http://www.ncbi.nlm.nih.gov/Taxonomy" target="_blank">NCBI taxonomy ID</a>, and species common name.</p>
                            <p>Optional information includes sample attributes (these are automatically populated in <em>Giga</em>DB if an <a href="http://www.ncbi.nlm.nih.gov/biosample" target="_blank">NCBI BioSample ID</a> is provided).</p>
                            <h5>Files</h5>
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
                        <div role="tabpanel" class="tab-pane" id="vocabulary">
                            <h4 id="datasettypes" style="color: #099242; margin-bottom: 10px;">Dataset types</h4>
                            <p><strong>Genomic</strong> - Includes all genetic and genomic data eg sequence, assemblies, alignments, genotypes, variation and annotation. Minimal requirements: DNA sequence data eg next-gen raw reads (fastq files) OR assembled DNA sequences (fasta files).</p>
                            <p><strong>Transcriptomic</strong> - Includes all data relating to mRNA. Minimal requirements: RNA sequence data eg next-gen raw reads (fastq files) OR transcript statistics eg RNA coverage/depth.</p>
                            <p><strong>Epigenomic</strong> - Includes methylation and histone modification data. Minimal requirements: Details on methylation sites/status eg qmap files OR details on histone modification sites/status.</p>
                            <p><strong>Metagenomic</strong> - Includes all genetic and genomic data eg sequence, assemblies, alignments, genotypes, variation and annotation from environmental samples. Minimal requirements: Environmental DNA sequence data eg next-gen raw reads (fastq files) OR assembled DNA sequences (fasta files).</p>
                            <p><strong>Metatranscriptomic</strong> - RNA sequences analysis data from environmental samples, e.g. assemblies, expression profiles, variations, annotations etc.</p>
                            <p><strong>Genome mapping</strong> - Datasets containing sequence analysis of genes/conserved sequences mapped to genome(s), and/or optical maps of entire genomes.</p>
                            <p><strong>Imaging</strong> - Includes all imaging data, e.g. light microscopy, 3D imaging, high-resolution images, camera-trap images etc...</p>
                            <p><strong>Software</strong> - Includes datasets that package code together into a useful bioinformatics tool. Note, datasets that contain short scripts are not labelled as software.</p>
                            <p><strong>Virtual-Machine</strong> - Includes software that has been packaged into a virtual machine environment.</p>
                            <p><strong>Workflow</strong> - Datasets that include tools that have been pieced together into a workflow using CWL or other workflow languages.</p>
                            <p><strong>Metabolomic</strong> - Includes analysis of specific metabolights across multiple samples and/or multiple metabolights in fewer samples, usually LC-MS data.</p>
                            <p><strong>Proteomic</strong> - Includes all mass spec data. Minimal requirements: Peptide/protein data eg mass spec.</p>
                            <p><strong>Lipidomic</strong> - Includes datasets with focus on lipid analysis, usually using mass spectrometry.</p>
                            <p><strong>Metabarcoding</strong> - Datasets using barcode sequences for environmental analysis and/or monitoring studies e.g. biodiversity assessment.</p>
                            <p><strong>Metadata</strong> - Denotes datasets where there is a focus on collection of metadata e.g. ontologies or metadata standards.</p>
                            <p><strong>Network-Analysis</strong> - Datasets containing analysis of biological networks, either species interactions or at the molecular level.</p>
                            <p><strong>Neuroscience</strong> - Includes all datasets that hold data about brains/neurons, can be imaging, molecular and/or software/tools.</p>
                            <p><strong>Electro-encephalography (EEG)</strong> - Datasets containing or using EEG data.</p>
                            <p><strong>Phenotyping</strong> - Includes datasets with extensive phenotypic information about the samples/specimens.</p>
                            <p><strong>Ecology</strong> - Data used/collected for ecological studies.</p>
                            <p><strong>Climate</strong> - Data used/collected for climate studies.</p>
                            <p>Additional dataset types can be added, upon review, as new submissions are received.</p>
                            <h4 id="filetypes" style="color: #099242; margin-bottom: 10px;">File types</h4>
                            <p>File types and examples of associated file extensions:</p>
                            <p><strong>Alignments:</strong> .bam, .chain, .maf, .net, .sam</p>
                            <p><strong>Allele frequencies:</strong> .frq</p>
                            <p><strong>Annotation:</strong> .gff, .ipr, .kegg, .wego</p>
                            <p><strong>Coding sequence:</strong> .cds, .fa</p>
                            <p><strong>InDels:</strong> .gff, .txt, .vcf</p>
                            <p><strong>ISA-Tab:</strong> see <a href="http://isa-tools.org/format/specification.html" target="_blank">ISA tools</a></p>
                            <p><strong>Genome assembly:</strong> .agp, .contig, .depth, .fa, .length, .scafseq</p>
                            <p><strong>Genome sequence:</strong> .fastq, .fq</p>
                            <p><strong>Haplotypes:</strong> .haplotype</p>
                            <p><strong>Methylome data:</strong> .fa, .qmap, .rpm, .txt</p>
                            <p><strong>Protein sequence:</strong> .fa, .pep</p>
                            <p><strong>Readme:</strong> .pdf, .txt</p>
                            <p><strong>SNPs:</strong> .annotation, .gff, .txt, .vcf</p>
                            <p><strong>SVs:</strong> .gff, .txt, .vcf</p>
                            <p><strong>Transcriptome data:</strong> .depth, .rpkm, .wig</p>
                            <p><strong>Other:</strong> .xls, .pdf, .txt</p>
                            <p>Additional file types can be added, upon review, as new submissions are received.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 id="fileformats" style="color: #099242; margin-bottom: 10px;">File formats</h4>
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
                            <p id="agp"><strong>AGP</strong> (.agp) - the Accessioned Golden Path (AGP) file describes the assembly of a larger sequence object from smaller objects:</p>
                            <pre>
chr1 1       1972671 0 W scaffold43  1 1972671 m
chr1 1972672 3061819 1 W scaffold8   1 1089148 p
chr1 3061820 3181505 2 W scaffold548 1 119686  m
chr1 3181506 4176151 3 W scaffold313 1 994646  m</pre>
                            <p>The large object can be a contig, a scaffold (supercontig), or a chromosome. See <a href="http://www.ncbi.nlm.nih.gov/projects/genome/assembly/agp/AGP_Specification.shtml" target="_blank">AGP Specification v2.0</a></p>
                            <p id="bam"><strong>BAM</strong> (.bam) - the <a href="http://genome.sph.umich.edu/wiki/BAM" target="_blank">Binary Alignment/Map (BAM) format</a> is the compressed binary version of the Sequence Alignment/Map (SAM) format, a compact and index-able representation of nucleotide sequence alignments.</p>
                            <p id="bigwig"><strong>BIGWIG</strong> (.bw) - the <a href="http://genome.ucsc.edu/goldenPath/help/bigWig.html" target="_blank">BIGWIG format</a> is for storing dense, continuous data (such as GC percent, probability scores, and transcriptome data) that will be displayed in the UCSC Genome Browser as a graph. BIGWIG files are created initially from wiggle (WIG) type files, using the program wigToBigWig.</p>
                            <p id="chain"><strong>CHAIN</strong> (.chain) - the  <a href="http://genome.ucsc.edu/goldenPath/help/chain.html" target="_blank">CHAIN format</a> describes a pairwise alignment that allow gaps in both sequences simultaneously and is used by the UCSC Genome Browser.</p>
                            <p id="contig"><strong>CONTIG</strong> (.contig) - the <a href="http://soap.genomics.org.cn/soapdenovo.html" target="_blank">CONTIG format</a> is a direct output from the SOAPdenovo alignment program:</p>
                            <pre>
>1 length 32 cvg_0.0_tip_0
GAGAACGGCGAAGCCTGCTCGGGCCCGTTATA
>3 length 32 cvg_23.0_tip_0
TAGCAGCGATTTGATCAAACTCAATCTTACCG
>5 length 32 cvg_40.0_tip_0
GGTAAGATTGAGTTTGATCAAATCGCTGCTAT</pre>
                            <p id="excel"><strong>EXCEL</strong> (.xls, .xlsx) - Microsoft office spreadsheet files</p>
                            <p id="fasta"><strong>FASTA</strong> (.fasta, .fa, .seq, .cds, .pep, .scafseq [SOAPdenovo output file - sequence of each scaffold]) -  <a href="http://www.ebi.ac.uk/help/formats.html#fasta" target="_blank">FASTA</a> is a text-based format for representing either nucleotide sequences or peptide sequences.</p>
                            <p id="fastq"><strong>FASTQ</strong> (.fq, .fastq) - the <a href="http://maq.sourceforge.net/fastq.shtml" target="_blank">FASTQ format</a> stores sequences  (usually nucleotide sequence) and Phred qualities in a single file.</p>
                            <p id="gff"><strong>GFF</strong> (.gff) - The <a href="http://www.sanger.ac.uk/resources/software/gff/" target="_blank">General Feature Format (GFF)</a> is used for describing genes and other features of DNA, RNA and protein sequences.</p>
                            <p id="ipr"><strong>IPR</strong> (.ipr) - the <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a> consists of the protein ID, followed by column(s) that are the IPR (<a href="http://www.ebi.ac.uk/interpro/" target="_blank">InterPro</a>) ID(s):</p>
                            <pre>
CR_ENSP00000334840
CR_ENSMMUP00000018123 IPR000504 IPR003954
CR_ENSP00000333725    IPR001781 IPR015880 IPR007087 IPR001909</pre>
                            <p>See <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a></p>
                            <p id="kegg"><strong>KEGG</strong> (.kegg) - the <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a> consists of the protein ID, followed by column(s) that are the <a href="http://www.genome.jp/kegg/" target="_blank">KEGG</a> (Kyoto Encyclopedia of Genes and Genomes) ID(s):</p>
                            <pre>
CR_ENSMMUP00000031408 ko03010
CR_ENSP00000364815    ko00970 ko00290
CR_ENSP00000414605    ko05146 ko04510 ko04512</pre>
                            <p>See <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a></p>
                            <p id="maf"><strong>MAF</strong> (.maf) - the <a href="http://genome.ucsc.edu/FAQ/FAQformat.html#format5" target="_blank">Multiple Alignment Format (MAF)</a> stores a series of multiple alignments at the DNA level between entire genomes.</p>
                            <p id="net"><strong>NET</strong> (.net) - the <a href="http://genome.ucsc.edu/goldenPath/help/net.html" target="_blank">NET file format</a> is used to describe the axtNet data that underlie the net alignment annotations in the UCSC Genome Browser.</p>
                            <p id="pdf"><strong>PDF</strong> (.pdf) - portable document format</p>
                            <p id="png"><strong>PNG</strong> (.png) - portable network graphics</p>
                            <p id="qmap"><strong>QMAP</strong> (.qmap) - <a href="http://yh.genomics.org.cn/Format%20of%20qmap%20file.txt" target="_blank">QMAP</a> files are generated for methylation data from an internal <a href="http://www.genomics.cn/en" target="_blank">BGI</a> pipeline.</p>
                            <p id="qual"><strong>QUAL</strong> (.qual) - the <a href="http://www.broadinstitute.org/crd/wiki/index.php/Qual" target="_blank">QUAL file format</a> represents base quality score file for NextGen data (similar in format to fasta).</p>
                            <p id="rpkm"><strong>RPKM</strong> (.rpkm) - Gene expression levels are calculated by <a href="http://sourceforge.net/apps/mediawiki/seqgene/index.php?title=SeqGene#.rpkm:_expression_estimates_generated_by_rpkm.py" target="_blank">Reads Per Kilobase per Million (RPKM) mapped reads</a> eg 1kb transcript with 1000 alignments in a sample of 10 million reads (out of which 8 million reads can be mapped) will have RPKM = 1000/(1 * 8) = 125:</p>
                            <pre>
ENSP00000379387 15.5651433366423 6002951 289 3093
ENSP00000349977 24.7483107230444 6002951 398 2679
ENSP00000368887 24.6477413647837 6002951 174 1176</pre>
                            <p id="sam"><strong>SAM</strong> (.sam) - the <a href="http://genome.sph.umich.edu/wiki/SAM" target="_blank">Sequence Alignment/Map (SAM) format</a> is a TAB-delimited text format consisting of a header section, which is optional, and an alignment section. Most often it is generated as a human readable version of its sister <a href="http://gigadb.org/site/help#bam" target="_blank">BAM</a> format, which stores the same data in a compressed, indexed, binary form. Currently, most SAM format data is output from aligners that read <a href="http://gigadb.org/site/help#fastq" target="_blank">FASTQ</a> files and assign the sequences to a position with respect to a known reference genome. In the future, SAM will also be used to archive unaligned sequence data generated directly from sequencing machines. See <a href="http://www.ncbi.nlm.nih.gov/pmc/articles/PMC2723002/" target="_blank">The Sequence Alignment/Map format and SAMtools</a></p>
                            <p id="tar"><strong>TAR</strong> (.tar) - an archive containing other files</p>
                            <p id="text"><strong>TEXT</strong> (.doc, .readme, .text, .txt) - a text file</p>
                            <p id="vcf"><strong>VCF</strong> (.vcf) -  the <a href="http://www.1000genomes.org/wiki/Analysis/Variant%20Call%20Format/vcf-variant-call-format-version-40" target="_blank">Variant Call Format (VCF)</a> is a text file format for representing eg SNPs, InDels, CNVs, SVs, microsatellites, genotypes.</p>
                            <p id="wego"><strong>WEGO</strong> (.wego) - the <a href="http://wego.genomics.org.cn/cgi-bin/wego/Documents.pl" target="_blank">Web Gene Ontology (WEGO) Annotation format</a> consists of the protein ID, followed by column(s) that are the <a href="http://www.geneontology.org/" target="_blank">GO</a> ID(s):</p>
                            <pre>
Bmb015379_2_IPR001092
Bmb003749_1_IPR006329 GO:0009168 GO:0003876
Bmb006173_1_IPR000909 GO:0007165 GO:0004629 GO:0007242<</pre>
                            <p>See <a href="http://www.ncbi.nlm.nih.gov/pubmed/16845012?dopt=Abstract" target="_blank">WEGO: a web tool for plotting GO annotations</a></p>
                            <p id="wig"><strong>WIG</strong> (.wig) - the output file from <a href="http://tophat.cbcb.umd.edu/" target="_blank">TopHat</a> is a <a href="http://genome.ucsc.edu/goldenPath/help/wiggle.html" target="_blank">UCSC wigglegram</a> of alignment coverage.</p>
                            <p id="unknown"><strong>UNKNOWN</strong> - any file format not in this list</p>
                            <p id="xml"><strong>XML</strong> (.xml) - <a href="http://www.w3schools.com/xml/" target="_blank">eXtensible Markup Language</a></p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Upload status</a></h4>
                            <p><strong>Publish: </strong>this dataset is fully consented for immediate release upon <em>Giga</em>DB approval </p>
                            <p><strong>HUP: </strong>this dataset should be Held Until Publication (HUP)</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">DOI relationship</h4>
                            <p>The DOI relationship vocabulary is taken from the <a href="http://schema.datacite.org/meta/kernel-2.2/doc/DataCite-MetadataKernel_v2.2.pdf" target="_blank">DataCite</a> 'relationType' schema property (ID=12.2).</p>
                            <p>Definition: Description of the relationship of the resource being registered (A) and the related resource (B).</p>
                            <p><strong>IsSupplementTo:</strong> indicates that A is a supplement to B</p>
                            <p><strong>IsSupplementedBy:</strong> indicates that B is a supplement to A</p>
                            <p><strong>IsNewVersionOf:</strong> indicates A is a new edition of B, where the new edition has been modified or updated</p>
                            <p><strong>IsPreviousVersionOf:</strong> indicates A is a previous edition of B</p>
                            <p><strong>IsPartOf:</strong> indicates A is a portion of B; may be used for elements of a series</p>
                            <p><strong>HasPart:</strong> indicates A includes the part B</p>
                            <p><strong>References:</strong> indicates B is used as a source of information for A</p>
                            <p><strong>IsReferencedBy:</strong> indicates A is used as a source of information by B</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Missing Value reporting</h4>
                            <p>For attributes (sample, dataset or files) that have some or all values missing please use the following controlled value terms to describe the exact reason for the missing value.</p>
                            <p><strong>not applicable:</strong> information is inappropriate to report, often this attribute can be removed entirely.</p>
                            <p><strong>restricted access:</strong> information exists but cannot be released openly because of privacy concerns</p>
                            <p><strong>not provided:</strong> information is not available at the time of submission, a value may be provided at the later stage</p>
                            <p><strong>not collected:</strong> information was not collected and will therefore never be available</p>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="interface">
                            <h4 style="color: #099242; margin-bottom: 10px;">Availability</h4>
                            <p>The current API version is available on our main production database. This version will be periodically updated with new additional functionality and we will whenever possible maintain backwards compatibility. Occasionally this may not be possible and for this reason we recommend regularly checking and updating your usage of our API. </p>
                            <p>The basic functionality of the API is to retrieve dataset metadata held in <em>GigaDB</em>. The actual data files will still need to be pulled by FTP, but you can gather the exact FTP locations from the metadata using the API, then use that to pull only the files you actually need/want.</p>
                            <p>Search function is based on the web-search function and will therefore give the same results.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Comments and Bug reporting</h4>
                            <p>The GigaScience github issue for the API works is here:</p>
                            <p><a href="https://github.com/gigascience/gigadb-website/issues/27" target="_blank">https://github.com/gigascience/gigadb-website/issues/27</a></p>
                            <p>Please add feedback / comments/ questions to that issue.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Summary</h4>
                            <p>It is currently possible to search "all" fields, or to specify one of a select few fields to search.</p>
                            <p>It is possible to have results return all metadata for each dataset with "hits" to the search term, or to specify a particular portion of the metadata, these portions are currently "dataset", "sample" and "file", which is in alignment with the same functionality on the web-search tool. The default is to return results as <a href="https://sites.google.com/a/gigasciencejournal.com/gigascience/public-pages/xml-schema">GigaDB v3 XML</a></p>
                            <p>It is planned that we will have the option to specify the format to be GigaDBv3-JSON or ISA2.0-JSON in the future, but that has not been implemented yet.</p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Terminology</h4>
                            <p>To specify exact fields to return data from, use terms; dataset?=, sample?=, file?=, (or experiment?=*)</p>
                            <p>* - experiment will be implemented in the future</p>
                            <p>To search for datasets without the ID's, use the term search?keyword=</p> 
                            <p>To search by specific attributes use search?&lt;attribute_name&gt;=</p>
                            <p>Available <strong>attribute_name</strong> to search include:</p>
                            <p><strong>taxno</strong> = Taxonomic ID (NCBI)</p>
                            <p><strong>taxname</strong> = species name (nb must exact spelling, no synonyms searched)</p>
                            <p><strong>author</strong> = restricts search to the author table</p>
                            <p><strong>datasettype</strong> = restricts search to the types of datasets, e.g. metagenomic, genomic, transcriptomic etc..</p>
                            <p><strong>manuscript</strong> = restricts search to the manuscript ID associated with <em>GigaDB</em> dataset(s) e.g. search?manuscript=10.1186/2047-217X-3-21</p>
                            <p><strong>project</strong> = restricts search to the project name, e.g. Genome 10K</p>
                            <p>eg..../search?taxno=9606</p>
                            <p>To specify results to be returned are ONLY a particular level of data, add the phrase <strong>&amp;results=dataset</strong> ,or file or sample: </p>e.g.
                            <p><a href="http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample" target="_blank">http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample</a></p>
                            <p>NB - the search still looks everywhere, but the results returned are only those samples that are in datasets that are found by the search.</p>
                            <p><strong>Default results are "dataset" only.</strong></p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Examples</h4>
                            <p>1. retrieve known datasets by doi</p>
                            <p><a href="http://gigadb.org/api/dataset?doi=100051" target="_blank">http://gigadb.org/api/dataset?doi=100051</a></p>
                            <p>2. retrieve samples from a known DOI</p>
                            <p><a href="http://gigadb.org/api/sample?doi=100051" target="_blank">http://gigadb.org/api/sample?doi=100051</a></p>
                            <p>3. retrieve file information from a known DOI</p>
                            <p><a href="http://gigadb.org/api/file?doi=100051" target="_blank">http://gigadb.org/api/file?doi=100051</a></p>
                            <p>4. Search all <em>GigaDB</em> by keyword, return only the top level dataset metadata</p>
                            <p><a href="http://gigadb.org/api/search?keyword=chimp&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=dataset</a></p>
                            <p>5.Search all <em>GigaDB</em> by keyword, return only the sample level metadata</p>
                            <p><a href="http://gigadb.org/api/search?keyword=chimp&amp;result=sample" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=sample</a></p>
                            <p>6.Search all <em>GigaDB</em> by keyword, return only the file level metadata</p>
                            <p><a href="http://gigadb.org/api/search?keyword=chimp&amp;result=file" target="_blank">http://gigadb.org/api/search?keyword=chimp&amp;result=file</a></p>
                            <p>7. refine search to just the title of the dataset</p>
                            <p><a href="http://gigadb.org/api/search?keyword=title:human&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=title:human&amp;result=dataset</a></p>
                            <p>8. refine search to the descriptions of datasets</p>
                            <p><a href="http://gigadb.org/api/search?keyword=description:human&amp;result=dataset" target="_blank">http://gigadb.org/api/search?keyword=description:human&amp;result=dataset</a></p>
                            <p>9.refine search to NCBI taxonomic ID</p>
                            <p><a href="http://gigadb.org/api/search?taxno=9606&amp;result=dataset" target="_blank">http://gigadb.org/api/search?taxno=9606&amp;result=dataset</a></p>
                            <p>10. refine search to taxonomic names</p>
                            <p><a href="http://gigadb.org/api/search?taxname=Homo%20sapiens&amp;result=dataset" target="_blank">http://gigadb.org/api/search?taxname=Homo%20sapiens&amp;result=dataset</a></p>
                            <p>11. refine search to Authors</p>
                            <p><a href="http://gigadb.org/api/search?author=Wang%20Jun" target="_blank">http://gigadb.org/api/search?author=Wang%20Jun</a></p>
                            <p>12. refine search to linked manuscript IDs</p>
                            <p><a href="http://gigadb.org/api/search?manuscript=10.1371/journal.pone.0005795" target="_blank">http://gigadb.org/api/search?manuscript=10.1371/journal.pone.0005795</a></p>
                            <p>13. refine search to dataset types</p>
                            <p><a href="http://gigadb.org/api/search?datasettype=Genomic" target="_blank">http://gigadb.org/api/search?datasettype=Genomic</a></p>
                            <p>14. refine search to project names</p>
                            <p><a href="http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample" target="_blank">http://gigadb.org/api/search?project=Genome%2010K&amp;result=sample</a></p>
                            <p>15. list all published dataset DOIs (listed in publication date order)</p>
                            <p><a href="http://gigadb.org/api/list" target="_blank">http://gigadb.org/api/list</a></p>
                            <p>16. dump the database</p>
                            <p><a href="http://gigadb.org/api/dump" target="_blank">http://gigadb.org/api/dump</a></p>
                            <p>17. list all dataset DOI's published in a date range (results ordered by publication date)</p>
                            <p><a href="http://gigadb.org/api/list?start_date=2018-01-01&end_date=2018-01-30" target="_blank">http://gigadb.org/api/list?start_date=2018-01-01&end_date=2018-01-30</a></p>
                            <hr style="border-top-style: dashed;">
                            <h4 style="color: #099242; margin-bottom: 10px;">Command line usage</h4>
                            <p>You can also use the curl commands on the command line to retrieve metadata :</p>
                            <p>eg.</p>
                            <p>curl <a href="http://gigadb.org/api/dataset?doi=100051" target="_blank">http://gigadb.org/api/dataset?doi=100051</a></p>
                            <p>If you want to check whether a search will work you can use the -I flag:</p>
                            <p>curl -I <a href="http://gigadb.org/api/dataset?doi=100051" target="_blank">http://gigadb.org/api/dataset?doi=100051</a></p>
                            <p>HTTP/1.1 200 OK</p>
                            <p>or </p>
                            <p>HTTP/1.1 404 Not Found / HTTP/1.1 500 Internal server error</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
<script type="text/javascript">
$(document).ready(function () {
    if(location.hash != '' && location.hash != null){
        $("a[href='"+location.hash+"']").click();
    }
});

function DatasetFunction(){
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
    
