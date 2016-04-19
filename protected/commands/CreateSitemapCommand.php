<?php

class CreateSitemapCommand extends CConsoleCommand {

    public function getHelp() {
        echo "Create site map";
    }

    public function run($args) {
        $site = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // datasets
        foreach (Dataset::model()->findAll() as $dataset) {
            $url = 'dataset/'.$dataset->identifier;
            $site .= $this->createUrlBlock($url, null, 'weekly', 1.0);
        }

        // news
        foreach (News::model()->findAll() as $newsItem) {
            $url = 'news/id/'.$newsItem->id;
            $site .= $this->createUrlBlock($url, null, 'weekly', 0.5);
        }

        // static pages
        $site .= $this->createUrlBlock('site/about', null, 'yearly', 0.3);
        $site .= $this->createUrlBlock('site/contact', null, 'yearly', 0.3);
        $site .= $this->createUrlBlock('site/term', null, 'yearly', 0.3);


        $site .= '</urlset>';
        //write to file
        $file = fopen(dirname(__FILE__)."/../../sitemap.xml", 'w');
        fwrite($file, $site);
        fclose($file);
    }

    /* change freq and priority are not in use right now */
#        <changefreq>$freq</changefreq>
#        <priority>$priority</priority>

    protected function createUrlBlock($url, $date, $freq, $priority) {
        date_default_timezone_set('Asia/Taipei');
        if (!$date) {
          $date = date('Y-m-d');
        } else if (!empty($date)) {
          $date = date('Y-m-d', strtotime($date));
        } else {
          $date = date('Y-m-d');
        }
        $link = $this->createLink($url);
        $url = "
    <url>
        <loc>$link</loc>
        <lastmod>$date</lastmod>
    </url>";
        return $url;
    }

    protected function createLink($directory) {
        return Yii::App()->params['home_url'] . "/$directory";
    }

}

?>
