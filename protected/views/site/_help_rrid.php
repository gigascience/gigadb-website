<section class="m-0 prose" aria-labelledby="rridTitle">
  <h2 id="rridTitle">
    GigaScience <abbr title="Research Resource Identification">RRID</abbr> list
  </h2>

  <h3>What are <abbr>RRID</abbr>s?</h3>

  <p>
    The
    <def id="rridDef"
      ><b>R</b>esearch <b>R</b>esource <b>ID</b>entification initiative
      (<abbr>RRID</abbr>)</def
    >
    is designed to help researchers sufficiently cite the key resources used to
    produce the scientific findings reported in the biomedical literature.
    Resources (e.g. antibodies, model organisms, and software projects) reported
    in the biomedical literature often lack sufficient detail to enable
    reproducibility or reuse. For example, catalog numbers for antibody reagents
    are infrequently reported, and the version numbers for software programs
    used for data analysis are often omitted. This has been called out as a
    problem, serious enough by the NIH, to introduce new guidelines for Rigor
    and Transparency. To make it easy for authors to find the appropriate RRIDs
    and to format their citations, the Resource Identification Portal was
    created, where authors can search across all sources from a single location.
    In addition to facilitating reproducibility and reuse, the inclusion of RRID
    citations in the literature allows resource providers, funders and others to
    better track usage and impact. GigaScience encourages their use (see our
    minimum reporting standards
    <a
      href="https://academic.oup.com/gigascience/pages/Minimum_Standards_of_Reporting_Checklist"
      target="_blank"
      rel="noopener noreferrer"
      >reproducibility checklist</a
    >), and this handy page includes many of the RRIDs useful for bioinformatics
    studies here for easy reference.
  </p>

  <h3>What is SciCrunch?</h3>

  <p>
    The portal to search and register RRIDs.
    <a
      href="https://en.wikipedia.org/wiki/SciCrunch"
      target="_blank"
      rel="noopener noreferrer"
      >Wikipedia calls SciCrunch</a
    >: a collaboratively edited knowledge base about scientific resources, a
    community portal for researchers and a content management system for data
    and databases. It is intended to provide a common source of data to the
    research community and the data about Research Resource Identifiers (RRIDs),
    which can be used in scientific publications. Hosted by the University of
    California, San Diego, SciCrunch was also designed to help communities of
    researchers create their own portals to provide access to resources,
    databases and tools of relevance to their research areas.
  </p>

  <h3>How do you include these in your papers?</h3>

  <p>
    These needed to be listed after resources in brackets. If you are citing
    papers for these resources, the RRID do not replace these, and both should
    be included. This can be included in the methods section of the paper
    similar to the RRIDs included here:
  </p>

  <p>
    The filtered reads were assembled using SOAPdenovo2 v2.04.4 software
    (SOAPdenovo2 , RRID:SCR_014986) [13] with optimized parameters (pregraph -K
    79 -d 1; contig -M 1; scaff -F -b 1.5 -p 16) to generate contigs and
    original scaffolds. The completeness of our assembly was evaluated by using
    and BUSCO (BUSCO, RRID:SCR_015008) [14].
  </p>

  <p>
    To search for or register new RRIDs see:
    <a href="https://scicrunch.org/" target="_blank" rel="noopener noreferrer"
      >https://scicrunch.org/</a
    >
    and Add Resource:
    <a
      href="https://scicrunch.org/browse/resourcedashboard"
      target="_blank"
      rel="noopener noreferrer"
      >https://scicrunch.org/browse/resourcedashboard</a
    >
  </p>

  <h3>Common bioinformatics RRIDs</h3>

  <p>
    Below we list a variety of commonly used resources that authors may find
    helpful, or use the search function at
    <a href="https://scicrunch.org/" target="_blank" rel="noopener noreferrer"
      >https://scicrunch.org/</a
    >
    to discover other resources or register your own.
  </p>

  <h3>BGI SOAP suite</h3>
  <ul>
    <li>SOAPdenovo2 , RRID:SCR_014986</li>
    <li>SOAP , RRID:SCR_000689</li>
    <li>SOAPaligner/soap2 , RRID:SCR_005503</li>
    <li>SOAP3 , RRID:SCR_005502</li>
    <li>SOAPdenovo-Trans , RRID:SCR_013268</li>
    <li>SOAPfuse , RRID:SCR_000078</li>
    <li>SOAPfusion , RRID:SCR_000079</li>
    <li>SOAPindel , RRID:SCR_005272</li>
    <li>SOAPsnp , RRID:SCR_010602</li>
    <li>SOAPsplice , RRID:SCR_013253</li>
    <li>SOAPnuke, RRID:SCR_015025</li>
    <li>GapCloser, RRID:SCR_015026</li>
  </ul>

  <h3>Other Assembly/alignment tools</h3>

  <ul>
    <li>ABySS , RRID:SCR_010709</li>

    <li>ALLPATHS-LG , RRID:SCR_010742</li>

    <li>BWA , RRID:SCR_010910</li>

    <li>Celera assembler, RRID:SCR_010750</li>

    <li>Canu, RRID:SCR_015880</li>

    <li>Falcon, RRID:SCR_016089</li>

    <li>GATK , RRID:SCR_001876</li>

    <li>GeneWise, RRID:SCR_015054</li>

    <li>GMcloser, RRID:SCR_000646</li>

    <li>Hmmer, RRID:SCR_005305</li>

    <li>IDBA-UD, RRID:SCR_011912</li>

    <li>ISAAC, RRID:SCR_012772</li>

    <li>JOINMAP , RRID:SCR_009248</li>

    <li>LAST , RRID:SCR_006119</li>

    <li>MaSuRCA, RRID:SCR_010691</li>

    <li>MIRA, RRID:SCR_010731</li>

    <li>Oases, RRID:SCR_011896</li>

    <li>PASA, RRID:SCR_014656</li>

    <li>Pilon , RRID:SCR_014731</li>

    <li>Platanus, RRID:SCR_015531</li>

    <li>SPAdes , RRID:SCR_000131</li>

    <li>SSPACE , RRID:SCR_005056</li>

    <li>T-Coffee, RRID:SCR_011818</li>

    <li>Trinity , RRID:SCR_013048</li>

    <li>Velvet, RRID:SCR_010755</li>
  </ul>

  <h3>Tuxedo Suite</h3>

  <ul>
    <li>Bowtie , RRID:SCR_005476</li>

    <li>Cufflinks , RRID:SCR_014597</li>

    <li>Cuffdiff , RRID:SCR_001647</li>

    <li>CummeRbund , RRID:SCR_014568</li>

    <li>HiSat2, RRID:SCR_015530</li>

    <li>StringTie, RRID:SCR_016323</li>

    <li>TopHat , RRID:SCR_013035</li>
  </ul>

  <h3>Benchmarking/QC</h3>

  <ul>
    <li>BUSCO , RRID:SCR_015008</li>

    <li>CEGMA, RRID:SCR_015055</li>

    <li>FASTAX Toolkit, RRID:SCR_015042</li>

    <li>FastQC , RRID:SCR_014583</li>

    <li>Kraken, RRID:SCR_005484</li>

    <li>QUAST, RRID:SCR_001228</li>

    <li>Trimmomatic , RRID:SCR_011848</li>
  </ul>

  <h3>Other genomics resources</h3>

  <ul>
    <li>AdapterRemoval, RRID:SCR_011834</li>

    <li>bcl2fastq, RRID:SCR_015058</li>

    <li>BEDTools , RRID:SCR_006646</li>

    <li>cutadapt, RRID:SCR_011841</li>

    <li>FreeBayes, RRID:SCR_010761</li>

    <li>Jellyfish, RRID:SCR_005491</li>

    <li>PBJelly, RRID:SCR_012091</li>

    <li>Picard, RRID:SCR_006525</li>

    <li>Poretools, RRID:SCR_015879</li>

    <li>QuorUM, RRID:SCR_011840</li>

    <li>SAMTOOLS , RRID:SCR_002105</li>

    <li>VCFtools, RRID:SCR_001235</li>
  </ul>

  <h3>Annotation</h3>

  <ul>
    <li>ANNOVAR , RRID:SCR_012821</li>

    <li>Augustus: Gene Prediction , RRID:SCR_008417</li>

    <li>Barrnap, RRID:SCR_015995</li>

    <li>Blast2GO, RRID:SCR_005828</li>

    <li>DAVID, RRID:SCR_001881</li>

    <li>DOGMA, RRID:SCR_015060</li>

    <li>eggNOG, RRID:SCR_002456</li>

    <li>Ensembl , RRID:SCR_002344</li>

    <li>GENSCAN , RRID:SCR_012902</li>

    <li>GlimmerHMM , RRID:SCR_002654</li>

    <li>GO , RRID:SCR_002811</li>

    <li>Infernal , RRID:SCR_011809</li>

    <li>InterPro , RRID:SCR_006695</li>

    <li>InterProScan , RRID:SCR_005829</li>

    <li>JCVI TIGRFAMS, RRID:SCR_005493</li>

    <li>KEGG , RRID:SCR_012773</li>

    <li>LTR_Finder, RRID:SCR_015247</li>

    <li>MAKER, RRID:SCR_005309</li>

    <li>MUSCLE , RRID:SCR_011812</li>

    <li>PANTHER , RRID:SCR_004869</li>

    <li>PASA, RRID:SCR_014656</li>

    <li>Pfam , RRID:SCR_004726</li>

    <li>PRINTS , RRID:SCR_003412</li>

    <li>PROSITE , RRID:SCR_003457</li>

    <li>RepeatModeler, RRID:SCR_015027</li>

    <li>RepeatMasker , RRID:SCR_012954</li>

    <li>RepeatScout , RRID:SCR_014653</li>

    <li>Rfam , RRID:SCR_007891</li>

    <li>SMART , RRID:SCR_005026</li>

    <li>SNAP - SNP Annotation and Proxy Search, RRID:SCR_002127</li>

    <li>SUPERFAMILY, RRID:SCR_007952</li>

    <li>Tree families database , RRID:SCR_013401</li>

    <li>tRNAscan-SE, RRID:SCR_010835</li>

    <li>UniProt , RRID:SCR_002380</li>

    <li>
      WebApollo: A Web-Based Sequence Annotation Editor for Community Annotation
      , RRID:SCR_005321
    </li>
  </ul>

  <h3>BLAST tools</h3>

  <ul>
    <li>NCBI BLAST , RRID:SCR_004870</li>

    <li>BLASTN, RRID:SCR_001598</li>

    <li>BLASTP , RRID:SCR_001010</li>

    <li>BLASTX , RRID:SCR_001653</li>

    <li>TBLASTN , RRID:SCR_011822</li>

    <li>TBLASTX , RRID:SCR_011823</li>

    <li>BLAT, RRID:SCR_011919</li>
  </ul>

  <h3>Metagenomics</h3>

  <ul>
    <li>HUMAnN2, RRID:SCR_016280</li>

    <li>QIIME, RRID:SCR_008249</li>

    <li>MOCAT, RRID:SCR_011943</li>

    <li>MetaPhlAn, RRID:SCR_004915</li>

    <li>MG-RAST , RRID:SCR_004814</li>

    <li>mothur , RRID:SCR_011947</li>
  </ul>

  <h3>Languages and workflows</h3>

  <ul>
    <li>EMBOSS, RRID:SCR_008493</li>

    <li>MATLAB , RRID:SCR_001622</li>

    <li>Python Programming Language , RRID:SCR_008394</li>

    <li>R Project for Statistical Computing , RRID:SCR_001905</li>

    <li>RStudio, RRID:SCR_000432</li>

    <li>Shiny, RRID:SCR_001626</li>

    <li>Bioconductor , RRID:SCR_006442</li>

    <li>Galaxy , RRID:SCR_006281</li>
  </ul>

  <h3>Phylogenetics and phylogenomics resources</h3>

  <ul>
    <li>BEAST , RRID:SCR_010228</li>

    <li>Eigensoft , RRID:SCR_004965</li>

    <li>MAFFT, RRID:SCR_011811</li>

    <li>MrBayes , RRID:SCR_012067</li>

    <li>MEGA Software , RRID:SCR_000667</li>

    <li>OrthoMCL DB: Ortholog Groups of Protein Sequences, RRID:SCR_007839</li>

    <li>PAML , RRID:SCR_014932</li>

    <li>PhyML , RRID:SCR_014629</li>

    <li>RAxML, RRID:SCR_006086</li>

    <li>RSEM , RRID:SCR_013027</li>

    <li>SeaView, RRID:SCR_015059</li>
  </ul>

  <h3>Other software</h3>

  <ul>
    <li>BioMart Project , RRID:SCR_002987</li>

    <li>Cytoscape, RRID:SCR_003032</li>

    <li>Easyfig, RRID:SCR_013169</li>

    <li>Gephi, RRID:SCR_004293</li>

    <li>GigaDB , RRID:SCR_004002</li>

    <li>ImageJ , RRID:SCR_003070</li>

    <li>PLINK , RRID:SCR_001757</li>
  </ul>

  <h3>Useful wet lab RRIDs for cell lines, etc.</h3>

  <ul>
    <li>
      NA12878/CVCL_7526 (GIAB) cell line = (Coriell Cat# GM12878,
      RRID:CVCL_7526)
    </li>

    <li>HeLa = (CLS Cat# 300194/p772_HeLa, RRID:CVCL_0030)</li>
  </ul>
</section>
