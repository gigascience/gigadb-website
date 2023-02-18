<?php 
class LessCompilerCommand extends CConsoleCommand {
  public function run($args) {
    $less = new lessc;
    $basePath = Yii::app()->basePath;
      try {
          echo "Compiling site.less files at $basePath".PHP_EOL;
          $less->compileFile($basePath."/../less/site.less", $basePath."/../css/site.css");
          echo "Compiling current.less files at $basePath".PHP_EOL;
          $less->compileFile($basePath."/../less/current.less", $basePath."/../css/current.css");
      } catch (exception $e) {
          echo "fatal error: " . $e->getMessage();
      }
  }
}
