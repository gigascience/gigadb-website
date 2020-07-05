<?php
/**
 * Configuration script to generate a JSON file listing all file types 
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class GenerateFileTypesCommand extends CConsoleCommand {
	
	public function getHelp() {
        return 'Usage for generating file types JSON feed: yiic generatefiletypes';
    }

    public function run($args) {
    	$outputFile = "/var/www/files/data/filetypes.json";
        Yii::log('Running Yii command generatefiletypes', 'debug');
        echo "Running Yii command generatefiletypes".PHP_EOL;
        $systemUnderTest = new FileTypeDAO();
		$filetypes = $systemUnderTest->toJSON();
		file_put_contents($outputFile, $filetypes);
		Yii::log("Wrote $outputFile", 'debug');
		echo "Wrote $outputFile".PHP_EOL;
	}
}

?>