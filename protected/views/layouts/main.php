<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="language" content="en" />
  <?php
  $isPrivate = $this->metaData['private'] === true;
  $isNonLiveEnv = in_array(getenv('GIGADB_ENV'), ['dev', 'CI', 'staging']);
  $isLiveEnv = getenv('GIGADB_ENV') === 'live';

  if ($isPrivate || $isNonLiveEnv) {
      echo '<meta name="robots" content="noindex, nofollow">';
      echo '<meta name="googlebot" content="noindex, nofollow">';
  } elseif (!$isPrivate && $isLiveEnv) {
      echo '<meta name="robots" content="all">';
      echo '<meta name="googlebot" content="all">';
  }
  ?>

    <!-- Primary Meta Tags -->
    <title>Meta Tags — Preview, Edit and Generate</title>
    <meta name="title" content="GigaDB Dataset - DOI 10.5524/<?php echo $this->metaData['doi'];?> - <?php echo $this->metaData['title']; ?>"/>
    <meta name="description" content="<?php echo $this->metaData['description']; ?>"/>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?php echo $this->metaData['doiUrl']; ?>"/>
    <meta property="og:title" content="GigaDB Dataset - DOI 10.5524/<?php echo $this->metaData['doi'];?> - <?php echo $this->metaData['title']; ?>"/>
    <meta property="og:description" content="<?php echo $this->metaData['description']; ?>"/>
    <meta property="og:image" content="<?php echo $this->metaData['imageUrl']; ?>"/>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image"/>
    <meta property="twitter:url" content="<?php echo $this->metaData['doiUrl']; ?>"/>
    <meta property="twitter:title" content="GigaDB Dataset - DOI 10.5524/<?php echo $this->metaData['doi'];?> - <?php echo $this->metaData['title']; ?>"/>
    <meta property="twitter:description" content="<?php echo $this->metaData['description']; ?>"/>
    <meta property="twitter:image" content="<?php echo $this->metaData['imageUrl']; ?>"/>

  <?php if ($this->metaData['redirect']) {
    Yii::app()->clientScript->registerMetaTag("5;url={$this->metaData['redirect']}", null, 'refresh');
  }
  ?>

  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="/fonts/open_sans/v13/open_sans.css">
  <link rel="stylesheet" type="text/css" href="/fonts/pt_sans/v8/pt_sans.css">
  <link rel="stylesheet" type="text/css" href="/fonts/lato/v11/lato.css">
  <link rel="stylesheet" type="text/css" href="/css/index.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" defer></script>
  <?php if (isset($this->loadBaBbqPolyfills) && $this->loadBaBbqPolyfills) {
    $this->renderPartial('//shared/_baBbqPolyfills');
  } ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js" defer></script>
  <title>
    <?php echo CHtml::encode($this->pageTitle); ?>
  </title>
  <?php if (!empty($this->canonicalUrl)) { ?>
    <link rel="canonical" href="<?= CHtml::encode($this->canonicalUrl) ?>" />
  <?php } ?>
  <?php $this->renderPartial('//shared/_matomo') ?>
</head>

<body>
  <?php
  $this->renderPartial('//shared/_header');
  ?>
  <main id="maincontent">
    <?php echo $content; ?>
  </main>
  <?php
  $this->renderPartial('//shared/_footer');
  ?>
  <script type="application/ld+json">
    { "@context": "http://schema.org", "@type": "DataCatalog", "name": "GigaDB.org", "description": "GigaDB primarily serves as a repository to host data and tools associated with articles in GigaScience; however, it also includes a subset of datasets that are not associated with GigaScience articles. GigaDB defines a dataset as a group of files (e.g., sequencing data, analyses, imaging files, software programs) that are related to and support an article or study. Through our association with DataCite, each dataset in GigaDB will be assigned a DOI that can be used as a standard citation for future use of these data in other articles by the authors and other researchers. Datasets in GigaDB all require a title that is specific to the dataset, an author list, and an abstract that provides information specific to the data included within the set. We encourage detailed information about the data we host to be submitted by their creators in ISA-Tab, a format used by the BioSharing and ISA Commons communities that we work with to maintain the highest data and metadata standards in our journal. To maximize its utility to the research community, all datasets in GigaDB are placed under a CC0 waiver (for more information on the issues surrounding CC0 and data see Hrynaszkiewicz and Cockerill, 2012).Datasets that are not affiliated with a GigaScience article are approved for inclusion by the Editors of GigaScience. The majority of such datasets are from internal projects at the BGI, given their sponsorship of GigaDB. Many of these datasets may not have another discipline-specific repository suitably able to host them or have been rapidly released prior to any publications for use by the research community, whilst enabling their producers to obtain credit through data citation. The GigaScience Editors may also consider the inclusion of particularly interesting, previously unpublished datasets in GigaDB, especially if they meet our criteria and inclusion as Data Note articles in the journal.", "alternateName": "GigaScience Journal Database", "license": "Public Domain", "citation": "Tam P. Sneddon, Xiao Si Zhe, Scott C. Edmunds, Peter Li, Laurie Goodman, Christopher I. Hunter; GigaDB: promoting data dissemination and reproducibility, Database, Volume 2014, 1 January 2014, bau018, https://doi.org/10.1093/database/bau018", "url": "https://GigaDB.org/", "keywords": ["registry", "life science", "GigaScience Journal"], "provider": [{ "@type": "Person", "name": "GigaDB.org support", "email": "database@gigasciencejournal.com" }] }
    </script>
    <?= $this->renderPartial('//shared/_handle_external_links') ?>
</body>

</html>