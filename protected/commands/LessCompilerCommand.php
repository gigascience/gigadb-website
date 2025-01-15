<?php
class LessCompilerCommand extends CConsoleCommand {
  public function run($args) {
    $less = new lessc;
    $basePath = "/var/www/";
      try {
          echo "Compiling index.less files at $basePath".PHP_EOL;
          $less->compileFile($basePath."less/index.less", $basePath."css/index.css");
      } catch (exception $e) {
          echo "fatal error: " . $e->getMessage();
      }
  }
}
