<?php

class ImageHaver extends CActiveRecord {

    public $size = 600;
    public $thumbSize = 120;
    public $smallThumbSize = 64;

    public function createDirs($type) {
        $dir = Yii::getPathOfAlias('uploadPath') . "/{$type}";
        $dir_thumbs = "$dir/thumbs";
        $dir_small_thumbs = "$dir/small_thumbs";
        if (!file_exists($dir)) mkdir($dir);
        if (!file_exists($dir_thumbs)) mkdir($dir_thumbs);
        if (!file_exists($dir_small_thumbs)) mkdir($dir_small_thumbs);
    }

    # Path to file without leading directory or URL
    public function getPath($type, $size='') {
        $class = get_class($this);
        $dir = "/$type";
        switch ($size) {
            case 'thumb': $dir = "$dir/thumbs"; break;
            case 'small_thumb': $dir = "$dir/small_thumbs"; break;
            // Default: Full-sized image straight in $dir
        }

        if (property_exists($class, 'has_photo_id') and isset($this->photo_id)) {
            $path = "$dir/{$class}_{$this->photo_id}.png";
        } else {
            $path = "$dir/{$class}_{$this->id}.png";
        }
        #Yii::log(__FUNCTION__."> path: $path", 'debug');
        return $path;
    }

    public function getFullPath($type, $size="") {
        return Yii::getPathOfAlias('uploadPath') . $this->getPath($type, $size);
    }

    public function getUrl($type, $size="") {
        return Yii::app()->request->baseURL . Yii::getPathOfAlias('uploadURL') . $this->getPath($type, $size);
    }

    public function image($type) {
        $path = $this->getFullPath($type);
        if (!file_exists($path)) return null;
        return $this->getUrl($type);
    }

    public function thumb($type) {
        $path = $this->getFullPath($type, 'thumb');
        if (!file_exists($path)) return null;
        return $this->getUrl($type, 'thumb');
    }

    public function smallThumb($type) {
        $path = $this->getFullPath($type, 'small_thumb');
        if (!file_exists($path)) return null;
        return $this->getUrl($type, 'small_thumb');
    }

    public function setImage($type, $image) {
        $path = $this->getFullPath($type);
        $thumbPath = $this->getFullPath($type, 'thumb');
        $smallThumbPath = $this->getFullPath($type, 'small_thumb');
        #Yii::log(__FUNCTION__."> path: $path", 'debug');
        #Yii::log(__FUNCTION__."> thumbPath: $thumbPath", 'debug');
        #Yii::log(__FUNCTION__."> smallThumbPath: $smallThumbPath", 'debug');
        if ($image->getSize() > 0) {
            Yii::log(__FUNCTION__."> attempting to store image : $path",'debug');
            $this->createDirs($type);
            if (!$image->saveAs($path)) {
                Yii::log("Could not save file to path: $path", 'error');
                return false;
            }
	    #Yii::log("Got it to: $path", 'debug');
            //            $this->transformImage($path, $path, $this->size, null);
            $this->transformImage($path, $thumbPath, $this->thumbSize, null);
            $this->transformImage($path, $smallThumbPath, $this->smallThumbSize, null);
        } else {
            if (file_exists($path)) return unlink($path);
            else return true;
        }
    }

    # Generate new thumbnails from existing image
    public function resizeImages($type) {
        $path = $this->getFullPath($type);
        $thumbPath = $this->getFullPath($type, 'thumb');
        $smallThumbPath = $this->getFullPath($type, 'small_thumb');
        #Yii::log(__FUNCTION__.'> Path: ' . $path, 'debug');
        $this->createDirs($type);

        if (file_exists($path)) {
            Yii::log(__FUNCTION__.'> Resizing image: ' . $path, 'debug');
            $this->transformImage($path, $thumbPath, $this->thumbSize, null);
            $this->transformImage($path, $smallThumbPath, $this->smallThumbSize, null);
        }
    }


  // Where should this go?
  public function imageChooserField($type) {
    $image = $this->image($type);

    // Quick fix for the problem with Images_.png
    $fn = '' ;
    if($image){
        $fn = explode('/' , $image);
        $fn = end($fn);
    }

    if ($image !== null && $fn != 'Images_.png') {
      ?>
        <table>
          <tr>
            <td width="30"><?=CHtml::radioButton("use_$type", true, array('value'=>'current'))?></td>
            <td>Keep <a href="<?= $image ?>">current</a></td>
          </tr>
          <tr>
            <td width="30"><?=CHtml::radioButton("use_$type", false, array('value'=>''))?></td>
            <td><?=CHtml::fileField("{$type}_image")?></td>
          </tr>
        </table>
      <?php
        } else {
      echo CHtml::fileField("{$type}_image");
    }
  }

    // Or this, for that matter
    public function updateImage($type) {
        if (!isset($_POST["use_$type"]) or $_POST["use_$type"]!= 'current') {
    	    $image = CUploadedFile::getInstanceByName("{$type}_image");
	        if ($image !== null) {
                $this->setImage($type, $image);
                Yii::log('update image', 'debug');
            }
        }
    }

    public function imageTag($type, $alt='') {
        $url = $this->image($type);
        if (!$url) return $this->defaultImage($type);
        return "<img src='$url' alt='$alt' />";
    }

    public function thumbTag($type, $alt='') {
      $url = $this->thumb($type);
      if (!$url) return $this->thumbDefaultImage($type);
      return "<img src='$url' alt='$alt' class='thumbTag' />";
    }

    public function smallThumbTag($type, $alt='') {
        $url = $this->smallThumb($type);
        if (!$url) return $this->smallDefaultImage($type);
        return "<img src='$url' alt='$alt' class='thumbTag' />";
    }

    public function featureTitleTag($type, $alt='') {
        $url = $this->smallThumb($type);
        if ($url)
          return "<img src='$url' alt='$alt' height='18' width='18' />";
    }

    public function defaultImage($type, $alt='') {
        if (!$alt) {
            $alt = "anonymous $type";
        }
        $url = Yii::app()->request->baseURL.'/images/anon_'.$type.'.png';
        return "<img src='$url' alt='$alt' />";
    }

    public function thumbDefaultImage($type, $alt='') {
        if (!$alt) {
            $alt = "anonymous $type";
        }
        $url = Yii::app()->request->baseURL.'/images/anon_'.$type.'.png';
        return "<img src='$url' alt='$alt' class='thumbTag' />";
    }

    public function smallDefaultImage($type, $alt='') {
        if (!$alt) {
            $alt = "anonymous $type";
        }
        $url = Yii::app()->request->baseURL.'/images/small_anon_'.$type.'.png';
        return "<img src='$url' alt='$alt' />";
    }

    public function transformImage($fromPath, $toPath, $width, $height) {
        $image = Yii::app()->image->load($fromPath);
        if ($image) {
            $image->resize($width, $height);
            #Yii::log("From path: $fromPath", "error");
            #Yii::log("Saving to path: $toPath", "error");
            $image->save($toPath);
        }
    }

}
