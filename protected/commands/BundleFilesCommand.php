<?php

class BundleFilesCommand extends CConsoleCommand {
    public function run($args) {

        $this->process_data();
        return 0;
    }

    private function process_data()
    {
        $queue = "bundle_queue";
        $local_dir = "/tmp";

        try {

            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('filespackaging');

            while (true) {
                // list($queue, $serialised_bundle) = Yii::app()->redis->executeCommand("BLPOP",array($queue, 0));
                // $bundle = unserialize($serialised_bundle);

                $job = $consumer->reserve();
                $result = $consumer->touch($job['id']);

                if( $result )
                {
                    $body_array = json_decode($job['body'], true);
                    $bundle = unserialize($body_array['list']);
                    echo var_dump($bundle);

                    //create directory for the files
                    $bundle_dir = self::random_string(20);
                    mkdir("$local_dir/$bundle_dir", 0700);

                    //create a compressed tar archive
                    $tar = new Archive_Tar("$local_dir/bundle_$bundle_dir.tar.gz", "gz");

                    foreach ($bundle as $file => $file_name) {
                        $full_local_path = Yii::app()->ftp->get($file," $local_dir/$bundle_dir/$file_name");
                    }

                    echo "Done...\n";
                    $consumer->delete($job['id']);
                    echo $consumer->statsJob($job['id']);
                }
                else
                {
                    // handle failure here
                    echo "Burying...\n";
                    $consumer->bury($job['id']);
                    echo $consumer->statsJob($job['id']);
                }



            }

            $consumer->disconnect();

        } catch (Exception $ex) {
            $error = $ex->getMessage();
            echo $error;
        }
    }


    public static function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}


}
