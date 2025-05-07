<?php

$isPrivate = $this->metaData['private'] === true;
$isNonLiveEnv = in_array(YII_ENV, ['dev', 'CI', 'staging']);
$isLiveEnv = YII_ENV === 'live';

if ($isPrivate || $isNonLiveEnv) {
    echo '<meta name="robots" content="noindex, nofollow">';
} elseif (!$isPrivate && $isLiveEnv) {
    echo '<meta name="robots" content="all">';
}
?>

<!-- Primary Meta Tags -->
<meta name="title" content="<?php echo rtrim($this->pageTitle); ?>"/>
<meta name="description" content="<?php echo $this->metaData['description']; ?>"/>

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website"/>
<meta property="og:url" content="<?php echo $this->metaData['doiUrl']; ?>"/>
<meta property="og:title" content="<?php echo rtrim($this->pageTitle); ?>"/>
<meta property="og:description" content="<?php echo $this->metaData['description']; ?>"/>
<meta property="og:image" content="<?php echo $this->metaData['imageUrl']; ?>"/>

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image"/>
<meta property="twitter:url" content="<?php echo $this->metaData['doiUrl']; ?>"/>
<meta property="twitter:title" content="<?php echo rtrim($this->pageTitle) ?>"/>
<meta property="twitter:description" content="<?php echo $this->metaData['description']; ?>"/>
<meta property="twitter:image" content="<?php echo $this->metaData['imageUrl']; ?>"/>
