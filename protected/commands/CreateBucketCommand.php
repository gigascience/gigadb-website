<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class CreateBucketCommand extends CConsoleCommand {
  public function run($args) {

    $this->attachBehavior("commandline", new CommandLineBehavior()) ;

    $this->parseArguments($args,array("h" => "help",  "p" => "preview", "b" => "bundle", "a" =>"all", "c" => "fromconfig"));
    $this->setHelpMessage(array(
      "Usage:",
      "/vagrant/protected/yiic createbucket -h|--help",
      "/vagrant/protected/yiic createbucket -b|--bundle=<bucket name 1> -p|--preview=<bucket name 2>",
      "/vagrant/protected/yiic createbucket -a|--all=<bucket name>",
      "/vagrant/protected/yiic createbucket -c|--fromconfig"
    ));


    if (0 === $this->optionsCount()) {
        $this->printHelpMessage("No arguments passed") ;
        return 1;
    }
    else if( $this->getOption('fromconfig') ) {
        $bundle_bucket = Yii::app()->aws->bundle_bucket ;
        $preview_bucket = Yii::app()->aws->preview_bucket ;
    }
    else if ( $this->getOption('all') ) {
        $bundle_bucket =  $this->getOption('all') ;
        $preview_bucket = $this->getOption('all') ;
    }
    else if ( $this->getOption('bundle') || $this->getOption('preview')) {
        $bundle_bucket =  $this->getOption('bundle') ;
        $preview_bucket = $this->getOption('preview') ;
    }
    else {
        $this->printHelpMessage("Incorrect arguments") ;
        return 1;
    }

    $s3 = Yii::app()->aws->getS3Instance();


    if( $bundle_bucket ) {
        echo "Creating bucket for bundle functionality: " . $bundle_bucket . PHP_EOL ;
        $s3->createBucket(array('Bucket' => $bundle_bucket));
        $s3->waitUntil('BucketExists', array('Bucket' => $bundle_bucket));
    }

    if ( $preview_bucket ) {
        echo "Creating bucket for preview functionality: " . $preview_bucket . PHP_EOL ;
        $s3->createBucket(array('Bucket' => $preview_bucket));
        $s3->waitUntil('BucketExists', array('Bucket' => $preview_bucket));
    }

    return 0 ;
  }
}

?>
