<?php
$this->pageTitle='GigaDB - About';

//echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/about.html');
?>

<div class="clear"></div>
<div class="row">
	<div class="about-tabs-container">
	    <a class="btn about-tabs tab-active" href="/site/about">General Information</a>
	    <a class="btn about-tabs" href="/site/advisory">Advisory Panel</a>
	    <a class="btn about-tabs" href="/site/faq">FAQ</a>
	</div>
	<div class="about1">  
		<a href="/site/about#journal">Journal:GigaScience</a>
		<br>
		<a href="/site/about#database">Database:GigaDB</a>
		<br>
		<a href="/site/about#citation">Thomson Recuter Data Citation Index</a>
		<br>
		<br>
		<a name="journal"></a>
		<h2>Journal: <em>GigaScience</em></h2>
		<p>
			<a href="http://www.gigasciencejournal.com/" target="_blank">
				<em>GigaScience</em>
			</a> is an online, open-access journal that includes, as part of its publishing activities, the database 
			<a href="/site/index" target="_blank"><em>GigaDB</em></a>. 
			<em>GigaScience</em> is co-published in collaboration between 
			<a href="http://www.genomics.cn/" target="_blank">BGI</a> and 
			<a href="http://www.biomedcentral.com/" target="_blank">BioMed Central</a>, 
			to meet the needs of a new generation of biological and biomedical research as it enters the era of “big-data.” 
			The journal’s scope covers studies from the entire spectrum of the life sciences that produce and use large-scale data as 
			the center of their work. Data from these articles are hosted in <a href="/site/index/" target="_blank"><em>GigaDB</em></a>, 
			from where they can be cited to provide a direct link between the study and the data supporting it, as well as access to 
			relevant tools for reproducing or reusing these data. The journal also publishes commentaries and reviews to provide a 
			forum for discussions surrounding best practices and issues in handling large-scale data. See 
			<a href="http://www.gigasciencejournal.com/" target="_blank">http://www.gigasciencejournal.com/</a> 
			for additional information about the journal and prospective article submission.
		</p>
		<a name="database"></a>                               
		<h2>Database: <em>GigaDB</em></h2>
		<p>
			<a href="/site/index" target="_blank"><em>GigaDB</em></a> 
			primarily serves as a repository to host data and tools 
			associated with articles in <a href="http://www.gigasciencejournal.com/" target="_blank">
			<em>GigaScience</em></a>; however, it also includes a subset of datasets that are not associated with GigaScience articles 
			(see below). <a href="/site/index/" target="_blank"><em>GigaDB</em></a> defines a dataset as a group of files 
			(e.g., sequencing data, analyses, imaging files, software programs) that are related to and support an article or study. 
			Through our association with <a href="http://www.datacite.org/" target="_blank">DataCite</a>, 
			each dataset in <a href="/site/index/" target="_blank"><em>GigaDB</em></a> will be assigned a 
			<a href="http://www.doi.org/" target="_blank">DOI</a> that can be used as a standard citation for future use of these 
			data in other articles by the authors and other researchers.  Datasets in <a href="/site/index/" target="_blank">
			<em>GigaDB</em></a> all require a title that is specific to the dataset, an author list, and an abstract that provides 
			information specific to the data included within the set. We encourage detailed information about the data we host to be 
			submitted by their creators in ISA-Tab, a format used by the BioSharing and ISA Commons communities that we work with to 
			maintain the highest data and metadata standards in our journal. To maximize its utility to the research community, all 
			datasets in <a href="/site/index/" target="_blank"><em>GigaDB</em></a> are placed under a 
			<a href="http://creativecommons.org/publicdomain/zero/1.0/" target="_blank">CC0 waiver</a> 
			(for more information on the issues surrounding CC0 and data see 
			<a href="http://www.biomedcentral.com/1756-0500/5/494" target="_blank">Hrynaszkiewicz and Cockerill, 2012</a>).
		</p>

		<p>
			Datasets that are not affiliated with a <a href="http://www.gigasciencejournal.com/" target="_blank">
			<em>GigaScience</em></a> article are approved for inclusion by the Editors of 
			<a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a>. 
			The majority of such datasets are from internal projects at the <a href="http://www.genomics.cn/" target="_blank">BGI</a>, 
			given their sponsorship of <a href="/site/index/" target="_blank"><em>GigaDB</em></a>. 
			Many of these datasets may not have another discipline-specific repository suitably able to host them or have been rapidly 
			released prior to any publications for use by the research community, whilst enabling their producers to obtain credit 
			through data citation. The <a href="http://www.gigasciencejournal.com/" target="_blank"><em>GigaScience</em></a> 
			Editors may also consider the inclusion of particularly interesting, previously unpublished datasets in 
			<a href="/site/index/" target="_blank"><em>GigaDB</em></a>, especially if they meet our criteria and inclusion as
			Data Note articles in the journal (see our author instructions 
			<a href="http://www.gigasciencejournal.com/authors/instructions/datanote" target="_blank">here</a>).
		</p>

		<p>
			To submit data to <em>Giga</em>DB, visit <a href="/site/index/" target="_blank">GigaDB.org</a> 
			or contact us at: <a href="mailto:database@gigasciencejournal.com" target="_blank">database@gigasciencejournal.com</a>.
		</p>
		<a name="citation"></a>

		<h2>Thomson Reuters Data Citation Index</h2>
		<p>
			<a href="/site/index/" target="_blank"><em>GigaDB</em></a> has been included in the 
			<a href="http://search.datacite.org/ui/"><em>DataCite search engine</em></a> and Thomson Reuters 
			<a href="http://wokinfo.com/products_tools/multidisciplinary/dci/"><em>Data Citation Index (DCI)</em></a> 
			to aid data discovery. Through DataCite the metadata is also exposed and accessible through their 
			<a href="http://oai.datacite.org/"><em>metadata store</em></a> through the Open Archives Initiative Protocol for 
			Metadata Harvesting (OAI-PMH). The DCI allows data to be discoverable to other researchers around the world. 
			This indexes a significant number of the world’s leading data repositories of critical interest to the scientific community. 
			The records for the datasets, which include authors, institutions, keywords, citations and other metadata, are connected to 
			related peer-reviewed literature indexed in their Web of Knowledge database.
		</p>         
		
		<a href="http://www.datacite.org/" target="_blank" class="aboutImgBot"><img title="DataCite_header_final1_1" src="/images/DataCite_header_final1_1.png" alt=""></a>
		<a href="http://isa-tools.org/" target="_blank" class="aboutImgBot"><img title="isa" src="/images/isa.jpg" alt=""></a>
		<a href="http://www.biosharing.org/" target="_blank" class="aboutImgBot"><img title="bioshare" src="/images/bioshare.jpg" alt=""></a>
		<a href="http://wokinfo.com/products_tools/multidisciplinary/dci/" target="_blank" class="aboutImgBot"><img title="data_citation" src="/images/data_citation.png" alt=""></a>
     
    </div>
</div>

