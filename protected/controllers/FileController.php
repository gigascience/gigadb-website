<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 15:09
 */




class FileController extends Controller
{
    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */
    public $layout='//layouts/column2';

    function init() {
        $this->attachBehavior("fs", new LocalFileSystemBehavior());
        parent::init();
    }


    /**
     * @return array action filters
     */

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('Bundle', 'Download', 'Preview'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }


    public function actionBundle($operation) {

        $location = false;
        $filename = false;

        $result = array('status' => "ERROR");
        $fileinfo = Yii::app()->request->getParam('fileinfo');
        $dataset_id = Yii::app()->request->getParam('dataset_id');

        if(isset($fileinfo)) {
            $fileinfo_array = unserialize($fileinfo);
        }

        // echo var_dump($fileinfo_array);
        // Yii::app()->end();

        //init session bundle
        if(!isset(Yii::app()->session['bundle'])){
          Yii::app()->session['bundle'] = serialize(array());
        }


        if ($operation === 'addToBundle') {


            //add new item to session myvar
            $bundle = unserialize(Yii::app()->session['bundle']);
            //$bundle[$fileinfo_array['dataset']] = array() ;
            if ( !isset($bundle[$fileinfo_array['dataset']])) {
                $bundle[] = array($fileinfo_array['dataset'] => []);
            }
            $bundle[$fileinfo_array['dataset']][$fileinfo_array['location']] = array( "location" => $fileinfo_array['location'], "filename" =>  $fileinfo_array['filename'], "type" => $fileinfo_array['type']) ;
            Yii::app()->session['bundle'] = serialize($bundle);
            $result["status"] = "OK";
            $result["lastop"] = "addToBundle";
            //error_log("Updated session bundler (" .$fileinfo_array['dataset'] . "): " . Yii::app()->session['bundle'],0);
        }
        else if ($operation === 'removeFromBundle') {
            $bundle = unserialize(Yii::app()->session['bundle']);
            unset($bundle[$fileinfo_array['dataset']][$fileinfo_array['location']]);
            Yii::app()->session['bundle'] = serialize($bundle);
            $result["status"] = "OK";
            $result["lastop"] = "removeFromBundle";
        }
        else if ($operation === 'downloadSelection') {
            $bid = $this->prepare_bundle_job(Yii::app()->session['bundle'], $dataset_id);

            if ( false === $bid ) {
                $result["status"] = "ERROR";
                $result["error"] = "Failing to queue a files packaging job";
            }
            else {
                $result["status"] = "OK";
                $result["bid"] = $bid;
            }
            $result["lastop"] = "downloadSelection";
        }
        else {
            $result["status"] = "ERROR";
            $result["error"] = "Unrecognised operation";
        }

        echo json_encode($result);
        // Yii::app()->session[$location] = 1;
        // echo Yii::app()->session[$location]; // Prints "value"
        //unset(Yii::app()->session[$location]);

        //var_dump(Yii::app()->session->keys);
		Yii::app()->end();

    }

    public function actionDownload($bid) {

        $download_url = 'http://gigadb-bundles-test.s3.amazonaws.com/'.$bid.'.tar.gz' ;
        $bundle = new Bundle();
        $bundle->bid = $bid;
        $bundle->download_url = $download_url;
        // echo var_dump($bundle);
        // Yii::app()->end();
        $this->render('download',array(
            'bundle'=>$bundle
        ));
    }

    public function actionPreview() {

        $supported_formats = Yii::app()->preview->supported_media_types ;

        $result = array('status' => "UNKNOWN");

        if(isset($_POST['location'])){
			$location = $_POST['location'];
        } else {
            echo json_encode($result);
            Yii::app()->end();
        }

        $ext = pathinfo(parse_url($location)['path'], PATHINFO_EXTENSION) ;
        $mime_type = $this->extension_to_mime_type(  $ext );
        //in case the file is a gzipped file, we need to check mime type of uncompressed file
        if ("application/x-gzip" === $mime_type ) {
            $uncompressed_extension =  pathinfo(pathinfo(parse_url($location)['path'], PATHINFO_FILENAME),PATHINFO_EXTENSION) ;
            $mime_type = $this->extension_to_mime_type(  $uncompressed_extension );
        }

        $result['mime_type'] = $mime_type ;
        $result['extension'] = $ext ;

        if( false ===  in_array($mime_type , $supported_formats) ) {
            $result['status'] = "UNSUPPORTED";
            echo json_encode($result);
            Yii::app()->end();
        }


        $preview_status_raw = Yii::app()->redis->executeCommand('GET',array(md5($location))) ;


        if (isset($preview_status_raw)) {
            $result['redis_raw'] = $preview_status_raw ;
            $preview_status = json_decode($preview_status_raw, true) ;
            if ( isset($preview_status['status']) && ("INITIATED" === $preview_status['status'] || "INPROGRESS" === $preview_status['status'] || "DELAYED" === $preview_status['status'] )) {
                $result['status'] = "PENDING";
            }
            else if ( isset($preview_status['status']) && "FAILED" === $preview_status['status']) {
                $result['status'] = "ERROR";
            }
            else if ( isset($preview_status['status']) && "COMPLETED" === $preview_status['status'] &&  isset($preview_status['url']) ) {

                if ( true === Yii::app()->mfr->is_preview_server_ok() && in_array($ext, Yii::app()->mfr->getsupportedExtensions() ) ) {
                    $result['preview_url'] =  Yii::app()->mfr->get_preview_server_url("render?url=") . $preview_status['url'];
                }
                else {
                    $result['preview_url'] =  $preview_status['url'];
                }
                $result['status'] = "OK";

            }
            else {
                $this->prepare_preview_job($location);
                $result['status'] = "INITIATED";
            }
        }
        else {
            $this->prepare_preview_job($location);
            $result['status'] = "INITIATED";
        }


        echo json_encode($result);
        Yii::app()->end();

    }

    private function prepare_preview_job($location) {
        if($location) {
            $client = Yii::app()->beanstalk->getClient();
            $client->connect();
            $client->useTube(Yii::app()->preview->preview_job_queue);
            $jobDetails = [
                'application'=>'gigadb-website',
                'location'=>$location,
                'submission_time'=>date("c"),
            ];

            $jobDetailString = json_encode($jobDetails);

            $ret = $client->put(
                0, // priority
                0, // do not wait, put in immediately
                90, // will run within n seconds
                $jobDetailString // job body
            );

            if ($ret) {
                return $ret; //return the bundle id that identifies the bundle across all systems
            }
            else {
                return false;
            }

        }
        else {
            return false;
        }
    }
    private function prepare_bundle_job($serialised_bundle, $dataset_id) {
        $bundle = unserialize($serialised_bundle);

        if(isset($bundle) && count($bundle[$dataset_id]) > 0  ) {
            $bid = self::random_string(20);
            $client = Yii::app()->beanstalk->getClient();
            $client->connect();
            $client->useTube('filespackaging');
            $jobDetails = [
                'application'=>'gigadb-website',
                'list'=>$serialised_bundle,
                'bid'=>$bid,
                'dataset_id'=>$dataset_id,
                'submission_time'=>date("c"),
            ];

            $jobDetailString = json_encode($jobDetails);

            $ret = $client->put(
                0, // priority
                0, // do not wait, put in immediately
                90, // will run within n seconds
                $jobDetailString // job body
            );

            if ($ret) {
                return $bid; //return the bundle id that identifies the bundle across all systems
            }
            else {
                return false;
            }


        }
        else {
            return false;
        }
    }

    private static function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

}
