<?php
$this->pageTitle = 'GigaDB - About';

//echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/about.html');
?>

<div class="clear"></div>
<div class="content">
    <div class="container">
      <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => 'General information',
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['isActive' => true, 'label' => 'General information'],
          ]
        ]);
        ?>
        <div class="subsection">
            <img src="../images/new_interface_image/about.png" alt="Photo of the GigaDB team">
        </div>
        <div class="section">
            <h2 class="page-subtitle">Database: <em>GigaDB</em></h2>
            <p><a href="/site/index" target="_blank"><em>GigaDB</em></a> is a data repository supporting scientific
                publications in the Life/Biomedical Sciences domain. <a href="/site/index" target="_blank"><em>GigaDB</em></a> organises
                and curates data from individually publishable units into datasets, which are provided openly and in as
                FAIR manner as possible for the global research community. Originally <a href="/site/index" target="_blank"><em>GigaDB</em></a>
                primarily served as a repository to host data and tools associated with articles in <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a>; however, it
                is now accepting datasets that are not associated with <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a>
                articles (see <a href="/site/index" target="_blank"><em>GigaDB</em></a> Submission Criteria below). <a href="/site/index" target="_blank"><em>GigaDB</em></a> defines a dataset as a group of files
                (e.g., sequencing data, analyses, imaging files, software programs) that are related to and support an
                article or study. Through our association with <a href="http://www.datacite.org/">DataCite</a>, each
                dataset in <a href="/site/index" target="_blank"><em>GigaDB</em></a> will be assigned a <a href="http://www.doi.org/">DOI</a> that can be used as a standard citation for future use of
                these data in other articles by the authors and other researchers. Datasets in <a href="/site/index" target="_blank"><em>GigaDB</em></a>
                all require a title that is specific to the dataset, an author list, and an abstract that provides
                information specific to the data included within the set. We encourage detailed information about the
                data we host to be submitted by their creators in ISA-Tab, a format used by the BioSharing and ISA
                Commons communities that we work with to maintain the highest data and metadata standards in our
                journal. To maximize its utility to the research community, all datasets in <a href="/site/index" target="_blank"><em>GigaDB</em></a>
                are placed under a <a href="http://creativecommons.org/publicdomain/zero/1.0/">CC0 waiver</a> (for more
                information on the issues surrounding CC0 and data see <a href="http://dx.doi.org/10.1186/1756-0500-5-494">Hrynaszkiewicz and Cockerill, 2012</a>).</p>
            <hr>
            <h2 class="page-subtitle"><em>GigaDB</em> Submission Criteria</h2>
            <p><a href="/site/index" target="_blank"><em>GigaDB</em></a> has also been accepting submission of datasets
                associated with Open Access publications, and is currently working to scale this out with other
                publishers. As with all current datasets in <a href="/site/index" target="_blank"><em>GigaDB</em></a>
                the authors will be required to make the data available under a CC0 license (except where ethically
                inappropriate, e.g. personal data). In order to complete the dataset review and curation process <em>GigaDB</em>
                staff will require full access to the pre-publication manuscript. Authors and other journals interested
                in this option should contact the <em>GigaScience</em> team via <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a>.</p>
            <hr>
            <h2 class="page-subtitle">Journal: <em>GigaScience</em></h2>
            <p><a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a> is an online,
                open-access journal that includes, as part of its publishing activities, the database <a href="/site/index" target="_blank"><em>GigaDB</em></a>. <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a> is
                co-published in collaboration between <a href="http://www.genomics.cn/">BGI</a> and <a href="https://academic.oup.com/gigascience">Oxford University Press</a>, to meet the needs of a
                new generation of biological and biomedical research as it enters the era of “big-data.” The journal’s
                scope covers studies from the entire spectrum of the life sciences that produce and use large-scale data
                as the center of their work. Data from these articles are hosted in <a href="/site/index" target="_blank"><em>GigaDB</em></a>,
                from where they can be cited to provide a direct link between the study and the data supporting it, as
                well as access to relevant tools for reproducing or reusing these data. The journal also publishes
                commentaries and reviews to provide a forum for discussions surrounding best practices and issues in
                handling large-scale data. See <a href="http://www.gigasciencejournal.com/">http://www.gigasciencejournal.com/</a>
                for additional information about the journal and prospective article submission.</p>
            <hr>
            <h2 class="page-subtitle">Indexing</h2>
            <p><a href="/site/index" target="_blank"><em>GigaDB</em></a> has been included in several external indexing
                systems including <a href="https://toolbox.google.com/datasetsearch">Google Dataset Search</a> (via
                schema.org markup), the <a href="http://search.datacite.org/ui/">DataCite search engine</a>, NCBI <a href="https://datamed.org/">DataMed</a>, the <a href="http://wokinfo.com/products_tools/multidisciplinary/dci/">Data Citation Index (DCI)</a>,
                and Repositive to aid data discovery. <em>GigaDB</em> pushes dataset metadata to DataCite every time a
                DOI is minted, this is exposed and accessible via their <a href="http://oai.datacite.org/">metadata
                    store</a> through the Open Archives Initiative Protocol for Metadata Harvesting (OAI-PMH). The
                records for the datasets, which include authors, institutions, keywords, citations and other metadata,
                are connected to related peer-reviewed literature indexed in their Web of Knowledge database. In
                addition, <em>GigaDB</em> is listed in <a href="https://fairsharing.org/">FAIRsharing</a>, <a href="http://re3data.org/">Re3Data.org</a> and other database catalogues to ensure we reach as
                wide an audience as possible.</p>
            <div>
                <h3 class="sr-only">External Indexing Systems</h3>
                <ul class="logo-list list-unstyled">
                    <li>
                        <a href="http://www.datacite.org/" aria-label="Visit DataCite website">
                            <img src="/images/DataCite_header_final1_1.png" alt="DataCite logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="http://isa-tools.org/" aria-label="Visit ISA Tools website">
                            <img src="/images/isa.jpg" alt="ISA Tools logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="https://fairsharing.org" aria-label="Visit Fairsharing website">
                            <img src="/images/fairshare.png" alt="Fairsharing logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="http://wokinfo.com/products_tools/multidisciplinary/dci/" aria-label="Visit Clarivate Analytics website">
                            <img src="/images/data_citation.png" alt="Data Citation Index logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="http://Re3Data.org" aria-label="Visit Re3Data website">
                            <img src="/images/re3data.png" alt="Re3Data logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="https://repositive.io/" aria-label="Visit Repositive website">
                            <img src="/images/repositive.png" alt="Repositive logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                    <li>
                        <a href="https://datamed.org/" aria-label="Visit DataMed website">
                            <img src="/images/datamed.png" alt="DataMed logo" class="img-responsive logo-list-img">
                        </a>
                    </li>
                </ul>
                <p class="mt-4">This website's content and logo has been published under the Creative Commons CC0 license</p>
            </div>
        </div>
    </div>
</div>