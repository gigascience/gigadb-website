<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 20/02/2017
 * Time: 16:01
 */



class DirectoryListing extends GFtpFile implements DatasetFilesInterface
{
    public $isDirectory;
    public $location;
    public $dataset_identifier;


    public static function toDirectoryListing(GFtpFile $f, $location_path, $dataset_identifier) {
        $dl = Yii::createComponent(array('class' => 'DirectoryListing',
                                                            'filename' => $f->filename,
                                                            'group' => $f->group,
                                                            'mdTime' => $f->mdTime,
                                                            'rights' => $f->rights,
                                                            'size' => $f->size,
                                                            'user' => $f->user
        ));



        $dl->location = $location_path.'/'.$dl->filename;
        $dl->dataset_identifier = $dataset_identifier ;
        $dl->isDirectory = substr($dl->rights, 0, 1) === 'd'?true:false;
        $dl->attachBehavior("bundle", new BundleBehavior());

        return $dl;
    }

    public static function toBreadCrumbs($dataset_identifier, $location_path, $location_url) {
        $unactive_crumbs = explode("/",$location_path);
        $trimmed_unactive_crumbs = array_splice($unactive_crumbs,4); // pub/doi/x_y/identifier should not appear in the breadcrumb navigation
        $breadcrumbs = [] ;
        $url_under_construction =  "ftp://climb.genomics.cn/pub/10.5524/100001_101000/$dataset_identifier" ;
        foreach( $trimmed_unactive_crumbs as $current) {
            $url_under_construction = $url_under_construction . "/" . $current  ;
            $breadcrumbs["$current/"] = CHtml::encode(Yii::app()->request->getBaseUrl(true) . "/" . Yii::app()->request->pathInfo . "?location=" . $url_under_construction . "#file_table") ;
        }

        //last element shouldn't be a link
        array_pop($breadcrumbs) ;
        $breadcrumbs[] = "$current ";

        return $breadcrumbs;
    }

    /**
     * Implementing method from the DatasetFilesInterface
     * @return return a dataset identifier (the document related part of a DOI)
     */
    public function getDatasetIdentifier() {
        return $this->dataset_identifier ;
    }


    /**
     * Implementing method from the DatasetFilesInterface
     * @return return an url string
     */
    public function getLocationUrl() {
        return $this->location ;
    }


}
