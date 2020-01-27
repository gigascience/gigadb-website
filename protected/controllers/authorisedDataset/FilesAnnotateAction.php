<?php
/**
 * This action will load the metadata form
 *
 * URL: /authorisedDataset/filesAnnotate/100006
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FilesAnnotateAction extends CAction
{

    public function run($id)
    {
        $this->getController()->layout='uploader_layout';
        $this->getController()->render("filesAnnotate", array("identifier" => $id));
    }
}

?>