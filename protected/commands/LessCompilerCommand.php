<?php 
class LessCompilerCommand extends CConsoleCommand {
  public function run($args) {
    $less = new lessc;
    $basePath = Yii::app()->basePath;
    $less->compileFile($basePath."/../less/site.less", $basePath."/../css/site.css");
  }
}