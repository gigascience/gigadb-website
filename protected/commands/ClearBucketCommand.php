<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class ClearBucketCommand extends CConsoleCommand {

    public function run($args) {

        $this->attachBehavior("commandline", new CommandLineBehavior()) ;

        $this->parseArguments($args,array("h" => "help",  "b" => "bucket", "d" => "delete"));
        $this->setHelpMessage(array(
            "Usage:",
            "/vagrant/protected/yiic clearbucket -h|--help",
            "/vagrant/protected/yiic clearbucket -b|--bucket=<bucket name>|preview [ -d|--delete=yes|no ]"
        ));

        $this->validateMandatoryOptions(["bucket"]) ;

        if( "preview" === $this->getOption('bucket') ) {
            $this->setOption('bucket',Yii::app()->aws->preview_bucket) ;
        }


        $s3 = Yii::app()->aws->getS3Instance();


      //Delete the objects in the bucket before attempting to delete
      //the bucket
      echo "about to clear objects from bucket: " .  $this->getOption('bucket') . PHP_EOL ;
      $clear = new  \Aws\S3\Model\ClearBucket($s3,  $this->getOption('bucket'));
      $clear->clear();

      // Delete the bucket
      if ("yes" === $this->getOption('delete')) {
          echo "about to delete bucket: " .  $this->getOption('bucket') . PHP_EOL ;
          $s3->deleteBucket(array('Bucket' =>  $this->getOption('bucket')));
          // Wait until the bucket is not accessible
          $s3->waitUntil('BucketNotExists', array('Bucket' => $this->getOption('bucket')));
          echo "Bucket " . $this->getOption('bucket') . " deleted" . PHP_EOL ;
          return 0 ;
      }
      else {
          echo "Bucket NOT deleted" . PHP_EOL ;
      }
      return 0 ;

    }

}

?>
