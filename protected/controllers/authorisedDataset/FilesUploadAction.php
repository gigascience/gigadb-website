<?php
/**
 * This action will load Uppy.io based file uploader for dataset
 *
 * URL: /authorisedDataset/uploadFiles/100006
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FilesUploadAction extends CAction
{

    public function run($id)
    {
        $this->getController()->layout='uploader_layout';
        $this->getController()->render("filesUpload", array("identifier" => $id));
    }
}

?>