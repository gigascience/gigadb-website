<?php


function gftpfilesort($f1, $f2){
	$fn1 = strtolower($f1->filename);
	$fn2 = strtolower($f2->filename);
		
	$t1 = substr($f1->rights, 0, 1) == "d" ? 0 : 1;
	$t2 = substr($f2->rights, 0, 1) == "d" ? 0 : 1;
		
	if ($t1 == $t2) {
		if ($fn1 < $fn2) return -1;
		else if ($fn1 > $fn2) return 1;
		else return 0;
	} else if ($t1 < $t2) {
		return -1;
	} else {
		return 1;
	}
}

/**
 * Widget used to display FTP folder content under a Yii grid.
 * 
 * @author Hervé Guenot
 * @link http://www.guenot.info
 * @copyright Copyright &copy; 2012 Hervé Guenot
 * @license GNU LESSER GPL 3
 * @version 1.0
 */
class GFtpWidget extends CWidget {
	
	/** 
	 * @var GFtpComponent|GFtpApplicationComponent FTP connection. 
	 */	
	public $ftp = null;
	
	/** 
	 * @var string Folder content. 
	 */
	public $baseFolder = null;
	
	/** 
	 * @var bool Flag indicating if link will be displayed on folder to allow navigation. 
	 */
	public $allowNavigation = true;
	
	/** 
	 * @var string Navigation parameter name (passed as GET variable). 
	 */
	public $navKey = null;
	
	/** 
	 * @var array Columns to display. 
	 */
	public $columns = null;
	
	public function init() {
		if ($this->ftp == null) {
			if (!isset(Yii::app()->ftp)) {
				throw new CException('No ftp connection found. Please set an application component call "ftp" or set property ftp ');
			}
			$this->ftp = Yii::app()->ftp;
		}
		
		if ($this->baseFolder == null || in_array(trim($this->baseFolder), array("", ".", ".."))) {
			$this->baseFolder = "/";
		}
		
		if ($this->navKey == null || in_array(trim($this->navKey), array("", ".", ".."))) {
			$this->navKey = 'ftpNavFolder';
		}
		
		if ($this->columns == null) {
			$this->columns = array('rights', 'user', 'group', 'size', 'mdTime', 'filename');
		}
		
		if ($this->columns != null && !is_array($this->columns)) {
			$this->columns = array($this->columns);
		}
	}
	
	public function run() {
		$error = null;
		$files = array();
		
		if ($this->allowNavigation && isset($_GET[$this->navKey])) {
			try {
				$this->ftp->chdir($_GET[$this->navKey]);
				$this->baseFolder = $_GET[$this->navKey];
			} catch (GFtpException $e) {
				$error = $e;
			}
		} else {
			try {
				$this->ftp->chdir($this->baseFolder);
			} catch (GFtpException $e) {
				$error = $e;
			}
		}
		
		if ($error == null) {
			try {
				$files = $this->ftp->ls('.', true, false);
				$current = null;
				$parent = false;
				foreach ($files as $idx=>$file) {
					if ($file->filename == '.') {
						$current = $idx;
					} else if ($file->filename == '..') {
						$parent = true;
					}
				}

				if ($current !== null){
					unset($files[$current]);
				}
				
				usort ($files, 'gftpfilesort');
				
				if (!$parent && trim($this->baseFolder) != "/") {
					array_unshift($files, Yii::createComponent(array('class' => 'ext.GFtp.GFtpFile', 'filename' => '..', 'rights' => 'drwxr-xr-x')));
				}
			} catch (GFtpException $e) {
				$error = $e;
			}
		}
		
		$this->render ('ftpWidget', array('files' => $files, 'error' => $error));
	}
	
}

