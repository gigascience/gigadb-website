<?php
/**
 * Configuration script to generate a JSON file listing all file formats 
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class GenerateFileFormatsCommand extends CConsoleCommand {
	
	public function getHelp() {
        return 'Usage for generating file formats JSON feed: yiic generatefileformats';
    }

    public function run($args) {
    	$outputFile = "/var/www/files/data/fileformats.json";
        Yii::log('Running Yii command generatefileformats', 'debug');
        echo "Running Yii command generatefileformats".PHP_EOL;
        $dao = new FileFormatDAO();
		$fileformats = $dao->toJSON();
		file_put_contents($outputFile, $fileformats);
		Yii::log("Wrote $outputFile", 'debug');
		echo "Wrote $outputFile".PHP_EOL;
	}
}

?>